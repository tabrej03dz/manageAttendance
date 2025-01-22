@extends('layouts.app')

@section('content')
    <div class="bg-white">
        <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6 col-lg-4">
                        <!-- Logo -->
                        <div class="text-center">
                            <div class="mx-auto mb-4" style="width: 250px;">
                                <img src="{{ asset('asset/img/rvg_logo.png') }}" alt="Logo" class="img-fluid"
                                     style="max-width: 250px;" />
                            </div>
                        </div>

                        <!-- Error Display -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <div class="bg-light shadow rounded p-4">
                            <h1 class="text-center mb-4 fw-bold">{{ __('Login Page') }}</h1>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Username Field -->
                                <div class="mb-3">
                                    <input type="text" class="form-control form-control-lg" placeholder="Username" name="username"
                                           required>
                                </div>

                                <!-- Password Field -->
                                <div class="mb-3 position-relative">
                                    <input type="password" id="password" class="form-control form-control-lg"
                                           placeholder="Password" name="password" required>
                                    <span id="eye-icon" class="position-absolute top-50 end-0 translate-middle-y pe-3"
                                          style="cursor: pointer;">
                                        <i class="bi bi-eye"></i> <!-- Eye icon -->
                                    </span>
                                </div>

                                <!-- Login Button -->
                                <button type="submit" class="btn btn-danger btn-lg w-100">LOGIN</button>

                                <!-- Forgot Password -->
                                @if (Route::has('password.request'))
                                    <div class="mb-3 text-center mt-3">
                                        <a href="{{ route('password.request') }}"
                                           class="text-danger text-decoration-none fw-semibold">{{ __('Forgot Your Password?') }}</a>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css"
              rel="stylesheet">

        <script>
            // Toggle password visibility
            const eyeIcon = document.getElementById('eye-icon');
            const passwordField = document.getElementById('password');

            eyeIcon.addEventListener('click', () => {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    eyeIcon.innerHTML = '<i class="bi bi-eye-slash"></i>'; // Change to eye-slash icon
                } else {
                    passwordField.type = "password";
                    eyeIcon.innerHTML = '<i class="bi bi-eye"></i>'; // Change back to eye icon
                }
            });
        </script>
    </div>
@endsection
