@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100 bg-dark">
        <div class="card shadow-lg p-4 bg-gray-800 text-white rounded-4" style="max-width: 450px; width: 100%;">
            <div class="card-header bg-danger text-white text-center rounded-top-4">
                <h2 class="mb-0">{{ __('Login Page') }}</h2>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="username" class="form-label">{{ __('Enter Email or Phone Number') }}</label>
                        <input id="username" type="text"
                            class="form-control bg-dark text-white border-0 @error('username') is-invalid @enderror"
                            name="username" value="{{ old('username') }}" required autocomplete="phone" autofocus>
                        @error('username')
                            <div class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password"
                            class="form-control bg-dark text-white border-0 @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password">
                        <span class="position-absolute me-3 translate-middle-y" id="toggle-password"
                            style="cursor: pointer; color: black;">
                            <i class="bi bi-eye"></i>
                        </span>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-light" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <!-- Login Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-danger btn-lg btn-3d">
                            {{ __('Login') }}
                        </button>
                    </div>

                    <!-- Forgot Password -->
                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a class="text-light" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif

                    @guest


                        @if (Route::has('register'))
                            <a class="nav-link text-center" href="{{ route('register') }}">{{ __('Register') }}</a>
                        @endif
                    @else
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>

                    @endguest
                </form>
            </div>
        </div>
    </div>

    <style>
        /* 3D effect for button */
        .btn-3d {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 0 8px 16px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-3d:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3), 0 12px 24px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        .card {
            background: linear-gradient(145deg, #2c2c2c, #1a1a1a);
        }

        .card-header {
            background: linear-gradient(145deg, #d32f2f, #c62828);
        }

        @media (max-width: 576px) {
            .card {
                padding: 2rem;
            }

            .card-header {
                font-size: 1.5rem;
            }
        }

        /* Custom styles for eye icon */
        #toggle-password {
            top: 75%;
            right: 0px;
            transform: translateY(-50%);
            color: black;
        }

        #toggle-password i {
            font-size: 1.25rem;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#toggle-password');
            const passwordInput = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle the eye icon
                this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' :
                    '<i class="bi bi-eye-slash"></i>';
            });
        });
    </script>
@endsection
