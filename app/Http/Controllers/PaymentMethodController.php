<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Make sure this is imported
use Illuminate\Support\Facades\Log; // Added for potential logging if needed for debugging

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the payment methods.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::latest()->get();
        return view('payment_methods.index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new payment method.
     */
    public function create()
    {
        return view('payment_methods.create');
    }

    /**
     * Store a newly created payment method in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean', // Checkbox value is 1 if checked, absent if unchecked
        ]);

        try {
            PaymentMethod::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'), // Correctly handles checkbox state
            ]);

            return redirect()->route('payment_methods.index')->with('success', 'Payment method created successfully.');

        } catch (\Exception $e) {
            Log::error("Error creating payment method: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create payment method.');
        }
    }

    /**
     * Show the form for editing the specified payment method.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        // Laravel's Route Model Binding will automatically fetch the PaymentMethod
        // based on the 'payment_id' route parameter, since you've specified
        // protected $primaryKey = 'payment_id'; in your PaymentMethod model.
        return view('payment_methods.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified payment method in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // FIX: Explicitly tell unique rule the primary key column name
                Rule::unique('payment_methods', 'name')->ignore($paymentMethod->payment_id, 'payment_id'),
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean', // Checkbox value is 1 if checked, absent if unchecked
        ]);

        try {
            $paymentMethod->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'), // Correctly handles checkbox state
            ]);

            return redirect()->route('payment_methods.index')->with('success', 'Payment method updated successfully.');

        } catch (\Exception $e) {
            Log::error("Error updating payment method (ID: {$paymentMethod->payment_id}): " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update payment method.');
        }
    }

    public function show(PaymentMethod $paymentMethod)
    {
        
        return view('payment_methods.show', compact('paymentMethod'));
    }

    
    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            
            if ($paymentMethod->mauzo()->count() > 0) {
                return back()->with('error', 'Cannot delete payment method: It is linked to existing sales records. Please update or delete related sales first.');
            }

            $paymentMethod->delete();
            return redirect()->route('payment_methods.index')->with('success', 'Payment method deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Error deleting payment method (ID: {$paymentMethod->payment_id}): " . $e->getMessage());
            return back()->with('error', 'Failed to delete payment method: ' . $e->getMessage());
        }
    }
}