<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alizeti;
use App\Models\Stock;
use App\Models\Uchujaji; 
use App\Models\Uzalishaji;
use App\Models\Mauzo; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;

class AlizetiController extends Controller
{

    public function displaySummary(Request $request)
{
    $alizetiQuery = Alizeti::with('user')->select(
        'tarehe',
        'batch_no',
        'al_kilogram',
        'gunia_total',
        'price_per_kilo',
        'total_price',
        'user_id',
        'ali_id',
    )->latest();

    if ($request->has('batch_no') && $request->batch_no != '') {
        $alizetiQuery->where('batch_no', $request->batch_no);
    }

    if ($request->has('start_date') && $request->has('end_date')) {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($startDate > $endDate) {
            return back()->with('error', 'Start date cannot be after end date.');
        }

        $alizetiQuery->whereBetween('tarehe', [$startDate, $endDate]);
    }

    $alizetiSummary = $alizetiQuery->get();
    $uniqueBatches = Alizeti::select('batch_no')->distinct()->get();

    return view('alizeti.summary', compact('alizetiSummary', 'uniqueBatches'));
}
   
    public function index(Request $request)
    {
        $alizetiQuery = Alizeti::with('user','uzalishajiz','mauzo')->latest();

        if ($request->has('batch_no') && $request->batch_no != '') {
            $alizetiQuery->where('batch_no', $request->batch_no);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            if ($startDate > $endDate) {
                return back()->with('error', 'Start date cannot be after end date.');
            }

            $alizetiQuery->whereBetween('tarehe', [$startDate, $endDate]);
        }

        $alizeti = $alizetiQuery->get();
        $uniqueBatches = Alizeti::select('batch_no')->distinct()->get();

        return view('alizeti.index', compact('alizeti', 'uniqueBatches'));
    }

    
    public function create()
    {
        return view('alizeti.create');
    }

    public function generateBatch(Request $request)
    {
        try {
            $currentYear = date('y');
            $latestAlizeti = Alizeti::where('batch_no', 'LIKE', 'BATCH-' . $currentYear . '-%')->latest()->first();

            if ($latestAlizeti) {
                $latestBatchNo = $latestAlizeti->batch_no;
                $batchNumber = (int)substr($latestBatchNo, strrpos($latestBatchNo, '-') + 1) + 1;
            } else {
                $batchNumber = 1;
            }

            $batchNo = 'BATCH-' . $currentYear . '-' . str_pad($batchNumber, 4, '0', STR_PAD_LEFT);

            return $batchNo;
            
        } catch (\Exception $e) {
            Log::error('Error generating batch number: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'tarehe' => 'required|date',
            'al_kilogram' => 'required|numeric|min:0',
            'gunia_total' => 'required|integer|min:0',
            'price_per_kilo' => 'required|numeric|min:0',
            'batch_no' => 'required|unique:alizeti,batch_no', 
        ]);

        try {
            DB::beginTransaction();
           
            $totalPrice = $request->al_kilogram * $request->price_per_kilo;            
            $alizeti = Alizeti::create([
                'tarehe' => $request->tarehe,
                'user_id' => Auth::id(),
                'batch_no' => $request->batch_no,
                'al_kilogram' => $request->al_kilogram,
                'gunia_total' => $request->gunia_total,
                'price_per_kilo' => $request->price_per_kilo,
                'total_price' => $totalPrice,
            ]);
            
            $stock = Stock::where('alizeti_id', $alizeti->ali_id)->first();

            if ($stock) {
                $stock->total_al_kgms += $request->al_kilogram;
                $stock->save();
            } else {
                Stock::create([
                    'alizeti_id' => $alizeti->ali_id,
                    'total_al_kgms' => $request->al_kilogram,
                    'mafuta_machafu' => 0,
                    'mafuta_masafi' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('alizeti.index')->with('success', 'Record added successfully and stock updated/created.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding alizeti record: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while adding the record.');
        }
    }
    
    public function show(Alizeti $alizeti)
    {
        return view('alizeti.show', compact('alizeti'));
    }
    
    public function edit(Alizeti $alizeti)
    {
        return view('alizeti.edit', compact('alizeti'));
    }
    
    public function update(Request $request, Alizeti $alizeti)
    {
        
        $request->validate([
            'al_kilogram' => 'required|numeric|min:0',
            'gunia_total' => 'required|integer|min:0',
            'price_per_kilo' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = $request->al_kilogram * $request->price_per_kilo;

            $oldAlKilogram = $alizeti->al_kilogram;

            
            $alizeti->update([
                'al_kilogram' => $request->al_kilogram,
                'gunia_total' => $request->gunia_total,
                'price_per_kilo' => $request->price_per_kilo,
                'total_price' => $totalPrice,
            ]);
            
            $stock = Stock::where('alizeti_id', $alizeti->ali_id)->first();

            if ($stock) {

                $stock->total_al_kgms = $stock->total_al_kgms - $oldAlKilogram + $request->al_kilogram;
                $stock->save();
            } else {
                Log::error('Stock record not found for alizeti_id: ' . $alizeti->ali_id);
            }

            DB::commit();

            return redirect()->route('alizeti.index')->with('success', 'Record updated successfully and stock updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating alizeti record: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the record.');
        }
    }
   
    public function destroy(Alizeti $alizeti)
    {
        $alizeti->delete();
        return redirect()->route('alizeti.index')->with('success', 'Record deleted successfully.');
    }
}