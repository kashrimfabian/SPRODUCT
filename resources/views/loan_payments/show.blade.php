@extends('layouts.appw')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Loan Payment Details</h1>

        <div class="mb-4">
            <strong>Payment ID:</strong> {{ $payment->id }}
        </div>

        <div class="mb-4">
            <strong>Loan ID:</strong> {{ $payment->loan->id ?? 'N/A' }}
        </div>

        <div class="mb-4">
            <strong>Amount Paid:</strong> {{ number_format($payment->amount_paid, 2) }} TZS
        </div>

        <div class="mb-4">
            <strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->toFormattedDateString() }}
        </div>

        <div class="mb-4">
            <strong>Notes:</strong> {{ $payment->notes ?? 'No notes available.' }}
        </div>

        <a href="{{ route('loan_payments.index') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
            Back to Payments
        </a>
    </div>
</div>
@endsection
