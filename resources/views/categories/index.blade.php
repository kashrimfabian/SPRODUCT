@extends('layouts.appw')

@section('content')
<div class="container">
<h4
        style="text-align: center; color: #3490dc; font-family: 'Arial', sans-serif; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;">
        Category Records</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <div class="col-md-auto" style="margin-left: auto;">
            <a href="{{ route('categories.create') }}" class="btn btn-success mb-3" style="min-width: 200px;">
                <i class="fas fa-plus"></i> Add Record
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>S/N</th>
                    <th>Date</th>
                    <th>Category_Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $loop->iteration}}</td>
                    <td>{{ $category->tarehe }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>
                        <div style="display: flex; justify-content: center; align-items: center; padding: 5px;">
                            <a href="{{ route('categories.edit', $category->category_id) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection