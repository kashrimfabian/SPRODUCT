@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h4 class="text-center text-dark text-uppercase mb-4"> Loans Payment Records</h4>
    </div>
    <a href="{{ route('loan_payments.create') }}" class="btn btn-success mb-3">Add Payment</a>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>S/N</th>
                    <th>Lender_Name</th>
                    <th>Payment_Date</th>
                    <th>Amount_Payed</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $payment->loan->lender_name }}</td>
                    <td>{{ $payment->payment_date }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->notes }}</td>
                    <td>
                        <div style="display: flex; justify-content: center; align-items: center; padding: 5px;">
                            <a href="{{ route('loan_payments.edit', $payment->loanPy_id) }}"
                                class="btn btn-sm btn-primary"> <i class="fas fa-edit"></i> </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection