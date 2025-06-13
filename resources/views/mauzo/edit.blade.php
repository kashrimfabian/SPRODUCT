@extends('layouts.appw')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Edit Mauzo Record</h4>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('mauzo.update', $mauzo->mauzo_id) }}" method="POST" id="mauzoEditForm">
                @csrf
                @method('PUT') {{-- Use PUT method for updates --}}

                @php
                    $isDisabled = $mauzo->is_confirmed;
                @endphp

                <div class="mb-3">
                    <label for="tarehe" class="form-label">Date</label>
                    <input type="date" name="tarehe" id="tarehe" class="form-control datepicker" required
                        value="{{ old('tarehe', $mauzo->tarehe) }}"
                        {{ $isDisabled ? 'readonly' : '' }}>
                </div>

                <div class="mb-3">
                    <label for="alizeti_id" class="form-label">Select Batch Number</label>
                    <select id="alizeti_id" name="alizeti_id" class="form-select" required
                        {{ $isDisabled ? 'disabled' : '' }}>
                        <option value="">-- Select Batch --</option>
                        @foreach($alizeti as $batch)
                        <option value="{{ $batch->ali_id }}"
                            data-available-mafuta="{{ $batch->stock ? $batch->stock->mafuta_masafi : 0 }}"
                            data-available-mashudu="{{ $batch->stock ? $batch->stock->mashudu : 0 }}"
                            data-available-ugido="{{ $batch->stock ? $batch->stock->ugido : 0 }}"
                            data-available-lami="{{ $batch->stock ? $batch->stock->lami : 0 }}"
                            {{ old('alizeti_id', $mauzo->alizeti_id) == $batch->ali_id ? 'selected' : '' }}>
                            {{ $batch->batch_no }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="product_id" class="form-label">Product Type</label>
                    <select id="product_id" name="product_id" class="form-select" required
                        {{ $isDisabled ? 'disabled' : '' }}>
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                        
                        <option value="{{ $product->product_id }}"
                            {{ old('product_id', $mauzo->product_id) == $product->product_id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price_display" class="form-label">Unit Price</label>
                    <input type="text" id="price_display" class="form-control" readonly
                        value="{{ old('price', number_format($mauzo->price, 2)) }}">
                    <input type="hidden" id="price" name="price" value="{{ old('price', $mauzo->price) }}">
                </div>

                <div class="mb-3">
                    <label for="available_quantity" class="form-label">Available Quantity (Lts/Kgs)</label>
                    <input type="text" id="available_quantity" class="form-control" readonly value="0">
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity (Lts/Kgs)</label>
                    <input type="number" step="any" id="quantity" name="quantity" class="form-control" required
                        value="{{ old('quantity', $mauzo->quantity) }}"
                        {{ $isDisabled ? 'readonly' : '' }}>
                </div>

                <div class="mb-3">
                    <label for="discount" class="form-label">Discount (TZS)</label>
                    <input type="number" name="discount" id="discount" class="form-control" step="0.01"
                        value="{{ old('discount', $mauzo->discount) }}"
                        {{ $isDisabled ? 'readonly' : '' }}>
                </div>

                <div class="mb-3">
                    <label for="total_price_display" class="form-label">Total Price</label>
                    <div class="border p-2">
                        <span id="total_price_display">{{ number_format($mauzo->total_price, 2) }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="sells_type" class="form-label">Sales Category (Jumla/Rejareja)</label>
                    <select name="sells_type" id="sells_type" class="form-select" required
                        {{ $isDisabled ? 'disabled' : '' }}>
                        <option value="jumla" {{ old('sells_type', $mauzo->sells_type) == 'jumla' ? 'selected' : '' }}>jumla</option>
                        <option value="rejareja" {{ old('sells_type', $mauzo->sells_type) == 'rejareja' ? 'selected' : '' }}>rejareja
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_id" class="form-label">Payment Method</label>
                    <select name="payment_id" id="payment_id" class="form-select" required
                        {{ $isDisabled ? 'disabled' : '' }}>
                        <option value="" disabled>-- Select Payment Method --</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->payment_id }}" data-name="{{ $method->name }}"
                                {{ old('payment_id', $mauzo->payment_id) == $method->payment_id ? 'selected' : '' }}>
                                {{ $method->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div id="debitDetails" style="display: none;">
                    <input type="hidden" name="customer_name" id="customer_name" 
                        value="{{ old('customer_name', $customerDebitDetails->customer_name ?? '') }}">
                    <input type="hidden" name="phone" id="phone" 
                        value="{{ old('phone', $customerDebitDetails->phone ?? '') }}">
                    <span id="debit_amount_display" style="display: none;">0.00</span>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-success mt-3"
                        {{ $isDisabled ? 'disabled' : '' }}>
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('mauzo.index') }}" class="btn btn-secondary mt-3"><i
                            class="fas fa-arrow-left"></i> Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SweetAlert2 CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function() {
    const $alizeti = $('#alizeti_id');
    const $productId = $('#product_id');
    const $priceDisplay = $('#price_display');
    const $price = $('#price');
    const $quantity = $('#quantity');
    const $discount = $('#discount');
    const $available = $('#available_quantity');
    const $totalPriceDisplay = $('#total_price_display');
    const $paymentMethodSelect = $('#payment_id');
    
    const $customerName = $('#customer_name'); 
    const $customerPhone = $('#phone');
    const $mauzoEditForm = $('#mauzoEditForm');

    const isConfirmedSale = {{ $mauzo->is_confirmed ? 'true' : 'false' }};

    const productCategoryMap = {
        'mafuta': 'mafuta_masafi',
        'mashudu': 'mashudu',
        'ugido': 'ugido',
        'lami': 'lami'
    };

    function getStockCategoryFromProductName(productName) {
        const lowerCaseName = productName.toLowerCase();
        for (const key in productCategoryMap) {
            if (lowerCaseName.includes(key)) {
                return productCategoryMap[key];
            }
        }
        return null;
    }

    function updateFields() {
        console.log('--- updateFields triggered (Edit) ---');
        const selectedAlizetiOption = $alizeti.find('option:selected');
        const selectedProductOption = $productId.find('option:selected');
        
        const alizetiId = selectedAlizetiOption.val();
        const productId = selectedProductOption.val();
        const productName = selectedProductOption.text().trim();
        const qty = parseFloat($quantity.val()) || 0;
        const disc = parseFloat($discount.val()) || 0;

        console.log('Alizeti ID (Edit):', alizetiId);
        console.log('Product ID (Edit):', productId);
        console.log('Product Name (Edit):', productName);
        console.log('Quantity (Edit):', qty);
        console.log('Discount (Edit):', disc);

        let availableQuantity = 0;
        const stockCategory = getStockCategoryFromProductName(productName);

        if (stockCategory) {
            if (stockCategory === 'mafuta_masafi') {
                 availableQuantity = parseFloat(selectedAlizetiOption.data('available-mafuta')) || 0;
            } else {
                 availableQuantity = parseFloat(selectedAlizetiOption.data('available-' + stockCategory)) || 0;
            }
        }
        $available.val(availableQuantity.toFixed(2)); 
        console.log('Available Quantity (Edit):', availableQuantity);

        if (alizetiId && productId && alizetiId !== "" && productId !== "") {
            
            const ajaxUrl = `/get-price/${alizetiId}/${productId}`;
            console.log('Making AJAX call to ' + ajaxUrl + ' (Edit)');
            $.ajax({
                url: ajaxUrl, 
                type: 'GET',
                success: function(response) {
                    let fetchedPrice = parseFloat(response.price) || 0;
                    console.log('AJAX Success (Edit)! Fetched Price:', fetchedPrice);
                    $priceDisplay.val(fetchedPrice.toFixed(2));
                    $price.val(fetchedPrice);
                    calculateTotalPrice(qty, fetchedPrice, disc);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error fetching price (Edit):', status, error, xhr.responseText);
                    $priceDisplay.val('Error');
                    $price.val(0);
                    calculateTotalPrice(qty, 0, disc);
                    showCustomAlert('Error fetching price. Please check batch and product selection.');
                }
            });
        } else {
            console.log('Missing Alizeti ID or Product ID, or empty selection. Not making AJAX call (Edit). Resetting prices.');
            $priceDisplay.val('0.00');
            $price.val(0);
            $available.val('0.00');
            calculateTotalPrice(qty, 0, disc);
        }
    }

    function calculateTotalPrice(qty, price, disc) {
        let subtotal = qty * price;
        let finalPrice = subtotal - disc;
        console.log('Calculating Total Price (Edit): Quantity=' + qty + ', Price=' + price + ', Discount=' + disc + ' -> Final Price=' + finalPrice.toFixed(2));
        $totalPriceDisplay.text(finalPrice.toFixed(2));
    }

    function showDebitDetailsPopup() {
        const currentTotalPrice = parseFloat($totalPriceDisplay.text()) || 0;
        const initialCustomerName = $customerName.val();
        const initialPhone = $customerPhone.val();

        Swal.fire({
            title: 'Debit Customer Details',
            html: `
                <div class="swal2-content text-start">
                    <div class="mb-3">
                        <label class="form-label">Debit Amount</label>
                        <div class="border p-2 bg-light">
                            <span id="swal_debit_amount_display">${currentTotalPrice.toFixed(2)}</span> TZS
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="swal_customer_name" class="form-label">Customer Name</label>
                        <input id="swal_customer_name" class="swal2-input form-control" placeholder="Customer Name" value="${initialCustomerName}">
                    </div>
                    <div class="mb-3">
                        <label for="swal_phone" class="form-label">Customer Phone (Optional)</label>
                        <input id="swal_phone" class="swal2-input form-control" placeholder="Phone Number" value="${initialPhone}">
                    </div>
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Save Details',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false, 
            allowEscapeKey: false, 
            didOpen: () => {
                const customerNameInput = Swal.getPopup().querySelector('#swal_customer_name');
                if (customerNameInput) {
                    customerNameInput.focus();
                }
            },
            preConfirm: () => {
                const swalCustomerName = Swal.getPopup().querySelector('#swal_customer_name').value;
                const swalPhone = Swal.getPopup().querySelector('#swal_phone').value;

                if (!swalCustomerName.trim()) {
                    Swal.showValidationMessage('Customer Name is required for debit sales.');
                    return false;
                }

                return { customerName: swalCustomerName, phone: swalPhone };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $customerName.val(result.value.customerName).prop('required', true);
                $customerPhone.val(result.value.phone).prop('required', false);
                
                Swal.fire({
                    title: 'Details Saved!',
                    text: 'Debit customer information has been recorded.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

            } else if (result.dismiss === Swal.DismissReason.cancel || result.dismiss === Swal.DismissReason.backdrop || result.dismiss === Swal.DismissReason.esc) {
                $paymentMethodSelect.val('');
                $customerName.val('').prop('required', false);
                $customerPhone.val('').prop('required', false);
                Swal.fire('Operation Cancelled', '', 'info');
            }
        });
    }

    $paymentMethodSelect.on('change', function() {
        const selectedPaymentMethodName = $(this).find('option:selected').data('name');
        
        if (selectedPaymentMethodName === 'Debits' && !isConfirmedSale) {
            showDebitDetailsPopup();
        } else if (selectedPaymentMethodName !== 'Debits' && !isConfirmedSale) {
            $customerName.val('').prop('required', false);
            $customerPhone.val('').prop('required', false);
        }
    });

    $alizeti.on('change', updateFields);
    $productId.on('change', updateFields);
    $quantity.on('input', function() {
        calculateTotalPrice(parseFloat($(this).val()) || 0, parseFloat($price.val()) || 0, parseFloat(
            $discount.val()) || 0);
    });
    $discount.on('input change', function() {
        calculateTotalPrice(parseFloat($quantity.val()) || 0, parseFloat($price.val()) || 0, parseFloat(
            $(this).val()) || 0);
    });
    
    
    updateFields();

    const initialPaymentMethodName = $paymentMethodSelect.find('option:selected').data('name');
    if (initialPaymentMethodName === 'Debits') {
        $('#debitDetails').show();
        if (!isConfirmedSale) {
            $customerName.prop('required', true);
            if (!$customerName.val().trim()) {
                showDebitDetailsPopup();
            }
        }
    } else {
        $('#debitDetails').hide();
    }

    function showCustomAlert(message) {
        const alertContainer = document.querySelector('.container');
        if (alertContainer) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            alertContainer.prepend(alertDiv);
            setTimeout(() => alertDiv.remove(), 5000);
        } else {
            console.warn("Custom Alert:", message);
        }
    }
});
</script>

@endsection