@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4>Edit Alizeti Record</h4>

            <form action="{{ route('alizeti.update', $alizeti->ali_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="tarehe">Date</label>
                    <input type="date" class="form-control" id="tarehe" name="tarehe" value="{{ $alizeti->tarehe }}" required>
                </div>

                <div class="form-group">
                    <label for="batch_no">Batch No</label>
                    <input type="text" class="form-control" id="batch_no" name="batch_no" value="{{ $alizeti->batch_no }}" required>
                </div>

                <div class="form-group">
                    <label for="al_kilogram">Al Kilogram</label>
                    <input type="number" class="form-control" id="al_kilogram" name="al_kilogram" value="{{ $alizeti->al_kilogram }}" required>
                </div>

                <div class="form-group">
                    <label for="gunia_total">Total Gunia</label>
                    <input type="number" class="form-control" id="gunia_total" name="gunia_total" value="{{ $alizeti->gunia_total }}" required>
                </div>

                <div class="form-group">
                    <label for="price_per_kilo">Price per Kilo</label>
                    <input type="number" class="form-control" id="price_per_kilo" name="price_per_kilo" value="{{ $alizeti->price_per_kilo }}" required>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="fas fa-save align-middle"></i> Update
                </button>

                <a href="{{ route('alizeti.summary') }}" class="btn btn-secondary mt-3">
                    <i class="fas fa-arrow-left align-middle"></i> Back
                </a>
            </form>
        </div>
    </div>
</div>
@endsection