@extends('layouts.appw')

@section('content')
<div class="container">
    <h2>Create Shudu Sale</h2>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
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

    <form action="{{ route('mashudu.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="tarehe">Date</label>
            <input type="text" name="tarehe" id="tarehe" class="form-control datepicker" placeholder="Select a date"
                required value="{{ old('tarehe') }}">
        </div>

        <div class="mb-3">
            <label for="alizeti_id">Select Batch Number</label>
            <select id="alizeti_id" name="alizeti_id" class="form-control" required>
                <option value="">-- Select Batch --</option>
                @foreach($alizeti as $batch)
                <option value="{{ $batch->alizeti_id }}" data-shudu="{{ $batch->shudu }}"
                    data-price-of-mashudu="{{ $batch->price_of_mashudu }}">
                    {{ $batch->batch_no }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="shudu">Available Shudu (kg)</label>
            <input type="text" id="shudu" class="form-control" readonly value="-- Select Batch --">
        </div>

        <div class="mb-3">
            <label for="price_of_mashudu">Price of Shudu (TZS/kg)</label>
            <input type="text" id="price_of_mashudu" class="form-control" readonly value="-- Select Batch --">
        </div>

        <div class="form-group">
            <label for="mashudu">Shudu (kg)</label>
            <input type="number" step="any"  name="mashudu" id="mashudu" class="form-control" required min="0"
                value="{{ old('mashudu') }}">
        </div>
        
        <div class="form-group">
            <label for="discount">Discount</label>
            <input type="number" step="any"  name="discount" id="discount" class="form-control" min="0" step="0.01"
                value="{{ old('discount', 0) }}">
        </div>

        <div class="form-group">
            <label for="payment_way">Payment Way</label>
            <select name="payment_way" id="payment_way" class="form-control">
                <option value="">-- Select Payment Way --</option>
                <option value="cash" {{ old('payment_way') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="Lipa_Namba" {{ old('payment_way') == 'Lipa_Namba' ? 'selected' : '' }}>Lipa Namba</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save Sale</button>
        <a href="{{ route('mashudu.index') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alizetiSelect = document.getElementById('alizeti_id');
    const shuduInput = document.getElementById('shudu');
    const priceInput = document.getElementById('price_of_mashudu');
    const mashuduInput = document.getElementById('mashudu');
    const discountInput = document.getElementById('discount');

    function calculateTotalPrice() {
        const price = parseFloat(priceInput.value) || 0;
        const mashudu = parseFloat(mashuduInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const totalPrice = (price * mashudu) - discount;
    }

    alizetiSelect.addEventListener('change', function () {
        const selectedOption = alizetiSelect.options[alizetiSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const availableShudu = selectedOption.getAttribute('data-shudu');
            const priceOfMashuduStr = selectedOption.getAttribute('data-price-of-mashudu');
            const priceOfMashudu = parseFloat(priceOfMashuduStr);

            if (!isNaN(priceOfMashudu)) {
                priceInput.value = priceOfMashudu + " TZS/kg";
            } else {
                priceInput.value = "Invalid Price";
            }

            shuduInput.value = availableShudu + " kg";
            calculateTotalPrice();
        } else {
            shuduInput.value = '-- Select Batch --';
            priceInput.value = '-- Select Batch --';
        }
    });

    mashuduInput.addEventListener('input', calculateTotalPrice);
    discountInput.addEventListener('input', calculateTotalPrice);

    // Initialize display on page load
    if (alizetiSelect.value) {
        alizetiSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection