<?php

namespace App\Http\Controllers;

use App\Models\CustClean;        
use App\Models\CustAlizeti;    
use App\Models\CustomerStock;  
use App\Models\User;           
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustCleanController extends Controller
{
    public function index()
    {
        $custCleanOperations = CustClean::with(['custAlizeti.customer', 'user'])->latest('tarehe')->paginate(10);
        return view('cust_clean.index', compact('custCleanOperations'));
    }

    
    public function create()
    {
        $availableCustAlizetiBatches = CustAlizeti::with('customerStock')->whereHas('customerStock', function ($query) {
                $query->where('uncleaned_kg', '>', 0);
            })->get();
    
        $users = User::all(); 

        return view('cust_clean.create', compact('availableCustAlizetiBatches', 'users'));
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cust_ali_id' => 'required|exists:cust_alizeti,cust_ali_id',
            'tarehe' => 'required|date',
            'uncleaned_kg' => 'required|numeric|min:0.01', 
            'makapi_kg' => 'required|numeric|min:0',
            'initial_units' => 'nullable|numeric|min:0',
            'final_units' => 'nullable|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validated['makapi_kg'] > $validated['uncleaned_kg']) {
            return back()->withInput()->with('error', 'Makapi (waste) amount cannot be greater than uncleaned input amount.');
        }

        $cleanedOutput = $validated['uncleaned_kg'] - $validated['makapi_kg'];
        if ($cleanedOutput < 0) {
            return back()->withInput()->with('error', 'Calculated cleaned amount resulted in a negative value. Please review input amounts.');
        }
        $validated['cleaned'] = $cleanedOutput; 

        
        if ($validated['initial_units'] !== null && $validated['final_units'] !== null) {
            if ($validated['initial_units'] <= $validated['final_units']) {
                return back()->withInput()->with('error', 'Initial Electricity Unit must be greater than Final Electricity Unit.');
            }
            $validated['unit_used'] = $validated['initial_units'] - $validated['final_units'];
        } else {
            $validated['unit_used'] = null; 
        }
        
        if (($validated['initial_units'] === null && $validated['final_units'] !== null) ||
            ($validated['initial_units'] !== null && $validated['final_units'] === null)) {
            return back()->withInput()->with('error', 'Both Initial and Final Electricity Units must be provided if one is entered.');
        }

        
        $custAlizetiBatch = CustAlizeti::with('customerStock')->findOrFail($validated['cust_ali_id']);
        $customerStock = $custAlizetiBatch->customerStock;

        
        if (!$customerStock || $customerStock->uncleaned_kg < $validated['uncleaned_kg']) {
            $available = $customerStock ? $customerStock->uncleaned_kg : 0.00;
            return back()->withInput()->with('error', 'Insufficient uncleaned seeds in this customer\'s batch (' . $custAlizetiBatch->batch_no . '). Available: ' . $available . ' kg.');
        }

       
        $custCleanOperation = CustClean::create($validated);

        
        $customerStock->uncleaned_kg -= $validated['uncleaned_kg'];
        $customerStock->cleaned_kg += $validated['cleaned'];
        
        
        $customerStock->save();

        return redirect()->route('cust_clean.index')->with('success', 'Customer cleaning operation recorded and stock updated successfully!');
    }

   
    public function show(CustClean $cust_clean) 
    {
        $cust_clean->load(['custAlizeti.customer', 'custAlizeti.customerStock', 'user']);
        return view('cust_clean.show', compact('cust_clean'));
    }

   
    public function edit(CustClean $cust_clean)
    {
        $cust_clean->load(['custAlizeti.customer', 'custAlizeti.customerStock', 'user']);
        $availableCustAlizetiBatches = CustAlizeti::with('customerStock')->get(); 
        $users = User::all();

        return view('cust_clean.edit', compact('cust_clean', 'availableCustAlizetiBatches', 'users'));
    }

    
    public function update(Request $request, CustClean $cust_clean)
    {
        $validated = $request->validate([
            'cust_ali_id' => 'required|exists:cust_alizeti,cust_ali_id',
            'tarehe' => 'required|date',
            'uncleaned_kg' => 'required|numeric|min:0.01',
            'makapi_kg' => 'required|numeric|min:0',
            'initial_units' => 'nullable|numeric|min:0',
            'final_units' => 'nullable|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ]);

        
        $originalUncleanedInputKg = $cust_clean->uncleaned_kg;
        $originalCleanedOutput = $cust_clean->cleaned;
        $originalCustAliId = $cust_clean->cust_ali_id; 

        
        $newCleanedOutput = $validated['uncleaned_kg'] - $validated['makapi_kg'];
        if ($newCleanedOutput < 0) {
            return back()->withInput()->with('error', 'Calculated new cleaned output resulted in a negative value.');
        }
        $validated['cleaned'] = $newCleanedOutput;

        
        if ($validated['initial_units'] !== null && $validated['final_units'] !== null) {
            if ($validated['initial_units'] <= $validated['final_units']) {
                return back()->withInput()->with('error', 'Initial Electricity Unit must be greater than Final Electricity Unit.');
            }
            $validated['unit_used'] = $validated['initial_units'] - $validated['final_units'];
        } else {
            $validated['unit_used'] = null;
        }
       
        if (($validated['initial_units'] === null && $validated['final_units'] !== null) ||
            ($validated['initial_units'] !== null && $validated['final_units'] === null)) {
            return back()->withInput()->with('error', 'Both Initial and Final Electricity Units must be provided if one is entered.');
        }

        $oldCustAlizetiBatch = CustAlizeti::with('customerStock')->findOrFail($originalCustAliId);
        $oldCustomerStock = $oldCustAlizetiBatch->customerStock;

        if ($oldCustomerStock) {
            $oldCustomerStock->uncleaned_kg += $originalUncleanedInputKg; 
            $oldCustomerStock->cleaned_kg -= $originalCleanedOutput;      
            if ($oldCustomerStock->uncleaned_kg < 0) $oldCustomerStock->uncleaned_kg = 0;
            if ($oldCustomerStock->cleaned_kg < 0) $oldCustomerStock->cleaned_kg = 0;
            $oldCustomerStock->save();
        } else {
            return back()->withInput()->with('error', "Original customer stock record for batch ID {$originalCustAliId} not found during update.");
        }

        
        $currentCustAlizetiBatch = CustAlizeti::with('customerStock')->findOrFail($validated['cust_ali_id']);
        $currentCustomerStock = $currentCustAlizetiBatch->customerStock;

        if (!$currentCustomerStock) {
            
            $oldCustomerStock->uncleaned_kg -= $originalUncleanedInputKg;
            $oldCustomerStock->cleaned_kg += $originalCleanedOutput;
            $oldCustomerStock->save();
            return back()->withInput()->with('error', "Target customer stock record for batch ID {$validated['cust_ali_id']} not found during update.");
        }

        
        if ($currentCustomerStock->uncleaned_kg < $validated['uncleaned_kg']) {
            
            $oldCustomerStock->uncleaned_kg -= $originalUncleanedInputKg; 
            $oldCustomerStock->cleaned_kg += $originalCleanedOutput;    
            $oldCustomerStock->save(); 
            return back()->withInput()->with('error', 'Insufficient uncleaned seeds in selected Alizeti batch (' . $currentCustAlizetiBatch->batch_no . ') for updated quantity. Available: ' . $currentCustomerStock->uncleaned_kg . ' kg.');
        }

        $currentCustomerStock->uncleaned_kg -= $validated['uncleaned_kg'];
        $currentCustomerStock->cleaned_kg += $validated['cleaned'];
        $currentCustomerStock->save();

        
        $cust_clean->update($validated);

        return redirect()->route('cust_clean.index')->with('success', 'Customer cleaning operation updated and stock adjusted successfully!');
    }

   
    public function destroy(CustClean $cust_clean)
    {

        $cust_clean->load(['custAlizeti.customerStock']);
        $customerStock = $cust_clean->custAlizeti->customerStock;

        if ($customerStock) {
            
            $customerStock->uncleaned_kg += $cust_clean->uncleaned_kg;
            $customerStock->cleaned_kg -= $cust_clean->cleaned;

            if ($customerStock->uncleaned_kg < 0) $customerStock->uncleaned_kg = 0;
            if ($customerStock->cleaned_kg < 0) $customerStock->cleaned_kg = 0;
            $customerStock->save();
        } else {
            return back()->with('error', "Customer stock record for batch ID {$cust_clean->cust_ali_id} missing. Cannot revert stock.");
        }

        
        $cust_clean->delete();

        return redirect()->route('cust_clean.index')->with('success', 'Customer cleaning operation deleted and stock reversed.');
    }
}