@extends('layouts.appw')

@section('content')
<div class="container ">

    <div class="card-body">
        <h4 class="mb-4 text-center">Add Loan Payment</h4>


        @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <h5 class="alert-heading">Whoops!</h5>
            <p>There were some problems with your input:</p>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('loan_payments.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="loan_id" class="form-label">Lender Name</label>
                <select name="loan_id" id="loan_id" class="form-control" required>
                    <option value="">-- Select lender_name --</option>
                    @foreach($loans as $loan)
                    <option value="{{ $loan->loan_id }}" data-balance="{{ $loan->remaining_balance }}"
                        {{ old('loan_id') == $loan->loan_id ? 'selected' : '' }}>
                        {{ $loan->lender_name }} - Remaining: {{ number_format($loan->amount, 2) }} TZS
                    </option>
                    @endforeach
                </select>
                @error('loan_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" name="payment_date" id="payment_date" class="form-control datepicker"
                    placeholder="select date" value="{{ old('payment_date')}}" required>
                @error('payment_date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="amount" class="form-label">Amount (TZS)</label>
                <input type="number" name="amount" step="0.01" id="amount" class="form-control"
                    value="{{ old('amount') }}" required>
                @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="notes" class="form-label">Notes (Optional)</label>
                <textarea name="notes" id="notes" class="form-control ">{{ old('notes') }}</textarea>
                @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <button type="submit" class="btn btn-success ">
                    <i class="fas fa-save "></i> Save Payment
                </button>
                <a href="{{ route('loan_payments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left "></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>



<script>
$(document).ready(function() {
    const $loanSelect = $('#loan_id');
    const $remainingBalanceDisplay = $('#remaining_balance_display');

    function updateLoanBalance() {
        const selectedOption = $loanSelect.find('option:selected');
        const balance = selectedOption.data('balance'); 

        if (balance !== undefined) {
            $remainingBalanceDisplay.val(parseFloat(balance).toFixed(2));
        } else {
            $remainingBalanceDisplay.val('0.00');
        }
    }

    
    updateLoanBalance();

    
    $loanSelect.on('change', updateLoanBalance);
});
</script>
@endsection