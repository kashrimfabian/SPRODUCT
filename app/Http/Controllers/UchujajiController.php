<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Uchujaji;
use App\Models\Alizeti;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use Illuminate\Validation\ValidationException; 

class UchujajiController extends Controller
{
    public function index(Request $request)
    {
        $uchujajiQuery = Uchujaji::with(['user', 'alizeti'])->latest();

        if ($request->has('alizeti_id') && $request->alizeti_id != '') {
            $uchujajiQuery->where('alizeti_id', $request->alizeti_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            if ($startDate > $endDate) {
                return back()->with('error', 'Start date cannot be after end date.');
            }

            $uchujajiQuery->whereBetween('tarehe', [$startDate, $endDate]);
        }

        // Eager load initial_unit and final_unit for potential use in calculation or display
        $uchujaji = $uchujajiQuery->get();

        $uniqueBatches = Uchujaji::with('alizeti')->get()->unique('alizeti_id')->map(function ($item) {
                return [
                    'alizeti_id' => $item->alizeti_id,
                    'batch_no' => $item->alizeti->batch_no,
                ];
            })->values();

        return view('uchujaji.index', compact('uchujaji', 'uniqueBatches'));
    }

    public function create()
    {
        $alizeti = Alizeti::all();
        $stocks = Stock::where('mafuta_machafu', '>', 0)->get();

        $availableAlizeti = $alizeti->filter(function ($item) use ($stocks) {
            $stock = $stocks->firstWhere('alizeti_id', $item->ali_id);
            return $stock && $stock->mafuta_machafu > 0;
        });

        return view('uchujaji.create', compact('availableAlizeti'));
    }

    public function store(Request $request)
    {
        // Added initial_unit and final_unit to validation
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'alizeti_id' => 'required|integer|exists:alizeti,ali_id',
            'mafuta_machafu' => 'required|numeric|min:0',
            'mafuta_masafi' => 'required|numeric|min:0',
            'ugido' => 'required|numeric|min:0', // Made required as per your previous code
            'lami' => 'required|numeric|min:0',    // Made required as per your previous code
            'initial_unit' => 'required|numeric|min:0', // New: Initial unit validation
            'final_unit' => 'required|numeric|min:0|lte:initial_unit', // New: Final must be <= Initial
        ]);

