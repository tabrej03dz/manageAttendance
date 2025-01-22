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
                                    <input type="text" class="form-control form-control-lg" placeholder="Username"
                                        name="username" required>
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

                                <!-- New User Link -->
                                <div class="mb-3 text-center mt-3">
                                    <a href="javascript:void(0);" class="text-danger text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#newUserModal">New Users</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal New User -->
        <div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="min-height: 400px;">
                    <div class="modal-header border-0 bg-danger text-white text-center">
                        <h5 class="modal-title" id="newUserModalLabel">New User Registration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('request.store') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="compan_name" class="form-label text-dark">Company Name</label>
                                <input type="text" name="compan_name" id="compan_name" class="form-control"
                                    placeholder="Company Name" style="border-radius: 10px;">
                            </div>

                            <div class="mb-3">
                                <label for="owner_name" class="form-label text-dark">Admin Name</label>
                                <input type="text" name="owner_name" id="owner_name" class="form-control"
                                    placeholder="Account Owner / Admin Name" style="border-radius: 10px;">
                            </div>

                            <div class="mb-3">
                                <label for="number" class="form-label text-dark">Mobile No</label>
                                <input type="text" name="number" id="number" inputmode="numeric" class="form-control"
                                    placeholder="Mobile No" style="border-radius: 10px;">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label text-dark">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="example@gmail.com" style="border-radius: 10px;">
                            </div>

                            <div class="mb-3">
                                <label for="company_address" class="form-label text-dark">Company Address</label>
                                <input type="text" name="company_address" id="company_address" class="form-control"
                                    placeholder="Company Address" style="border-radius: 10px;">
                            </div>

                            <div class="mb-3">
                                <label for="emp_size" class="form-label text-dark">Employee Size</label>
                                <select class="form-select" name="emp_size" id="emp_size" style="border-radius: 10px;">
                                    <option value="">Select Employee Size</option>
                                    <option value="0-10">0-10</option>
                                    <option value="10-25">10-25</option>
                                    <option value="25-50">25-50</option>
                                    <option value="50-100">50-100</option>
                                    <option value="100-500">100-500</option>
                                    <option value="500+">500+</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="desgi" class="form-label text-dark">Designation</label>
                                <select class="form-select" name="designation" id="desgi" style="border-radius: 10px;">
                                    <option value="">Select Designation</option>
                                    <option value="Owner">Owner</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Employee">Employee</option>
                                    <option value="HR">HR</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-danger w-100 py-3 mt-3 rounded-3 shadow-lg hover-shadow-lg">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">

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
