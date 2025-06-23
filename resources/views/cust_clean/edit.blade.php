@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Edit Customer Cleaning Operation</h4>

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
                <div class="card-header">Edit Cleaning Record (ID: {{ $cust_clean->cl_id }})</div>
                <div class="card-body">
                    <form action="{{ route('cust_clean.update', $cust_clean->cl_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="cust_ali_id" class="form-label">Customer Alizeti Batch</label>
                            <select class="form-select" id="cust_ali_id" name="cust_ali_id" required>
                                <option value="">Select a Customer Batch</option>
                                @foreach($availableCustAlizetiBatches as $batch)
                                <option value="{{ $batch->cust_ali_id }}" 
                                    {{ old('cust_ali_id', $cust_clean->cust_ali_id) == $batch->cust_ali_id ? 'selected' : '' }}
                                    data-available-uncleaned="{{ number_format($batch->customerStock->uncleaned_kg ?? 0, 2) }}"
                                >
                                    {{ $batch->batch_no }} (Customer: {{ $batch->customer->first_name ?? '' }} {{ $batch->customer->last_name ?? '' }} - Uncleaned: {{ number_format($batch->customerStock->uncleaned_kg ?? 0, 2) }} kg)
                                </option>
                                @endforeach
                            </select>
                            <div id="selected_batch_stock_info" class="form-text text-muted mt-2">
                                Select a batch to see its current uncleaned stock.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Recorded By</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $cust_clean->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tarehe" class="form-label">Operation Date</label>
                            <input type="date" class="form-control" id="tarehe" name="tarehe"
                                value="{{ old('tarehe', $cust_clean->tarehe) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="uncleaned_kg" class="form-label">Uncleaned Input for Cleaning (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="uncleaned_kg"
                                name="uncleaned_kg" value="{{ old('uncleaned_kg', $cust_clean->uncleaned_kg) }}" min="0.01" required>
                            <div class="form-text text-muted">Amount of uncleaned seeds taken from the batch for this operation.</div>
                        </div>

                        <div class="mb-3">
                            <label for="makapi_kg" class="form-label">Makapi (Waste) Produced (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="makapi_kg" name="makapi_kg"
                                value="{{ old('makapi_kg', $cust_clean->makapi_kg) }}" min="0" required>
                            <div class="form-text text-muted">Weight of waste/chaff from this cleaning.</div>
                        </div>

                        <div class="mb-3">
                            <label for="cleaned_display" class="form-label">Cleaned Seeds Output (kg)</label>
                            <input type="text" class="form-control" id="cleaned_display" value="{{ number_format(old('cleaned', $cust_clean->cleaned), 2) }}" readonly>
                            <div class="form-text text-muted">Calculated amount of clean seeds produced.</div>
                        </div>

                        <hr>
                        <h5 class="mt-4 mb-3">Electricity Usage (Optional)</h5>
                        <div class="mb-3">
                            <label for="initial_units" class="form-label">Initial Electricity Unit Reading</label>
                            <input type="number" step="0.01" class="form-control" id="initial_units" name="initial_units"
                                value="{{ old('initial_units', $cust_clean->initial_units) }}" min="0">
                            <div class="form-text text-muted">Enter the meter reading at the start of the operation.</div>
                        </div>

                        <div class="mb-3">
                            <label for="final_units" class="form-label">Final Electricity Unit Reading</label>
                            <input type="number" step="0.01" class="form-control" id="final_units" name="final_units"
                                value="{{ old('final_units', $cust_clean->final_units) }}" min="0">
                            <div class="form-text text-muted">Enter the meter reading at the end of the operation.</div>
                        </div>

                        <div class="mb-3">
                            <label for="unit_used_display" class="form-label">Units Used (Calculated)</label>
                            <input type="text" class="form-control" id="unit_used_display" value="{{ number_format(old('unit_used', $cust_clean->unit_used), 2) }}" readonly>
                        </div>

                        <button type="submit" class="btn btn-success">Update Cleaning</button>
                        <a href="{{ route('cust_clean.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const custAliIdSelect = document.getElementById('cust_ali_id');
        const selectedBatchStockInfo = document.getElementById('selected_batch_stock_info');
        const uncleanedInputKg = document.getElementById('uncleaned_kg');
        const makapiKg = document.getElementById('makapi_kg');
        const cleanedDisplay = document.getElementById('cleaned_display');
        const initialUnitsInput = document.getElementById('initial_units');
        const finalUnitsInput = document.getElementById('final_units');
        const unitUsedDisplay = document.getElementById('unit_used_display');

        function updateSelectedBatchStockInfo() {
            const selectedOption = custAliIdSelect.options[custAliIdSelect.selectedIndex];
            const availableUncleaned = selectedOption.getAttribute('data-available-uncleaned');
            if (availableUncleaned) {
                selectedBatchStockInfo.textContent = `Current Uncleaned Stock for this batch: ${availableUncleaned} kg`;
            } else {
                selectedBatchStockInfo.textContent = `Select a batch to see its current uncleaned stock.`;
            }
        }

        function calculateCleanedOutput() {
            const uncleaned = parseFloat(uncleanedInputKg.value) || 0;
            const makapi = parseFloat(makapiKg.value) || 0;
            let cleaned = uncleaned - makapi;
            if (cleaned < 0) {
                cleaned = 0;
            }
            cleanedDisplay.value = cleaned.toFixed(2);
        }

        function calculateUnitsUsed() {
            const initial = parseFloat(initialUnitsInput.value);
            const final = parseFloat(finalUnitsInput.value);

            if (!isNaN(initial) && !isNaN(final)) {
                const unitsUsed = initial - final;
                unitUsedDisplay.value = Math.max(0, unitsUsed).toFixed(2);
            } else {
                unitUsedDisplay.value = '0.00';
            }
        }

        custAliIdSelect.addEventListener('change', updateSelectedBatchStockInfo);
        uncleanedInputKg.addEventListener('input', calculateCleanedOutput);
        makapiKg.addEventListener('input', calculateCleanedOutput);
        initialUnitsInput.addEventListener('input', calculateUnitsUsed);
        finalUnitsInput.addEventListener('input', calculateUnitsUsed);

        updateSelectedBatchStockInfo(); 
        calculateCleanedOutput(); 
        calculateUnitsUsed(); 
    });
</script>
@endsection