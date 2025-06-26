@extends('layouts.appw')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h4 class="text-center text-dark text-uppercase mb-4">
        Sales Records
        </h4>
    </div>
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-auto mb-2 mb-md-0">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('mauzo.index') }}" class="btn btn-info">
                    All Sales
                </a>
                <a href="{{ route('mauzo.mafuta_summary') }}" class="btn btn-primary">
                    Mafuta Summary
                </a>
                <a href="{{ route('mauzo.mashudu_summary') }}" class="btn btn-secondary">
                    Mashudu Summary
                </a>
                <a href="{{ route('mauzo.ugido_summary') }}" class="btn btn-warning">
                    Ugido Summary
                </a>
                <a href="{{ route('mauzo.lami_summary') }}" class="btn btn-dark">
                    Lami Summary
                </a>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="{{ route('mauzo.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Mauzo
                </a>
                {{-- Button to toggle the filter collapse --}}
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="fas fa-filter"></i> Filter Sales
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Form (Collapsible) --}}
    <div class="collapse mb-4" id="filterCollapse">
        <div class="card card-body shadow-sm">
            <form action="{{ route('mauzo.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="alizeti_id" class="form-label">Alizeti Batch</label>
                        <select name="alizeti_id" id="alizeti_id" class="form-select">
                            <option value="">All Batches</option>
                            @foreach($alizeti as $batch)
                            <option value="{{ $batch->ali_id }}"
                                {{ request('alizeti_id') == $batch->ali_id ? 'selected' : '' }}>
                                {{ $batch->batch_no }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control datepicker"
                            value="{{ request('start_date') }}" placeholder="Start Date">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control datepicker"
                            value="{{ request('end_date') }}" placeholder="End Date">
                    </div>
                    <div class="col-md-4">
                        <label for="payment_method_id" class="form-label">Payment Method</label>
                        <select name="payment_id" id="payment_method_id" class="form-select">
                            <option value="">All Payment Methods</option>
                            @foreach($paymentMethods as $method)
                            <option value="{{ $method->payment_id }}"
                                {{ request('payment_id') == $method->payment_id ? 'selected' : '' }}>
                                {{ $method->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="sells_type" class="form-label">Sales Type</label>
                        <select name="sells_type" id="sells_type" class="form-select">
                            <option value="">All Sales Types</option>
                            <option value="jumla" {{ request('sells_type') == 'jumla' ? 'selected' : '' }}>Jumla</option>
                            <option value="rejareja" {{ request('sells_type') == 'rejareja' ? 'selected' : '' }}>Rejareja</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Apply Filters</button>
                        <a href="{{ route('mauzo.index') }}" class="btn btn-warning"><i class="fas fa-undo"></i> Reset Filters</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>User Name</th>
                    <th>Date</th>
                    <th>Batch_No.</th>
                    <th>Product</th>
                    <th>Quantity(Lts)</th>
                    <th>Price_(TZS)</th>
                    <th>Discount_(TZS)</th>
                    <th>Total Price_(TZS)</th>
                    <th>Payment Ways</th>
                    <th>Sales type</th>
                    <th>Payment Status</th>
                    <th>Sales Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mauzoRecords as $mauzoItem)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mauzoItem->user->first_name }}
                        @if($mauzoItem->user->middle_name)
                        {{ $mauzoItem->user->last_name }}
                        @endif
                    </td>
                    <td>{{ $mauzoItem->tarehe }}</td>
                    <td>{{ $mauzoItem->alizeti->batch_no }}</td>
                    <td>{{ $mauzoItem->product->name }}</td>
                    <td>{{ $mauzoItem->quantity }}</td>
                    <td>{{ number_format($mauzoItem->price, 0) }}</td>
                    <td>{{ number_format($mauzoItem->discount, 0) }}</td>
                    <td>{{ number_format($mauzoItem->total_price, 0) }}</td>
                    <td>
                        {{-- Attempt to display payment method name instead of ID --}}
                        {{ $mauzoItem->paymentMethod ? $mauzoItem->paymentMethod->name : 'N/A' }}
                    </td>
                    <td>{{ $mauzoItem->sells_type}}</td>
                    <td>
                        {{-- Display Payment Status with badges --}}
                        @if ($mauzoItem->payment_status == 'payed')
                        <span class="badge bg-success">Payed</span>
                        @elseif ($mauzoItem->payment_status == 'not payed')
                        <span class="badge bg-warning text-dark">Not Payed</span>
                        @else
                        {{ $mauzoItem->payment_status }} {{-- Fallback if status is neither expected value --}}
                        @endif
                    </td>
                    <td>
                        {{-- Display Confirmation Status --}}
                        @if ($mauzoItem->is_confirmed)
                        <span class="badge bg-success">Sold</span>
                        @else
                        <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            @if (!$mauzoItem->is_confirmed)
                            <a href="{{ route('mauzo.edit', $mauzoItem->mauzo_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('mauzo.destroy', $mauzoItem->mauzo_id) }}" method="POST"
                                class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <form action="{{ route('mauzo.confirm', $mauzoItem->mauzo_id) }}" method="POST"
                                class="d-inline confirm-form">
                                @csrf
                                <button type="button" class="btn btn-sm btn-info confirm-btn">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted"><i class="fas fa-lock"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">Total:</td>
                    <td>{{ $mauzoRecords->sum('quantity') }} Lts</td>
                    <td colspan="2"></td>
                    <td>{{ number_format($mauzoRecords->sum('total_price'), 0) }} TZS</td>
                    <td colspan="4"></td> 
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert for Delete confirmation
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
                confirmButtonText: 'Yes, delete it!',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    });

    // SweetAlert for Confirm confirmation
    const confirmForms = document.querySelectorAll('.confirm-form');
    confirmForms.forEach(form => {
        const confirmButton = form.querySelector('.confirm-btn');
        confirmButton.addEventListener('click', function() {
            Swal.fire({
                title: 'Confirm Sale?',
                text: "Are you sure you want to confirm this sale? This will update the stock.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, confirm it!',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    });

    // --- JavaScript for Filter Collapse ---
    const filterCollapseElement = document.getElementById('filterCollapse');
    const filterInputs = filterCollapseElement.querySelectorAll('select, input[type="date"]'); // Get all filter inputs

    let filtersActive = false;
    filterInputs.forEach(input => {
        // Check if any filter input has a value that's not empty or "All"
        // This is a more robust check for filter activation
        if (input.value && input.value !== '' && input.value !== '0' && input.value !== 'All') { // Added '0' and 'All' checks for common default values
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