        try {
            DB::beginTransaction();

            $stock = Stock::where('alizeti_id', $validated['alizeti_id'])->first();

            if (!$stock) {
                throw new \Exception('Stock record not found for this alizeti batch.');
            }

            // Check if enough mafuta machafu is available based on current stock
            if ($stock->mafuta_machafu < $validated['mafuta_machafu']) {
                throw new \Exception('Not enough mafuta machafu available in stock.');
            }

            // Create the Uchujaji record with new unit fields
            $uchujaji = Uchujaji::create([
                'tarehe' => $validated['tarehe'],
                'mafuta_machafu' => $validated['mafuta_machafu'],
                'mafuta_masafi' => $validated['mafuta_masafi'],
                'ugido' => $validated['ugido'],
                'lami' => $validated['lami'],
                'initial_unit' => $validated['initial_unit'], // New: Store initial unit
                'final_unit' => $validated['final_unit'],     // New: Store final unit
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'alizeti_id' => $validated['alizeti_id'],
            ]);

            // Update Stock amounts based on uchujaji process
            $stock->mafuta_machafu -= $validated['mafuta_machafu'];
            $stock->mafuta_masafi += $validated['mafuta_masafi'];
            $stock->ugido += $validated['ugido'];
            $stock->lami += $validated['lami']; 
            $stock->save();

            DB::commit();

            return redirect()->route('uchujaji.index')->with('success', 'Uchujaji record created and Stock updated successfully.');
        } catch (ValidationException $e) {
            // Catch validation exceptions specifically to show errors gracefully
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error in UchujajiController@store: " . $e->getMessage(), ['request_data' => $request->all()]);
            // Removed dd($e) for production readiness, now redirects with error
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($uchujaji_id)
    {
        $uchujaji = Uchujaji::findOrFail($uchujaji_id);
        $alizeti = Alizeti::all();
        return view('uchujaji.edit', compact('uchujaji', 'alizeti'));
    }

    public function update(Request $request, $uchujaji_id)
    {
        // Added initial_unit and final_unit to validation
        $validated = $request->validate([
            'mafuta_machafu' => 'required|numeric|min:0',
            'mafuta_masafi' => 'required|numeric|min:0',
            'ugido' => 'required|numeric|min:0', // Made required for consistency
            'lami' => 'required|numeric|min:0',    // Made required for consistency
            'initial_unit' => 'required|numeric|min:0', // New: Initial unit validation
            'final_unit' => 'required|numeric|min:0|lte:initial_unit', // New: Final must be <= Initial
        ]);

        $uchujaji = Uchujaji::findOrFail($uchujaji_id);

        DB::beginTransaction();

        try {
            // Get old values including the new unit fields
            $oldValues = $uchujaji->only([
                'mafuta_machafu',
                'mafuta_masafi',
                'ugido',
                'lami',
                'initial_unit', // Include initial_unit
                'final_unit'    // Include final_unit
            ]);

            $stock = Stock::where('alizeti_id', $uchujaji->alizeti_id)->first();

            if (!$stock) {
                throw new \Exception('Stock record not found for this alizeti batch.');
            }

            // Recalculate mafuta_machafu stock for the current update request
            // Add back the old mafuta_machafu first, then subtract the new one
            $recalculated_mafuta_machafu_stock = $stock->mafuta_machafu + $oldValues['mafuta_machafu'];
            if ($recalculated_mafuta_machafu_stock < $validated['mafuta_machafu']) {
                throw new \Exception('Not enough mafuta machafu stock after adjusting for old value.');
            }
            
            // Adjust stock for mafuta_machafu (subtraction)
            $stock->mafuta_machafu += $oldValues['mafuta_machafu'] - $validated['mafuta_machafu'];
            
            // Corrected calculation for mafuta_masafi (add back old, then subtract new)
            $stock->mafuta_masafi += $validated['mafuta_masafi'] - $oldValues['mafuta_masafi'];

            // Adjust stock for ugido and lami (add back old, then subtract new)
            $stock->ugido += $validated['ugido'] - $oldValues['ugido'];
            $stock->lami += $validated['lami'] - $oldValues['lami'];
            
            $stock->save();
            
            // Update the Uchujaji record itself with new unit fields
            $uchujaji->update([
                'mafuta_machafu' => $validated['mafuta_machafu'],
                'mafuta_masafi' => $validated['mafuta_masafi'],
                'ugido' => $validated['ugido'],
                'lami' => $validated['lami'],
                'initial_unit' => $validated['initial_unit'], // Update initial unit
                'final_unit' => $validated['final_unit'],     // Update final unit
                'updated_by' => Auth::id(), // Ensure updated_by is set
            ]);

            DB::commit();

            return redirect()->route('uchujaji.index')->with('success', 'Uchujaji record updated successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in UchujajiController@update (ID: {$uchujaji_id}): " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $uchujaji = Uchujaji::findOrFail($id);

        DB::beginTransaction();

        try {
            $stock = Stock::where('alizeti_id', $uchujaji->alizeti_id)->first();
            if ($stock) {
                // When deleting an uchujaji record, reverse its impact on stock
                $stock->mafuta_masafi -= $uchujaji->mafuta_masafi; // Subtract mafuta_masafi that was added
                $stock->mafuta_machafu += $uchujaji->mafuta_machafu; // Add back mafuta_machafu that was consumed
                $stock->ugido -= $uchujaji->ugido; // Subtract ugido that was added
                $stock->lami -= $uchujaji->lami;   // Subtract lami that was added
                $stock->save();
            }

            $uchujaji->delete();

            DB::commit();

            return redirect()->route('uchujaji.index')->with('success', 'Uchujaji record deleted and Stock updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in UchujajiController@destroy (ID: {$id}): " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}