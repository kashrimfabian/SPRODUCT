@extends('layouts.appw')

@section('content')
<div class="container">
    <h1>Stock List</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>S/N</th>
                    <th>Batch_No</th>
                    <th>Total_Al_Kgms</th>
                    <th>Mafuta_Masafi</th>
                    <th>Mashudu</th>
                    <th>Mafuta_machafu</th>
                    <th>Ugido(20dumlts)</th>
                    <th>Lami(20dumlts)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $stock->alizeti->batch_no }}</td>
                    <td>{{ $stock->total_al_kgms }}</td>
                    <td>{{ $stock->mafuta_masafi }}</td>
                    <td>{{ $stock->mashudu }}</td>
                    <td>{{ $stock->mafuta_machafu }}</td>
                    <th>{{$stock->ugido}}</th>
                    <th>{{$stock->lami}}</th>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection