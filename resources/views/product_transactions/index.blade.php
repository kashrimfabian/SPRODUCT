@extends('layouts.appw') 

@section('content')
<div class="container">
    
    <div class="row justify-content-center">
        <h4 class="text-center text-dark text-uppercase mb-4">
        Product Transactions (Collections & Sales) records
        </h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('product_transactions.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Record New Transaction
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Transaction Type</th>                    
                    <th>Customer Batch</th>
                    <th>Recorded By</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Amount (TZS)</th>
                    <th>Buyer Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productTransactions as $transaction)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $transaction->tarehe }}</td>
                    <td>{{ ucfirst($transaction->trans_type) }}</td>                    
                    <td>{{ $transaction->custAlizeti->batch_no ?? 'N/A' }} ({{ $transaction->custAlizeti->customer->first_name ?? 'N/A' }})</td>
                    <td>{{ $transaction->user->first_name ?? 'N/A' }} {{ $transaction->user->last_name ?? '' }}</td>
                    <td>{{ $transaction->product->name ?? 'N/A' }}</td>
                    <td>{{ number_format($transaction->quantity, 2) }} {{ $transaction->product->unit_of_measure ?? '' }}</td>
                    <td>{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->buyer_name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge 
                            @if($transaction->status == 'pending') bg-warning
                            @elseif($transaction->status == 'confirmed') bg-success
                            @endif
                        ">{{ ucfirst($transaction->status) }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('product_transactions.show', $transaction->trans_id) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($transaction->status == 'pending')
                            <a href="{{ route('product_transactions.edit', $transaction->trans_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('product_transactions.confirm', $transaction->trans_id) }}" method="POST" class="d-inline confirm-form">
                                @csrf
                                <button type="button" class="btn btn-sm btn-success confirm-btn" title="Confirm Transaction">
                                    <i class="fas fa-check"></i> 
                                </button>
                            </form>
                            <form action="{{ route('product_transactions.destroy', $transaction->trans_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center">No product transactions recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $productTransactions->links() }}
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            const deleteButton = form.querySelector('.delete-btn');
            deleteButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    allowOutsideClick: false,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        });

        
        const confirmForms = document.querySelectorAll('.confirm-form');
        confirmForms.forEach(form => {
            const confirmButton = form.querySelector('.confirm-btn');
            confirmButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'Confirm Transaction?',
                    text: "This will deduct stock from the customer's inventory and the transaction will become view-only.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Confirm!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    });
</script>
@endsection