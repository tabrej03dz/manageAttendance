@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-white py-5">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
            color: rgba(255, 255, 255, .85);
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
    </style>

    <div class="login-wrapper px-3">

        <div class="text-center">
            <div class="logo-box">
                <img src="{{ asset('asset/img/RVG HRMS COLOUR ICON.png') }}" alt="RVG HRMS Logo">
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success rounded-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="login-card">
            <div class="login-header">
                <h1>Login Page</h1>
                <p>Enter your mobile number to receive OTP</p>
            </div>

            <div class="p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color:#2b214a;">
                            Mobile Number
                        </label>

                        <input type="text"
                               class="form-control"
                               placeholder="Enter Mobile Number"
                               name="phone"
                               value="{{ old('phone') }}"
                               maxlength="10"
                               required>
                    </div>

                    <button type="submit" class="btn login-btn w-100 mt-3">
                        Send OTP
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection