@extends('layouts.appw')

@section('content')
<div class="container mt-5">
    <h1>Payment Methods</h1>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <a href="{{ route('payment_methods.create') }}" class="btn btn-primary mb-3">Add New Payment Method</a>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($paymentMethods as $method)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $method->name }}</td>
                    <td>{{ $method->description ?? 'N/A' }}</td>
                    <td>
                        @if ($method->is_active)
                        <span class="badge bg-success">Yes</span>
                        @else
                        <span class="badge bg-danger">No</span>
                        @endif
                    </td>
                    <td>{{ $method->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('payment_methods.edit', $method->payment_id) }}"
                            class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No payment </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection