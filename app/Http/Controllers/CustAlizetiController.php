<?php

namespace App\Http\Controllers;

use App\Models\CustAlizeti;
use App\Models\Customer; 
use App\Models\CustomerStock; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class CustAlizetiController extends Controller
{
    
    public function index()
    {
        
        $custAlizetiInputs = CustAlizeti::with(['customer', 'customerStock'])->latest('tarehe')->paginate(10);
        return view('cust_alizeti.index', compact('custAlizetiInputs'));
    }
   
    public function create()
    {
        $customers = Customer::orderBy('first_name')->get(); 
        return view('cust_alizeti.create', compact('customers'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,cust_id', 
            'batch_no' => 'required|string|max:255|unique:cust_alizeti,batch_no', 
            'uncleaned_kg' => 'required|numeric|min:0.01',
            'tarehe' => 'required|date',
            'status' => 'nullable|string', 
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'received'; 
        }

        
        $custAlizeti = CustAlizeti::create($validated);


        CustomerStock::create([
            'ali_id' => $custAlizeti->cust_ali_id,
            'uncleaned_kg' => $custAlizeti->uncleaned_kg,
            
        ]);

        return redirect()->route('cust_alizeti.index')->with('success', 'Customer Alizeti input recorded and stock initialized successfully!');
    }

    
    public function show(CustAlizeti $cust_alizeti) 
    {
        
        $cust_alizeti->load(['customer', 'customerStock']); 
        return view('cust_alizeti.show', compact('cust_alizeti'));
    }

    
    public function edit(CustAlizeti $cust_alizeti)
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('cust_alizeti.edit', compact('cust_alizeti', 'customers'));
    }

    
    public function update(Request $request, CustAlizeti $cust_alizeti)
    {
        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,cust_id',
            'batch_no' => 'required|string|max:255|unique:cust_alizeti,batch_no,' . $cust_alizeti->cust_ali_id . ',cust_ali_id', 
            'uncleaned_kg' => 'required|numeric|min:0.01',
            'tarehe' => 'required|date',
            'status' => 'required|string', 
        ]);


        $originalUncleanedKg = $cust_alizeti->uncleaned_kg;


        $cust_alizeti->update($validated);

        if ($cust_alizeti->uncleaned_kg !== $originalUncleanedKg) {
            $customerStock = $cust_alizeti->customerStock; 

           
            if ($customerStock) {
                
                $difference = $cust_alizeti->uncleaned_kg - $originalUncleanedKg;
                
                
                $customerStock->uncleaned_kg += $difference;

                
                if ($customerStock->uncleaned_kg < 0) {
                    $customerStock->uncleaned_kg = 0;
                }
                $customerStock->save();
            } else {

                \Log::warning("CustomerStock record not found for cust_ali_id: {$cust_alizeti->cust_ali_id} during update.");
                
            }
        }

        return redirect()->route('cust_alizeti.index')->with('success', 'Customer Alizeti input updated and associated stock adjusted successfully!');
    }

    
    public function destroy(CustAlizeti $cust_alizeti)
    {
        
        $cust_alizeti->delete();
        
        return redirect()->route('cust_alizeti.index')->with('success', 'Customer Alizeti input and associated stock deleted successfully!');
    }
}