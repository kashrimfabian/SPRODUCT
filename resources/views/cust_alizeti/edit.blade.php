@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Edit Customer Alizeti Input</h4>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-header">Edit Alizeti Input (ID: {{ $cust_alizeti->cust_ali_id }})</div>
                <div class="card-body">
                    <form action="{{ route('cust_alizeti.update', $cust_alizeti->cust_ali_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="cust_id" class="form-label">Customer</label>
                            <select class="form-select" id="cust_id" name="cust_id" required>
                                <option value="">Select a Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->cust_id }}" {{ old('cust_id', $cust_alizeti->cust_id) == $customer->cust_id ? 'selected' : '' }}>
                                    {{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->phone_number }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="batch_no" class="form-label">Batch Number</label>
                            <input type="text" class="form-control" id="batch_no" name="batch_no"
                                value="{{ old('batch_no', $cust_alizeti->batch_no) }}" required>
                            <div class="form-text text-muted">A unique identifier for this customer's alizeti batch.</div>
                        </div>

                        <div class="mb-3">
                            <label for="uncleaned_kg" class="form-label">Uncleaned Quantity (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="uncleaned_kg"
                                name="uncleaned_kg" value="{{ old('uncleaned_kg', $cust_alizeti->uncleaned_kg) }}" min="0.01" required>
                            <div class="form-text text-muted">The initial weight of raw alizeti brought by the customer.</div>
                        </div>

                        <div class="mb-3">
                            <label for="tarehe" class="form-label">Input Date</label>
                            <input type="date" class="form-control" id="tarehe" name="tarehe"
                                value="{{ old('tarehe', $cust_alizeti->tarehe) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="received" {{ old('status', $cust_alizeti->status) == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="in_processing" {{ old('status', $cust_alizeti->status) == 'in_processing' ? 'selected' : '' }}>In Processing</option>
                                <option value="processing_complete" {{ old('status', $cust_alizeti->status) == 'processing_complete' ? 'selected' : '' }}>Processing Complete</option>
                                {{-- Add more statuses as needed --}}
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Update Input</button>
                        <a href="{{ route('cust_alizeti.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection