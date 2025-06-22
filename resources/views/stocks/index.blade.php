@extends('layouts.appw')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h4 class="text-center text-dark text-uppercase mb-4">
            Stock list records
        </h4>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>S/N</th>
                    <th>Batch_No</th>
                    <th>Uncleaned_Kgs</th>
                    <th>Cleaned_kgs
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
                    <td>{{ $stock->cleaned_kgm }}</td>
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