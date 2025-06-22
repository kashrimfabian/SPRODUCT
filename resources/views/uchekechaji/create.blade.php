@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="container">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Record New Uchekechaji Operation</h4>

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
                <div class="card-header">New Uchekechaji Record</div>
                <div class="card-body">
                    <form action="{{ route('uchekechaji.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="tarehe" class="form-label">Operation Date</label>
                            <input type="date" class="form-control datepicker" id="tarehe" name="tarehe"
                                value="{{ old('tarehe', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="alizeti_id" class="form-label">Source Alizeti Batch</label>
                            <select class="form-select" id="alizeti_id" name="alizeti_id" required>
                                <option value="">Select an Alizeti batch</option>
                                @foreach($availableAlizetiBatches as $batch)
                                <option value="{{ $batch->ali_id }}"
                                    {{ old('alizeti_id') == $batch->ali_id ? 'selected' : '' }}>
                                    {{ $batch->batch_no }} (Available: {{ number_format($batch->stock->total_al_kgms, 2) }} kg)
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="uncleaned_amount" class="form-label">Uncleaned Input Amount (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="uncleaned_amount"
                                name="uncleaned_amount" value="{{ old('uncleaned_amount') }}" min="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label for="makapi_amount" class="form-label">Makapi (Waste) Amount (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="makapi_amount"
                                name="makapi_amount" value="{{ old('makapi_amount', 0) }}" min="0" required>
                        </div>


                        <div class="mb-3">
                            <label for="cleaned_amount_display" class="form-label">Cleaned Output (kg)</label>
                            <input type="text" class="form-control" id="cleaned_amount_display" value="0.00" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="initial_unit" class="form-label">Initial Electricity Unit Reading</label>
                            <input type="number" step="0.01" class="form-control" id="initial_unit" name="initial_unit"
                                value="{{ old('initial_unit') }}" min="0">
                            
                        </div>

                        <div class="mb-3">
                            <label for="final_unit" class="form-label">Final Electricity Unit Reading</label>
                            <input type="number" step="0.01" class="form-control" id="final_unit" name="final_unit"
                                value="{{ old('final_unit') }}" min="0">
                            
                        </div>

                        <div class="mb-3">
                            <label for="units_used_display" class="form-label">Units Used</label>
                            <input type="text" class="form-control" id="units_used_display" value="0.00" readonly>
                            
                        </div>
                        
                        <button type="submit" class="btn btn-success">Save</button>
                        <a href="{{ route('uchekechaji.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uncleanedAmountInput = document.getElementById('uncleaned_amount');
    const makapiAmountInput = document.getElementById('makapi_amount');
    const cleanedAmountDisplay = document.getElementById('cleaned_amount_display');

    const initialUnitInput = document.getElementById('initial_unit');
    const finalUnitInput = document.getElementById('final_unit');
    const unitsUsedDisplay = document.getElementById('units_used_display');

    function calculateCleanedAmount() {
        const uncleaned = parseFloat(uncleanedAmountInput.value) || 0;
        const makapi = parseFloat(makapiAmountInput.value) || 0;

        let cleaned = uncleaned - makapi;
        if (cleaned < 0) {
            cleaned = 0;
        }
        cleanedAmountDisplay.value = cleaned.toFixed(2);
    }

    function calculateUnitsUsed() {
            const initial = parseFloat(initialUnitInput.value);
            const final = parseFloat(finalUnitInput.value);
            
            if (!isNaN(initial) && !isNaN(final)) {
                const unitsUsed = initial - final;                 
                unitsUsedDisplay.value = Math.max(0, unitsUsed).toFixed(2); 
            } else {
                unitsUsedDisplay.value = '0.00';
            }
    }
    
    uncleanedAmountInput.addEventListener('input', calculateCleanedAmount);
    makapiAmountInput.addEventListener('input', calculateCleanedAmount);
    initialUnitInput.addEventListener('input', calculateUnitsUsed);
    finalUnitInput.addEventListener('input', calculateUnitsUsed);
    
    calculateCleanedAmount();
    calculateUnitsUsed();
});
</script>
@endsection