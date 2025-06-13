@extends('layouts.appw')

@section('content')
<div class="container">
    <h2>Edit Mashudu Sale</h2>

    <form action="{{ route('mashudu.update', $mashudu->mashudu_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="tarehe">Date</label>
            <input type="date" name="tarehe" id="tarehe" class="form-control" value="{{ $mashudu->tarehe }}" required>
        </div>

        <div class="form-group">
            <label for="mashudu">Mashudu (kg)</label>
            <input type="number" name="mashudu" id="mashudu" class="form-control" value="{{ $mashudu->mashudu }}" required min="0">
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ $mashudu->price }}" required min="0" step="0.01">
        </div>

        <div class="form-group">
            <label for="discount">Discount</label>
            <input type="number" name="discount" id="discount" class="form-control" value="{{ $mashudu->discount }}" min="0" step="0.01">
        </div>

        <div class="form-group">
            <label for="alizeti_id">Alizeti</label>
            <select name="alizeti_id" id="alizeti_id" class="form-control" required>
                <option value="">Select Alizeti</option>
                @foreach($alizeti as $item)
                    <option value="{{ $item->alizeti_id }}" {{ $item->alizeti_id == $mashudu->alizeti_id ? 'selected' : '' }}>
                        {{ $item->batch_no }} - {{ $item->shudu }} kg available
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="payment_way">Payment Way</label>
            <select name="payment_way" id="payment_way" class="form-control">
                <option value="">-- Select Payment Way --</option>
                <option value="cash" {{ $mashudu->payment_way == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="Lipa_Namba" {{ $mashudu->payment_way == 'Lipa_Namba' ? 'selected' : '' }}>Lipa Namba</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update</button>

        <a href="{{ route('mashudu.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i></a>
    </form>
</div>
@endsection