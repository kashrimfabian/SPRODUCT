@extends('layouts.appw')

@section('content')
    <div class="container mt-5">
        <h1>Edit Payment Method: {{ $paymentMethod->name }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('payment_methods.update', $paymentMethod->payment_id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Use PUT method for update --}}

            <div class="mb-3">
                <label for="name" class="form-label">Payment Method Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $paymentMethod->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $paymentMethod->description) }}</textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Is Active?</label>
            </div>
            <button type="submit" class="btn btn-success">Update Payment Method</button>
            <a href="{{ route('payment_methods.index') }}" class="btn btn-secondary">Back to List</a>
        </form>
    </div>
@endsection