@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Edit Category</h4>

            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="tarehe">Tarehe</label>
                    <input type="date" name="tarehe" id="tarehe" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control">{{ $category->description }}</textarea>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="fas fa-save"></i> Update
                </button>

                <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-3">
                    <i class="fas fa-arrow-left align-middle"></i> Back
                </a>
            </form>
        </div>
    </div>
</div>
@endsection