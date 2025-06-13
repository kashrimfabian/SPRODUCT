@extends('layouts.appw')
@section('content')
<div class="container">
    <h4
        style="text-align: center; color: #3490dc; font-family: 'Arial', sans-serif; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;">
        Mafuta Sales Records</h4>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.index') }}" class="btn btn-info w-100 w-md-auto">
                Mafuta Summary
            </a>
        </div>

        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.mashudu_summary') }}" class="btn btn-primary w-100 w-md-auto">
                Mashudu Summary
            </a>
        </div>

        <div class="col-12 col-md-auto mb-3">
            <a href="{{ route('mauzo.create') }}" class="btn btn-success w-100 w-md-auto">
                <i class="fas fa-plus"></i> Add New Mauzo
            </a>
        </div>
    </div>

    <form action="{{ route('mauzo.index') }}" method="GET" class="row gx-2 gy-2 align-items-center mb-3">
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
                <label for="payment_method_id" class="form-label">Payment Method</label>
                {{-- This is the select element for payment methods --}}
                <select name="payment_id" id="payment_method_id" class="form-select" required>
                    <option value="">-- Select Payment Method --</option>
                    @foreach($paymentMethods as $method)
                    <option value="{{ $method->payment_id }}" data-name="{{ $method->name }}"
                        {{ old('payment_id') == $method->payment_id ? 'selected' : '' }}>
                        {{ $method->name }}
                    </option>
                    @endforeach
                </select>
                @error('payment_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-auto">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-tags"></i></span>
                <select name="sells_type" id="sells_type" class="form-select">
                    <option value="">All Sales Types</option>
                    <option value="jumla" {{ request('sells_type') == 'jumla' ? 'selected' : '' }}>Jumla</option>
                    <option value="rejareja" {{ request('sells_type') == 'rejareja' ? 'selected' : '' }}>Rejareja
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('mauzo.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>User Name</th>
                    <th>Date</th>
                    <th>Batch_No.</th>
                    <th>Mafuta_(Lts)</th>
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
                    <td colspan="4">Total:</td>
                    <td>{{ $mauzoRecords->sum('quantity') }} Lts</td>
                    <td colspan="2"></td>
                    <td>{{ number_format($mauzoRecords->sum('total_price'), 0) }} TZS</td>
                    <td colspan="5"></td> {{-- ADJUSTED COLSPAN FOR NEW COLUMN --}}
                </tr>
            </tfoot>
        </table>
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