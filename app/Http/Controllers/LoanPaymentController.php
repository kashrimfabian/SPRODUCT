<?php

namespace App\Http\Controllers;

use App\Models\LoanPayment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Log;  
class LoanPaymentController extends Controller
{
    public function index()
    {
        $payments = LoanPayment::with('loan')->latest()->get();
        return view('loan_payments.index', compact('payments'));
    }

    public function create()
    {
        
        $loans = Loan::where('loan_status', 'not paid') ->where('amount', '>', 0)->get();
        return view('loan_payments.create', compact('loans'));
    }

    public function store(Request $request)
    {
        
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'loan_id' => 'required|exists:loans,loan_id',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string',
            ]);

            
            $payment = LoanPayment::create($validatedData);

            
            $loan = Loan::where('loan_id', $request->loan_id)->first();

            if (!$loan) {
                DB::rollBack();
                Log::error("Loan not found for loan_id: {$request->loan_id} during payment store.");
                return redirect()->back()->with('error', 'Associated loan record not found. Payment could not be processed.');
            }

            
            $loan->amount -= $request->amount;
            
            
            if ($loan->amount < 0) {
                $loan->amount = 0;
            }


            $newLoanStatus = ($loan->amount <= 0) ? 'paid' : 'not paid';

           
            Log::info("LoanPaymentController@store: Loan ID {$loan->loan_id}");
            Log::info("   Payment Amount: {$request->amount}");
            Log::info("   New Loan Amount (calculated): {$loan->amount}");
            Log::info("   Calculated New Loan Status: {$newLoanStatus}");
            


            $loan->loan_status = $newLoanStatus;
            $loan->save();


            DB::commit();

            return redirect()->route('loan_payments.index')->with('success', 'Payment recorded and loan updated successfully.');

        } catch (\Exception $e) {
           
            DB::rollBack();
            Log::error("Error storing loan payment: " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    public function edit(LoanPayment $loanPayment)
    {
        
        $loans = Loan::all();
        return view('loan_payments.edit', compact('loanPayment', 'loans'));
    }

    public function update(Request $request, LoanPayment $loanPayment)
    {
        
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'loan_id' => 'required|exists:loans,loan_id',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string',
            ]);

            $oldAmount = $loanPayment->amount; 
            $oldLoanId = $loanPayment->loan_id;

            
            $loanPayment->update($validatedData);

            
            if ($oldLoanId != $request->loan_id) {
               
                $oldLoan = Loan::where('loan_id', $oldLoanId)->first();
                if ($oldLoan) {
                    $oldLoan->amount += $oldAmount;
                    $oldLoan->amount = max(0, $oldLoan->amount);
                    $oldLoan->loan_status = ($oldLoan->amount <= 0) ? 'paid' : 'not paid';
                    $oldLoan->save();

                    Log::info("LoanPaymentController@update: Old Loan ID {$oldLoan->loan_id} updated. New Amount: {$oldLoan->amount}, Status: {$oldLoan->loan_status}");
                }

               
                $newLoan = Loan::where('loan_id', $request->loan_id)->first();
                if ($newLoan) {
                    $newLoan->amount -= $request->amount;
                    $newLoan->amount = max(0, $newLoan->amount); 
                    $newLoan->loan_status = ($newLoan->amount <= 0) ? 'paid' : 'not paid';
                    $newLoan->save();

                    Log::info("LoanPaymentController@update: New Loan ID {$newLoan->loan_id} updated. New Amount: {$newLoan->amount}, Status: {$newLoan->loan_status}");
                }
            } else {
                
                $loan = Loan::where('loan_id', $request->loan_id)->first();
                if ($loan) {

                    $loan->amount += $oldAmount; 
                    $loan->amount -= $request->amount; 

                    $loan->amount = max(0, $loan->amount); 
                    $loan->loan_status = ($loan->amount <= 0) ? 'paid' : 'not paid'; 
                    $loan->save();

                    Log::info("LoanPaymentController@update: Loan ID {$loan->loan_id} (same loan) updated. New Amount: {$loan->amount}, Status: {$loan->loan_status}");
                }
            }

           
            DB::commit();

            return redirect()->route('loan_payments.index')->with('success', 'Payment and loan updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating loan payment: " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update payment: ' . $e->getMessage());
        }
    }

    public function destroy(LoanPayment $loanPayment)
    {
       
        DB::beginTransaction();

        try {
            
            $loan = Loan::where('loan_id', $loanPayment->loan_id)->first();
            if ($loan) {
                $loan->amount += $loanPayment->amount;
                $loan->amount = max(0, $loan->amount); 
                $loan->loan_status = ($loan->amount <= 0) ? 'paid' : 'not paid'; 
                $loan->save();

                Log::info("LoanPaymentController@destroy: Loan ID {$loan->loan_id} updated after payment deletion. New Amount: {$loan->amount}, Status: {$loan->loan_status}");
            }

            $loanPayment->delete(); 
           
            DB::commit();

            return redirect()->route('loan_payments.index')->with('success', 'Payment deleted and loan updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting loan payment: " . $e->getMessage(), ['loan_payment_id' => $loanPayment->id]);
            return redirect()->back()->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }
}