@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-white py-5">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --rvg-blue: #1f7df2;
            --rvg-purple: #2b214a;
            --rvg-violet: #7f35b2;
            --rvg-pink: #e00063;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .logo-box {
            width: 120px;
            height: 120px;
            margin: 0 auto 24px;
            border-radius: 30px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 50px rgba(43, 33, 74, .12);
            border: 1px solid #eef2f7;
        }

        .logo-box img {
            max-width: 90px;
            max-height: 90px;
            object-fit: contain;
        }

        .login-card {
            border-radius: 28px;
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 25px 70px rgba(43, 33, 74, .14);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(90deg, var(--rvg-blue), var(--rvg-violet), var(--rvg-pink));
            padding: 28px 24px;
            text-align: center;
            color: #fff;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
        }

        .login-header p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, .8);
            font-size: 14px;
        }

        .form-control {
            height: 54px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-weight: 600;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: var(--rvg-pink);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(224, 0, 99, .10) !important;
        }

        .login-btn {
            height: 54px;
            border-radius: 999px;
            border: 0;
            background: linear-gradient(90deg, var(--rvg-blue), var(--rvg-violet), var(--rvg-pink));
            color: #fff;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .login-btn:hover {
            color: #fff;
            opacity: .95;
        }

        .theme-link {
            color: var(--rvg-pink);
            font-weight: 700;
            text-decoration: none;
        }

        .theme-link:hover {
            color: var(--rvg-purple);
        }

        #eye-icon {
            cursor: pointer;
            color: var(--rvg-purple);
        }
    </style>

    <div class="login-wrapper px-3">

        {{-- Logo --}}
        <div class="text-center">
            <div class="logo-box">
                <img src="{{ asset('asset/img/RVG HRMS COLOUR ICON.png') }}" alt="RVG HRMS Logo">
            </div>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Login Card --}}
        <div class="login-card">
            <div class="login-header">
                <h1>Login Page</h1>
                <p>Login to your RVG HRMS account</p>
            </div>

            <div class="p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color:#2b214a;">
                            Email or Mobile No
                        </label>
                        <input type="text"
                               class="form-control"
                               placeholder="Email or Mobile No"
                               name="username"
                               value="{{ old('username') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color:#2b214a;">
                            Password
                        </label>

                        <div class="position-relative">
                            <input type="password"
                                   id="password"
                                   class="form-control pe-5"
                                   placeholder="Password"
                                   name="password"
                                   required>

                            <span id="eye-icon"
                                  class="position-absolute top-50 end-0 translate-middle-y pe-3">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn login-btn w-100 mt-3">
                        Login
                    </button>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-4">
                            <a href="{{ route('password.request') }}" class="theme-link">
                                Forgot Your Password?
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const eyeIcon = document.getElementById('eye-icon');
            const passwordField = document.getElementById('password');

            if (eyeIcon && passwordField) {
                eyeIcon.addEventListener('click', function () {
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        eyeIcon.innerHTML = '<i class="bi bi-eye-slash"></i>';
                    } else {
                        passwordField.type = 'password';
                        eyeIcon.innerHTML = '<i class="bi bi-eye"></i>';
                    }
                });
            }
        });
    </script>
</div>
@endsection