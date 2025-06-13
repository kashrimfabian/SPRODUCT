@extends('layouts.appw')

@section('content')
<div class="container">
    <h4
        style="text-align: center; color: #3490dc; font-family: 'Arial', sans-serif; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;">
        Uchujaji Records</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between mb-3">
        <div class="col-12 col-md-auto mb-2 mb-md-0">
            <a href="{{ route('uchujaji.create') }}" class="btn btn-success w-100 w-md-auto">
                <i class="fas fa-plus"></i> Add Record
            </a>
        </div>

        <form action="{{ route('uchujaji.index') }}" method="GET" class="row gx-2 gy-2 align-items-center">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                    <select name="alizeti_id" id="alizeti_id" class="form-select">
                        <option value="">All Batches</option>
                        @foreach($uniqueBatches as $batch)
                        <option value="{{ $batch['alizeti_id'] }}"
                            {{ request('alizeti_id') == $batch['alizeti_id'] ? 'selected' : '' }}>
                            {{ $batch['batch_no'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" name="start_date" id="start_date" class="form-control datepicker"
                        value="{{ request('start_date') }}" placeholder="start date">
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" name="end_date" id="end_date" class="form-control datepicker"
                        value="{{ request('end_date') }}" placeholder="end date">
                </div>
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary mb-2 mb-md-0"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('uchujaji.index') }}" class="btn btn-secondary mb-2 mb-md-0"><i
                        class="fas fa-undo"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>S/N</th>
                    <th>User_Name</th>
                    <th>Tarehe</th>
                    <th>Batch_No</th>
                    <th>Mafuta Machafu(20DumLts)</th>
                    <th>Mafuta Masafi(Lts)</th>
                    <th>Ugido(20DumLts)</th>
                    <th>Lami(20DumLts)</th>
                    <th>Initial Unit</th> 
                    <th>Final Unit</th> 
                    <th>Units Used</th> 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($uchujaji as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        @if ($item->user)
                        {{ $item->user->first_name }}
                        @if ($item->user->middle_name)
                        {{ $item->user->middle_name }}
                        @endif
                        @else
                        N/A
                        @endif
                    </td>

                    <td>{{ $item->tarehe }}</td>
                    <td>{{ $item->alizeti->batch_no }}</td>
                    <td>{{ number_format($item->mafuta_machafu, 2) }}</td>
                    <td>{{ number_format($item->mafuta_masafi, 2) }}</td>
                    <td>{{ number_format($item->ugido, 2) }}</td>
                    <td>{{ number_format($item->lami, 2) }}</td>
                    <td>{{ number_format($item->initial_unit, 2) }}</td> 
                    <td>{{ number_format($item->final_unit, 2) }}</td> 
                    <td>{{ number_format($item->units_used, 2) }}</td>
                    <td>
                        <div style="display: flex; justify-content: center; align-items: center; padding: 5px;">
                            <a href="{{ route('uchujaji.edit', $item->uchujaji_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center py-4">No uchujaji records found.</td>
                    
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Totals:</strong></td>
                    <td><strong>{{ number_format($uchujaji->sum('mafuta_machafu'), 2) }}</strong></td>
                    <td><strong>{{ number_format($uchujaji->sum('mafuta_masafi'), 2) }}</strong></td>
                    <td><strong>{{ number_format($uchujaji->sum('ugido'), 2) }}</strong></td>
                    <td><strong>{{ number_format($uchujaji->sum('lami'), 2) }}</strong></td>
                    <td></td> {{-- Empty for Initial_Unit Total (not summed) --}}
                    <td></td> {{-- Empty for Final_Unit Total (not summed) --}}
                    <td><strong>{{ number_format($uchujaji->sum('units_used'), 2) }}</strong></td>
                    {{-- Total for Units_Used --}}
                    <td></td> {{-- Empty for Actions Total --}}
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection