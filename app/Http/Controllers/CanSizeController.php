<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Expense;
use App\Models\CanSize;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;

class CanSizeController extends Controller
{
    public function index()
    {
        $canSizes = CanSize::all();
        return view('can-sizes.index', compact('canSizes'));
    }

    public function create()
    {
        return view('can-sizes.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'size' => 'required|in:1,2,3,5,10,20',
            'price_per_can' => 'required|numeric|min:0'
        ]);

        CanSize::create($validatedData);

        return redirect()->route('can-sizes.index')->with('success', 'Can size created successfully');
    }

    public function show(CanSize $canSize)
    {
        return view('can-sizes.show', compact('canSize'));
    }

    public function edit(CanSize $canSize)
    {
        return view('can-sizes.edit', compact('canSize'));
    }

    public function update(Request $request, CanSize $canSize)
    {
        $validatedData = $request->validate([
            'size' => 'required|in:1,2,3,5,10,20',
            'price_per_can' => 'required|numeric|min:0'
        ]);

        $canSize->update($validatedData);

        return redirect()->route('can-sizes.index')->with('success', 'price updated successfully');
    }

    public function destroy(CanSize $canSize)
    {
        $canSize->delete();
        return redirect()->route('can-sizes.index')->with('success', 'Can size deleted successfully');
    }
}
