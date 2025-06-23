@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Customer Alizeti Input Details</h4>

            <div class="card">
                <div class="card-header">Input ID: {{ $cust_alizeti->cust_ali_id }} (Batch: {{ $cust_alizeti->batch_no }})</div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Customer Name:</strong> {{ $cust_alizeti->customer->first_name ?? 'N/A' }} {{ $cust_alizeti->customer->last_name ?? '' }}
                    </div>
                    <div class="mb-3">
                        <strong>Customer Phone:</strong> {{ $cust_alizeti->customer->phone_number ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Uncleaned Quantity (kg):</strong> {{ number_format($cust_alizeti->uncleaned_kg, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Input Date:</strong> {{ $cust_alizeti->tarehe }}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $cust_alizeti->status)) }}
                    </div>
                    <div class="mb-3">
                        <strong>Recorded At:</strong> {{ $cust_alizeti->created_at }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated At:</strong> {{ $cust_alizeti->updated_at }}
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('cust_alizeti.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('cust_alizeti.edit', $cust_alizeti->cust_ali_id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('cust_alizeti.destroy', $cust_alizeti->cust_ali_id) }}" method="POST" class="d-inline delete-form">
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