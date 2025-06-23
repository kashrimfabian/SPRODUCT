@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Customer Cleaning Operation Details</h4>

            <div class="card">
                <div class="card-header">Cleaning ID: {{ $cust_clean->cl_id }}</div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Operation Date:</strong> {{ $cust_clean->tarehe }}
                    </div>
                    <div class="mb-3">
                        <strong>Customer Name:</strong> {{ $cust_clean->custAlizeti->customer->first_name ?? 'N/A' }} {{ $cust_clean->custAlizeti->customer->last_name ?? '' }}
                    </div>
                    <div class="mb-3">
                        <strong>Alizeti Batch No.:</strong> {{ $cust_clean->custAlizeti->batch_no ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Recorded By:</strong> {{ $cust_clean->user->first_name ?? 'N/A' }} {{ $cust_clean->user->last_name ?? '' }}
                    </div>
                    <hr>
                    <h5>Quantities</h5>
                    <div class="mb-3">
                        <strong>Uncleaned Seeds Input:</strong> {{ number_format($cust_clean->uncleaned_kg, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Makapi (Waste):</strong> {{ number_format($cust_clean->makapi_kg, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Cleaned Seeds Output:</strong> {{ number_format($cust_clean->cleaned, 2) }} kg
                    </div>
                    <hr>
                    <h5>Electricity Usage</h5>
                    <div class="mb-3">
                        <strong>Initial Units:</strong> {{ number_format($cust_clean->initial_units, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Final Units:</strong> {{ number_format($cust_clean->final_units, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Units Used:</strong> {{ number_format($cust_clean->unit_used, 2) }}
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong>Recorded At:</strong> {{ $cust_clean->created_at }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated At:</strong> {{ $cust_clean->updated_at }}
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('cust_clean.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('cust_clean.edit', $cust_clean->cl_id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('cust_clean.destroy', $cust_clean->cl_id) }}" method="POST" class="d-inline delete-form">
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