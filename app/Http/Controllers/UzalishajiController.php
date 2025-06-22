<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Uzalishaji;
use App\Models\Alizeti; 
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException; 

class UzalishajiController extends Controller
{
    
    public function index(Request $request)
    {
        
        $uzalishajiQuery = Uzalishaji::with(['user', 'alizeti'])->latest();

        
        if ($request->has('alizeti_id') && $request->alizeti_id != '') {
            $uzalishajiQuery->where('alizeti_id', $request->alizeti_id);
        }


        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

           
            if ($startDate > $endDate) {
                return back()->with('error', 'Start date cannot be after end date.');
            }

            $uzalishajiQuery->whereBetween('tarehe', [$startDate, $endDate]);
        }

        
        $uzalishaji = $uzalishajiQuery->get();
                
        
        $uniqueBatches = Uzalishaji::with('alizeti')->get()->unique('alizeti_id') ->map(function ($item) {
                               
            return [
                'alizeti_id' => $item->alizeti_id,
                'batch_no' => $item->alizeti->batch_no ?? 'N/A', 
            ];
        })->values(); 

        
        return view('uzalishaji.index', compact('uzalishaji', 'uniqueBatches'));
    }

    
    public function create()
    {
        
        $allAlizeti = Alizeti::all();
      
        $availableStocks = Stock::where('cleaned_kgm', '>', 0)->get();

        $availableAlizeti = $allAlizeti->filter(function ($alizeti) use ($availableStocks) {
            $stock = $availableStocks->firstWhere('alizeti_id', $alizeti->ali_id);
            if ($stock) {
                $alizeti->stock = $stock; 
                return true; 
            }
            return false; 
        });
        
        
        return view('uzalishaji.create', compact('availableAlizeti'));
    }

    
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'alizeti_id' => 'required|exists:alizeti,ali_id',
            'alizeti_kgm' => 'required|numeric|min:0',
            'mafuta_machafu' => 'required|numeric|min:0',
            'mashudu' => 'required|numeric|min:0',
            'initial_unit' => 'required|numeric|min:0', 
            'final_unit' => 'required|numeric|min:0|lte:initial_unit', 
        ]);

        
        DB::beginTransaction();

        try {
            
            $stock = Stock::where('alizeti_id', $validated['alizeti_id'])->first();

            
            if (!$stock) {
                throw new \Exception('Stock record not found for this alizeti batch.');
            }

            
            if ($stock->cleaned_kgm < $validated['alizeti_kgm']) {
                throw new \Exception('Insufficient alizeti stock: Not enough alizeti_kgm available.');
            }

            
            $stock->cleaned_kgm -= $validated['alizeti_kgm'];
            $stock->mafuta_machafu += $validated['mafuta_machafu'];
            $stock->mashudu += $validated['mashudu'];
            $stock->save(); 

            
            Uzalishaji::create([
                'tarehe' => $validated['tarehe'],
                'alizeti_kgm' => $validated['alizeti_kgm'],
                'mafuta_machafu' => $validated['mafuta_machafu'],
                'mashudu' => $validated['mashudu'],
                'initial_unit' => $validated['initial_unit'], 
                'final_unit' => $validated['final_unit'],     
                'created_by' => Auth::id(), 
                'updated_by' => Auth::id(), 
                'alizeti_id' => $validated['alizeti_id'], 
            ]);

            DB::commit(); 

            return redirect()->route('uzalishaji.index')->with('success', 'Uzalishaji record created and stock updated successfully.');
        } catch (ValidationException $e) {
            
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            
            DB::rollBack();
            Log::error('Error creating Uzalishaji record: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->with('error', 'Error creating Uzalishaji record: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($uzalishaji_id)
    {
        
        $uzalishaji = Uzalishaji::findOrFail($uzalishaji_id);
        
        
        $allAlizeti = Alizeti::all();
        $availableStocks = Stock::all(); 
        $alizeti = $allAlizeti->filter(function ($item) use ($availableStocks) {
            $stock = $availableStocks->firstWhere('alizeti_id', $item->ali_id);
            if ($stock) {
                $item->stock = $stock; 
                return true;
            }
            return false;
        });

        return view('uzalishaji.edit', compact('uzalishaji', 'alizeti'));
    }

    
    public function update(Request $request, $uzalishaji_id)
    {
        
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'alizeti_id' => 'required|exists:alizeti,ali_id',
            'alizeti_kgm' => 'required|numeric|min:0',
            'mafuta_machafu' => 'required|numeric|min:0',
            'mashudu' => 'required|numeric|min:0',
            'initial_unit' => 'required|numeric|min:0', 
            'final_unit' => 'required|numeric|min:0|lte:initial_unit', 
        ]);

        $uzalishaji = Uzalishaji::findOrFail($uzalishaji_id);

        
        DB::beginTransaction();

        try {
           
            $original = $uzalishaji->getOriginal();

            
            $uzalishaji->update(array_merge($validated, [
                'updated_by' => Auth::id(), 
            ]));

            
            $stock = Stock::where('alizeti_id', $uzalishaji->alizeti_id)->first();

            if (!$stock) {
                
                Log::warning('Stock record not found for alizeti_id ' . $uzalishaji->alizeti_id . ' during Uzalishaji update. Rolling back.');
                throw new \Exception('Stock record not found for this alizeti batch.');
            }

            
            $stock->cleaned_kgm += ($original['alizeti_kgm'] - $uzalishaji->alizeti_kgm);
            $stock->mafuta_machafu += ($uzalishaji->mafuta_machafu - $original['mafuta_machafu']);
            $stock->mashudu += ($uzalishaji->mashudu - $original['mashudu']);
            
            
            $stock->save(); 

            DB::commit(); 

            return redirect()->route('uzalishaji.index')->with('success', 'Uzalishaji record updated and stock adjusted successfully.');
        } catch (ValidationException $e) {
            
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
          
            DB::rollBack();
            Log::error('Error updating Uzalishaji record (ID: ' . $uzalishaji_id . '): ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->with('error', 'Error updating Uzalishaji record: ' . $e->getMessage())->withInput();
        }
    }

    
    public function destroy($uzalishaji_id)
    {
        $uzalishaji = Uzalishaji::findOrFail($uzalishaji_id);

        
        DB::beginTransaction();

        try {
            
            $stock = Stock::where('alizeti_id', $uzalishaji->alizeti_id)->first();

            if ($stock) {
               
                $stock->cleaned_kgm += $uzalishaji->alizeti_kgm;
               
                $stock->mafuta_machafu -= $uzalishaji->mafuta_machafu;
                $stock->mashudu -= $uzalishaji->mashudu;
                $stock->save(); 
            } else {
                
                Log::warning('Stock record not found for alizeti_id ' . $uzalishaji->alizeti_id . ' during Uzalishaji destroy. Stock not updated.');
            }

            
            $uzalishaji->delete();

            DB::commit(); 

            return redirect()->route('uzalishaji.index')->with('success', 'Uzalishaji record deleted and stock restored successfully.');
        } catch (\Exception $e) {
            
            DB::rollBack();
            Log::error('Error deleting Uzalishaji record (ID: ' . $uzalishaji_id . '): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting Uzalishaji record: ' . $e->getMessage());
        }
    }
}