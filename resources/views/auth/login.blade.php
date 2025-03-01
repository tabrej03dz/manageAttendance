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
                                    <input type="text" class="form-control form-control-lg"
                                        placeholder="Email or Mobile No" name="username" required>
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
                                <!-- New User Link -->
                                <div class="mb-3 text-center mt-3">
                                    <a href="javascript:void(0);" class="text-danger text-decoration-none fw-semibold"
                                        data-bs-toggle="modal" data-bs-target="#newUserModal">New Users</a>
                                </div>
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

        <!-- Modal New User -->

        <!-- Bootstrap & jQuery (Si no están ya incluidos) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Modal del Formulario -->
        <div class="modal fade" id="newUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">New User Registration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form id="requestDemoForm">
                            @csrf

                            <!-- Campo: Company Name -->
                            <div class="mb-3">
                                <label class="form-label">Company Name:</label>
                                <input type="text" class="form-control" name="company_name"
                                    placeholder="Enter Company Name">
                                <p class="text-danger error-company_name"></p>
                            </div>

                            <!-- Campo: Owner Name -->
                            <div class="mb-3">
                                <label class="form-label">Admin <span class="text-danger">*</span>:</label>
                                <input type="text" class="form-control" name="owner_name" placeholder="Admin Name"
                                    required>
                                <p class="text-danger error-owner_name"></p>
                            </div>

                            <!-- Campo: Mobile Number -->
                            <div class="mb-3">
                                <label class="form-label">Mobile No <span class="text-danger">*</span>:</label>
                                <input type="text" class="form-control" name="number" placeholder="Mobile No" required>
                                <p class="text-danger error-number"></p>
                            </div>

                            <!-- Campo: Email -->
                            <div class="mb-3">
                                <label class="form-label">Email:</label>
                                <input type="email" class="form-control" name="email" placeholder="example@gmail.com">
                                <p class="text-danger error-email"></p>
                            </div>

                            <!-- Campo: Pin Code -->
                            <div class="mb-3">
                                <label class="form-label">Pin Code:</label>
                                <input type="number" class="form-control" name="pin_code" placeholder="Pin Code" required>
                                <p class="text-danger error-pin_code"></p>
                            </div>

                            <!-- Campo: Company Address -->
                            <div class="mb-3">
                                <label class="form-label">Company Address:</label>
                                <input type="text" class="form-control" name="company_address"
                                    placeholder="Company Address">
                                <p class="text-danger error-company_address"></p>
                            </div>

                            <!-- Campo: Employee Size -->
                            <div class="mb-3">
                                <label class="form-label">Employee Size:</label>
                                <select class="form-control" name="emp_size">
                                    <option value="">Select Employee Size</option>
                                    <option value="0-10">0-10</option>
                                    <option value="10-25">10-25</option>
                                    <option value="25-50">25-50</option>
                                    <option value="50-100">50-100</option>
                                    <option value="100-500">100-500</option>
                                    <option value="500+">500+</option>
                                </select>
                                <p class="text-danger error-emp_size"></p>
                            </div>

                            <!-- Campo: Designation -->
                            <div class="mb-3">
                                <label class="form-label">Designation:</label>
                                <select class="form-control" name="designation">
                                    <option value="">Select Designation</option>
                                    <option value="Owner">Owner</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Employee">Employee</option>
                                    <option value="HR">HR</option>
                                    <option value="Other">Other</option>
                                </select>
                                <p class="text-danger error-designation"></p>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">
                                    <span id="submitText">Submit</span>
                                    <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Éxito -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-body p-4">
                        <h3 class="text-success">Success!</h3>
                        <p>Your request has been submitted successfully.</p>
                        <button class="btn btn-success" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#requestDemoForm').on('submit', function(e) {
                    e.preventDefault();
                    let formData = $(this).serialize();

                    // Mostrar mensaje de "Submitting..." y spinner
                    $('#submitText').text('Submitting...');
                    $('#loadingSpinner').removeClass('d-none');

                    $.ajax({
                        url: "{{ route('request.store') }}",
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        beforeSend: function() {
                            $('p.text-danger').text('');
                            $('#formErrors').addClass('d-none');
                            $('#errorList').html('');
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#newUserModal').modal('hide'); // Oculta el formulario
                                $('#successModal').modal('show'); // Muestra el modal de éxito
                                $('#requestDemoForm')[0].reset(); // Limpia el formulario
                            }
                        },
                        error: function(xhr) {
                            $('#submitText').text('Submit');
                            $('#loadingSpinner').addClass('d-none');

                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $('#formErrors').removeClass('d-none');
                                $.each(errors, function(key, value) {
                                    $('.error-' + key).text(value[0]);
                                    $('#errorList').append('<li>' + value[0] + '</li>');
                                });
                            } else {
                                alert("Something went wrong. Please try again.");
                            }
                        },
                        complete: function() {
                            $('#submitText').text('Submit');
                            $('#loadingSpinner').addClass('d-none');
                        }
                    });
                });
            });
        </script>


        <!-- Modal de Agradecimiento -->
        <div id="thankYouModal" class="hidden fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75">
            {{-- <div class="bg-white p-6 rounded-lg text-center">
        <h2 class="text-2xl font-semibold text-green-600">Thank you!</h2>
        <p>Your request has been submitted successfully.</p>
        <button id="closeThankYou" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">OK</button>
    </div> --}}
        </div>

        <script>
            $(document).ready(function() {
                $('#requestDemoForm').on('submit', function(e) {
                    e.preventDefault();
                    let formData = $(this).serialize();

                    $.ajax({
                        url: "{{ route('request.store') }}",
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        beforeSend: function() {
                            $('p.text-red-500').text('');
                            $('#formErrors').addClass('d-none');
                            $('#errorList').html('');
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#newUserModal').modal('hide');
                                $('#thankYouModal').removeClass('hidden');
                                $('#requestDemoForm')[0].reset();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $('#formErrors').removeClass('d-none');
                                $.each(errors, function(key, value) {
                                    $('.error-' + key).text(value[0]);
                                    $('#errorList').append('<li>' + value[0] + '</li>');
                                });
                            } else {
                                alert("Something went wrong. Please try again.");
                            }
                        }
                    });
                });

                $('#closeThankYou').click(function() {
                    $('#thankYouModal').addClass('hidden');
                });
            });
        </script>


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
