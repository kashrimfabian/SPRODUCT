@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title text-center mb-4">Edit Uzalishaji</h1>

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
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

            <form action="{{ route('uzalishaji.update', $uzalishaji->uzalishaji_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="tarehe" class="form-label">Select Date</label>
                    <input type="date" id="tarehe" name="tarehe" class="form-control" placeholder="Select a date"
                        value="{{ old('tarehe', $uzalishaji->tarehe) }}" required>
                </div>

                <div class="mb-3">
                    <label for="alizeti_id" class="form-label">Select Batch Number (Available Kg)</label>
                    <select id="alizeti_id" name="alizeti_id" class="form-select" required>
                        <option value="">-- Select Batch --</option>
                        @foreach($alizeti as $batch)
                            @php
                                $stock = \App\Models\Stock::where('alizeti_id', $batch->ali_id)->first();
                                $availableKg = $stock ? $stock->total_al_kgms : 0;
                            @endphp
                            <option value="{{ $batch->ali_id }}" data-available-kg="{{ $availableKg }}" {{ $uzalishaji->alizeti_id == $batch->ali_id ? 'selected' : '' }}>
                                {{ $batch->batch_no }} ({{ $availableKg }} Kg)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="available_kg" class="form-label">Available Alizeti (Kg)</label>
                    <input type="text" id="available_kg" class="form-control" readonly value="{{ old('available_kg', \App\Models\Stock::where('alizeti_id', $uzalishaji->alizeti_id)->first()->total_al_kgms ?? 0) }}">
                </div>

                <div class="mb-3">
                    <label for="alizeti_kgm" class="form-label">Alizeti Used (Kg) - Original: {{ $uzalishaji->alizeti_kgm }}</label>
                    <input type="number" name="alizeti_kgm" id="alizeti_kgm" class="form-control" min="1"
                        value="{{ old('alizeti_kgm', $uzalishaji->alizeti_kgm) }}" required>
                </div>

                <div class="mb-3">
                    <label for="mafuta_machafu" class="form-label">Mafuta Machafu (Liters)</label>
                    <input type="number" name="mafuta_machafu" id="mafuta_machafu" class="form-control" min="1"
                        value="{{ old('mafuta_machafu', $uzalishaji->mafuta_machafu) }}" required>
                </div>

                <div class="mb-3">
                    <label for="mashudu" class="form-label">Mashudu (Kg)</label>
                    <input type="number" name="mashudu" id="mashudu" class="form-control" min="1" value="{{ old('mashudu', $uzalishaji->mashudu) }}" required>
                </div>

                <div class="mb-3">
                    <label for="initial_unit" class="form-label">Initial Electricity Unit</label>
                    <input type="number" name="initial_unit" id="initial_unit" class="form-control" value="{{ old('initial_unit', $uzalishaji->initial_unit) }}"
                        min="0" step="0.01" required>
                </div>

               
                <div class="mb-3">
                    <label for="final_unit" class="form-label">Final Electricity Unit</label>
                    <input type="number" name="final_unit" id="final_unit" class="form-control" value="{{ old('final_unit', $uzalishaji->final_unit) }}"
                        min="0" step="0.01" required>
                </div>


                <div class="mb-3">
                    <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Update</button>
                    <a href="{{ route('uzalishaji.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Back to list</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alizetiSelect = document.getElementById('alizeti_id');
        const availableKgInput = document.getElementById('available_kg');

        function updateAvailableKg() {
            const selectedOption = alizetiSelect.options[alizetiSelect.selectedIndex];
            if (selectedOption.value) {
                availableKgInput.value = selectedOption.dataset.availableKg;
            } else {
                availableKgInput.value = '';
            }
        }

        updateAvailableKg(); // Initialize on page load

        alizetiSelect.addEventListener('change', updateAvailableKg);
    });
</script>
@endsection