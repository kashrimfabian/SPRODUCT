@extends('layouts.appw') 

@section('content')
<div class="container">
    <h4 class="text-center mb-4">Oil Filtering Operations</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('filtering.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Record New Filtering Operation
        </a>
        {{-- Button to toggle the filter collapse --}}
        <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="fas fa-filter"></i> Filter Records
        </button>
    </div>

    {{-- Filter Form --}}
    <div class="collapse mb-4" id="filterCollapse">
        <div class="card card-body shadow-sm">
            <form action="{{ route('filtering.index') }}" method="GET">
                <div class="row g-3">
                    {{-- Date Range Filter --}}
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control datepicker" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control datepicker" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>

                    {{-- Customer Batch Filter --}}
                    <div class="col-md-4">
                        <label for="cust_ali_id" class="form-label">Customer Batch No.</label>
                        <select class="form-select" id="cust_ali_id" name="cust_ali_id">
                            <option value="">All Batches</option>
                            @foreach($custAlizetiBatches as $batch)
                                <option value="{{ $batch->cust_ali_id }}" {{ request('cust_ali_id') == $batch->cust_ali_id ? 'selected' : '' }}>
                                    {{ $batch->batch_no }} ({{ $batch->customer->first_name ?? 'N/A' }} {{ $batch->customer->last_name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Recorded By Filter --}}
                    <div class="col-md-4">
                        <label for="user_id" class="form-label">Recorded By</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Apply Filters</button>
                        <a href="{{ route('filtering.index') }}" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Reset Filters</a>
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
                    <th>Customer Name</th>
                    <th>Alizeti Batch No.</th>
                    <th>Recorded By</th>
                    <th>Crude Oil Input (Ltr)</th>
                    <th>Refined Oil Output (Ltr)</th>
                    <th>Ugido Output (kg)</th> 
                    <th>Lami Output (kg)</th>
                    <th>Elec. Units Used</th>
                    <th>Cost Used</th> 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filteringOperations as $operation)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $operation->tarehe }}</td>
                    <td>{{ $operation->custAlizeti->customer->first_name ?? 'N/A' }} {{ $operation->custAlizeti->customer->last_name ?? '' }}</td>
                    <td>{{ $operation->custAlizeti->batch_no ?? 'N/A' }}</td>
                    <td>{{ $operation->user->first_name ?? 'N/A' }} {{ $operation->user->last_name ?? '' }}</td>
                    <td>{{ number_format($operation->crude_oil, 2) }}</td>
                    <td>{{ number_format($operation->refined_oil, 2) }}</td>
                    <td>{{ number_format($operation->ugido_kg, 2) }}</td> 
                    <td>{{ number_format($operation->lami_kg, 2) }}</td>
                    <td>{{ number_format($operation->unit_used, 2) }}</td>
                    <td>{{ number_format($operation->cost_used, 2) }}</td> 
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('filtering.show', $operation->filter_id) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('filtering.edit', $operation->filter_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('filtering.destroy', $operation->filter_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center">No filtering operations recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{-- Important: Use appends() to retain filter parameters during pagination --}}
        {{ $filteringOperations->appends(request()->except('page'))->links() }}
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

        // --- JavaScript for Filter Collapse ---
        const filterCollapseElement = document.getElementById('filterCollapse');
        const filterInputs = filterCollapseElement.querySelectorAll('input[type="date"], select');
        
        let filtersActive = false;
        filterInputs.forEach(input => {
            // Check if any filter input has a value
            if (input.value && input.value !== '') {
                filtersActive = true;
            }
        });

        // If any filters are active, ensure the collapse is shown on page load
        if (filtersActive) {
            // Use Bootstrap's Collapse JavaScript API to show the element
            // This assumes Bootstrap's JS is loaded (usually with appw layout)
            const collapseInstance = new bootstrap.Collapse(filterCollapseElement, {
                toggle: false // Do not toggle, just control explicitly
            });
            collapseInstance.show(); // Show the collapse div
        }
    });
</script>
@endsection