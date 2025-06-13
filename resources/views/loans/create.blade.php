@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">

            <h4 class="card-title text-center mb-4"> Add New Loan</h4>

            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Something went wrong:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('loans.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="lender_name" class="form-label">Lender Name</label>
                        <input type="text" class="form-control" id="lender_name" name="lender_name"
                            value="{{ old('lender_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="loan_date" class="form-label">Loan Date</label>
                        <input type="date" name="loan_date" class="form-control datepicker" placeholder="select date"
                            value="{{ old('loan_date') }}" required>

                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Loan Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                            value="{{ old('amount') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control datepicker" placeholder="select date"
                            value="{{ old('due_date') }}">
                    </div>

                    <div class="mb-3">
                        <label for="interest_rate" class="form-label">Interest Rate (%)</label>
                        <input type="number" step="0.01" class="form-control" id="interest_rate" name="interest_rate"
                            value="{{ old('interest_rate', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success mt-3">Save Loan</button>
                    <a href="{{ route('loans.index') }}" class="btn btn-secondary mt-3">Cancel</a>


                </form>
            </div>
        </div>
    </div>
</div>
@endsection