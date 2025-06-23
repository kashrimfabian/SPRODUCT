@extends('layouts.appw') 

@section('content')
<div class="container">
    <h4 class="text-center mb-4">Customer Cleaning Operations</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('cust_clean.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Record New Cleaning Operation
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Alizeti Batch No.</th>
                    <th>Recorded By</th>
                    <th>Uncleaned Input (kg)</th>
                    <th>Makapi (kg)</th>
                    <th>Cleaned Output (kg)</th>
                    <th>Elec. Units Used</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($custCleanOperations as $operation)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $operation->tarehe }}</td>
                    <td>{{ $operation->custAlizeti->customer->first_name ?? 'N/A' }} {{ $operation->custAlizeti->customer->last_name ?? '' }}</td>
                    <td>{{ $operation->custAlizeti->batch_no ?? 'N/A' }}</td>
                    <td>{{ $operation->user->first_name ?? 'N/A' }}</td>
                    <td>{{ number_format($operation->uncleaned_kg, 2) }}</td>
                    <td>{{ number_format($operation->makapi_kg, 2) }}</td>
                    <td>{{ number_format($operation->cleaned, 2) }}</td>
                    <td>{{ number_format($operation->unit_used, 2) }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('cust_clean.show', $operation->cl_id) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('cust_clean.edit', $operation->cl_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('cust_clean.destroy', $operation->cl_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">No customer cleaning operations recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $custCleanOperations->links() }}
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            const deleteButton = form.querySelector('.delete-btn');
            deleteButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    });
</script>
@endsection