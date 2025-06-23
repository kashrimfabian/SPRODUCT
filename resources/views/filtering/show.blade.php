@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Oil Filtering Operation Details</h4>

            <div class="card">
                <div class="card-header">Filtering ID: {{ $filtering->filter_id }}</div> {{-- Updated model name --}}
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Operation Date:</strong> {{ $filtering->tarehe }} {{-- Updated model name --}}
                    </div>
                    <div class="mb-3">
                        <strong>Customer Name:</strong> {{ $filtering->custAlizeti->customer->first_name ?? 'N/A' }} {{ $filtering->custAlizeti->customer->last_name ?? '' }}
                    </div>
                    <div class="mb-3">
                        <strong>Alizeti Batch No.:</strong> {{ $filtering->custAlizeti->batch_no ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Recorded By:</strong> {{ $filtering->user->first_name ?? 'N/A' }} {{ $filtering->user->last_name ?? '' }} {{-- Updated model name --}}
                    </div>
                    <hr>
                    <h5>Quantities</h5>
                    <div class="mb-3">
                        <strong>Crude Oil Input:</strong> {{ number_format($filtering->crude_oil, 2) }} Liters {{-- Updated field name --}}
                    </div>
                    <div class="mb-3">
                        <strong>Refined Oil Output:</strong> {{ number_format($filtering->refined_oil, 2) }} Liters {{-- Updated field name --}}
                    </div>
                    <div class="mb-3">
                        <strong>Ugido Output:</strong> {{ number_format($filtering->ugido_kg, 2) }} kg {{-- Updated field name --}}
                    </div>
                    <div class="mb-3">
                        <strong>Lami (Waste) Output:</strong> {{ number_format($filtering->lami_kg, 2) }} kg {{-- Updated field name --}}
                    </div>
                    <hr>
                    <h5>Electricity Usage & Costs</h5>
                    <div class="mb-3">
                        <strong>Initial Units:</strong> {{ number_format($filtering->initial_units, 2) }} {{-- Updated model name --}}
                    </div>
                    <div class="mb-3">
                        <strong>Final Units:</strong> {{ number_format($filtering->final_units, 2) }} {{-- Updated model name --}}
                    </div>
                    <div class="mb-3">
                        <strong>Units Used:</strong> {{ number_format($filtering->unit_used, 2) }} {{-- Updated model name --}}
                    </div>
                    <div class="mb-3">
                        <strong>Cost Used:</strong> {{ number_format($filtering->cost_used, 2) }} TZS {{-- Updated model name --}}
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong>Recorded At:</strong> {{ $filtering->created_at }} {{-- Updated model name --}}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated At:</strong> {{ $filtering->updated_at }} {{-- Updated model name --}}
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('filtering.index') }}" class="btn btn-secondary">Back to List</a> {{-- Updated route name --}}
                        <a href="{{ route('filtering.edit', $filtering->filter_id) }}" class="btn btn-primary">Edit</a> {{-- Updated route name and model --}}
                        <form action="{{ route('filtering.destroy', $filtering->filter_id) }}" method="POST" class="d-inline delete-form"> {{-- Updated route name and model --}}
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