@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h4 class="text-center text-dark text-uppercase mb-4">
            Sunflower Seeds Purchasing Records
        </h4>
    </div>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between mb-3">
        <div class="col-12 col-md-auto mb-2 mb-md-0">
            <a href="{{ route('alizeti.create') }}" class="btn btn-success w-100 w-md-auto">
                <i class="fas fa-plus"></i> Add Record
            </a>
        </div>

        <form action="{{ route('alizeti.summary') }}" method="GET" class="row gx-2 gy-2 align-items-stretch">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                    <select name="batch_no" id="batch_no" class="form-select">
                        <option value="">All Batches</option>
                        @foreach($alizetiSummary as $batch)
                        <option value="{{ $batch->batch_no }}"
                            {{ request('batch_no') == $batch->batch_no ? 'selected' : '' }}>
                            {{ $batch->batch_no }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" name="start_date" id="start_date" class="form-control datepicker"
                        value="{{ request('start_date') }}" placeholder="start date">
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" name="end_date" id="end_date" class="form-control datepicker"
                        value="{{ request('end_date') }}" placeholder="end date">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-primary mb-2 mb-md-0"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('alizeti.summary') }}" class="btn btn-secondary mb-2 mb-md-0"><i
                        class="fas fa-undo"></i> Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>S/N</th>
                    <th>User_Name</th>
                    <th>Date</th>
                    <th>Batch_No</th>
                    <th>Al_Kilogram_(KGS)</th>
                    <th>Total_Gunia</th>
                    <th>Price_per_Kilo_(TZS)</th>
                    <th>Total_Price_(TZS)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alizetiSummary as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->user->first_name }}
                        @if($item->user->middle_name)
                        @endif
                    </td>
                    <td>{{ $item->tarehe }}</td>
                    <td>{{ $item->batch_no }}</td>
                    <td>{{ $item->al_kilogram }}</td>
                    <td>{{ $item->gunia_total }}</td>
                    <td>{{ number_format($item->price_per_kilo) }}</td>
                    <td>{{ number_format($item->total_price, 0) }}</td>
                    <td>
                        <div style="display: flex; justify-content: center; align-items: center; padding: 5px;">
                            <a href="{{ route('alizeti.edit', $item->ali_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Totals:</strong></td>
                    <td><strong>{{ $alizetiSummary->sum('al_kilogram'), 2 }}</strong></td>
                    <td><strong>{{ $alizetiSummary->sum('gunia_total'), 2 }}</strong></td>
                    <td></td>
                    <td><strong>{{ number_format($alizetiSummary->sum('total_price'), 0) }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection