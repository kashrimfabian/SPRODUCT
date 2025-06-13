@extends('layouts.appw')

@section('content')
{{-- Main container for centering and background --}}
<div class="container d-flex justify-content-center align-items-center min-vh-100 py-4">
    <div class="card shadow-lg rounded-3" style="max-width: 500px; width: 100%;">
        <div class="card-header bg-primary text-white text-center py-3 rounded-top">
            <h4 class="mb-0">Edit Customer Debit Payment</h4>
        </div>
        <div class="card-body p-4">

            {{-- Success/Error Messages --}}
            @if ($errors->any())
            <div class="alert alert-danger rounded-3" role="alert">
                <h5 class="alert-heading">Whoops!</h5>
                <p>There were some problems with your input:</p>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger rounded-3" role="alert">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('customer_debit_payments.update', $customer_debit_payment->debitpay_id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Method spoofing for PUT request --}}

                <div class="mb-3">
                    <label for="debit_id" class="form-label">Customer Debit</label>
                    <select name="debit_id" id="debit_id" class="form-select rounded-pill @error('debit_id') is-invalid @enderror" required>
                        <option value="">Select Customer</option>
                        @foreach($debits as $debit)
                            <option value="{{ $debit->debit_id }}"
                                data-balance="{{ $debit->balance }}" {{-- Added data-balance attribute --}}
                                {{ old('debit_id', $customer_debit_payment->debit_id) == $debit->debit_id ? 'selected' : '' }}>
                                {{ $debit->customer_name }} - Balance: {{ number_format($debit->balance, 2) }} TZS
                            </option>
                        @endforeach
                    </select>
                    @error('debit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- New field for displaying remaining balance --}}
                <div class="mb-3">
                    <label for="remaining_balance" class="form-label">Remaining Debit Balance (TZS)</label>
                    <input type="text" id="remaining_balance" class="form-control rounded-pill" readonly value="0.00">
                </div>

                <div class="mb-3">
                    <label for="payment_date" class="form-label">Payment Date</label>
                    <input type="date" name="payment_date" id="payment_date" class="form-control rounded-pill @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $customer_debit_payment->payment_date) }}" required>
                    @error('payment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (TZS)</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control rounded-pill @error('amount') is-invalid @enderror" value="{{ old('amount', $customer_debit_payment->amount) }}" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea name="notes" id="notes" class="form-control rounded-3 @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $customer_debit_payment->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-success btn-lg flex-grow-1 rounded-pill">
                        <i class="fas fa-save me-2"></i> Update Payment
                    </button>
                    <a href="{{ route('customer_debit_payments.index') }}" class="btn btn-secondary btn-lg flex-grow-1 rounded-pill">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- jQuery for Bootstrap's JS functionality and custom script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const $debitSelect = $('#debit_id');
        const $remainingBalanceInput = $('#remaining_balance');

        function updateRemainingBalance() {
            const selectedOption = $debitSelect.find('option:selected');
            const balance = selectedOption.data('balance'); // Get the balance from data-balance attribute

            if (balance !== undefined) {
                $remainingBalanceInput.val(parseFloat(balance).toFixed(2));
            } else {
                $remainingBalanceInput.val('0.00'); // Reset if no option or balance is found
            }
        }

        // Call on page load to set initial balance based on the pre-selected debit
        updateRemainingBalance();

        // Call whenever the select box value changes
        $debitSelect.on('change', updateRemainingBalance);
    });
</script>
@endsection