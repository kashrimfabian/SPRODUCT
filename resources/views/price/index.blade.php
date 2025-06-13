@extends('layouts.appw')

@section('content')
<h4
    style="text-align: center; color: #3490dc; font-family: 'Arial', sans-serif; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;">
    Batch Prices</h4>
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="d-flex justify-content-end mb-3">
    <div class="col-md-auto" style="margin-left: auto;">
        <a href="{{ route('price.create') }}" class="btn btn-success mb-3" style="min-width: 200px;">
            <i class="fas fa-plus"></i> Add Price
        </a>
    </div>
</div>



<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>User_Name</th>
                <th>Batch_No</th>
                <th>Mafuta Price(TZS)</th>
                <th>Mashudu Price(TZS)</th>
                <th>Ugido Price(TZS)</th>
                <th>Lami Price(TZS)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prices as $price)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $price->user->first_name }}
                    @if($price->user->middle_name)
                    {{ $price->user->middle_name }}
                    @endif
                    {{ $price->user->last_name }}
                </td>
                <td>{{ $price->alizeti->batch_no }}</td>
                <td>{{ $price->price_per_litre }}</td>
                <td>{{ $price->price_of_mashudu }}</td>
                <td>{{ $price->price_of_ugido }}</td>
                <td>{{ $price->price_of_lami }}</td>
                <td>
                    <div style="display: flex; justify-content: center; align-items: center; padding: 5px;">
                        <a href="{{ route('price.edit', $price->prices_id) }}" class="btn btn-sm btn-primary"><i
                                class="fas fa-edit"></i> </a>
                    </div>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection