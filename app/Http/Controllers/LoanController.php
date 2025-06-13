<?php
namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with('loanPayments')->get();
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        return view('loans.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'lender_name' => 'required|string|max:255',
                'loan_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'due_date' => 'nullable|date',
                'interest_rate' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);

            $loan = Loan::create([
                'lender_name' => $validatedData['lender_name'],
                'amount' => $validatedData['amount'],
                'original_loan_amount' => $validatedData['amount'],
                'loan_date' => $validatedData['loan_date'],
                'due_date' => $validatedData['due_date'],
                'interest_rate' => $validatedData['interest_rate'],
                'notes' => $validatedData['notes'],
                'loan_status' => 'not paid',
                'is_confirmed' => false, 
            ]);

            DB::commit();

            return redirect()->route('loans.index')->with('success', 'Loan created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating loan: " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create loan: ' . $e->getMessage());
        }
    }

    public function confirm(Loan $loan)
    {
        DB::beginTransaction();
        try {
            if (!$loan->is_confirmed) {
                $loan->is_confirmed = true;
                $loan->save();
                DB::commit();
                return redirect()->route('loans.index')->with('success', 'Loan confirmed successfully!');
            } else {
                DB::rollBack();
                return redirect()->route('loans.index')->with('info', 'Loan is already confirmed.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error confirming loan (ID: {$loan->loan_id}): " . $e->getMessage());
            return redirect()->route('loans.index')->with('error', 'Failed to confirm loan: ' . $e->getMessage());
        }
    }

    public function show(Loan $loan)
    {
        $loan->load('loanPayments');
        return view('loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        return view('loans.edit', compact('loan'));
    }

    public function update(Request $request, Loan $loan)
    {
        
        if ($loan->is_confirmed) {
            return redirect()->route('loans.index')->with('error', 'Confirmed loans cannot be updated.');
        }

        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'lender_name' => 'required|string|max:255',
                'loan_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'due_date' => 'nullable|date',
                'interest_rate' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);

            $updateData = [
                'lender_name' => $validatedData['lender_name'],
                'loan_date' => $validatedData['loan_date'],
                'due_date' => $validatedData['due_date'],
                'interest_rate' => $validatedData['interest_rate'],
                'notes' => $validatedData['notes'],
            ];

            
            $updateData['original_loan_amount'] = $validatedData['amount'];
            $updateData['amount'] = $validatedData['amount']; 

            $loan->update($updateData);

            DB::commit();

            return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating loan (Loan ID: {$loan->loan_id}): " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update loan: ' . $e->getMessage());
        }
    }

    public function destroy(Loan $loan)
    {
        
        if ($loan->is_confirmed) {
            return redirect()->route('loans.index')->with('error', 'Confirmed loans cannot be deleted.');
        }

        DB::beginTransaction();

        try {
            $loan->delete();
            DB::commit();

            return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting loan (Loan ID: {$loan->loan_id}): " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete loan: ' . $e->getMessage());
        }
    }
}