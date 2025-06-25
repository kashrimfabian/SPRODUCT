@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Product Transaction Details</h4>

            <div class="card">
                <div class="card-header">Transaction ID: {{ $product_transaction->trans_id }}</div> {{-- Updated --}}
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Transaction Date:</strong> {{ $product_transaction->tarehe }} {{-- Updated --}}
                    </div>
                    <div class="mb-3">
                        <strong>Transaction Type:</strong> {{ ucfirst($product_transaction->trans_type) }} {{-- Updated --}}
                    </div>
                    <div class="mb-3">
                        <strong>Customer Batch (Source):</strong> {{ $product_transaction->custAlizeti->batch_no ?? 'N/A' }} (Customer: {{ $product_transaction->custAlizeti->customer->first_name ?? 'N/A' }} {{ $product_transaction->custAlizeti->customer->last_name ?? '' }})
                    </div>
                    <div class="mb-3">
                        <strong>Recorded By:</strong> {{ $product_transaction->user->first_name ?? 'N/A' }} {{ $product_transaction->user->last_name ?? '' }}
                    </div>
                    @if ($product_transaction->trans_type == 'sale') {{-- Updated --}}
                    <div class="mb-3">
                        <strong>Buyer Name:</strong> {{ $product_transaction->buyer_name ?? 'N/A' }}
                    </div>
                    @endif
                    <hr>
                    <h5>Quantities Transacted</h5>
                    <div class="mb-3">
                        <strong>Product:</strong> {{ $product_transaction->product->name ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Quantity:</strong> {{ number_format($product_transaction->quantity, 2) }} {{ $product_transaction->product->unit_of_measure ?? '' }}
                    </div>
                    <hr>
                    <h5>Financial Details & Notes</h5>
                    <div class="mb-3">
                        <strong>Amount {{ $product_transaction->trans_type == 'collection' ? 'Paid by Customer' : 'Received' }}:</strong> {{ number_format($product_transaction->amount, 2) }} TZS {{-- Updated --}}
                    </div>
                    <div class="mb-3">
                        <strong>Notes:</strong> {{ $product_transaction->notes ?? 'None' }}
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong>Recorded At:</strong> {{ $product_transaction->created_at }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated At:</strong> {{ $product_transaction->updated_at }}
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('product_transactions.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('product_transactions.edit', $product_transaction->trans_id) }}" class="btn btn-primary">Edit</a> {{-- Updated --}}
                        <form action="{{ route('product_transactions.destroy', $product_transaction->trans_id) }}" method="POST" class="d-inline delete-form"> {{-- Updated --}}
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger delete-btn">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
                    confirmButtonText: 'Yes, delete it!'
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