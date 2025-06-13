@extends('layouts.appw')

@section('content')
<div class="container">
    <h1>Uzalishaji Details</h1>
    <table class="table mt-4">
        <tbody>
            <tr>
                <th>Tarehe</th>
                <td>{{ $uzalishaji->tarehe }}</td>
            </tr>
            <tr>
                <th>Mafta Machafu (Liters)</th>
                <td>{{ $uzalishaji->mafuta_machafu }}</td>
            </tr>
            <tr>
                <th>Mashudu (Kg)</th>
                <td>{{ $uzalishaji->mashudu }}</td>
            </tr>
            <tr>
                <th>User</th>
                <td>{{ $uzalishaji->user->name }}</td>
            </tr>
            <tr>
                <th>Alizeti</th>
                <td>{{ $uzalishaji->alizeti->name }}</td>
            </tr>
        </tbody>
    </table>
    <a href="{{ route('uzalishas.index') }}" class="btn btn-primary">Back to List</a>
</div>
@endsection
