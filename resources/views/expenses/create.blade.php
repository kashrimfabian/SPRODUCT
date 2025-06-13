@extends('layouts.appw')

@section('content')
<div class="container">
    <h1>Create Expense</h1>

    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="tarehe">Tarehe</label>
            <input type="date" name="tarehe" id="tarehe" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" id="description" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>


@endsection