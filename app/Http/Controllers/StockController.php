<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    
    public function index()
    {
        $stocks = Stock::with('alizeti')->get(); 
        return view('stocks.index', compact('stocks'));
    }

    
    public function create()
    {
        return view('stocks.create');
    }

    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'alizeti_id' => 'required|exists:alizeti,ali_id',
            'mafuta_masafi' => 'required|numeric|min:0',
            'mashudu' => 'required|numeric|min:0',
            'mafuta_machafu' => 'required|numeric|min:0',
            'ugido'=>'required|numeric|min:0',
            'lami'=>'required|numeric|min:0',
        ]);

        Stock::create($validatedData);

        return redirect()->route('stocks.index')->with('success', 'Stock created successfully.');
    }

    
    public function show(Stock $stock)
    {
        return view('stocks.show', compact('stock'));
    }

    
    public function edit(Stock $stock)
    {
        return view('stocks.edit', compact('stock'));
    }

    
    public function update(Request $request, Stock $stock)
    {
        $validatedData = $request->validate([
            'alizeti_id' => 'required|exists:alizeti,ali_id',
            'mafuta_masafi' => 'required|numeric|min:0',
            'mashudu' => 'required|numeric|min:0',
            'mafuta_machafu' => 'required|numeric|min:0',
            'ugido'=>'required|numeric|min:0',
            'lami'=>'required|numeric|min:0',
        ]);

        $stock->update($validatedData);

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }

    
    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }
}