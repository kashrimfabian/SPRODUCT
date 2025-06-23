@extends('layouts.appw') 
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Record New Ukamuaji (Oil Pressing) Operation</h4>

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
                <div class="card-header">New Ukamuaji Record</div>
                <div class="card-body">
                    <form action="{{ route('ukamuaji.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="cust_ali_id" class="form-label">Customer Alizeti Batch</label>
                            <select class="form-select" id="cust_ali_id" name="cust_ali_id" required>
                                <option value="">Select a Customer Batch with Cleaned Seeds</option>
                                @foreach($availableCustAlizetiBatches as $batch)
                                <option value="{{ $batch->cust_ali_id }}" 
                                    {{ old('cust_ali_id') == $batch->cust_ali_id ? 'selected' : '' }}
                                    data-available-cleaned="{{ number_format($batch->customerStock->cleaned_kg ?? 0, 2) }}"
                                >
                                    {{ $batch->batch_no }} (Customer: {{ $batch->customer->first_name ?? '' }} {{ $batch->customer->last_name ?? '' }} - Cleaned Available: {{ number_format($batch->customerStock->cleaned_kg ?? 0, 2) }} kg)
                                </option>
                                @endforeach
                            </select>
                            <div id="selected_batch_stock_info" class="form-text text-muted mt-2">
                                Select a batch to see its current cleaned stock available for pressing.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Recorded By</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', Auth::id()) == $user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tarehe" class="form-label">Operation Date</label>
                            <input type="date" class="form-control datepicker" id="tarehe" name="tarehe"
                                value="{{ old('tarehe', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="cleaned_kg" class="form-label">Cleaned Seeds Input for Pressing (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="cleaned_kg"
                                name="cleaned_kg" value="{{ old('cleaned_kg') }}" min="0.01" required>
                            <div class="form-text text-muted">Amount of cleaned seeds taken from the batch for pressing.</div>
                        </div>

                        <div class="mb-3">
                            <label for="crude_oil" class="form-label">Crude Oil Produced (Liters)</label>
                            <input type="number" step="0.01" class="form-control" id="crude_oil" name="crude_oil"
                                value="{{ old('crude_oil', 0) }}" min="0" required>
                            <div class="form-text text-muted">Volume of crude oil obtained from this pressing.</div>
                        </div>

                        <div class="mb-3">
                            <label for="mashudu_kg" class="form-label">Mashudu (Cake) Produced (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="mashudu_kg" name="mashudu_kg"
                                value="{{ old('mashudu_kg', 0) }}" min="0" required>
                            <div class="form-text text-muted">Weight of sunflower cake byproduct.</div>
                        </div>

                        <hr>
                        <h5 class="mt-4 mb-3">Electricity Usage (Optional)</h5>
                        <div class="mb-3">
                            <label for="initial_units" class="form-label">Initial Electricity Unit Reading</label>
                            <input type="number" step="0.01" class="form-control" id="initial_units" name="initial_units"
                                value="{{ old('initial_units') }}" min="0">
                            <div class="form-text text-muted">Enter the meter reading at the start of the operation.</div>
                        </div>

                        <div class="mb-3">
                            <label for="final_units" class="form-label">Final Electricity Unit Reading</label>
                            <input type="number" step="0.01" class="form-control" id="final_units" name="final_units"
                                value="{{ old('final_units') }}" min="0">
                            <div class="form-text text-muted">Enter the meter reading at the end of the operation.</div>
                        </div>

                        <div class="mb-3">
                            <label for="unit_used_display" class="form-label">Units Used (Calculated)</label>
                            <input type="text" class="form-control" id="unit_used_display" value="0.00" readonly>
                        </div>

                        <button type="submit" class="btn btn-success">Record Ukamuaji</button>
                        <a href="{{ route('ukamuaji.index') }}" class="btn btn-secondary">Cancel</a>
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
        const initialUnitsInput = document.getElementById('initial_units');
        const finalUnitsInput = document.getElementById('final_units');
        const unitUsedDisplay = document.getElementById('unit_used_display');

        function updateSelectedBatchStockInfo() {
            const selectedOption = custAliIdSelect.options[custAliIdSelect.selectedIndex];
            const availableCleaned = selectedOption.getAttribute('data-available-cleaned');
            if (availableCleaned) {
                selectedBatchStockInfo.textContent = `Current Cleaned Stock for this batch: ${availableCleaned} kg`;
            } else {
                selectedBatchStockInfo.textContent = `Select a batch to see its current cleaned stock.`;
            }
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
        initialUnitsInput.addEventListener('input', calculateUnitsUsed);
        finalUnitsInput.addEventListener('input', calculateUnitsUsed);

        updateSelectedBatchStockInfo(); 
        calculateUnitsUsed(); 
    });
</script>
@endsection