<?php

namespace App\Http\Controllers;

use App\Models\Uchekechaji;
use App\Models\Alizeti;
use App\Models\Stock;;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UchekechajiController extends Controller
{
    public function index()
    {
        $uchekechajiOperations = Uchekechaji::with(['user', 'alizeti'])->latest('tarehe')->paginate(10);
        return view('uchekechaji.index', compact('uchekechajiOperations'));
    }
    
    public function create()
    {        
        $availableAlizetiBatches = Alizeti::with('stock')
            ->whereHas('stock', function ($query) {
                $query->where('total_al_kgms', '>', 0);
            })
            ->get();
        
        
        $globalUncleanedStockDisplay = 0; 
        $globalCleanedStockDisplay = 0; 

        return view('uchekechaji.create', compact('availableAlizetiBatches', 'globalUncleanedStockDisplay', 'globalCleanedStockDisplay'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'alizeti_id' => 'required|exists:alizeti,ali_id',
            'uncleaned_amount' => 'required|numeric|min:0.01',
            'makapi_amount' => 'required|numeric|min:0',
            'initial_unit' => 'nullable|numeric|min:0',
            'final_unit' => 'nullable|numeric|min:0',
        ]);

        if ($validated['makapi_amount'] > $validated['uncleaned_amount']) {
            return back()->withInput()->with('error', 'Makapi (waste) amount cannot be greater than uncleaned input amount.');
        }

        if (($validated['initial_unit'] !== null && $validated['final_unit'] === null) ||
            ($validated['initial_unit'] === null && $validated['final_unit'] !== null)) {
            return back()->withInput()->with('error', 'Both Initial and Final Electricity Units must be provided if one is entered.');
        }
        if ($validated['initial_unit'] !== null && $validated['final_unit'] !== null &&
            $validated['initial_unit'] <= $validated['final_unit']) {
            return back()->withInput()->with('error', 'Initial Electricity Unit must be greater than Final Electricity Unit.');
        }
   
        
        $alizetiBatch = Alizeti::with('stock')->findOrFail($validated['alizeti_id']); 

        if (!$alizetiBatch->stock || $alizetiBatch->stock->total_al_kgms < $validated['uncleaned_amount']) {
            $available = $alizetiBatch->stock ? $alizetiBatch->stock->total_al_kgms : 0.00;
            return back()->withInput()->with('error', 'Insufficient uncleaned seeds in selected Alizeti batch (' . $alizetiBatch->batch_no . '). Available: ' . $available . ' kg.');
        }

        $cleanedAmount = $validated['uncleaned_amount'] - $validated['makapi_amount'];
        if ($cleanedAmount < 0) {
             return back()->withInput()->with('error', 'Calculated cleaned amount resulted in a negative value. Please review input amounts.');
        }
           
        
        $uchekechajiOperation = Uchekechaji::create([
            'tarehe' => $validated['tarehe'],
            'user_id' => Auth::id(),
            'alizeti_id' => $validated['alizeti_id'],
            'uncleaned_amount' => $validated['uncleaned_amount'],
            'makapi_amount' => $validated['makapi_amount'],
            'cleaned_amount' => $cleanedAmount,
            'initial_unit' => $validated['initial_unit'],
            'final_unit' => $validated['final_unit'],
        ]);
       
        
        $alizetiBatch->stock->total_al_kgms -= $validated['uncleaned_amount']; 
        $alizetiBatch->stock->cleaned_kgm += $cleanedAmount; 
        
        $alizetiBatch->stock->save();      
        
        return redirect()->route('uchekechaji.index')->with('success', 'Uchekechaji operation recorded and batch stock updated successfully!');
    }

    public function show($uchek_id)
    {
        $uchekechaji = Uchekechaji::with(['user', 'alizeti.stock'])->findOrFail($uchek_id);
        
        return view('uchekechaji.show', compact('uchekechaji'));
    }
    
    public function edit($uchek_id)
    {
        $uchekechaji = Uchekechaji::with(['alizeti.stock'])->findOrFail($uchek_id);
        $availableAlizetiBatches = Alizeti::with('stock')->get(); 

        $globalUncleanedStockDisplay = 0; 
        $globalCleanedStockDisplay = 0; 

        return view('uchekechaji.edit', compact('uchekechaji', 'availableAlizetiBatches', 'globalUncleanedStockDisplay', 'globalCleanedStockDisplay'));
    }
    
    public function update(Request $request, $uchek_id)
    {
        $uchekechaji = Uchekechaji::with(['alizeti.stock'])->findOrFail($uchek_id);

        $validated = $request->validate([
            'tarehe' => 'required|date',
            'alizeti_id' => 'required|exists:alizeti,ali_id',
            'uncleaned_amount' => 'required|numeric|min:0.01',
            'makapi_amount' => 'required|numeric|min:0',
            'initial_unit' => 'nullable|numeric|min:0',
            'final_unit' => 'nullable|numeric|min:0',
        ]);

        if ($validated['makapi_amount'] > $validated['uncleaned_amount']) {
            return back()->withInput()->with('error', 'Makapi (waste) amount cannot be greater than uncleaned input amount.');
        }

        if (($validated['initial_unit'] !== null && $validated['final_unit'] === null) ||
            ($validated['initial_unit'] === null && $validated['final_unit'] !== null)) {
            return back()->withInput()->with('error', 'Both Initial and Final Electricity Units must be provided if one is entered.');
        }
        if ($validated['initial_unit'] !== null && $validated['final_unit'] !== null &&
            $validated['initial_unit'] <= $validated['final_unit']) {
            return back()->withInput()->with('error', 'Initial Electricity Unit must be greater than Final Electricity Unit.');
        }
        
        $originalUncleanedAmount = $uchekechaji->uncleaned_amount;
        $originalCleanedAmount = $uchekechaji->cleaned_amount;
        $originalAlizetiId = $uchekechaji->alizeti_id;

        $newCleanedAmount = $validated['uncleaned_amount'] - $validated['makapi_amount'];
        if ($newCleanedAmount < 0) {
            return back()->withInput()->with('error', 'Calculated new cleaned amount resulted in a negative value.');
        }
        
        
        $oldAlizetiStock = Alizeti::with('stock')->findOrFail($originalAlizetiId)->stock;
        if (!$oldAlizetiStock) {
            return back()->withInput()->with('error', "Original stock record for Alizeti batch ID {$originalAlizetiId} not found during update.");
        }

        
        $oldAlizetiStock->total_al_kgms += $originalUncleanedAmount; 
        $oldAlizetiStock->cleaned_kgm -= $originalCleanedAmount;     
        $oldAlizetiStock->save(); 

        
        $currentAlizetiBatch = Alizeti::with('stock')->findOrFail($validated['alizeti_id']);
        $currentAlizetiStock = $currentAlizetiBatch->stock;
        if (!$currentAlizetiStock) {
            return back()->withInput()->with('error', "Current stock record for Alizeti batch ID {$validated['alizeti_id']} not found during update.");
        }

        
        if ($currentAlizetiStock->total_al_kgms < $validated['uncleaned_amount']) {
            
            $oldAlizetiStock->total_al_kgms -= $originalUncleanedAmount;
            $oldAlizetiStock->cleaned_kgm += $originalCleanedAmount;
            $oldAlizetiStock->save();
            return back()->withInput()->with('error', 'Insufficient uncleaned seeds in selected Alizeti batch (' . $currentAlizetiBatch->batch_no . ') for updated quantity. Available: ' . $currentAlizetiStock->total_al_kgms . ' kg.');
        }
        
        
        $currentAlizetiStock->total_al_kgms -= $validated['uncleaned_amount']; 
        $currentAlizetiStock->cleaned_kgm += $newCleanedAmount; 
        
        $currentAlizetiStock->save();

        
        $uchekechaji->update([
            'tarehe' => $validated['tarehe'],
            'alizeti_id' => $validated['alizeti_id'],
            'uncleaned_amount' => $validated['uncleaned_amount'],
            'makapi_amount' => $validated['makapi_amount'],
            'cleaned_amount' => $newCleanedAmount,
            'initial_unit' => $validated['initial_unit'],
            'final_unit' => $validated['final_unit'],
        ]);
        
        return redirect()->route('uchekechaji.index')->with('success', 'Uchekechaji operation updated and batch stock adjusted successfully!');
    }

    public function destroy($uchek_id)
    {
        $uchekechaji = Uchekechaji::with(['alizeti.stock'])->findOrFail($uchek_id);
      
        
        $alizetiStock = $uchekechaji->alizeti->stock;

        
        if ($alizetiStock) {
            $alizetiStock->total_al_kgms += $uchekechaji->uncleaned_amount; 
            $alizetiStock->cleaned_kgm -= $uchekechaji->cleaned_amount;     

            if ($alizetiStock->total_al_kgms < 0) $alizetiStock->total_al_kgms = 0; 
            if ($alizetiStock->cleaned_kgm < 0) $alizetiStock->cleaned_kgm = 0;   
            
            $alizetiStock->save();
        } else {
            return back()->with('error', "Stock record for Alizeti batch ID {$uchekechaji->alizeti_id} missing during uchekechaji destroy. Cannot revert stock.");
        }
        
        
        $uchekechaji->delete();
        
        return redirect()->route('uchekechaji.index')->with('success', 'Uchekechaji operation deleted and batch stock reversed.');
    }
}