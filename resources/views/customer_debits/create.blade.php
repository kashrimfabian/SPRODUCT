@extends('layouts.appw')

@section('content')
<div class="container card p-4">
    <h3>Add Customer Debit</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('customer_debits.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Mauzo ID</label>
            <select name="mauzo_id" class="form-control" required>
                <option value="">-- Select Mauzo --</option>
                @foreach($mauzo as $m)
                    <option value="{{ $m->mauzo_id }}">{{ $m->mauzo_id }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Customer Name</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label>Total Amount</label>
            <input type="number" step="0.01" name="total_amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Amount Paid</label>
            <input type="number" step="0.01" name="amount_paid" class="form-control" required>
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('customer_debits.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
