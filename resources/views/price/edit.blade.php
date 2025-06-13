@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title text-center mb-4">Edit Price</h1>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('price.update', $price->prices_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="batch_no" class="form-label">Alizeti Batch</label>
                    <input type="text" name="batch_no" class="form-control" value="{{ $price->alizeti->batch_no }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="price_per_litre" class="form-label">Price Per Liter</label>
                    <input type="number" name="price_per_litre" class="form-control" value="{{ $price->price_per_litre }}" required step="0.01">
                </div>
        

                <div class="mb-3">
                    <label for="price_of_mashudu" class="form-label">Price Per Mashudu</label>
                    <input type="number" name="price_of_mashudu" class="form-control" value="{{ $price->price_of_mashudu }}" required step="0.01">
                </div>

                <div class="mb-3">
                    <label for="price_of_mashudu" class="form-label">Price of ugido</label>
                    <input type="number" name="price_of_ugido" class="form-control" value="{{ $price->price_of_ugido }}" required step="0.01">
                </div>

                <div class="mb-3">
                    <label for="price_of_mashudu" class="form-label">Price Per Mashudu</label>
                    <input type="number" name="price_of_lami" class="form-control" value="{{ $price->price_of_lami }}" required step="0.01">
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-save "></i> Update Price
                    </button>
                    <a href="{{ route('price.index') }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-arrow-left"></i> Back to list
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection