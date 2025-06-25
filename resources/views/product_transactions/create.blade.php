@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Record New Product Transaction</h4>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-header">New Transaction Record</div>
                <div class="card-body">
                    <form action="{{ route('product_transactions.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="cust_ali_id" class="form-label">Customer Alizeti Batch (Source of Product)</label>
                            <select class="form-select" id="cust_ali_id" name="cust_ali_id" required>
                                <option value="">Select a Customer Batch with Available Stock</option>
                                @foreach($availableCustAlizetiBatches as $batch)
                                <option value="{{ $batch->cust_ali_id }}" 
                                    {{ old('cust_ali_id') == $batch->cust_ali_id ? 'selected' : '' }}
                                    data-refined-oil-available="{{ number_format($batch->customerStock->refined_oil ?? 0, 2) }}"
                                    data-mashudu-available="{{ number_format($batch->customerStock->mashudu_kg ?? 0, 2) }}"
                                    data-ugido-available="{{ number_format($batch->customerStock->ugido_kg ?? 0, 2) }}"
                                    data-lami-available="{{ number_format($batch->customerStock->lami_kg ?? 0, 2) }}" 
                                >
                                    {{ $batch->batch_no }} (Customer: {{ $batch->customer->first_name ?? '' }} {{ $batch->customer->last_name ?? '' }})
                                    - Refined Oil: {{ number_format($batch->customerStock->refined_oil ?? 0, 2) }} Ltr
                                    - Mashudu: {{ number_format($batch->customerStock->mashudu_kg ?? 0, 2) }} kg
                                    - Ugido: {{ number_format($batch->customerStock->ugido_kg ?? 0, 2) }} kg
                                    - Lami: {{ number_format($batch->customerStock->lami_kg ?? 0, 2) }} kg 
                                </option>
                                @endforeach
                            </select>
                            <div id="selected_batch_stock_info" class="form-text text-muted mt-2">
                                Select a batch to see its current stock levels.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Recorded By</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', Auth::id()) == $user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tarehe" class="form-label">Transaction Date</label> {{-- Updated --}}
                            <input type="date" class="form-control datepicker" id="tarehe" name="tarehe" {{-- Updated --}}
                                value="{{ old('tarehe', date('Y-m-d')) }}" required> {{-- Updated --}}
                        </div>

                        <div class="mb-3">
                            <label for="trans_type" class="form-label">Transaction Type</label> {{-- Updated --}}
                            <select class="form-select" id="trans_type" name="trans_type" required> {{-- Updated --}}
                                <option value="">Select Type</option>
                                <option value="collection" {{ old('trans_type') == 'collection' ? 'selected' : '' }}>Customer Collection</option> {{-- Updated --}}
                                <option value="sale" {{ old('trans_type') == 'sale' ? 'selected' : '' }}>Factory Sale (on customer's behalf)</option> {{-- Updated --}}
                            </select>
                        </div>

                        <hr>
                        <h5>Product Details</h5>
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product Name</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Select a Product</option>
                                @foreach($products as $product)
                                <option value="{{ $product->product_id }}" {{-- Using product_id as value --}}
                                    {{ old('product_id') == $product->product_id ? 'selected' : '' }}
                                    data-unit-of-measure="{{ $product->unit_of_measure }}"
                                    data-standard-price="{{ number_format($product->standard_price, 2) }}"
                                    data-product-name="{{ $product->name }}"
                                >
                                    {{ $product->name }}
                                </option>
                                @endforeach
                            </select>
                            <div id="product_info" class="form-text text-muted mt-2">
                                Select a product to see its unit and standard price.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity Transacted (<span id="quantity_unit">Units</span>)</label>
                            <input type="number" step="0.01" class="form-control" id="quantity" name="quantity"
                                value="{{ old('quantity', 0) }}" min="0.01" required>
                            <div class="form-text text-muted" id="quantity_price_info">
                                Standard Price: <span id="standard_price_display">0.00</span> TZS/<span id="quantity_unit_2">Units</span>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (TZS)</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                value="{{ old('amount') }}" min="0">
                            <div class="form-text text-muted" id="amount_text_info">
                                For 'Collection': This is an optional service fee paid by customer. <br>
                                For 'Sale': This is the required amount received from the buyer.
                            </div>
                        </div>

                        <div class="mb-3" id="buyer_name_field" style="display: none;">
                            <label for="buyer_name" class="form-label">Buyer Name (For Sales)</label>
                            <input type="text" class="form-control" id="buyer_name" name="buyer_name"
                                value="{{ old('buyer_name') }}" maxlength="255">
                            <div class="form-text text-muted">Name of the entity buying the product (only for 'Sale' transactions).</div>
                        </div>

                        <button type="submit" class="btn btn-success">Record Transaction</button>
                        <a href="{{ route('product_transactions.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const custAliIdSelect = document.getElementById('cust_ali_id');
        const selectedBatchStockInfo = document.getElementById('selected_batch_stock_info');
        const transactionTypeSelect = document.getElementById('trans_type'); // Updated ID
        const amountField = document.getElementById('amount');
        const amountTextInfo = document.getElementById('amount_text_info');
        const buyerNameField = document.getElementById('buyer_name_field');        
        const productIdSelect = document.getElementById('product_id');
        const quantityUnitSpan = document.getElementById('quantity_unit');
        const quantityUnitSpan2 = document.getElementById('quantity_unit_2');
        const standardPriceDisplay = document.getElementById('standard_price_display');
        const productInfoDiv = document.getElementById('product_info');
       
        const stockDisplayMap = {
            'Refined Oil': 'data-refined-oil-available',
            'Mashudu': 'data-mashudu-available',
            'Ugido': 'data-ugido-available',
            'Lami': 'data-lami-available'
        };


        function updateSelectedBatchStockInfo() {
            const selectedBatchOption = custAliIdSelect.options[custAliIdSelect.selectedIndex];
            
            const refinedOilAvailable = selectedBatchOption.getAttribute(stockDisplayMap['Refined Oil']);
            const mashuduAvailable = selectedBatchOption.getAttribute(stockDisplayMap['Mashudu']);
            const ugidoAvailable = selectedBatchOption.getAttribute(stockDisplayMap['Ugido']);
            const lamiAvailable = selectedBatchOption.getAttribute(stockDisplayMap['Lami']);
            
            if (refinedOilAvailable || mashuduAvailable || ugidoAvailable || lamiAvailable) {
                selectedBatchStockInfo.innerHTML = `
                    Current Stock for this batch: <br>
                    Refined Oil: <strong>${refinedOilAvailable} Ltr</strong> <br>
                    Mashudu: <strong>${mashuduAvailable} kg</strong> <br>
                    Ugido: <strong>${ugidoAvailable} kg</strong> <br>
                    Lami: <strong>${lamiAvailable} kg</strong>
                `;
            } else {
                selectedBatchStockInfo.textContent = `Select a batch to see its current stock levels.`;
            }
        }

        function toggleTransactionFields() {
            if (transactionTypeSelect.value === 'sale') {
                amountTextInfo.innerHTML = 'For \'Sale\': This is the required amount received from the buyer.';
                buyerNameField.style.display = 'block';
                amountField.setAttribute('required', 'required'); 
            } else if (transactionTypeSelect.value === 'collection') {
                amountTextInfo.innerHTML = 'For \'Collection\': This is an optional service fee paid by customer.';
                buyerNameField.style.display = 'none';
                amountField.removeAttribute('required'); 
                document.getElementById('buyer_name').value = ''; 
            } else {
                amountTextInfo.innerHTML = 'Select a transaction type.';
                buyerNameField.style.display = 'none';
                amountField.removeAttribute('required');
                document.getElementById('buyer_name').value = ''; 
            }
        }

        function updateProductInfo() {
            const selectedProductOption = productIdSelect.options[productIdSelect.selectedIndex];
            const unitOfMeasure = selectedProductOption.getAttribute('data-unit-of-measure');
            const standardPrice = selectedProductOption.getAttribute('data-standard-price');
            const productName = selectedProductOption.getAttribute('data-product-name');

            if (unitOfMeasure && standardPrice && productName) {
                quantityUnitSpan.textContent = unitOfMeasure;
                quantityUnitSpan2.textContent = unitOfMeasure;
                standardPriceDisplay.textContent = standardPrice;
                productInfoDiv.innerHTML = `You selected: <strong>${productName}</strong>. Unit: <strong>${unitOfMeasure}</strong>. Standard Price: <strong>${standardPrice} TZS/${unitOfMeasure}</strong>.`;
            } else {
                quantityUnitSpan.textContent = 'Units';
                quantityUnitSpan2.textContent = 'Units';
                standardPriceDisplay.textContent = '0.00';
                productInfoDiv.textContent = 'Select a product to see its unit and standard price.';
            }
        }

        custAliIdSelect.addEventListener('change', updateSelectedBatchStockInfo);
        transactionTypeSelect.addEventListener('change', toggleTransactionFields);
        productIdSelect.addEventListener('change', updateProductInfo);

       
        updateSelectedBatchStockInfo(); 
        toggleTransactionFields(); 
        updateProductInfo();
    });
</script>
@endsection