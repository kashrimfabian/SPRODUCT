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

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('product_transactions.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Record New Transaction
        </a>
        <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="fas fa-filter"></i> Filter Records
        </button>
    </div>

    
    <div class="collapse mb-4" id="filterCollapse">
        <div class="card card-body shadow-sm">
            <form action="{{ route('product_transactions.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control datepicker " id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control datepicker" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="trans_type" class="form-label">Transaction Type</label>
                        <select class="form-select" id="trans_type" name="trans_type">
                            <option value="">All Types</option>
                            <option value="collection" {{ request('trans_type') == 'collection' ? 'selected' : '' }}>Collection</option>
                            <option value="sale" {{ request('trans_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="product_id" class="form-label">Product</label>
                        <select class="form-select" id="product_id" name="product_id">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->product_id }}" {{ request('product_id') == $product->product_id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="user_id" class="form-label">Recorded By</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="buyer_name" class="form-label">Buyer Name</label>
                        <input type="text" class="form-control" id="buyer_name" name="buyer_name" value="{{ request('buyer_name') }}" placeholder="Buyer Name">
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Apply Filters</button>
                        <a href="{{ route('product_transactions.index') }}" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Reset Filters</a>
                    </div>
                </div>
            </form>
        </div>
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
        {{ $productTransactions->appends(request()->except('page'))->links() }}
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

        
        const filterCollapse = document.getElementById('filterCollapse');
        const filterInputs = filterCollapse.querySelectorAll('input, select');
        let filtersActive = false;
        filterInputs.forEach(input => {
            if (input.value && input.value !== '') { 
                filtersActive = true;
            }
        });

        if (filtersActive) {
            new bootstrap.Collapse(filterCollapse, {
                toggle: true
            });
        }
    });
</script>
@endsection