<?php

namespace App\Http\Controllers;

use App\Models\CustomerStock; 
use Illuminate\Http\Request;

class CustomerStockController extends Controller
{
    
    public function index()
    {
        
        $customerStocks = CustomerStock::with(['custAlizeti.customer'])->latest('updated_at')->paginate(10); 
                                        
        return view('customer_stocks.index', compact('customerStocks'));
    }

    public function show(CustomerStock $customer_stock) 
    {
        
        $customer_stock->load(['custAlizeti.customer']); 
        return view('customer_stocks.show', compact('customer_stock'));
    }
    
}