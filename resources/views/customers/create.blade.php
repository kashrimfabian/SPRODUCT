@extends('layouts.appw') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center mb-4">Add New Customer</h4>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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

            <div class="card">
                <div class="card-header">Customer Details</div>
                <div class="card-body">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                value="{{ old('first_name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name (Optional)</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                value="{{ old('last_name') }}">
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                value="{{ old('phone_number') }}" required>
                            <div class="form-text text-muted">Must be unique (e.g., +2557XXXXXXXX).</div>
                        </div>

                        <button type="submit" class="btn btn-success">Add Customer</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection