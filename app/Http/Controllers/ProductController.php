<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Log; 

class ProductController extends Controller
{
    
    public function index()
    {
        $products = Product::latest()->get(); 
        return view('products.index', compact('products'));
    }

    
    public function create()
    {
        return view('products.create');
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name', // Name must be unique
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean', // Checkbox value is 1 if checked, absent if unchecked
        ]);

        try {
            Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'), // Correctly handles checkbox state
            ]);

            return redirect()->route('products.index')->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            Log::error("Error creating product: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create product.');
        }
    }

   
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Rule to ensure name is unique, but ignore the current product's name
                Rule::unique('products', 'name')->ignore($product->id),
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        try {
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');

        } catch (\Exception | \Illuminate\Validation\ValidationException $e) {
            Log::error("Error updating product (ID: {$product->id}): " . $e->getMessage());
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return back()->withInput()->with('error', 'Failed to update product.');
        }
    }

    

    
    public function destroy(Product $product)
    {
        try {
            if ($product->mauzos()->count() > 0) {
                return back()->with('error', 'Cannot delete product: It is linked to existing sales records. Please update or delete related sales first.');
            }

            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Error deleting product (ID: {$product->id}): " . $e->getMessage());
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}