<?php

namespace App\Http\Controllers;

use App\Models\Filtering;        
use App\Models\CustAlizeti;   
use App\Models\CustomerStock;  
use App\Models\User;           
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FilteringController extends Controller
{   
    
    public function index(Request $request)
    {
       
        $query = Filtering::with(['custAlizeti.customer', 'user']);

        
        if ($request->filled('start_date')) {
            $query->where('tarehe', '>=', $request->input('start_date'));
        }
        
        if ($request->filled('end_date')) {
            $query->where('tarehe', '<=', $request->input('end_date'));
        }

        
        if ($request->filled('cust_ali_id')) {
            $query->where('cust_ali_id', $request->input('cust_ali_id'));
        }

        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        
        $query->orderBy('tarehe', 'desc');
        
        $filteringOperations = $query->paginate(10); 
        $custAlizetiBatches = CustAlizeti::with('customer')->orderBy('batch_no')->get(); 
        
        $users = User::orderBy('first_name')->get();   

       
        return view('filtering.index', compact(
            'filteringOperations',     
            'custAlizetiBatches', 
            'users'                    
        ));
    }
    
    public function create()
    {
        
        $availableCustAlizetiBatches = CustAlizeti::with('customerStock')->whereHas('customerStock', function ($query) {
                $query->where('crude_oil', '>', 0); 
            })->get();
        
        $users = User::all(); 

        return view('filtering.create', compact('availableCustAlizetiBatches', 'users')); 
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cust_ali_id' => 'required|exists:cust_alizeti,cust_ali_id',
            'user_id' => 'required|exists:users,id',
            'tarehe' => 'required|date',
            'crude_oil' => 'required|numeric|min:0.01', 
            'refined_oil' => 'required|numeric|min:0',    
            'lami_kg' => 'required|numeric|min:0',       
            'ugido_kg' => 'required|numeric|min:0',      
            'initial_units' => 'nullable|numeric|min:0',
            'final_units' => 'nullable|numeric|min:0',
            'cost_used' => 'required|numeric|min:0',     
        ]);

        
        if ($validated['initial_units'] !== null && $validated['final_units'] !== null) {
            if ($validated['initial_units'] <= $validated['final_units']) {
                return back()->withInput()->with('error', 'Initial Electricity Unit must be greater than Final Electricity Unit.');
            }
            $validated['unit_used'] = $validated['initial_units'] - $validated['final_units'];
        } else {
            
            if (($validated['initial_units'] === null && $validated['final_units'] !== null) ||
                ($validated['initial_units'] !== null && $validated['final_units'] === null)) {
                return back()->withInput()->with('error', 'Both Initial and Final Electricity Units must be provided if one is entered.');
            }
            $validated['unit_used'] = null; 
        }

        
        $custAlizetiBatch = CustAlizeti::with('customerStock')->findOrFail($validated['cust_ali_id']);
        $customerStock = $custAlizetiBatch->customerStock;


        if (!$customerStock || $customerStock->crude_oil < $validated['crude_oil']) {
            $available = $customerStock ? $customerStock->crude_oil : 0.00;
            return back()->withInput()->with('error', 'Insufficient crude oil in this customer\'s batch (' . $custAlizetiBatch->batch_no . '). Available: ' . $available . ' Liters.');
        }
        
        
        $filtering = Filtering::create($validated); 

        
        $customerStock->crude_oil -= $validated['crude_oil'];
        $customerStock->refined_oil += $validated['refined_oil'];
        $customerStock->lami_kg += $validated['lami_kg'];
        $customerStock->ugido_kg += $validated['ugido_kg'];
        
        $customerStock->save();

        return redirect()->route('filtering.index')->with('success', 'Filtering operation recorded and customer stock updated successfully!'); // Updated route name
    }

    
    public function show(Filtering $filtering) 
    {
        $filtering->load(['custAlizeti.customer', 'custAlizeti.customerStock', 'user']);
        return view('filtering.show', compact('filtering'));
    }

    
    public function edit(Filtering $filtering) 
    {
        $filtering->load(['custAlizeti.customer', 'custAlizeti.customerStock', 'user']);
        $availableCustAlizetiBatches = CustAlizeti::with('customerStock')->get();
        $users = User::all();

        return view('filtering.edit', compact('filtering', 'availableCustAlizetiBatches', 'users')); 
    }

    
    public function update(Request $request, Filtering $filtering) 
    {
        $validated = $request->validate([
            'cust_ali_id' => 'required|exists:cust_alizeti,cust_ali_id',
            'user_id' => 'required|exists:users,id',
            'tarehe' => 'required|date',
            'crude_oil' => 'required|numeric|min:0.01',
            'refined_oil' => 'required|numeric|min:0',
            'lami_kg' => 'required|numeric|min:0',
            'ugido_kg' => 'required|numeric|min:0',
            'initial_units' => 'nullable|numeric|min:0',
            'final_units' => 'nullable|numeric|min:0',
            'cost_used' => 'required|numeric|min:0',
        ]);

        
        $originalCrudeOilInputLtr = $filtering->crude_oil; 
        $originalRefinedOilOutputLtr = $filtering->refined_oil; 
        $originalLamiOutputKg = $filtering->lami_kg;      
        $originalUgidoOutputKg = $filtering->ugido_kg;     
        $originalCustAliId = $filtering->cust_ali_id;


        if ($validated['initial_units'] !== null && $validated['final_units'] !== null) {
            if ($validated['initial_units'] <= $validated['final_units']) {
                return back()->withInput()->with('error', 'Initial Electricity Unit must be greater than Final Electricity Unit.');
            }
            $validated['unit_used'] = $validated['initial_units'] - $validated['final_units'];
        } else {
            if (($validated['initial_units'] === null && $validated['final_units'] !== null) ||
                ($validated['initial_units'] !== null && $validated['final_units'] === null)) {
                return back()->withInput()->with('error', 'Both Initial and Final Electricity Units must be provided if one is entered.');
            }
            $validated['unit_used'] = null;
        }

       
        $oldCustAlizetiBatch = CustAlizeti::with('customerStock')->findOrFail($originalCustAliId);
        $oldCustomerStock = $oldCustAlizetiBatch->customerStock;

        if ($oldCustomerStock) {
            $oldCustomerStock->crude_oil += $originalCrudeOilInputLtr;
            $oldCustomerStock->refined_oil -= $originalRefinedOilOutputLtr;
            $oldCustomerStock->lami_kg -= $originalLamiOutputKg;
            $oldCustomerStock->ugido_kg -= $originalUgidoOutputKg;
            
            if ($oldCustomerStock->crude_oil < 0) $oldCustomerStock->crude_oil = 0;
            if ($oldCustomerStock->refined_oil < 0) $oldCustomerStock->refined_oil = 0;
            if ($oldCustomerStock->lami_kg < 0) $oldCustomerStock->lami_kg = 0;
            if ($oldCustomerStock->ugido_kg < 0) $oldCustomerStock->ugido_kg = 0;
            $oldCustomerStock->save();
        } else {
            return back()->withInput()->with('error', "Original customer stock record for batch ID {$originalCustAliId} not found during update.");
        }


        $currentCustAlizetiBatch = CustAlizeti::with('customerStock')->findOrFail($validated['cust_ali_id']);
        $currentCustomerStock = $currentCustAlizetiBatch->customerStock;

        if (!$currentCustomerStock) {
            
            $oldCustomerStock->crude_oil -= $originalCrudeOilInputLtr;
            $oldCustomerStock->refined_oil += $originalRefinedOilOutputLtr;
            $oldCustomerStock->lami_kg += $originalLamiOutputKg;
            $oldCustomerStock->ugido_kg += $originalUgidoOutputKg;
            $oldCustomerStock->save();
            return back()->withInput()->with('error', "Target customer stock record for batch ID {$validated['cust_ali_id']} not found during update.");
        }

        
        if ($currentCustomerStock->crude_oil < $validated['crude_oil']) {
           
            $oldCustomerStock->crude_oil -= $originalCrudeOilInputLtr;
            $oldCustomerStock->refined_oil += $originalRefinedOilOutputLtr;
            $oldCustomerStock->lami_kg += $originalLamiOutputKg;
            $oldCustomerStock->ugido_kg += $originalUgidoOutputKg;
            $oldCustomerStock->save();
            return back()->withInput()->with('error', 'Insufficient crude oil in selected Alizeti batch (' . $currentCustAlizetiBatch->batch_no . ') for updated quantity. Available: ' . $currentCustomerStock->crude_oil . ' Liters.');
        }

        $currentCustomerStock->crude_oil -= $validated['crude_oil'];
        $currentCustomerStock->refined_oil += $validated['refined_oil'];
        $currentCustomerStock->lami_kg += $validated['lami_kg'];
        $currentCustomerStock->ugido_kg += $validated['ugido_kg'];
        $currentCustomerStock->save();


        $filtering->update($validated);

        return redirect()->route('filtering.index')->with('success', 'Filtering operation updated and customer stock adjusted successfully!'); // Updated route name
    }

    
    public function destroy(Filtering $filtering)
    {
        
        $filtering->load(['custAlizeti.customerStock']);
        $customerStock = $filtering->custAlizeti->customerStock;

        if ($customerStock) {

            $customerStock->crude_oil += $filtering->crude_oil; 
            $customerStock->refined_oil -= $filtering->refined_oil; 
            $customerStock->lami_kg -= $filtering->lami_kg;       
            $customerStock->ugido_kg -= $filtering->ugido_kg;

            if ($customerStock->crude_oil < 0) $customerStock->crude_oil = 0;
            if ($customerStock->refined_oil < 0) $customerStock->refined_oil = 0;
            if ($customerStock->lami_kg < 0) $customerStock->lami_kg = 0;
            if ($customerStock->ugido_kg < 0) $customerStock->ugido_kg = 0;
            $customerStock->save();
        } else {
            return back()->with('error', "Customer stock record for batch ID {$filtering->cust_ali_id} missing. Cannot revert stock.");
        }

        
        $filtering->delete(); 

        return redirect()->route('filtering.index')->with('success', 'Filtering operation deleted and customer stock reversed.'); // Updated route name
    }
}