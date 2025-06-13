<?php

namespace App\Http\Controllers;

use App\Models\CustomerDebit;
use App\Models\Mauzo;
use Illuminate\Http\Request;

class CustomerDebitController extends Controller
{
    public function index()
    {
        $debits = CustomerDebit::with('mauzo')->latest()->get();
        return view('customer_debits.index', compact('debits'));
    }

    public function create()
    {
        $mauzo = Mauzo::all();
        return view('customer_debits.create', compact('mauzo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mauzo_id' => 'required|exists:mauzo,mauzo_id',
            'customer_name' => 'required|string',
            'phone' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $balance = $request->total_amount - $request->amount_paid;

        CustomerDebit::create([
            'mauzo_id' => $request->mauzo_id,
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'total_amount' => $request->total_amount,
            'amount_paid' => $request->amount_paid,
            'balance' => $balance,
        ]);

        return redirect()->route('customer_debits.index')->with('success', 'Customer debit recorded successfully.');
    }

    public function edit(CustomerDebit $customerDebit)
    {
        $mauzo = Mauzo::all();
        return view('customer_debits.edit', compact('customerDebit', 'mauzo'));
    }

    public function update(Request $request, CustomerDebit $customerDebit)
    {
        $request->validate([
            'mauzo_id' => 'required|exists:mauzo,mauzo_id',
            'customer_name' => 'required|string',
            'phone' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $balance = $request->total_amount - $request->amount_paid;

        $customerDebit->update([
            'mauzo_id' => $request->mauzo_id,
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'total_amount' => $request->total_amount,
            'amount_paid' => $request->amount_paid,
            'balance' => $balance,
        ]);

        return redirect()->route('customer_debits.index')->with('success', 'Customer debit updated successfully.');
    }

    public function destroy(CustomerDebit $customerDebit)
    {
        $customerDebit->delete();
        return redirect()->route('customer_debits.index')->with('success', 'Customer debit deleted.');
    }
}
