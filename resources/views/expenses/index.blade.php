@extends('layouts.appw')

@section('content')
<div class="container">
    <h4 class="text-center text-primary font-weight-bold mb-4 text-uppercase">
        Expense Records
    </h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between mb-3">

        <div class="col-12 col-md-auto mb-2 mb-md-0">
            <a href="{{ route('expenses.create') }}" class="btn btn-success w-100 w-md-auto">
                <i class="fas fa-plus"></i> Add Record
            </a>
        </div>

        <form action="{{ route('expenses.index') }}" method="GET" class="row gx-2 gy-2 align-items-center">
            <div class="col-12 col-md-3 mb-2 mb-md-0">

                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <select name="category_filter" id="category_filter" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->category_id }}"
                            {{ request('category_filter') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" name="date_filter" id="date_filter" class="form-control"
                        value="{{ request('date_filter') }}">
                </div>
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary mb-2 mb-md-0"><i class="fas fa-filter"></i> Filter
                </button>

                <a href="{{ route('expenses.index') }}" class="btn btn-secondary mb-2 mb-md-0"><i
                        class="fas fa-undo"></i> Reset</a>
                </a>
            </div>

        </form>
    </div>



    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>S/N</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount(TZS)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $expense->user->first_name }}</td>
                    <td>{{ $expense->category->name }}</td>
                    <td>{{ $expense->tarehe }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ number_format($expense->amount, 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('expenses.edit', $expense->expense_id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right font-weight-bold">Total Amount:</td>
                    <td class="font-weight-bold">{{ number_format($totalAmount, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection