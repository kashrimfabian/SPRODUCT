@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Edit Oil Filtering Operation</h4>

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
                <div class="card-header">Edit Filtering Record (ID: {{ $filtering->filter_id }})</div> {{-- Updated model name --}}
                <div class="card-body">
                    <form action="{{ route('filtering.update', $filtering->filter_id) }}" method="POST"> {{-- Updated route name and model --}}
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="cust_ali_id" class="form-label">Customer Alizeti Batch</label>
                            <select class="form-select" id="cust_ali_id" name="cust_ali_id" required>
                                <option value="">Select a Customer Batch</option>
                                @foreach($availableCustAlizetiBatches as $batch)
                                <option value="{{ $batch->cust_ali_id }}" 
                                    {{ old('cust_ali_id', $filtering->cust_ali_id) == $batch->cust_ali_id ? 'selected' : '' }} {{-- Updated model name --}}
                                    data-available-crude-oil="{{ number_format($batch->customerStock->crude_oil ?? 0, 2) }}"
                                >
                                    {{ $batch->batch_no }} (Customer: {{ $batch->customer->first_name ?? '' }} {{ $batch->customer->last_name ?? '' }} - Crude Oil Available: {{ number_format($batch->customerStock->crude_oil ?? 0, 2) }} Ltr)
                                </option>
                                @endforeach
                            </select>
                            <div id="selected_batch_stock_info" class="form-text text-muted mt-2">
                                Select a batch to see its current crude oil stock available for filtering.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Recorded By</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $filtering->user_id) == $user->id ? 'selected' : '' }}> {{-- Updated model name --}}
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tarehe" class="form-label">Operation Date</label>
                            <input type="date" class="form-control" id="tarehe" name="tarehe"
                                value="{{ old('tarehe', $filtering->tarehe) }}" required> {{-- Updated model name --}}
                        </div>

                        <div class="mb-3">
                            <label for="crude_oil" class="form-label">Crude Oil Input for Filtering (Liters)</label> {{-- Updated name --}}
                            <input type="number" step="0.01" class="form-control" id="crude_oil" name="crude_oil" {{-- Updated name --}}
                                value="{{ old('crude_oil', $filtering->crude_oil) }}" min="0.01" required> {{-- Updated name --}}
                            <div class="form-text text-muted">Volume of crude oil taken from the batch for filtering.</div>
                        </div>

                        <div class="mb-3">
                            <label for="refined_oil" class="form-label">Refined Oil Produced (Liters)</label> {{-- Updated name --}}
                            <input type="number" step="0.01" class="form-control" id="refined_oil" name="refined_oil" {{-- Updated name --}}
                                value="{{ old('refined_oil', $filtering->refined_oil) }}" min="0" required> {{-- Updated name --}}
                            <div class="form-text text-muted">Volume of refined oil obtained from this filtering.</div>
                        </div>

                        <div class="mb-3">
                            <label for="lami_kg" class="form-label">Lami (Waste) Produced (kg)</label> {{-- Updated name --}}
                            <input type="number" step="0.01" class="form-control" id="lami_kg" name="lami_kg" {{-- Updated name --}}
                                value="{{ old('lami_kg', $filtering->lami_kg) }}" min="0" required> {{-- Updated name --}}
                            <div class="form-text text-muted">Weight of lami/sludge byproduct.</div>
                        </div>

                        <div class="mb-3">
                            <label for="ugido_kg" class="form-label">Ugido Produced (kg)</label> {{-- Updated name --}}
                            <input type="number" step="0.01" class="form-control" id="ugido_kg" name="ugido_kg" {{-- Updated name --}}
                                value="{{ old('ugido_kg', $filtering->ugido_kg) }}" min="0" required> {{-- Updated name --}}
                            <div class="form-text text-muted">Weight of ugido byproduct.</div>
                        </div>

                        <hr>
                        <h5 class="mt-4 mb-3">Electricity Usage (Optional)</h5>
                        <div class="mb-3">
                            <label for="initial_units" class="form-label">Initial Electricity Unit Reading</label>
                            <input type="number" step="0.01" class="form-control" id="initial_units" name="initial_units"
                                value="{{ old('initial_units', $filtering->initial_units) }}" min="0"> {{-- Updated model name --}}
                            <div class="form-text text-muted">Enter the meter reading at the start of the operation.</div>
                        </div>

                        <div class="mb-3">
                            <label for="final_units" class="form-label">Final Electricity Unit Reading</label>
                            <input type="number" step="0.01" class="form-control" id="final_units" name="final_units"
                                value="{{ old('final_units', $filtering->final_units) }}" min="0"> {{-- Updated model name --}}
                            <div class="form-text text-muted">Enter the meter reading at the end of the operation.</div>
                        </div>

                        <div class="mb-3">
                            <label for="unit_used_display" class="form-label">Units Used (Calculated)</label>
                            <input type="text" class="form-control" id="unit_used_display" value="{{ number_format(old('unit_used', $filtering->unit_used), 2) }}" readonly> {{-- Updated model name --}}
                        </div>

                        <hr>
                        <div class="mb-3">
                            <label for="cost_used" class="form-label">Cost Used (e.g., in TZS)</label>
                            <input type="number" step="0.01" class="form-control" id="cost_used" name="cost_used"
                                value="{{ old('cost_used', $filtering->cost_used) }}" min="0" required> {{-- Updated model name --}}
                            <div class="form-text text-muted">Direct costs associated with this filtering operation.</div>
                        </div>

                        <button type="submit" class="btn btn-success">Update Filtering</button>
                        <a href="{{ route('filtering.index') }}" class="btn btn-secondary">Cancel</a> {{-- Updated route name --}}
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
            const availableCrudeOil = selectedOption.getAttribute('data-available-crude-oil');
            if (availableCrudeOil) {
                selectedBatchStockInfo.textContent = `Current Crude Oil Stock for this batch: ${availableCrudeOil} Ltr`;
            } else {
                selectedBatchStockInfo.textContent = `Select a batch to see its current crude oil stock.`;
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