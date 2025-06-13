@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title text-center mb-4">Edit Uchujaji Record</h1>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('uchujaji.update', $uchujaji->uchujaji_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="tarehe" class="form-label">Date</label>
                    <input type="date" name="tarehe" id="tarehe" class="form-control" value="{{ old('tarehe', $uchujaji->tarehe) }}" readonly>
                    <small class="text-muted">Date cannot be edited.</small>
                </div>

                <div class="mb-3">
                    <label for="alizeti_id" class="form-label">Batch Number</label>
                    <select name="alizeti_id" id="alizeti_id" class="form-select" readonly>
                        @foreach ($alizeti as $batch)
                            <option value="{{ $batch->ali_id }}" {{ $batch->ali_id == $uchujaji->alizeti_id ? 'selected' : '' }}>
                                {{ $batch->batch_no }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Batch number cannot be edited.</small>
                </div>

                <div class="mb-3">
                    <label for="mafuta_machafu" class="form-label">Mafuta Machafu (Lts)</label>
                    <input type="number" name="mafuta_machafu" id="mafuta_machafu" class="form-control" value="{{ old('mafuta_machafu', $uchujaji->mafuta_machafu) }}" required>
                </div>

                <div class="mb-3">
                    <label for="mafuta_masafi" class="form-label">Mafuta Masafi (Lts)</label>
                    <input type="number" name="mafuta_masafi" id="mafuta_masafi" class="form-control" value="{{ old('mafuta_masafi', $uchujaji->mafuta_masafi) }}" required>
                </div>

                <div class="mb-3">
                    <label for="ugido" class="form-label">Ugido (Kgs)</label>
                    <input type="number" name="ugido" id="ugido" class="form-control" value="{{ old('ugido', $uchujaji->ugido) }}" required>
                </div>

                <div class="mb-3">
                    <label for="lami" class="form-label">lami (Kgs)</label>
                    <input type="number" name="lami" id="ugido" class="form-control" value="{{ old('lami', $uchujaji->lami) }}" required>
                </div>

                
                <div class="mb-3">
                    <label for="initial_unit" class="form-label">Initial Electricity Unit</label>
                    <input type="number" name="initial_unit" id="initial_unit" class="form-control" value="{{ old('initial_unit',$uchujaji->initial_unit) }}"
                        min="0" step="0.01" required>
                </div>

               
                <div class="mb-3">
                    <label for="final_unit" class="form-label">Final Electricity Unit</label>
                    <input type="number" name="final_unit" id="final_unit" class="form-control" value="{{ old('final_unit',$uchujaji->final_unit) }}"
                        min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('uchujaji.index') }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection