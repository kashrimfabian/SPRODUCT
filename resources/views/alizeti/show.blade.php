@extends('layouts.appw')

@section('content')
    <div class="container">
        <h1>Alizeti Details</h1>

        <div class="card">
            <div class="card-header">
                <strong>Batch No: </strong> {{ $alizeti->batch_no }}
            </div>
            <div class="card-body">
                <ul>
                    <li><strong>Date:</strong> {{ $alizeti->tarehe }}</li>
                    <li><strong>Al Kilogram:</strong> {{ $alizeti->al_kilogram }} kg</li>
                    <li><strong>Total Gunia:</strong> {{ $alizeti->gunia_total }} gunia</li>
                    <li><strong>Price per Kilo:</strong> {{ number_format($alizeti->price_per_kilo, 2) }} TZS</li>
                    <li><strong>Total Price:</strong> {{ number_format($alizeti->total_price, 2) }} TZS</li>
                    <li><strong>Mafuta Machafu:</strong> {{ $alizeti->mafuta_machafu }} liters</li>
                    <li><strong>Shudu:</strong> {{ $alizeti->shudu }} kg</li>
                    <li><strong>Mafuta Masafi:</strong> {{ $alizeti->mafuta_masafi }} liters</li>
                    <li><strong>Ugido:</strong> {{ $alizeti->ugido }} kg</li>
                    <li><strong>Created by:</strong> {{ $alizeti->user->name }}</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('alizeti.index') }}" class="btn btn-primary">Back to List</a>
                <a href="{{ route('alizeti.edit', $alizeti->id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('alizeti.destroy', $alizeti->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
