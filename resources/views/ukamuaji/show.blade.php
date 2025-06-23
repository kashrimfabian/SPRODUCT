@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Ukamuaji (Oil Pressing) Operation Details</h4>

            <div class="card">
                <div class="card-header">Ukamuaji ID: {{ $ukamuaji->uk_id }}</div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Operation Date:</strong> {{ $ukamuaji->tarehe }}
                    </div>
                    <div class="mb-3">
                        <strong>Customer Name:</strong> {{ $ukamuaji->custAlizeti->customer->first_name ?? 'N/A' }} {{ $ukamuaji->custAlizeti->customer->last_name ?? '' }}
                    </div>
                    <div class="mb-3">
                        <strong>Alizeti Batch No.:</strong> {{ $ukamuaji->custAlizeti->batch_no ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Recorded By:</strong> {{ $ukamuaji->user->first_name ?? 'N/A' }} {{ $ukamuaji->user->last_name ?? '' }}
                    </div>
                    <hr>
                    <h5>Quantities</h5>
                    <div class="mb-3">
                        <strong>Cleaned Seeds Input:</strong> {{ number_format($ukamuaji->cleaned_kg, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Crude Oil Output:</strong> {{ number_format($ukamuaji->crude_oil, 2) }} Liters
                    </div>
                    <div class="mb-3">
                        <strong>Mashudu Output:</strong> {{ number_format($ukamuaji->mashudu_kg, 2) }} kg
                    </div>
                    <hr>
                    <h5>Electricity Usage</h5>
                    <div class="mb-3">
                        <strong>Initial Units:</strong> {{ number_format($ukamuaji->initial_units, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Final Units:</strong> {{ number_format($ukamuaji->final_units, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Units Used:</strong> {{ number_format($ukamuaji->unit_used, 2) }}
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong>Recorded At:</strong> {{ $ukamuaji->created_at }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated At:</strong> {{ $ukamuaji->updated_at }}
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('ukamuaji.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('ukamuaji.edit', $ukamuaji->uk_id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('ukamuaji.destroy', $ukamuaji->uk_id) }}" method="POST" class="d-inline delete-form">
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