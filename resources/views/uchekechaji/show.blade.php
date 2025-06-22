@extends('layouts.appw') {{-- Or your main layout file --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Uchekechaji (Cleaning) Operation Details</h4>

            <div class="card">
                <div class="card-header">Operation ID: {{ $uchekechaji->uchek_id }}</div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Operation Date:</strong> {{ $uchekechaji->tarehe }}
                    </div>
                    <div class="mb-3">
                        <strong>Recorded By:</strong> {{ $uchekechaji->user->first_name ?? 'N/A' }} {{ $uchekechaji->user->last_name ?? '' }}
                    </div>
                    <div class="mb-3">
                        <strong>Source Alizeti Batch:</strong> {{ $uchekechaji->alizeti->batch_no ?? 'N/A' }} (ID: {{ $uchekechaji->alizeti_id }})
                    </div>
                    <div class="mb-3">
                        <strong>Uncleaned Input Amount:</strong> {{ number_format($uchekechaji->uncleaned_amount, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Makapi (Waste) Amount:</strong> {{ number_format($uchekechaji->makapi_amount, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Cleaned Output Amount:</strong> {{ number_format($uchekechaji->cleaned_amount, 2) }} kg
                    </div>

                    <hr>
                    <h5>Electricity Usage</h5>
                    <div class="mb-3">
                        <strong>Initial Unit Reading:</strong> {{ number_format($uchekechaji->initial_unit, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Final Unit Reading:</strong> {{ number_format($uchekechaji->final_unit, 2) }}
                    </div>
                    <div class="mb-3">
                        <strong>Units Used:</strong> {{ number_format($uchekechaji->units_used, 2) }}
                    </div>
                    <hr>

                    <h5>Produced Cleaned Batch</h5>
                    @if($uchekechaji->cleanedSeedBatch)
                    <div class="mb-3">
                        <strong>Batch Number:</strong> {{ $uchekechaji->cleanedSeedBatch->batch_number }}
                    </div>
                    <div class="mb-3">
                        <strong>Quantity Produced:</strong> {{ number_format($uchekechaji->cleanedSeedBatch->quantity_kg, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Current Available Quantity:</strong> {{ number_format($uchekechaji->cleanedSeedBatch->current_available_quantity_kg, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Cleaned Date:</strong> {{ $uchekechaji->cleanedSeedBatch->cleaned_date }}
                    </div>
                    <div class="mb-3">
                        <strong>Notes:</strong> {{ $uchekechaji->cleanedSeedBatch->notes ?? 'N/A' }}
                    </div>
                    @else
                    <div class="alert alert-warning">No cleaned seed batch associated with this operation found.</div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('uchekechaji.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('uchekechaji.edit', $uchekechaji->uchek_id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('uchekechaji.destroy', $uchekechaji->uchek_id) }}" method="POST" class="d-inline delete-form">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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