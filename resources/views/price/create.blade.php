@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Create New Price</h4>

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('price.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="alizeti_id" class="form-label">Select Batch Number</label>
                    <select class="form-select" id="alizeti_id" name="alizeti_id" required>
                        <option value="">-- Select Batch --</option>
                        @foreach ($alizetiBatches as $batch)
                        <option value="{{ $batch->ali_id }}">{{ $batch->batch_no }}</option>
                        {{-- Make sure the value attribute is correctly set to the alizeti_id --}}
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="price_per_litre" class="form-label">Price Per Liter</label>
                    <input type="number" name="price_per_litre" class="form-control" required step="0.01"
                        value="{{ old('price_per_litre') }}">
                </div>

                
                <div class="mb-3">
                    <label for="price_of_mashudu" class="form-label">Price of Mashudu</label>
                    <input type="number" name="price_of_mashudu" class="form-control" required step="0.01"
                        value="{{ old('price_of_mashudu') }}">
                </div>

                <div class="mb-3">
                    <label for="price_of_mashudu" class="form-label">Price of Ugido</label>
                    <input type="number" name="price_of_ugido" class="form-control" required step="0.01"
                        value="{{ old('price_of_ugido') }}">
                </div>

                <div class="mb-3">
                    <label for="price_of_mashudu" class="form-label">Price of Lami</label>
                    <input type="number" name="price_of_lami" class="form-control" required step="0.01"
                        value="{{ old('price_of_lami') }}">
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-save "></i> Save Price
                    </button>
                    <a href="{{ route('price.index') }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection