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
      
        $availableStocks = Stock::where('total_al_kgms', '>', 0)->get();

        $availableAlizeti = $allAlizeti->filter(function ($alizeti) use ($availableStocks) {
            $stock = $availableStocks->firstWhere('alizeti_id', $alizeti->ali_id);
            if ($stock) {
                $alizeti->stock = $stock; 
                return true; // Include this Alizeti in the filtered collection
            }
            return false; // Exclude Alizeti batches without sufficient stock
        });
        
        // Return the view with the filtered Alizeti collection
        return view('uzalishaji.create', compact('availableAlizeti'));
    }

    /**
     * Store a newly created Uzalishaji record in storage.
     * Handles validation, stock updates, and database transactions.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'alizeti_id' => 'required|exists:alizeti,ali_id',
            'alizeti_kgm' => 'required|numeric|min:0',
            'mafuta_machafu' => 'required|numeric|min:0',
            'mashudu' => 'required|numeric|min:0',
            'initial_unit' => 'required|numeric|min:0', // Validation for initial electricity unit
            'final_unit' => 'required|numeric|min:0|lte:initial_unit', // Validation for final electricity unit
        ]);

        // Start a database transaction for atomicity
        DB::beginTransaction();

        try {
            // Find the associated stock record for the selected Alizeti batch
            $stock = Stock::where('alizeti_id', $validated['alizeti_id'])->first();

            // Check if stock record exists
            if (!$stock) {
                throw new \Exception('Stock record not found for this alizeti batch.');
            }

            // Check if there's enough alizeti_kgm in stock for production
            if ($stock->total_al_kgms < $validated['alizeti_kgm']) {
                throw new \Exception('Insufficient alizeti stock: Not enough alizeti_kgm available.');
            }

            // Reduce alizeti stock and add produced mafuta_machafu and mashudu
            $stock->total_al_kgms -= $validated['alizeti_kgm'];
            $stock->mafuta_machafu += $validated['mafuta_machafu'];
            $stock->mashudu += $validated['mashudu'];
            $stock->save(); // Save the updated stock record

            // Create the Uzalishaji record
            Uzalishaji::create([
                'tarehe' => $validated['tarehe'],
                'alizeti_kgm' => $validated['alizeti_kgm'],
                'mafuta_machafu' => $validated['mafuta_machafu'],
                'mashudu' => $validated['mashudu'],
                'initial_unit' => $validated['initial_unit'], // Store initial electricity unit
                'final_unit' => $validated['final_unit'],     // Store final electricity unit
                'created_by' => Auth::id(), // Record the user who created this record
                'updated_by' => Auth::id(), // Initially same as created_by
                'alizeti_id' => $validated['alizeti_id'], // Associate with Alizeti batch
            ]);

            DB::commit(); // Commit the transaction if all operations are successful

            return redirect()->route('uzalishaji.index')->with('success', 'Uzalishaji record created and stock updated successfully.');
        } catch (ValidationException $e) {
            // Catch Laravel validation exceptions and redirect back with errors and old input
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            // Catch any other general exceptions, log them, and redirect back with an error message
            DB::rollBack();
            Log::error('Error creating Uzalishaji record: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->with('error', 'Error creating Uzalishaji record: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified Uzalishaji record.
     *
     * @param int $uzalishaji_id
     * @return \Illuminate\View\View
     */
    public function edit($uzalishaji_id)
    {
        // Find the Uzalishaji record or abort with 404
        $uzalishaji = Uzalishaji::findOrFail($uzalishaji_id);
        
        // Fetch all Alizeti batches, similar to the create method,
        // attaching stock info for the dropdown in the edit form.
        $allAlizeti = Alizeti::all();
        $availableStocks = Stock::all(); // Get all stocks

        $alizeti = $allAlizeti->filter(function ($item) use ($availableStocks) {
            $stock = $availableStocks->firstWhere('alizeti_id', $item->ali_id);
            if ($stock) {
                $item->stock = $stock; // Attach stock
                return true;
            }
            return false;
        });

        return view('uzalishaji.edit', compact('uzalishaji', 'alizeti'));
    }

    /**
     * Update the specified Uzalishaji record in storage.
     * Handles validation, stock adjustments, and database transactions.
     *
     * @param Request $request
     * @param int $uzalishaji_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $uzalishaji_id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'tarehe' => 'required|date',
            'alizeti_id' => 'required|exists:alizeti,ali_id',
            'alizeti_kgm' => 'required|numeric|min:0',
            'mafuta_machafu' => 'required|numeric|min:0',
            'mashudu' => 'required|numeric|min:0',
            'initial_unit' => 'required|numeric|min:0', // Validation for initial electricity unit
            'final_unit' => 'required|numeric|min:0|lte:initial_unit', // Validation for final electricity unit
        ]);

        $uzalishaji = Uzalishaji::findOrFail($uzalishaji_id);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the original values of the Uzalishaji record BEFORE it's updated
            $original = $uzalishaji->getOriginal();

            // Update the Uzalishaji record with the new validated data
            $uzalishaji->update(array_merge($validated, [
                'updated_by' => Auth::id(), // Ensure updated_by is set on update
            ]));

            // Find the associated Stock record
            $stock = Stock::where('alizeti_id', $uzalishaji->alizeti_id)->first();

            if (!$stock) {
                // If stock record is not found, log a warning and throw an exception to rollback
                Log::warning('Stock record not found for alizeti_id ' . $uzalishaji->alizeti_id . ' during Uzalishaji update. Rolling back.');
                throw new \Exception('Stock record not found for this alizeti batch.');
            }

            // Adjust Stock quantities based on the difference between old and new Uzalishaji values
            // For consumed items (alizeti_kgm): Add back old, subtract new
            $stock->total_al_kgms += ($original['alizeti_kgm'] - $uzalishaji->alizeti_kgm);
            // For produced items (mafuta_machafu, mashudu): Subtract old, add new
            $stock->mafuta_machafu += ($uzalishaji->mafuta_machafu - $original['mafuta_machafu']);
            $stock->mashudu += ($uzalishaji->mashudu - $original['mashudu']);
            
            // Electricity units (initial_unit, final_unit) are for tracking consumption,
            // they do not affect the stock quantities of products.
            // So, no stock adjustments are needed for these fields.

            $stock->save(); // Save the updated stock record

            DB::commit(); // Commit the transaction

            return redirect()->route('uzalishaji.index')->with('success', 'Uzalishaji record updated and stock adjusted successfully.');
        } catch (ValidationException $e) {
            // Catch Laravel validation exceptions and redirect back with errors and old input
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Catch any other general exceptions, log them, and redirect back with an error message
            DB::rollBack();
            Log::error('Error updating Uzalishaji record (ID: ' . $uzalishaji_id . '): ' . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->with('error', 'Error updating Uzalishaji record: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified Uzalishaji record from storage.
     * Restores stock quantities based on the deleted record.
     *
     * @param int $uzalishaji_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($uzalishaji_id)
    {
        $uzalishaji = Uzalishaji::findOrFail($uzalishaji_id);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the associated Stock record
            $stock = Stock::where('alizeti_id', $uzalishaji->alizeti_id)->first();

            if ($stock) {
                // Restore consumed alizeti_kgm (add back to stock)
                $stock->total_al_kgms += $uzalishaji->alizeti_kgm;
                // Restore produced items (subtract from stock, as they were added during creation)
                $stock->mafuta_machafu -= $uzalishaji->mafuta_machafu;
                $stock->mashudu -= $uzalishaji->mashudu;
                $stock->save(); // Save the updated stock record
            } else {
                // Log a warning if stock record is not found during deletion
                Log::warning('Stock record not found for alizeti_id ' . $uzalishaji->alizeti_id . ' during Uzalishaji destroy. Stock not updated.');
            }

            // Delete the Uzalishaji record
            $uzalishaji->delete();

            DB::commit(); // Commit the transaction

            return redirect()->route('uzalishaji.index')->with('success', 'Uzalishaji record deleted and stock restored successfully.');
        } catch (\Exception $e) {
            // Catch any exceptions, log them, and redirect back with an error message
            DB::rollBack();
            Log::error('Error deleting Uzalishaji record (ID: ' . $uzalishaji_id . '): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting Uzalishaji record: ' . $e->getMessage());
        }
    }
}