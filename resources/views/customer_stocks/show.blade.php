@extends('layouts.appw') {{-- Assuming 'layouts.appw' is your main layout --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Customer Material Stock Details</h4>

            <div class="card">
                <div class="card-header">Stock ID: {{ $customer_stock->stock_id }} for Batch: {{ $customer_stock->custAlizeti->batch_no ?? 'N/A' }}</div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Customer Name:</strong> {{ $customer_stock->custAlizeti->customer->first_name ?? 'N/A' }} {{ $customer_stock->custAlizeti->customer->last_name ?? '' }}
                    </div>
                    <div class="mb-3">
                        <strong>Customer Phone:</strong> {{ $customer_stock->custAlizeti->customer->phone_number ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Alizeti Input Batch ID:</strong> {{ $customer_stock->custAlizeti->batch_no ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Initial Input Quantity (kg):</strong> {{ number_format($customer_stock->custAlizeti->uncleaned_kg ?? 0, 2) }}
                    </div>
                    <hr>
                    <h5>Current Stock Levels</h5>
                    <div class="mb-3">
                        <strong>Uncleaned Alizeti:</strong> {{ number_format($customer_stock->uncleaned_kg, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Cleaned Alizeti:</strong> {{ number_format($customer_stock->cleaned_kg, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Crude Oil:</strong> {{ number_format($customer_stock->crude_oil, 2) }} Liters
                    </div>
                    <div class="mb-3">
                        <strong>Refined Oil:</strong> {{ number_format($customer_stock->refined_oil, 2) }} Liters
                    </div>
                    <div class="mb-3">
                        <strong>Mashudu:</strong> {{ number_format($customer_stock->mashudu_kg, 2) }} kg
                    </div>
                    <div class="mb-3">
                        <strong>Lami:</strong> {{ number_format($customer_stock->lami_kg, 2) }} kg
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong>Record Created:</strong> {{ $customer_stock->created_at->format('Y-m-d H:i:s') }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated:</strong> {{ $customer_stock->updated_at->format('Y-m-d H:i:s') }}
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('customer_stocks.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection