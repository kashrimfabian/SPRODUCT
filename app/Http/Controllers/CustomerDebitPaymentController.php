<?php

namespace App\Http\Controllers;

use App\Models\CustomerDebitPayment;
use App\Models\CustomerDebit;
use App\Models\Mauzo; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 

class CustomerDebitPaymentController extends Controller
{
    public function index()
    {
        $payments = CustomerDebitPayment::with('customerDebit')->latest()->get();
        return view('customer_debit_payments.index', compact('payments'));
    }

    public function create()
    {
        
        $debits = CustomerDebit::whereHas('mauzo', function ($query) {
            $query->where('is_confirmed', true);
        })->where('debt_status', 'not payed') // Filter by the new debt_status
          ->where('balance', '>', 0)
          ->get();

        return view('customer_debit_payments.create', compact('debits'));
    }

    public function store(Request $request)
    {
        
        DB::beginTransaction();

        try {
            // 1. Validate the incoming request data
            $validatedData = $request->validate([
                'debit_id' => 'required|exists:customer_debits,debit_id',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string',
            ]);

            // 2. Create the new Customer Debit Payment record
            $payment = CustomerDebitPayment::create($validatedData);

            // 3. Find the associated CustomerDebit record
            $customerDebit = $payment->customerDebit; // Access through relationship

            if (!$customerDebit) {
                DB::rollBack();
                Log::error("CustomerDebit not found for payment_id: " . $payment->payment_id . " during store.");
                return redirect()->back()->with('error', 'Associated debit record not found. Payment could not be processed.');
            }

            // 4. Update the CustomerDebit record's balance, amount_paid, and debt_status
            $newBalance = round($customerDebit->balance - $validatedData['amount'], 2);
            $newAmountPaid = round($customerDebit->amount_paid + $validatedData['amount'], 2);

            // Determine the new debt_status
            $debtStatus = ($newBalance <= 0) ? 'payed' : 'not payed';

            $customerDebit->update([
                'balance' => $newBalance,
                'amount_paid' => $newAmountPaid,
                'debt_status' => $debtStatus, // <--- Update debt_status here
            ]);

            // 5. Update the associated Mauzo record's payment_status
            $mauzo = $customerDebit->mauzo; // Access associated Mauzo record
            if ($mauzo) {
                // If the debt_status is 'payed', update Mauzo's payment_status
                if ($debtStatus === 'payed' && $mauzo->payment_status !== 'payed') {
                    $mauzo->update(['payment_status' => 'payed']);
                }
                // If the debt_status is 'not payed', ensure Mauzo's payment_status is 'not payed'
                // This is important if a debit was paid off, then more payments are made and it goes back to 'not payed'
                elseif ($debtStatus === 'not payed' && $mauzo->payment_status === 'payed') {
                    $mauzo->update(['payment_status' => 'not payed']);
                }
            }

            // If all operations are successful, commit the transaction
            DB::commit();

            return redirect()->route('customer_debit_payments.index')->with('success', 'Payment added successfully and debit balance/status updated.');

        } catch (\Exception $e) {
            // If any error occurs, rollback all changes and log
            DB::rollBack();
            Log::error("Error storing customer debit payment: " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Failed to add payment: ' . $e->getMessage());
        }
    }

    public function show(CustomerDebitPayment $customer_debit_payment)
    {
        return view('customer_debit_payments.show', compact('customer_debit_payment'));
    }

    public function edit(CustomerDebitPayment $customer_debit_payment)
    {
        // For editing, show debits that are 'not payed' or the specific debit being edited
        $debits = CustomerDebit::whereHas('mauzo', function ($query) {
            $query->where('is_confirmed', true);
        })->where(function($query) use ($customer_debit_payment) {
            $query->where('debt_status', 'not payed')
                  ->orWhere('debit_id', $customer_debit_payment->debit_id); // Include the current debit regardless of its status
        })->get();
        
        return view('customer_debit_payments.edit', compact('customer_debit_payment', 'debits'));
    }

    public function update(Request $request, CustomerDebitPayment $customer_debit_payment)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Store the old amount and the original debit ID for balance recalculation
            $oldAmount = $customer_debit_payment->amount;
            $originalDebitId = $customer_debit_payment->debit_id; // Store original debit ID

            // 1. Validate the incoming request data
            $validatedData = $request->validate([
                'debit_id' => 'required|exists:customer_debits,debit_id',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string',
            ]);

            // 2. Perform the update on the Customer Debit Payment record
            $customer_debit_payment->update($validatedData);

            // 3. Handle the original debit if the debit_id has changed
            if ($originalDebitId !== $validatedData['debit_id']) {
                $oldCustomerDebit = CustomerDebit::find($originalDebitId);
                if ($oldCustomerDebit) {
                    // Revert changes on the old debit
                    $oldCustomerDebit->balance = round($oldCustomerDebit->balance + $oldAmount, 2);
                    $oldCustomerDebit->amount_paid = round($oldCustomerDebit->amount_paid - $oldAmount, 2);
                    $oldCustomerDebit->debt_status = ($oldCustomerDebit->balance <= 0) ? 'payed' : 'not payed';
                    $oldCustomerDebit->save();

                    // Update associated Mauzo's payment_status for the old debit
                    $oldMauzo = $oldCustomerDebit->mauzo;
                    if ($oldMauzo) {
                        if ($oldCustomerDebit->debt_status === 'not payed' && $oldMauzo->payment_status === 'payed') {
                            $oldMauzo->update(['payment_status' => 'not payed']);
                        } elseif ($oldCustomerDebit->debt_status === 'payed' && $oldMauzo->payment_status !== 'payed') {
                            $oldMauzo->update(['payment_status' => 'payed']);
                        }
                    }
                }
            }

            // 4. Find the (potentially new) associated CustomerDebit record
            $customerDebit = CustomerDebit::find($validatedData['debit_id']);

            if (!$customerDebit) {
                DB::rollBack();
                Log::error("CustomerDebit not found for updated payment_id: " . $customer_debit_payment->payment_id . " during update.");
                return redirect()->back()->with('error', 'Associated debit record not found. Payment could not be updated.');
            }

            // 5. Recalculate and update the current CustomerDebit record's balance, amount_paid, and debt_status
            // If debit_id didn't change, we effectively just adjust for amount change
            // If debit_id changed, we're applying the full new amount to the new debit
            if ($originalDebitId === $validatedData['debit_id']) {
                 // For the same debit, reverse old amount then apply new amount
                $revertedBalance = $customerDebit->balance + $oldAmount;
                $revertedAmountPaid = $customerDebit->amount_paid - $oldAmount;
                $customerDebit->balance = round($revertedBalance - $validatedData['amount'], 2);
                $customerDebit->amount_paid = round($revertedAmountPaid + $validatedData['amount'], 2);
            } else {
                 // For a new debit, just apply the new amount (old was handled by originalDebitId logic)
                $customerDebit->balance = round($customerDebit->balance - $validatedData['amount'], 2);
                $customerDebit->amount_paid = round($customerDebit->amount_paid + $validatedData['amount'], 2);
            }
            
            // Determine the new debt_status for the current debit
            $debtStatus = ($customerDebit->balance <= 0) ? 'payed' : 'not payed';
            $customerDebit->debt_status = $debtStatus;
            $customerDebit->save();

            // 6. Update the associated Mauzo record's payment_status based on the current debit's new status
            $mauzo = $customerDebit->mauzo; 
            if ($mauzo) {
                if ($debtStatus === 'payed' && $mauzo->payment_status !== 'payed') {
                    $mauzo->update(['payment_status' => 'payed']);
                } elseif ($debtStatus === 'not payed' && $mauzo->payment_status === 'payed') {
                    $mauzo->update(['payment_status' => 'not payed']);
                }
            }

            // Commit the transaction
            DB::commit();

            return redirect()->route('customer_debit_payments.index')->with('success', 'Payment updated successfully and debit balance/status adjusted.');

        } catch (\Exception $e) {
            // Rollback on error
            DB::rollBack();
            Log::error("Error updating customer debit payment: " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update payment: ' . $e->getMessage());
        }
    }

