@extends('layouts.appw')

@section('content')
<div class="container">
    <h2>Mashudu Sales</h2>
    <a href="{{ route('mashudu.create') }}" class="btn btn-primary mb-3">Record New Sale</a>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="mb-3">
        <form action="{{ route('mashudu.index') }}" method="GET">
            <div class="row">
                <div class="col-md-3">
                    <label for="date_from">Date From:</label>
                    <input type="text" name="date_from" id="date_from" class="form-control datepicker" placeholder="Select a date" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to">Date To:</label>
                    <input type="text" name="date_to" id="date_to" class="form-control datepicker" placeholder="Select a date" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label for="batch_no">Batch No:</label>
                    <select name="batch_no" id="batch_no" class="form-control">
                        <option value="">All</option>
                        @foreach($alizetiList as $batch)
                            <option value="{{ $batch->batch_no }}" {{ request('batch_no') == $batch->batch_no ? 'selected' : '' }}>{{ $batch->batch_no }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_way">Payment Way:</label>
                    <select name="payment_way" id="payment_way" class="form-control">
                        <option value="">All</option>
                        <option value="cash" {{ request('payment_way') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Lipa_Namba" {{ request('payment_way') == 'Lipa_Namba' ? 'selected' : '' }}>Lipa Namba</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('mashudu.index') }}" class="btn btn-secondary mt-2">Reset</a>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>S/N</th>
                    <th>User_Name</th>
                    <th>Tarehe</th>
                   
                    <th>Batch No</th>
                    <th>Mashudu (kgs)</th>
                    <th>Price per kg (TZS)</th>
                    <th>Discount</th>
                    <th>Total Price (TZS)</th>
                    <th>Payment Way</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mashuduSales as $sale)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sale->user->first_name }} {{ $sale->user->middle_name }} {{ $sale->user->last_name }}</td>
                    <td>{{ $sale->tarehe }}</td>
                    <td>{{ $sale->alizeti->batch_no }}</td>
                    <td>{{ $sale->mashudu }}</td>
                    <td>{{ $sale->price }}</td>
                    <td>{{ $sale->discount }}</td>
                    <td>{{ $sale->total_price }}</td>
                    <td>{{ $sale->payment_way }}</td>
                    <td>
                        <a href="{{ route('mashudu.edit', $sale->mashudu_id) }}" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Totals:</strong></td>
                    <td><strong>{{ $totalMashudu }} kgs</strong></td>
                    <td></td>
                    <td></td>
                    <td><strong>{{ $totalPrice }} TZS</strong></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection