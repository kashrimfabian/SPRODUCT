<?php

namespace App\Http\Controllers;

use App\Models\Mauzo;
use App\Models\Alizeti;
use App\Models\Price;
use App\Models\User;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\CustomerDebit; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MauzoController extends Controller
{
    
    public function index(Request $request)
    {
        $mauzo = Mauzo::query();
        if ($request->filled('alizeti_id')) {
            $mauzo->where('alizeti_id', $request->alizeti_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $mauzo->whereBetween('tarehe', [$request->start_date, $request->end_date]);
        }
        $mauzo->orderBy('created_at', 'desc');
        $mauzoRecords = $mauzo->paginate(10);
        $alizeti = Alizeti::with('stock')->get(); 
        $products = Product::all();
        $paymentMethods = PaymentMethod::all();
        return view('mauzo.index', compact('mauzoRecords', 'alizeti', 'products', 'paymentMethods'));
    }
    
    public function create()
    {
        $alizeti = Alizeti::with('stock')->get();
        $products = Product::where('is_active', true)->get();
        $users = User::all();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        return view('mauzo.create', compact('alizeti', 'products', 'users', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $selectedPaymentMethod = PaymentMethod::find($request->input('payment_id'));
            $isDebtSale = $selectedPaymentMethod && $selectedPaymentMethod->name === 'Debits';

            $product = Product::findOrFail($request->input('product_id'));
            $productNameLower = strtolower(trim($product->name));
            $stockCategory = '';

            if (str_contains($productNameLower, 'mafuta')) {
                $stockCategory = 'mafuta_masafi';
            } elseif (str_contains($productNameLower, 'mashudu')) {
                $stockCategory = 'mashudu';
            } elseif (str_contains($productNameLower, 'ugido')) {
                $stockCategory = 'ugido';
            } elseif (str_contains($productNameLower, 'lami')) {
                $stockCategory = 'lami';
            } else {
                throw new \Exception('Product name ("' . $product->name . '") does not correspond to a known stock category for price/stock determination. Please ensure product name contains "mafuta", "mashudu", "ugido", or "lami".');
            }
            
            $rules = [
                'tarehe' => 'required|date',
                'product_id' => 'required|exists:products,product_id',
                'alizeti_id' => 'required|exists:alizeti,ali_id',
                'quantity' => 'required|numeric|min:0.01',               
                'payment_id' => 'required|exists:payment_methods,payment_id',
                'discount' => 'nullable|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'sells_type' => 'required|in:jumla,rejareja',
                'customer_name' => 'nullable|string|max:255', 
                'phone' => 'nullable|string|max:20',
            ];

            if ($isDebtSale) {
                $rules['customer_name'] = 'required|string|max:255'; 
            }
            
            $validated = $request->validate($rules);

            $actualPriceRecord = Price::where('alizeti_id', $validated['alizeti_id'])
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$actualPriceRecord) {
                throw new \Exception('No price record found for the selected batch. Please ensure prices are set for this batch.');
            }

            $validatedPrice = 0;
            $stockField = '';

            if ($stockCategory === 'mafuta_masafi') {
                $validatedPrice = $actualPriceRecord->price_per_litre;
                $stockField = 'mafuta_masafi';
            } elseif ($stockCategory === 'mashudu') {
                $validatedPrice = $actualPriceRecord->price_of_mashudu;
                $stockField = 'mashudu';
            } elseif ($stockCategory === 'ugido') {
                $validatedPrice = $actualPriceRecord->price_of_ugido;
                $stockField = 'ugido';
            } elseif ($stockCategory === 'lami') {
                $validatedPrice = $actualPriceRecord->price_of_lami;
                $stockField = 'lami';
            } else {
                throw new \Exception('Internal error: Inferred stock category is invalid for price/stock determination. Category: "' . $stockCategory . '"');
            }

            if ($validatedPrice <= 0) {
                 throw new \Exception('Selected product has a zero or negative price. Please check price configuration in the prices table for category: ' . $stockCategory);
            }

            $alizetiRecord = Alizeti::find($validated['alizeti_id']);
            if (!$alizetiRecord) {
                throw new \Exception('Alizeti batch not found. Please ensure the selected batch exists.');
            }

            $stock = Stock::where('alizeti_id', $validated['alizeti_id'])->first();
            if (!$stock) {
                throw new \Exception('Stock record not found for this Alizeti batch. Please ensure stock is initialized for this batch.');
            }

            $stock->refresh();

            $availableStock = (float)($stock->$stockField ?? 0);
            $requestedQuantity = (float)$validated['quantity'];

            if ($availableStock < $requestedQuantity) {
                 Log::warning("Attempt to record sale with insufficient stock (soft check). Available: {$availableStock}, Requested: {$requestedQuantity}");
            }

            $totalPrice = ($requestedQuantity * $validatedPrice) - ($validated['discount'] ?? 0);
            if ($totalPrice < 0) {
                throw new \Exception('Total price cannot be negative. Please adjust quantity or discount.');
            }

            $paymentStatus = $isDebtSale ? 'not payed' : 'payed';

            $mauzo = Mauzo::create([
                'tarehe' => $validated['tarehe'],
                'quantity' => $requestedQuantity,
                'total_price' => $totalPrice,
                'payment_id' => $validated['payment_id'], 
                'discount' => $validated['discount'] ?? 0,
                'product_id' => $validated['product_id'],
                'alizeti_id' => $validated['alizeti_id'],
                'user_id' => Auth::id(),
                'prices_id' => $actualPriceRecord->prices_id,
                'price' => $validatedPrice,
                'sells_type' => $validated['sells_type'],
                'is_confirmed' => false, 
                'payment_status' => $paymentStatus,
                
            ]);

          
            if ($isDebtSale) {
                CustomerDebit::create([
                    'mauzo_id' => $mauzo->mauzo_id,
                    'total_amount' => $totalPrice,
                    'amount_paid' => 0,
                    'balance' => $totalPrice,
                    'customer_name' => $validated['customer_name'], 
                    'phone' => $validated['phone'] ?? null,         
                ]);
            }

            DB::commit();

            return redirect()->route('mauzo.index')->with([
                'success' => 'Sale recorded successfully as PENDING. Please confirm to deduct stock and finalize debits.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error recording sale: " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->route('mauzo.index')->with('error', 'Failed to record sale: ' . $e->getMessage());
        }
    }

    public function show(Mauzo $mauzo)
    {
        return view('mauzo.show', compact('mauzo'));
    }

    public function edit(Mauzo $mauzo)
    {
        if ($mauzo->is_confirmed) {
            return redirect()->route('mauzo.index')->with('error', 'Confirmed sales cannot be edited.');
        }

        $alizeti = Alizeti::all();
        $products = Product::all();
        $paymentMethods = PaymentMethod::all();

        $isDebitSale = $mauzo->paymentMethod && $mauzo->paymentMethod->name === 'Debits';

        
        $customerDebitDetails = null;
        if ($isDebitSale) {
            $customerDebitDetails = CustomerDebit::where('mauzo_id', $mauzo->mauzo_id)->first();
        }

        return view('mauzo.edit', compact('mauzo', 'alizeti', 'products', 'paymentMethods', 'isDebitSale', 'customerDebitDetails'));
    }

    public function update(Request $request, Mauzo $mauzo)
    {
        if ($mauzo->is_confirmed) {
            return redirect()->route('mauzo.index')->with('error', 'Confirmed sales cannot be updated.');
        }

        DB::beginTransaction();

        try {
            $selectedPaymentMethod = PaymentMethod::find($request->input('payment_id'));
            $isDebitSale = $selectedPaymentMethod && $selectedPaymentMethod->name === 'Debits';

            $product = Product::findOrFail($request->input('product_id'));
            $productNameLower = strtolower(trim($product->name));
            $stockCategory = '';

            if (str_contains($productNameLower, 'mafuta')) {
                $stockCategory = 'mafuta_masafi';
            } elseif (str_contains($productNameLower, 'mashudu')) {
                $stockCategory = 'mashudu';
            } elseif (str_contains($productNameLower, 'ugido')) {
                $stockCategory = 'ugido';
            } elseif (str_contains($productNameLower, 'lami')) {
                $stockCategory = 'lami';
            } else {
                throw new \Exception('Product name ("' . $product->name . '") does not correspond to a known stock category for price/stock determination. Please ensure product name contains "mafuta", "mashudu", "ugido", or "lami".');
            }

            $rules = [
                'tarehe' => 'required|date',
                'product_id' => 'required|exists:products,product_id',
                'alizeti_id' => 'required|exists:alizeti,ali_id',
                'quantity' => 'required|numeric|min:0.01',
                'payment_id' => 'required|exists:payment_methods,payment_id',
                'discount' => 'nullable|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'sells_type' => 'required|in:jumla,rejareja',
                'customer_name' => 'nullable|string|max:255', 
                'phone' => 'nullable|string|max:255',
            ];

            if ($isDebitSale) {
                $rules['customer_name'] = 'required|string|max:255'; 
            }

            $validated = $request->validate($rules);

            $actualPriceRecord = Price::where('alizeti_id', $validated['alizeti_id'])
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$actualPriceRecord) {
                throw new \Exception('No price record found for the selected batch. Please ensure prices are set for this batch.');
            }

            $validatedPrice = 0;
            if ($stockCategory === 'mafuta_masafi') {
                $validatedPrice = $actualPriceRecord->price_per_litre;
            } elseif ($stockCategory === 'mashudu') {
                $validatedPrice = $actualPriceRecord->price_of_mashudu;
            } elseif ($stockCategory === 'ugido') {
                $validatedPrice = $actualPriceRecord->price_of_ugido;
            } elseif ($stockCategory === 'lami') {
                $validatedPrice = $actualPriceRecord->price_of_lami;
            }

            if ($validatedPrice <= 0) {
                 throw new \Exception('Selected product has a zero or negative price. Please check price configuration in the prices table for category: ' . $stockCategory);
            }

            $requestedQuantity = (float)$validated['quantity'];
            $totalPrice = ($requestedQuantity * $validatedPrice) - ($validated['discount'] ?? 0);
            if ($totalPrice < 0) {
                throw new \Exception('Total price cannot be negative. Please adjust quantity or discount.');
            }

            $paymentStatus = $isDebitSale ? 'not payed' : 'payed';

           
            $mauzo->update([
                'tarehe' => $validated['tarehe'],
                'quantity' => $requestedQuantity,
                'total_price' => $totalPrice,
                'payment_id' => $validated['payment_id'],
                'discount' => $validated['discount'] ?? 0,
                'product_id' => $validated['product_id'],
                'alizeti_id' => $validated['alizeti_id'],
                'user_id' => Auth::id(),
                'prices_id' => $actualPriceRecord->prices_id,
                'price' => $validatedPrice,
                'sells_type' => $validated['sells_type'],
                'payment_status' => $paymentStatus,
                
            ]);

            
            if ($isDebitSale) {
                $customerDebit = CustomerDebit::where('mauzo_id', $mauzo->mauzo_id)->first();

                if ($customerDebit) {
                    
                    $customerDebit->update([
                        'total_amount' => $totalPrice,
                        'amount_paid' => 0, 
                        'balance' => $totalPrice, 
                        'customer_name' => $validated['customer_name'], 
                        'phone' => $validated['phone'] ?? null,         
                    ]);
                } else {
                    
                    CustomerDebit::create([
                        'mauzo_id' => $mauzo->mauzo_id,
                        'total_amount' => $totalPrice,
                        'amount_paid' => 0,
                        'balance' => $totalPrice,
                        'customer_name' => $validated['customer_name'],
                        'phone' => $validated['phone'] ?? null,
                    ]);
                }
            } else {
                
                $customerDebit = CustomerDebit::where('mauzo_id', $mauzo->mauzo_id)->first();
                if ($customerDebit) {
                    $customerDebit->delete();
                }
            }

            DB::commit();

            return redirect()->route('mauzo.index')->with('success', 'Sale record updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating sale (Mauzo ID: {$mauzo->mauzo_id}): " . $e->getMessage(), ['request_data' => $request->all()]);
            return redirect()->route('mauzo.index')->with('error', 'Failed to update sale: ' . $e->getMessage());
        }
    }

    
    public function destroy(Mauzo $mauzo)
    {
        if ($mauzo->is_confirmed) {
            return back()->with('error', 'Confirmed sales cannot be deleted.');
        }

        DB::beginTransaction();
        try {
            
            $customerDebit = CustomerDebit::where('mauzo_id', $mauzo->mauzo_id)->first();
            if ($customerDebit) {
                $customerDebit->delete();
            }

            $mauzo->delete();
            DB::commit();
            return back()->with('success', 'Sale record deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting sale (Mauzo ID: {$mauzo->mauzo_id}): " . $e->getMessage());
            return back()->with('error', 'Failed to delete sale: ' . $e->getMessage());
        }
    }

    public function confirm(Mauzo $mauzo)
    {
        DB::beginTransaction();

        try {
            if ($mauzo->is_confirmed) {
                DB::rollBack();
                return back()->with('warning', 'Sale is already confirmed.');
            }

            $product = Product::findOrFail($mauzo->product_id);
            $productNameLower = strtolower(trim($product->name));
            $stockCategory = '';

            if (str_contains($productNameLower, 'mafuta')) {
                $stockCategory = 'mafuta_masafi';
            } elseif (str_contains($productNameLower, 'mashudu')) {
                $stockCategory = 'mashudu';
            } elseif (str_contains($productNameLower, 'ugido')) {
                $stockCategory = 'ugido';
            } elseif (str_contains($productNameLower, 'lami')) {
                $stockCategory = 'lami';
            } else {
                throw new \Exception('Product name ("' . $product->name . '") does not correspond to a known stock category during confirmation.');
            }

            $stock = Stock::where('alizeti_id', $mauzo->alizeti_id)->first();
            if (!$stock) {
                throw new \Exception('Stock record not found for this Alizeti batch during confirmation. Cannot confirm sale.');
            }

            $stock->refresh();

            $stockField = '';
            if ($stockCategory === 'mafuta_masafi') { $stockField = 'mafuta_masafi'; }
            elseif ($stockCategory === 'mashudu') { $stockField = 'mashudu'; }
            elseif ($stockCategory === 'ugido') { $stockField = 'ugido'; }
            elseif ($stockCategory === 'lami') { $stockField = 'lami'; }

            $availableStock = (float)($stock->$stockField ?? 0);
            $requestedQuantity = (float)$mauzo->quantity;

            if ($availableStock < $requestedQuantity) {
                DB::rollBack();
                return back()->with('error', "Cannot confirm sale: Insufficient {$stockCategory} stock. Available: " . ($stock->$stockField ?? 0) . ". Requested: {$requestedQuantity}.");
            }

            
            $stock->$stockField -= $requestedQuantity;
            $stock->save();

            
            $paymentMethod = PaymentMethod::find($mauzo->payment_id);
            if ($paymentMethod && $paymentMethod->name === 'Debits') {
                
                $customerDebit = CustomerDebit::where('mauzo_id', $mauzo->mauzo_id)->first();

               
                if (!$customerDebit || empty($customerDebit->customer_name)) {
                    DB::rollBack();
                    throw new \Exception('Cannot confirm debit sale: Customer name is required but is missing from the associated debit record.');
                }
                
            } else {
                
                $customerDebit = CustomerDebit::where('mauzo_id', $mauzo->mauzo_id)->first();
                if ($customerDebit) {
                    $customerDebit->delete();
                }
            }

            
            $mauzo->is_confirmed = true;
            $mauzo->save();

            DB::commit();

            return back()->with('success', 'Sale confirmed successfully and stock updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error confirming sale (Mauzo ID: {$mauzo->mauzo_id}): " . $e->getMessage());
            return back()->with('error', 'Failed to confirm sale: ' . $e->getMessage());
        }
    }

    
    public function getPrice(Request $request, $alizetiId, $productId) {
        if (empty($alizetiId) || empty($productId)) {
           
            return response()->json(['error' => 'Missing alizeti_id or product_id parameters in URL route.'], 400);
        }

        try {
            
            $product = Product::findOrFail($productId);
            $productNameLower = strtolower(trim($product->name)); 

            $fetchedPrice = 0; 
            
            $actualPriceRecord = Price::where('alizeti_id', $alizetiId)->orderBy('created_at', 'desc') ->first();

            if (!$actualPriceRecord) {
                return response()->json(['price' => 0, 'error' => 'No price record found for the selected batch. Please ensure prices are set for this batch.']);
            }

            
            if (str_contains($productNameLower, 'mafuta')) {
                $fetchedPrice = $actualPriceRecord->price_per_litre;
            } elseif (str_contains($productNameLower, 'mashudu')) {
                $fetchedPrice = $actualPriceRecord->price_of_mashudu;
            } elseif (str_contains($productNameLower, 'ugido')) {
                $fetchedPrice = $actualPriceRecord->price_of_ugido;
            } elseif (str_contains($productNameLower, 'lami')) {
                $fetchedPrice = $actualPriceRecord->price_of_lami;
            } else {
                return response()->json(['price' => 0, 'error' => 'Product name does not correspond to a known price type for this batch.']);
            }
            
            return response()->json(['price' => (float)$fetchedPrice]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            
            return response()->json(['error' => 'Product not found for the given ID.', 'price' => 0], 404);
        } catch (\Exception $e) {
            Log::error("Error in getPrice API: " . $e->getMessage(), ['alizeti_id' => $alizetiId, 'product_id' => $productId]);
            return response()->json(['error' => 'An unexpected error occurred while fetching price.', 'price' => 0], 500);
        }
    }
}