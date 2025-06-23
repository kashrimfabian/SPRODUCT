@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Customer Details</h4>

            <div class="card">
                <div class="card-header">Customer ID: {{ $customer->cust_id }}</div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>First Name:</strong> {{ $customer->first_name }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Name:</strong> {{ $customer->last_name ?? '-' }}
                    </div>
                    <div class="mb-3">
                        <strong>Phone Number:</strong> {{ $customer->phone_number }}
                    </div>
                    <div class="mb-3">
                        <strong>Created At:</strong> {{ $customer->created_at->format('Y-m-d H:i:s') }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated At:</strong> {{ $customer->updated_at->format('Y-m-d H:i:s') }}
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('customers.edit', $customer->cust_id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('customers.destroy', $customer->cust_id) }}" method="POST" class="d-inline delete-form">
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