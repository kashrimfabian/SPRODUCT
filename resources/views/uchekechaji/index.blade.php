@extends('layouts.appw') 

@section('content')
<div class="container">
    
    <div class="row justify-content-center">
        <h4 class="text-center text-dark text-uppercase mb-4">
            Sunflower seeds processing Records
        </h4>
    </div>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('uchekechaji.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Uchekechaji Operation
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Date</th>
                    <th>Recorded By</th>
                    <th>Batch_No</th>
                    <th>Uncleaned Input (kg)</th>
                    <th>Makapi (Waste) (kg)</th>
                    <th>Cleaned Output (kg)</th>
                    <th>Initial Elec. (Units)</th>
                    <th>Final Elec. (Units)</th>
                    <th>Units Used</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($uchekechajiOperations as $operation)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $operation->tarehe }}</td> 
                    <td>{{ $operation->user->first_name ?? 'N/A' }}</td>
                    <td>{{ $operation->alizeti->batch_no ?? 'N/A' }}</td>
                    <td>{{ number_format($operation->uncleaned_amount, 2) }}</td>
                    <td>{{ number_format($operation->makapi_amount, 2) }}</td>
                    <td>{{ number_format($operation->cleaned_amount, 2) }}</td>
                    {{-- Display Initial and Final Electricity Units --}}
                    <td>{{ number_format($operation->initial_unit, 2) }}</td>
                    <td>{{ number_format($operation->final_unit, 2) }}</td>
                    {{-- Display Calculated Units Used (using model accessor) --}}
                    <td>{{ number_format($operation->units_used, 2) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('uchekechaji.edit', $operation->uchek_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>                            
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center">No uchekechaji operations recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $uchekechajiOperations->links() }}
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