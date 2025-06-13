@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Create Category</h4>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="tarehe">Tarehe</label>
                    <input type="date" name="tarehe" id="tarehe" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success mt-3">
                        <i class="fas fa-save"></i> Submit
                    </button>

                    <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection