<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with(['user', 'category']);

        // Filtering by category
        if ($request->has('category_filter') && $request->category_filter != '') {
            $expenses->where('category_id', $request->category_filter);
        }

        // Filtering by date
        if ($request->has('date_filter') && $request->date_filter != '') {
            $expenses->where('tarehe', $request->date_filter);
        }

        $expenses = $expenses->get();
        $totalAmount = $expenses->sum('amount');
        $categories = \App\Models\Category::all();

        return view('expenses.index', compact('expenses', 'totalAmount', 'categories'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'tarehe' => 'required|date',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        Expense::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'tarehe' => $request->tarehe,
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }

    public function edit(Expense $expense)
    {
        $categories = \App\Models\Category::all();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'tarehe' => 'required|date',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        $expense->update([
            'category_id' => $request->category_id,
            'tarehe' => $request->tarehe,
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}