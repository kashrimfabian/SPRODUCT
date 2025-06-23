@extends('layouts.appw') 

@section('content')
<div class="container">
    <h4 class="text-center mb-4">Ukamuaji (Oil Pressing) Operations</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('ukamuaji.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Record New Ukamuaji Operation
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
                    <th>Cleaned Input (kg)</th>
                    <th>Crude Oil Output (Ltr)</th>
                    <th>Mashudu Output (kg)</th>
                    <th>Elec. Units Used</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ukamuajiOperations as $operation)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $operation->tarehe }}</td>
                    <td>{{ $operation->custAlizeti->customer->first_name ?? 'N/A' }} {{ $operation->custAlizeti->customer->last_name ?? '' }}</td>
                    <td>{{ $operation->custAlizeti->batch_no ?? 'N/A' }}</td>
                    <td>{{ $operation->user->first_name ?? 'N/A' }} {{ $operation->user->last_name ?? '' }}</td>
                    <td>{{ number_format($operation->cleaned_kg, 2) }}</td>
                    <td>{{ number_format($operation->crude_oil, 2) }}</td>
                    <td>{{ number_format($operation->mashudu_kg, 2) }}</td>
                    <td>{{ number_format($operation->unit_used, 2) }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('ukamuaji.show', $operation->uk_id) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('ukamuaji.edit', $operation->uk_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('ukamuaji.destroy', $operation->uk_id) }}" method="POST" class="d-inline delete-form">
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
                    <td colspan="10" class="text-center">No Ukamuaji operations recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $ukamuajiOperations->links() }}
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