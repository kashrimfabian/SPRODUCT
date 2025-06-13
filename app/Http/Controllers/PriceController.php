<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Alizeti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceController extends Controller
{
     
    public function index()
    {
        
        $prices = Price::with('user','alizeti')->get();

        return view('price.index', compact('prices'));
    }

    
    public function create()
   {
      $alizetiBatches = Alizeti::all();
      return view('price.create', compact('alizetiBatches'));
   }
   
   public function store(Request $request)
{
    $request->validate([
        'price_per_litre' => 'required|numeric|min:0',
        'price_of_lami' => 'required|numeric|min:0',
        'price_of_ugido' => 'required|numeric|min:0',
        'price_of_mashudu' => 'required|numeric|min:0',
        'alizeti_id' => 'required|exists:alizeti,ali_id|unique:prices,alizeti_id',
    ], [
        'alizeti_id.required' => 'Please select a batch number.',
        'alizeti_id.exists' => 'The selected batch number is invalid.',
        'alizeti_id.unique' => 'Prices for this batch number have already been set.',
    ]);

    Price::create([
        'price_per_litre' => $request->price_per_litre,
        'price_of_mashudu' => $request->price_of_mashudu,
        'price_of_lami' => $request->price_of_lami,
        'price_of_ugido' => $request->price_of_ugido,
        'alizeti_id' => $request->alizeti_id,
        'user_id' => Auth::id(),
    ]);

    return redirect()->route('price.index')->with('success', 'Price set successfully.');
}
   
    public function edit($prices_id)
    {
        
        $price = Price::findOrFail($prices_id);
        $alizeti = Alizeti::all();
        return view('price.edit', compact('price', 'alizeti'));
    }

    
    public function update(Request $request, $prices_id)
    {
        $request->validate([
            'price_per_litre' => 'required|numeric|min:0',
            'price_of_mashudu' => 'required|numeric|min:0',
            'price_of_lami' => 'required|numeric|min:0',
            'price_of_ugido' => 'required|numeric|min:0',
            
        ]);

        
        $price = Price::findOrFail($prices_id);
        $price->update([
            'price_per_litre' => $request->price_per_litre,
            'price_of_mashudu' => $request->price_of_mashudu,
            'price_of_lami' => $request->price_of_lami,
            'price_of_lami' => $request->price_of_lami,
            
        ]);

        return redirect()->route('price.index')->with('success', 'Price updated successfully');
    }
}