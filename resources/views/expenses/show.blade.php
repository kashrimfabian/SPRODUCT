@extends('layouts.appw')

@section('content')
    <div class="container">
        <h1>Expense Details</h1>
        <p><strong>ID:</strong> {{ $expense->id }}</p>
        <p><strong>Amount:</strong> {{ $expense->amount }}</p>
        <p><strong>Description:</strong> {{ $expense->description }}</p>
        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('expenses.index') }}" class="btn btn-primary">Back to List</a>
    </div>
@endsection
