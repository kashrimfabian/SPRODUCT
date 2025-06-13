@extends('layouts.appw')

@section('content')
    <div class="container mt-5">
        <h1>Product Details: {{ $product->name }}</h1>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Basic Information</h5>
                <p class="card-text"><strong>ID:</strong> {{ $product->id }}</p>
                <p class="card-text"><strong>Name:</strong> {{ $product->name }}</p>
                <p class="card-text"><strong>Description:</strong> {{ $product->description ?? 'N/A' }}</p>
                <p class="card-text">
                    <strong>Status:</strong>
                    @if ($product->is_active)
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
                <p class="card-text"><strong>Created At:</strong> {{ $product->created_at->format('Y-m-d H:i:s') }}</p>
                <p class="card-text"><strong>Last Updated:</strong> {{ $product->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>

        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning me-2">Edit Product</a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to List</a>

        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">Delete</button>
        </form>
    </div>
@endsection