@extends('layouts.appw')

@section('content')
<div class="container">
    <h1>Mauzo Details</h1>

    <div class="card">
        <div class="card-header">
            <strong>Sale ID:</strong> {{ $mauzo->id }}
        </div>
        <div class="card-body">
            <p><strong>Tarehe:</strong> {{ $mauzo->tarehe }}</p>
            <p><strong>Mafuta:</strong> {{ $mauzo->mafuta }} Liters</p>
            <p><strong>Price:</strong> {{ number_format($mauzo->price, 2) }} TZS</p>
            <p><strong>Payment Way:</strong> {{ ucfirst($mauzo->payment_way) }}</p>
            <p><strong>Discount:</strong> {{ $mauzo->discount ?? '-' }}</p>
            <p><strong>Debt:</strong> {{ $mauzo->debt ?? '-' }}</p>
            <p><strong>Alizeti Batch:</strong> {{ $mauzo->alizeti->batch_no }}</p>
            <p><strong>User:</strong> {{ $mauzo->user->first_name }} {{ $mauzo->user->middle_name }} {{ $mauzo->user->last_name }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('mauzo.edit', $mauzo->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('mauzo.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
