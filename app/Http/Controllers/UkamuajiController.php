<?php

namespace App\Http\Controllers;

use App\Models\Ukamuaji;         
use App\Models\CustAlizeti;     
use App\Models\CustomerStock;   
use App\Models\User;            
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UkamuajiController extends Controller
{
    
    public function index()
    {
        $ukamuajiOperations = Ukamuaji::with(['custAlizeti.customer', 'user'])->latest('tarehe')->paginate(10);
        return view('ukamuaji.index', compact('ukamuajiOperations'));
    }

   
    public function create()
    {
        
        $availableCustAlizetiBatches = CustAlizeti::with('customerStock')->whereHas('customerStock', function ($query) {
                $query->where('cleaned_kg', '>', 0); 
            })->get();
        
        $users = User::all(); 

        return view('ukamuaji.create', compact('availableCustAlizetiBatches', 'users'));
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cust_ali_id' => 'required|exists:cust_alizeti,cust_ali_id',
            'user_id' => 'required|exists:users,id',
            'tarehe' => 'required|date',
            'cleaned_kg' => 'required|numeric|min:0.01',
            'crude_oil' => 'required|numeric|min:0',   
            'mashudu_kg' => 'required|numeric|min:0',   
            'initial_units' => 'nullable|numeric|min:0',
            'final_units' => 'nullable|numeric|min:0',
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

        
        if (!$customerStock || $customerStock->cleaned_kg < $validated['cleaned_kg']) {
            $available = $customerStock ? $customerStock->cleaned_kg : 0.00;
            return back()->withInput()->with('error', 'Insufficient cleaned seeds in this customer\'s batch (' . $custAlizetiBatch->batch_no . '). Available: ' . $available . ' kg.');
        }
        
        
        $ukamuaji = Ukamuaji::create($validated);

        $customerStock->cleaned_kg -= $validated['cleaned_kg'];
        $customerStock->crude_oil += $validated['crude_oil'];
        $customerStock->mashudu_kg += $validated['mashudu_kg'];
        
        $customerStock->save();

        return redirect()->route('ukamuaji.index')->with('success', 'Ukamuaji operation recorded and customer stock updated successfully!');
    }

    
    public function show(Ukamuaji $ukamuaji) 
    {
        $ukamuaji->load(['custAlizeti.customer', 'custAlizeti.customerStock', 'user']);
        return view('ukamuaji.show', compact('ukamuaji'));
    }

    
    public function edit(Ukamuaji $ukamuaji)
    {
        $ukamuaji->load(['custAlizeti.customer', 'custAlizeti.customerStock', 'user']);
        $availableCustAlizetiBatches = CustAlizeti::with('customerStock')->get(); 
        $users = User::all();

        return view('ukamuaji.edit', compact('ukamuaji', 'availableCustAlizetiBatches', 'users'));
    }

    
    public function update(Request $request, Ukamuaji $ukamuaji)
    {
        $validated = $request->validate([
            'cust_ali_id' => 'required|exists:cust_alizeti,cust_ali_id',
            'user_id' => 'required|exists:users,id',
            'tarehe' => 'required|date',
            'cleaned_kg' => 'required|numeric|min:0.01',
            'crude_oil' => 'required|numeric|min:0',
            'mashudu_kg' => 'required|numeric|min:0',
            'initial_units' => 'nullable|numeric|min:0',
            'final_units' => 'nullable|numeric|min:0',
        ]);


        $originalCleanedInputKg = $ukamuaji->cleaned_kg;
        $originalCrudeOilOutputLtr = $ukamuaji->crude_oil;
        $originalMashuduOutputKg = $ukamuaji->mashudu_kg;
        $originalCustAliId = $ukamuaji->cust_ali_id; 

        
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
            $oldCustomerStock->cleaned_kg += $originalCleanedInputKg;     
            $oldCustomerStock->crude_oil -= $originalCrudeOilOutputLtr;   
            $oldCustomerStock->mashudu_kg -= $originalMashuduOutputKg;    

            if ($oldCustomerStock->cleaned_kg < 0) $oldCustomerStock->cleaned_kg = 0;
            if ($oldCustomerStock->crude_oil < 0) $oldCustomerStock->crude_oil = 0;
            if ($oldCustomerStock->mashudu_kg < 0) $oldCustomerStock->mashudu_kg = 0;
            $oldCustomerStock->save();
        } else {
            return back()->withInput()->with('error', "Original customer stock record for batch ID {$originalCustAliId} not found during update.");
        }

        
        $currentCustAlizetiBatch = CustAlizeti::with('customerStock')->findOrFail($validated['cust_ali_id']);
        $currentCustomerStock = $currentCustAlizetiBatch->customerStock;

        if (!$currentCustomerStock) {
            
            $oldCustomerStock->cleaned_kg -= $originalCleanedInputKg;
            $oldCustomerStock->crude_oil += $originalCrudeOilOutputLtr;
            $oldCustomerStock->mashudu_kg += $originalMashuduOutputKg;
            $oldCustomerStock->save();
            return back()->withInput()->with('error', "Target customer stock record for batch ID {$validated['cust_ali_id']} not found during update.");
        }

        
        if ($currentCustomerStock->cleaned_kg < $validated['cleaned_kg']) {
            
            $oldCustomerStock->cleaned_kg -= $originalCleanedInputKg;
            $oldCustomerStock->crude_oil += $originalCrudeOilOutputLtr;
            $oldCustomerStock->mashudu_kg += $originalMashuduOutputKg;
            $oldCustomerStock->save();
            return back()->withInput()->with('error', 'Insufficient cleaned seeds in selected Alizeti batch (' . $currentCustAlizetiBatch->batch_no . ') for updated quantity. Available: ' . $currentCustomerStock->cleaned_kg . ' kg.');
        }

        $currentCustomerStock->cleaned_kg -= $validated['cleaned_kg'];
        $currentCustomerStock->crude_oil += $validated['crude_oil'];
        $currentCustomerStock->mashudu_kg += $validated['mashudu_kg'];
        $currentCustomerStock->save();

        
        $ukamuaji->update($validated);

        return redirect()->route('ukamuaji.index')->with('success', 'Ukamuaji operation updated and customer stock adjusted successfully!');
    }

    
    public function destroy(Ukamuaji $ukamuaji)
    {
        
        $ukamuaji->load(['custAlizeti.customerStock']);
        $customerStock = $ukamuaji->custAlizeti->customerStock;

        if ($customerStock) {
            
            $customerStock->cleaned_kg += $ukamuaji->cleaned_kg;
            $customerStock->crude_oil -= $ukamuaji->crude_oil;
            $customerStock->mashudu_kg -= $ukamuaji->mashudu_kg;

            if ($customerStock->cleaned_kg < 0) $customerStock->cleaned_kg = 0;
            if ($customerStock->crude_oil < 0) $customerStock->crude_oil = 0;
            if ($customerStock->mashudu_kg < 0) $customerStock->mashudu_kg = 0;
            $customerStock->save();
        } else {
            return back()->with('error', "Customer stock record for batch ID {$ukamuaji->cust_ali_id} missing. Cannot revert stock.");
        }

       
        $ukamuaji->delete();

        return redirect()->route('ukamuaji.index')->with('success', 'Ukamuaji operation deleted and customer stock reversed.');
    }
}