    public function destroy(CustomerDebitPayment $customer_debit_payment)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the amount and associated debit before deleting the payment
            $amountToDelete = $customer_debit_payment->amount;
            $customerDebit = $customer_debit_payment->customerDebit; // Get associated debit

            // Delete the payment record
            $customer_debit_payment->delete();

            if ($customerDebit) {
                // Revert the balance and amount_paid in the CustomerDebit record
                $newBalance = round($customerDebit->balance + $amountToDelete, 2);
                $newAmountPaid = round($customerDebit->amount_paid - $amountToDelete, 2);

                // Determine the new debt_status after reversal
                $debtStatus = ($newBalance <= 0) ? 'payed' : 'not payed';

                $customerDebit->update([
                    'balance' => $newBalance,
                    'amount_paid' => $newAmountPaid,
                    'debt_status' => $debtStatus, // <--- Update debt_status here
                ]);

                // Update the associated Mauzo record's payment_status
                $mauzo = $customerDebit->mauzo;
                if ($mauzo) {
                    // If balance becomes positive, set status to 'not payed'
                    if ($debtStatus === 'not payed' && $mauzo->payment_status === 'payed') {
                        $mauzo->update(['payment_status' => 'not payed']);
                    }
                    // If balance is still <= 0 after reversal (e.g., initial total_amount was 0 or negative due to error),
                    // ensure Mauzo remains 'payed'
                    elseif ($debtStatus === 'payed' && $mauzo->payment_status !== 'payed') {
                       $mauzo->update(['payment_status' => 'payed']);
                    }
                }
            }

            // Commit the transaction
            DB::commit();

            return redirect()->route('customer_debit_payments.index')->with('success', 'Payment deleted successfully and debit balance/status reverted.');

        } catch (\Exception $e) {
            // Rollback on error
            DB::rollBack();
            Log::error("Error deleting customer debit payment: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }
}