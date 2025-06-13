@extends('layouts.appw')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">User Management</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3"> <i class="bi bi-person-plus"></i> Add User</a>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light text-center">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                    <th>Reset_Password</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->first_name }} {{$user->middle_name}} {{ $user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role ? $user->role->name : 'No Role Assigned' }}</td>
                    <td class="text-center">
                        @if ($user->status === 1)
                        <span class="badge bg-success py-2 px-3 rounded">Active</span>
                        @else
                        <span class="badge bg-danger py-2 px-3 rounded">Inactive</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($user->status === 1)
                        <form action="{{ route('users.disable', $user->id) }}" method="POST" class="disable-form">
                            @csrf
                            <button type="submit" class="btn btn-danger disable-btn">
                                <i class="fas fa-user-slash"></i> Disable
                            </button>
                        </form>
                        @else
                        <form action="{{ route('users.enable', $user->id) }}" method="POST" class="enable-form">
                            @csrf
                            <button type="submit" class="btn btn-success enable-btn">
                                <i class="fas fa-user-check"></i> Enable
                            </button>
                        </form>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('users.reset-password', $user->id) }}" class="btn btn-primary reset-btn">
                            <i class="fas fa-key"></i> 
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.disable-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to disable this user?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, disable it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        });

        document.querySelectorAll('.enable-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to enable this user?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, enable it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        });

        document.querySelectorAll('.reset-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to reset this user's password?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, reset it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = this.getAttribute('href');
                    }
                });
            });
        });
    });
</script>
@endsection