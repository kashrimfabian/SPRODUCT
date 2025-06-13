@extends('layouts.appw')

@section('content')
<div class="container">
    
    <h4 class="mb-0">Customer Debit Payments</h4>

    <a href="{{ route('customer_debit_payments.create') }}" class="btn btn-success mb-3">
        <i class="fas fa-plus"></i> Add Payment
    </a>



    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped  table-bordered ">
            <thead class="table-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Payment Date</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Notes</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->debitpay_id }}</td>
                    <td>{{ $payment->customerDebit->customer_name ?? 'N/A' }}</td>
                    <td>{{ $payment->payment_date }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td> {{-- Format amount to 2 decimal places --}}
                    <td>{{ $payment->notes }}</td>
                    <td class="d-flex justify-content-center align-items-center gap-2">
                        <a href="{{ route('customer_debit_payments.edit', $payment->debitpay_id) }}"
                            class="btn btn-sm btn-warning rounded-pill">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No customer debit payments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection