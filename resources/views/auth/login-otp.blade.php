@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card mx-auto" style="max-width:400px;">
        <div class="card-body">
            <h4 class="mb-3">Verify OTP</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @error('otp')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <form method="POST" action="{{ route('login.otp.verify') }}">
                @csrf

                <div class="mb-3">
                    <label>Enter OTP</label>
                    <input type="text" name="otp" class="form-control" maxlength="4" required>
                </div>

                <button class="btn btn-primary w-100">Verify & Login</button>
            </form>
        </div>
    </div>
</div>
@endsection