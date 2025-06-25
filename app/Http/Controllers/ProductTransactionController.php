<?php

namespace App\Http\Controllers;

use App\Models\ProductTransaction;
use App\Models\CustAlizeti;
use App\Models\CustomerStock;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB; 

class ProductTransactionController extends Controller
{
    private $productToStockColumnMap = [
        'Mafuta' => 'refined_oil',
        'Mashudu' => 'mashudu_kg',
        'Ugido' => 'ugido_kg',
        'Rami' => 'lami_kg',
    ];

    
    public function index()
    {
        $productTransactions = ProductTransaction::with(['product', 'custAlizeti.customer', 'user'])->latest('tarehe')->paginate(10);
        return view('product_transactions.index', compact('productTransactions'));
    }

    
    public function create()
    {
        $availableCustAlizetiBatches = CustAlizeti::with('customerStock')
            ->whereHas('customerStock', function ($query) {
                $query->where('refined_oil', '>', 0)
                      ->orWhere('mashudu_kg', '>', 0)
                      ->orWhere('ugido_kg', '>', 0)
                      ->orWhere('lami_kg', '>', 0);
            })
            ->get();
        
        $users = User::all();
        $products = Product::whereIn('name', array_keys($this->productToStockColumnMap))->get(); 

        return view('product_transactions.create', compact('availableCustAlizetiBatches', 'users', 'products'));
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cust_ali_id' => 'required|exists:cust_alizeti,cust_ali_id',
            'user_id' => 'required|exists:users,id',
            'tarehe' => 'required|date',
            'trans_type' => 'required|in:collection,sale', 
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|numeric|min:0.01', 
            'amount' => 'nullable|numeric|min:0', 
            'buyer_name' => 'nullable|string|max:255', 
        ]);

        $validated['amount'] = $validated['amount'] ?? 0;

        if ($validated['trans_type'] === 'sale') {
            if ($validated['amount'] <= 0) {
                return back()->withInput()->with('error', 'Amount received must be greater than zero for sales.');
            }
        } else {
            $validated['buyer_name'] = null;
        }

        $validated['status'] = 'pending';

        ProductTransaction::create($validated);

        return redirect()->route('product_transactions.index')->with('success', 'Product transaction recorded successfully as PENDING. Please confirm it to update stock.');
    }

   
    public function confirm(ProductTransaction $product_transaction)
    {
       
        if ($product_transaction->status === 'confirmed') {
            return back()->with('error', 'This transaction is already CONFIRMED and cannot be confirmed again.');
        }
        if ($product_transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be confirmed.');
        }

        DB::beginTransaction();
        try {
            $product_transaction->load(['product', 'custAlizeti.customerStock']);

            $productName = $product_transaction->product->name ?? null;
            $stockColumn = $this->productToStockColumnMap[$productName] ?? null;

            if (!$stockColumn) {
                throw new \Exception('Product mapping missing for "' . $productName . '". Cannot confirm.');
            }

            $customerStock = $product_transaction->custAlizeti->customerStock;

            if (!$customerStock) {
                throw new \Exception('Customer stock record not found for this batch. Cannot confirm.');
            }

            if ($customerStock->$stockColumn < $product_transaction->quantity) {
                throw new \Exception('Insufficient ' . $productName . ' in customer\'s batch (' . $product_transaction->custAlizeti->batch_no . ') for confirmation. Available: ' . number_format($customerStock->$stockColumn, 2) . ' ' . $product_transaction->product->unit_of_measure . '.');
            }

            $customerStock->$stockColumn -= $product_transaction->quantity;
            $customerStock->save();

            $product_transaction->status = 'confirmed';
            $product_transaction->save();

            DB::commit();
            return redirect()->route('product_transactions.index')->with('success', 'Transaction confirmed and stock updated successfully! This transaction is now view-only.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to confirm transaction: ' . $e->getMessage());
        }
    }
    
    public function show(ProductTransaction $product_transaction)
    {
        $product_transaction->load(['product', 'custAlizeti.customer', 'custAlizeti.customerStock', 'user']);
        return view('product_transactions.show', compact('product_transaction'));
    }

    public function edit(ProductTransaction $product_transaction)
    {
        
        if ($product_transaction->status !== 'pending') {
            return redirect()->route('product_transactions.show', $product_transaction->trans_id)
                             ->with('error', 'Only pending transactions can be edited.');
        }

        $product_transaction->load(['product', 'custAlizeti.customer', 'custAlizeti.customerStock', 'user']);
        $availableCustAlizetiBatches = CustAlizeti::with('customerStock')->get(); 
        $users = User::all();
        $products = Product::whereIn('name', array_keys($this->productToStockColumnMap))->get(); 

        return view('product_transactions.edit', compact('product_transaction', 'availableCustAlizetiBatches', 'users', 'products'));
    }

   
    public function update(Request $request, ProductTransaction $product_transaction)
    {
        
        if ($product_transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be updated.');
        }

        $validated = $request->validate([
            'cust_ali_id' => 'required|exists:cust_alizeti,cust_ali_id',
            'user_id' => 'required|exists:users,id',
            'tarehe' => 'required|date',
            'trans_type' => 'required|in:collection,sale',
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|numeric|min:0.01',
            'amount' => 'nullable|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
        ]);

        $validated['amount'] = $validated['amount'] ?? 0;

        if ($validated['trans_type'] === 'sale') {
            if ($validated['amount'] <= 0) {
                return back()->withInput()->with('error', 'Amount received must be greater than zero for sales.');
            }
        } else {
            $validated['buyer_name'] = null; 
        }

       
        $product_transaction->update($validated);

        return redirect()->route('product_transactions.index')->with('success', 'Transaction updated successfully. Remember to CONFIRM it if it was previously pending and changes were made affecting stock readiness.');
    }

    
    public function destroy(ProductTransaction $product_transaction)
    {
        DB::beginTransaction();
        try {
            if ($product_transaction->status === 'confirmed') {
                $product_transaction->load(['product', 'custAlizeti.customerStock']);

                $productName = $product_transaction->product->name ?? null;
                $stockColumn = $this->productToStockColumnMap[$productName] ?? null;

                if (!$stockColumn) {
                    throw new \Exception('Product mapping missing for "' . $productName . '" during deletion reversal.');
                }

                $customerStock = $product_transaction->custAlizeti->customerStock;

                if ($customerStock) {
                    $customerStock->$stockColumn += $product_transaction->quantity;
                    if ($customerStock->$stockColumn < 0) $customerStock->$stockColumn = 0; 
                    $customerStock->save();
                } else {
                    throw new \Exception("Customer stock record for batch ID {$product_transaction->cust_ali_id} missing. Cannot revert stock.");
                }
            } 
            

            $product_transaction->delete();

            DB::commit();
            return redirect()->route('product_transactions.index')->with('success', 'Product transaction deleted. Stock ' . ($product_transaction->status === 'confirmed' ? 'reversed' : 'was not affected (pending)') . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }
}