@extends('layouts.appw')

@section('content')
<div class="container">
    <h4
        style="text-align: center; color: #3490dc; font-family: 'Arial', sans-serif; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;">
        Sunflower Processing Records</h4>

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

    <div class="d-flex flex-column flex-md-row justify-content-between mb-3">
        <div class="col-12 col-md-auto mb-2 mb-md-0">
            <a href="{{ route('uzalishaji.create') }}" class="btn btn-success w-100 w-md-auto">
                <i class="fas fa-plus"></i> Add Record
            </a>
        </div>

        <form action="{{ route('uzalishaji.index') }}" method="GET" class="row gx-2 gy-2 align-items-center">
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
                    <input type="date" name="start_date" id="start_date"class="form-control datepicker"
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
                <a href="{{ route('uzalishaji.index') }}" class="btn btn-secondary mb-2 mb-md-0"><i class="fas fa-undo"></i>
                    Reset</a>
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
                    <th>Alizeti kilo(kgs)</th>
                    <th>Mafta Machafu(20lt_Dumu)</th>
                    <th>Mashudu(Kgs)</th>
                    <th>Initial Units</th>
                    <th>Final Units</th>
                    <th>Units Used</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalAlizetiKilo = 0;
                $totalMafutaMachafu = 0;
                $totalMashudu = 0;
                @endphp
                @foreach($uzalishaji as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->user->first_name }}
                        @if($item->user->middle_name)
                        @endif
                        {{ $item->user->last_name }}
                    </td>
                    <td>{{ $item->tarehe }}</td>
                    <td>{{ $item->alizeti->batch_no }}</td>
                    <td>{{$item->alizeti_kgm}}</td>
                    <td>{{ $item->mafuta_machafu }}</td>
                    <td>{{ $item->mashudu }}</td>
                    <td>{{ $item->initial_unit }}</td>
                    <td>{{ $item->final_unit }}</td>
                    <td>{{ $item->units_used }}</td>
                    

                    <td>
                        <div style="display: flex; justify-content: center; align-items: center; padding: 5px;">
                            <a href="{{ route('uzalishaji.edit', $item->uzalishaji_id) }}"
                                class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @php
                $totalAlizetiKilo += $item->alizeti_kilo;
                $totalMafutaMachafu += $item->mafuta_machafu;
                $totalMashudu += $item->mashudu;
                @endphp
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" style=" font-weight: bold;">Totals:</td>
                    <td style="font-weight: bold;">{{ $totalAlizetiKilo }}</td>
                    <td style="font-weight: bold;">{{ $totalMafutaMachafu }}</td>
                    <td style="font-weight: bold;">{{ $totalMashudu }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection