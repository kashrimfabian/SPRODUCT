<?php

namespace App\Http\Controllers;

use App\Models\Mashudu;
use App\Models\Alizeti;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MashuduController extends Controller
{
    public function index(Request $request)
    {
        $query = Mashudu::with(['alizeti', 'price'])
            ->when($request->date_from, function ($query, $dateFrom) {
                return $query->where('tarehe', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                return $query->where('tarehe', '<=', $dateTo);
            })
            ->when($request->batch_no, function ($query, $batchNo) {
                return $query->whereHas('alizeti', function ($query) use ($batchNo) {
                    $query->where('batch_no', $batchNo);
                });
            })
            ->when($request->payment_way, function($query, $paymentWay){
                return $query->where('payment_way', $paymentWay);
            });

        $mashuduSales = $query->latest()->get();

        $totalMashudu = $mashuduSales->sum('mashudu');
        $totalPrice = $mashuduSales->sum('total_price');

        $alizetiList = Alizeti::all();

        return view('mashudu.index', compact('mashuduSales', 'alizetiList', 'totalMashudu', 'totalPrice'));
    }

    public function create()
    {
        $alizeti = Alizeti::all();
        return view('mashudu.create', compact('alizeti'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'mashudu' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'alizeti_id' => 'required|exists:alizeti,alizeti_id',
            'payment_way' => 'nullable|in:cash,Lipa_Namba',
        ]);

        DB::beginTransaction();

        $alizeti = Alizeti::find($validated['alizeti_id']);

        if ($alizeti->shudu < $validated['mashudu']) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Not enough shudu available in the selected alizeti.');
        }

        $alizeti->shudu -= $validated['mashudu'];
        $alizeti->save();

        $priceRecord = Price::where('alizeti_id', $validated['alizeti_id'])->latest()->first();

        if (!$priceRecord) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No price set for the selected alizeti batch.');
        }

        $price = $priceRecord->price_of_mashudu;

        $finalPrice = $price * $validated['mashudu'];
        $discount = $validated['discount'] ?? 0;
        $totalPrice = $finalPrice - $discount;

        Mashudu::create([
            'tarehe' => $validated['tarehe'],
            'mashudu' => $validated['mashudu'],
            'price' => $price,
            'discount' => $discount,
            'total_price' => $totalPrice,
            'alizeti_id' => $validated['alizeti_id'],
            'user_id' => auth()->id(),
            'price_id' => $priceRecord->prices_id,
            'payment_way' => $validated['payment_way'],
        ]);

        DB::commit();
        return redirect()->route('mashudu.index')->with('success', 'Mashudu sale recorded successfully!');
    }

    public function show(Mashudu $mashudu)
    {
        return response()->json($mashudu->load(['alizeti', 'price']));
    }

    public function edit(Mashudu $mashudu)
    {
        $alizeti = Alizeti::all();
        return view('mashudu.edit', compact('mashudu', 'alizeti'));
    }

    public function update(Request $request, Mashudu $mashudu)
    {
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'mashudu' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'alizeti_id' => 'required|exists:alizeti,alizeti_id',
            'payment_way' => 'nullable|in:cash,Lipa_Namba',
        ]);

        DB::beginTransaction();

        $alizeti = Alizeti::find($validated['alizeti_id']);
        $oldMashudu = $mashudu->mashudu;
        $newMashudu = $validated['mashudu'];

        if ($alizeti->shudu + $oldMashudu < $newMashudu) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Not enough mashudu available in the selected alizeti.');
        }

        $alizeti->shudu += $oldMashudu - $newMashudu;
        $alizeti->save();

        $priceRecord = Price::where('alizeti_id', $validated['alizeti_id'])->latest()->first();

        if (!$priceRecord) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No price set for the selected alizeti batch.');
        }

        $price = $priceRecord->price_of_mashudu;

        $finalPrice = $price * $validated['mashudu'];
        $discount = $validated['discount'] ?? 0;
        $totalPrice = $finalPrice - $discount;

        $mashudu->update([
            'tarehe' => $validated['tarehe'],
            'mashudu' => $validated['mashudu'],
            'price' => $price,
            'discount' => $discount,
            'total_price' => $totalPrice,
            'alizeti_id' => $validated['alizeti_id'],
            'user_id' => auth()->id(),
            'price_id' => $priceRecord->prices_id,
            'payment_way' => $validated['payment_way'],
        ]);

        DB::commit();
        return redirect()->route('mashudu.index')->with('success', 'Mashudu sale updated successfully!');
    }

    public function destroy(Mashudu $mashudu)
    {
        DB::beginTransaction();

        $alizeti = Alizeti::find($mashudu->alizeti_id);
        $alizeti->shudu += $mashudu->mashudu;
        $alizeti->save();

        $mashudu->delete();

        DB::commit();
        return redirect()->route('mashudu.index')->with('success', 'Mashudu sale deleted successfully!');
    }
}