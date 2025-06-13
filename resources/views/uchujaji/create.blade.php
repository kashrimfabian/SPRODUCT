@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title text-center mb-4">Add Uchujaji Record</h1>

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

            <form action="{{ route('uchujaji.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="tarehe" class="form-label">Select Date</label>
                    <input type="date" id="tarehe" name="tarehe" class="form-control datepicker"
                        value="{{ old('tarehe') }}" required placeholder="select date">
                </div>

                <div class="mb-3">
                    <label for="alizeti_id" class="form-label">Select Batch Number (Available Mafuta Machafu)</label>
                    <select id="alizeti_id" name="alizeti_id" class="form-select" required>
                        <option value="">-- Select Batch --</option>
                        @foreach($availableAlizeti as $alizeti)
                        <option value="{{ $alizeti->ali_id }}"
                            data-available-machafu="{{ $alizeti->stock->mafuta_machafu ?? 0 }}">
                            {{ $alizeti->batch_no }} ({{ $alizeti->stock->mafuta_machafu ?? 0 }} 20Lts)
                        </option>
                        @endforeach
                    </select>
                </div>


                <div class="mb-3">
                    <label for="mafuta_machafu" class="form-label">Mafuta Machafu (20 Dum Lts)</label>
                    <input type="number" name="mafuta_machafu" class="form-control" value="{{ old('mafuta_machafu') }}"
                        min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="mafuta_masafi" class="form-label">Mafuta Masafi (Lts)</label>
                    <input type="number" name="mafuta_masafi" class="form-control" value="{{ old('mafuta_masafi') }}"
                        min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="ugido" class="form-label">Ugido (20 Dum Lts)</label>
                    <input type="number" name="ugido" class="form-control" value="{{ old('ugido') }}" min="0"
                        step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="lami" class="form-label">Lami (20 Dum Lts)</label>
                    <input type="number" name="lami" class="form-control" value="{{ old('lami') }}" min="0"
                        step="0.01" required>
                </div>

              
                <div class="mb-3">
                    <label for="initial_unit" class="form-label">Initial Electricity Unit</label>
                    <input type="number" name="initial_unit" id="initial_unit" class="form-control" value="{{ old('initial_unit') }}"
                        min="0" step="0.01" required>
                </div>

               
                <div class="mb-3">
                    <label for="final_unit" class="form-label">Final Electricity Unit</label>
                    <input type="number" name="final_unit" id="final_unit" class="form-control" value="{{ old('final_unit') }}"
                        min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Submit</button>
                    <a href="{{ route('uchujaji.index') }}" class="btn btn-secondary mt-3"><i
                            class="fas fa-arrow-left "></i>Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#alizeti_id').change(function() {
        var availableMafutaMachafu = $(this).find(':selected').data('availableMachafu');
        
    });
});
</script>
@endsection