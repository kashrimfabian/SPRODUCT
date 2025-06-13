@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h4 class="text-center text-dark text-uppercase mb-4">
            Loans Records
        </h4>
    </div>

    <div class="col-12 col-md-auto mb-2 mb-md-0">
        <a href="{{ route('loans.create') }}" class="btn btn-success mb-3">
            <i class="fas fa-plus"></i> Add Record
        </a>
    </div>


    {{-- Session messages --}}
    @if(session('success'))
    <p>{{ session('success') }}</p>
    @endif
    @if(session('error'))
    <p>{{ session('error') }}</p>
    @endif
    @if(session('info'))
    <p>{{ session('info') }}</p>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Loan_Date</th> 
                    <th>Lender</th>
                    <th>Original_Amount</th>
                    <th>Current_Amount</th>
                    <th>Interest</th>
                    <th>Due_Date</th>
                    <th>Loan_Status</th>
                    <th>Confirmed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $loan->loan_date }}</td> 
                    <td>{{ $loan->lender_name }}</td>
                    <td>{{ number_format($loan->original_loan_amount, 2) }}</td>
                    <td>{{ number_format($loan->amount, 2) }}</td>
                    <td>{{ number_format($loan->interest_rate, 2) }}%</td>
                    <td>{{ $loan->due_date }}</td> 
                    <td>@if($loan->loan_status === 'paid')
                        <span class="badge bg-success rounded-pill px-3 py-2">Paid</span>
                        @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Not Paid</span>
                        @endif
                    </td>
                    <td>
                        @if($loan->is_confirmed)
                        <span class="badge bg-info text-dark rounded-pill px-3 py-2">Yes</span>
                        @else
                        <span class="badge bg-secondary rounded-pill px-3 py-2">No</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; justify-content: center; align-items: center; padding: 5px;">
                            {{-- Conditional Edit/View Button --}}
                            @if(!$loan->is_confirmed)
                                <a href="{{ route('loans.edit', $loan->loan_id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @else
                                {{-- Locked Edit/View Button for Confirmed Loans --}}
                                <a href="{{ route('loans.edit', $loan->loan_id) }}" class="btn btn-sm btn-secondary" title="Loan is confirmed, cannot be edited" style="pointer-events: none; opacity: 0.6;"> {{-- Added style to visually dim and disable pointer --}}
                                    <i class="fas fa-lock"></i>
                                </a>
                            @endif

                            {{-- Conditional Confirm Button (remains unchanged as per your logic) --}}
                            @if(!$loan->is_confirmed)
                            <form id="confirmForm_{{ $loan->loan_id }}"
                                action="{{ route('loans.confirm', $loan->loan_id) }}" method="POST"
                                style="display:inline; margin-left: 5px;">
                                @csrf
                                <button type="button" class="btn btn-sm btn-info"
                                    onclick="confirmLoan('{{ $loan->loan_id }}')">
                                    <i class="fas fa-check-circle"></i> Confirm
                                </button>
                            </form>
                            @endif

                            
                            @if(!$loan->is_confirmed)
                                <form id="deleteForm_{{ $loan->loan_id }}"
                                    action="{{ route('loans.destroy', $loan->loan_id) }}" method="POST"
                                    style="display:inline; margin-left: 5px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete('{{ $loan->loan_id }}')">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            @else
                                {{-- Locked Delete Button for Confirmed Loans --}}
                                <button type="button" class="btn btn-sm btn-secondary" title="Loan is confirmed, cannot be deleted" style="display:inline; margin-left: 5px; pointer-events: none; opacity: 0.6;"> {{-- Added style to visually dim and disable pointer --}}
                                    <i class="fas fa-lock"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4">No loan records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SweetAlert2 CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
{{-- jQuery (if not already in layouts.appw) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
function confirmDelete(loanId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm_' + loanId).submit();
        }
    });
}

// Function for loan confirmation using SweetAlert2
function confirmLoan(loanId) {
    Swal.fire({
        title: 'Confirm Loan?',
        text: "Are you sure you want to confirm this loan? Amount and dates cannot be edited after confirmation.",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, confirm!',
        cancelButtonText: 'No, cancel!',
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('confirmForm_' + loanId).submit();
        }
    });
}
</script>
@endsection