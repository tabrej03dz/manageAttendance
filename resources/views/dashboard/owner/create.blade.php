@extends('dashboard.layout.root')

@section('content')
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-12">
        <div class="content">
            <div class="container-fluid">
                <div class="card shadow-lg border-danger rounded-3">
                    <div class="card-header bg-danger text-white text-center rounded-top">
                        <h2 class="mb-0 py-3">Owner Create</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('owner.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name*</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email*</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone*</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Full Address</label>
                                    <textarea name="address" class="form-control" id="address" rows="2">{{ old('address') }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="photo" class="form-label">Photo</label>
                                    <input type="file" class="form-control" id="photo" name="photo">
                                </div>

                            </div>


                            <h4>Create Plan</h4>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="number_of_offices" class="form-label">Number Of office/Shop*</label>
                                    <input type="number" class="form-control" id="number_of_offices" name="number_of_offices" value="{{ old('number_of_offices') }}" placeholder="Ex-5" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="number_of_employees" class="form-label">Number Of Employee/Worker*</label>
                                    <input type="number" class="form-control" id="number_of_employees" name="number_of_employees" value="{{ old('number_of_employees') }}" placeholder="Ex-10" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price*</label>
                                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" placeholder="Ex-399" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Duration (in days)*</label>
                                    <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration') }}" placeholder="Ex-90" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Plan start Date </label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" placeholder="Ex-5">
                                </div>

                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-danger btn-lg">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom styles for focus ring */
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25); /* Focus ring effect */
            border-color: #dc3545; /* Optional: make the border red on focus */
        }

        /* Additional styles for input fields */
        .form-control {
            transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
        }

        .card {
            border-radius: 0.5rem; /* Rounded corners for the card */
            border: 2px solid #dc3545; /* Card border color */
        }

        .card-header {
            border-top-left-radius: 0.5rem; /* Rounded corners for the card header */
            border-top-right-radius: 0.5rem; /* Rounded corners for the card header */
        }

        /* Spacing and layout adjustments */
        .form-label {
            font-weight: bold; /* Make labels bold */
        }

        /* Media queries for responsiveness */
        @media (max-width: 768px) {
            .row {
                margin-bottom: 2rem; /* Adjust bottom margin for smaller screens */
            }
        }
    </style>
@endsection
