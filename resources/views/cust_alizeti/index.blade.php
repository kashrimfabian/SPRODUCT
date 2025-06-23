@extends('layouts.appw') 

@section('content')
<div class="container">
    <h4 class="text-center mb-4">Customer Alizeti Inputs</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('cust_alizeti.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Record New Customer Input
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Customer Name</th>
                    <th>Phone Number</th>
                    <th>Batch No</th>
                    <th>Uncleaned (kg)</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($custAlizetiInputs as $input)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $input->customer->first_name ?? 'N/A' }} {{ $input->customer->last_name ?? '' }}</td>
                    <td>{{ $input->customer->phone_number ?? 'N/A' }}</td>
                    <td>{{ $input->batch_no }}</td>
                    <td>{{ number_format($input->uncleaned_kg, 2) }}</td>
                    <td>{{ $input->tarehe }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $input->status)) }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('cust_alizeti.show', $input->cust_ali_id) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('cust_alizeti.edit', $input->cust_ali_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('cust_alizeti.destroy', $input->cust_ali_id) }}" method="POST" class="d-inline delete-form">
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
                    <td colspan="8" class="text-center">No customer alizeti inputs recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $custAlizetiInputs->links() }}
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