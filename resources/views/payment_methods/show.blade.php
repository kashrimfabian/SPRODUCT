@extends('layouts.appw')

@section('content')
    <div class="container mt-5">
        <h1>Payment Method Details: {{ $paymentMethod->name }}</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Basic Information</h5>
                <p class="card-text"><strong>ID:</strong> {{ $paymentMethod->payment_id }}</p>
                <p class="card-text"><strong>Name:</strong> {{ $paymentMethod->name }}</p>
                <p class="card-text"><strong>Description:</strong> {{ $paymentMethod->description ?? 'N/A' }}</p>
                <p class="card-text">
                    <strong>Status:</strong>
                    @if ($paymentMethod->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Timestamps</h5>
                <p class="card-text"><strong>Created At:</strong> {{ $paymentMethod->created_at->format('Y-m-d H:i:s') }}</p>
                <p class="card-text"><strong>Last Updated:</strong> {{ $paymentMethod->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>

        <a href="{{ route('payment_methods.edit', $paymentMethod->payment_id) }}" class="btn btn-warning me-2">Edit Payment Method</a>
        <a href="{{ route('payment_methods.index') }}" class="btn btn-secondary">Back to List</a>

        {{-- Optional: Delete button on show page --}}
        <form action="{{ route('payment_methods.destroy', $paymentMethod->payment_id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this payment method? This action cannot be undone.');">Delete</button>
        </form>
    </div>
@endsection