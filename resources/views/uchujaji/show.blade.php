@extends('layouts.appw')

@section('content')
<div class="container">
    <h1>View Uchujaji Record</h1>

    <div class="card">
        <div class="card-header">Details</div>
        <div class="card-body">
            <p><strong>Batch Number:</strong> {{ $uchujaji->batch_number }}</p>
            <p><strong>Mafuta Machafi (Lts):</strong> {{ $uchujaji->mafuta_machafi }}</p>
            <p><strong>Mashudu (Kgs):</strong> {{ $uchujaji->mashudu }}</p>
            <p><strong>Created At:</strong> {{ $uchujaji->created_at->format('d-m-Y H:i') }}</p>
            <p><strong>Updated At:</strong> {{ $uchujaji->updated_at->format('d-m-Y H:i') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('uchujaji.edit', $uchujaji->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('uchujaji.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
