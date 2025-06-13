@extends('layouts.appw')

@section('content')
<div class="container">


    <h4 class="mb-0">Customer Debits</h4>
    <a href="{{ route('customer_debits.create') }}" class="btn btn-success btn-sm mb-3">
        <i class="fas fa-plus me-2"></i> Add New Debit
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
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th scope="col">Customer Name</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Total Debt</th>
                    <th scope="col">Amount Paid</th>
                    <th scope="col">Balance</th>
                    <th scope="col">Debt Status</th> {{-- New Column --}}
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debits as $debit)
                <tr>
                    <td>{{ $debit->customer_name }}</td>
                    <td>{{ $debit->phone }}</td>
                    <td>{{ number_format($debit->total_amount, 2) }}</td>
                    <td>{{ number_format($debit->amount_paid, 2) }}</td>
                    <td>{{ number_format($debit->balance, 2) }}</td>
                    <td>
                        @if($debit->debt_status === 'payed')
                        <span class="badge bg-success rounded-pill px-3 py-2">Paid</span>
                        @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Not Paid</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-center align-items-center gap-2">
                        <a href="{{ route('customer_debits.edit', $debit->debit_id) }}"
                            class="btn btn-sm btn-warning rounded-pill">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">No customer debit records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection