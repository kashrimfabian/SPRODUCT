@extends('layouts.appw')

@section('content')
<div class="container">
    <h4 class="text-center mb-4 text-primary fw-bold">Mashudu Sales Records</h4>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Navigation buttons for all sales views --}}
        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.index') }}" class="btn btn-info w-100 w-md-auto">
                All Sales
            </a>
        </div>

    <div class="row mb-3">
        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.mafuta_summary') }}" class="btn btn-info w-100 w-md-auto">
                Mafuta Sales
            </a>
        </div>

        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.mashudu_summary') }}" class="btn btn-primary w-100 w-md-auto">
                Mashudu sales
            </a>
        </div>
        
        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.ugido_summary') }}" class="btn btn-warning w-100 w-md-auto">
                Ugido Sales
            </a>
        </div>

        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.lami_summary') }}" class="btn btn-dark w-100 w-md-auto">
                Lami Sales
            </a>
        </div>

        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.create') }}" class="btn btn-success w-100 w-md-auto">
                <i class="fas fa-plus"></i> Add New Record
            </a>
        </div>
    </div>

    <form action="{{ route('mauzo.mashudu_summary') }}" method="GET" class="row gx-2 gy-2 align-items-center mb-3">
        <div class="col-md-auto">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-filter"></i></span>
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
        </div>
        <div class="col-md-auto">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" name="start_date" id="start_date" class="form-control datepicker"
                    value="{{ request('start_date') }}" placeholder="Start Date">
            </div>
        </div>
        <div class="col-md-auto">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" name="end_date" id="end_date" class="form-control datepicker"
                    value="{{ request('end_date') }}" placeholder="End Date">
            </div>
        </div>
        <div class="col-md-auto">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-money-bill-alt"></i></span>
                <select name="payment_id" id="payment_id" class="form-select">
                    <option value="">All Payment Ways</option>
                    @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}" {{ request('payment_id') == $method->id ? 'selected' : '' }}>
                        {{ $method->method_name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('mauzo.mashudu_summary') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>User_Name</th>
                    <th>Date</th>
                    <th>Batch_No.</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price_(TZS)</th>
                    <th>Discount_(TZS)</th>
                    <th>Total_Price_(TZS)</th>
                    <th>Payment_Method</th>
                    <th>Payment Status</th>
                    <th>Sales Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mauzo as $sale)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sale->user->first_name ?? '' }} {{ $sale->user->last_name ?? '' }}</td>
                    <td>{{ $sale->tarehe }}</td>
                    <td>{{ $sale->alizeti->batch_no ?? 'N/A' }}</td>
                    <td>{{ $sale->product->name}}</td>
                    <td>{{ number_format($sale->quantity, 2) }}</td>
                    <td>{{ number_format($sale->price, 0) }}</td>
                    <td>{{ number_format($sale->discount, 0) }}</td>
                    <td>{{ number_format($sale->total_price, 0) }}</td>
                    <td>{{ $sale->paymentMethod->name }}</td>
                    <td>
                        {{-- Display Payment Status with badges --}}
                        @if ($sale->payment_status == 'payed')
                        <span class="badge bg-success">Payed</span>
                        @elseif ($sale->payment_status == 'not payed')
                        <span class="badge bg-warning text-dark">Not Payed</span>
                        @else
                        {{ $sale->payment_status }} 
                        @endif
                    <td>
                        @if ($sale->is_confirmed)
                        <span class="badge bg-success">sold</span>
                        @else
                        <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if (!$sale->is_confirmed)
                            <a href="{{ route('mauzo.edit', $sale->mauzo_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('mauzo.destroy', $sale->mauzo_id) }}" method="POST"
                                class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <form action="{{ route('mauzo.confirm', $sale->mauzo_id) }}" method="POST"
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
                @empty
                    <tr>
                        <td colspan="12" class="text-center">No sales records found for this product.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">Totals:</td>
                    <td>{{ number_format($totalQuantity, 2) }}</td>
                    <td colspan="2"></td>
                    <td>{{ number_format($totalSalesPrice, 0) }} TZS</td>
                    <td colspan="5"></td>
                    <!-- <td></td> -->
                </tr>
            </tfoot>
        </table>
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

    const confirmForms = document.querySelectorAll('.confirm-form');
    confirmForms.forEach(form => {
        const confirmButton = form.querySelector('.confirm-btn');
        confirmButton.addEventListener('click', function() {
            Swal.fire({
                title: 'Confirm Sale?',
                text: "Are you sure you want to confirm this sale? This will update the stock.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8', // Bootstrap info color
                cancelButtonColor: '#6c757d', // Bootstrap secondary color
                confirmButtonText: 'Yes, confirm it!'
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