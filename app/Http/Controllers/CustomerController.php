<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class CustomerController extends Controller
{
    
    public function index()
    {
        $customers = Customer::latest('created_at')->paginate(10);
        return view('customers.index', compact('customers'));
    }

   
    public function create()
    {
        return view('customers.create');
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255', 
            'phone_number' => 'required|string|max:255|unique:customers,phone_number', 
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully!');
    }

    
    public function show(Customer $customer) 
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255|unique:customers,phone_number,' . $customer->cust_id . ',cust_id', 
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}