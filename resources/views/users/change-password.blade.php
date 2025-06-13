@extends('layouts.appw')

@section('content')
<div class="container">

    <h4 class="text-center text-primary font-weight-bold mb-4 text-uppercase">Change Password</h4>

    <form id="changePasswordForm" method="POST" action="{{ route('change-password.update') }}">
        @csrf

        <div class="form-group">
            <label for="current_password" class="form-label"><i class="fas fa-lock"></i> Current Password</label>
            <input type="password" name="current_password" id="current_password" class="form-control">
            @error('current_password')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="new_password" class="form-label"><i class="fas fa-key"></i> New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control">
            @error('new_password')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="new_password_confirmation" class="form-label"><i class="fas fa-check-double"></i> Confirm New Password</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control">
            @error('new_password_confirmation')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary mt-3">
                <i class="fas fa-key"></i> Change Password
            </button>
            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const newPasswordConfirmation = document.getElementById('new_password_confirmation').value;

        if (!currentPassword || !newPassword || !newPasswordConfirmation) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });
            return; // Stop form submission if any field is empty
        }

        this.submit(); // Submit the form if all fields are filled
    });

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
    }).then(() => {
        window.location.href = "{{ route('dashboard.index') }}"; // Redirect on success
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
    });
    @endif
});
</script>
@endsection