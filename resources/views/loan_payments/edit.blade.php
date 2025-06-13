@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4"> Edit Loan Payment</h4>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('loan_payments.update', $loanPayment->loanPy_id) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label for="loan_id" class="form-label">Lender Name</label>
                    <select name="loan_id" class="form-control" required>
                        @foreach($loans as $loan)
                        <option value="{{ $loan->loan_id }}"
                            {{ $loanPayment->loan_id == $loan->loan_id ? 'selected' : '' }}>
                            {{ $loan->lender_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_date" class="form-label">Payment Date</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ $loanPayment->payment_date }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" name="amount" step="0.01" class="form-control"
                        value="{{ $loanPayment->amount }}" required>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" class="form-control">{{ $loanPayment->notes }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('loan_payments.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection