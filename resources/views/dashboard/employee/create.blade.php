@extends('dashboard.layout.root')

@section('content')
    @if($errors->any())
        <div class="text-danger mb-4">
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
                    <h2 class="mb-0 py-3">Employee Registration</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label" data-bs-toggle="tooltip" title="Please enter your full name.">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label" data-bs-toggle="tooltip" title="Please enter your email address.">
                                    Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address">
                                @if ($errors->has('email'))
                                    <p class="text-danger">{{ $errors->first('email')}}</p>
                                @endif
                            </div>

                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="phone" class="form-label" data-bs-toggle="tooltip" title="Please enter your phone number.">
                                    Phone <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Please enter your phone number">
                            </div>

                            <div class="col-md-6">
                                <label for="address" class="form-label" data-bs-toggle="tooltip" title="Please enter your full address.">
                                    Full Address
                                </label>
                                <textarea name="address" class="form-control" id="address" rows="2" placeholder="Enter your full address">{{ old('address') }}</textarea>
                            </div>

                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="photo" class="form-label" data-bs-toggle="tooltip" title="Upload employee's photo.">
                                    Employee's Photo
                                </label>
                                <input type="file" class="form-control" id="photo" name="photo">
                            </div>
                            <div class="col-md-6">
                                <label for="joining_date" class="form-label">Joining Date</label>
                                <input type="date" class="form-control" id="joining_date" name="joining_date"
                                    value="">
                            </div>
                            <div class="col-md-6">
                                <label for="aadhar_attachment" class="form-label" data-bs-toggle="tooltip" title="Attach Aadhar document.">
                                    Aadhar Attachment
                                </label>
                                <input type="file" class="form-control" id="aadhar_attachment" name="aadhar_attachment">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="pan_attachment" class="form-label" data-bs-toggle="tooltip" title="Attach Pan document.">
                                    Pan Attachment
                                </label>
                                <input type="file" class="form-control" id="pan_attachment" name="pan_attachment">
                            </div>
                            <div class="col-md-6">
                                <label for="other_attachment" class="form-label" data-bs-toggle="tooltip" title="Attach any other relevant document.">
                                    Other Attachment
                                </label>
                                <input type="file" class="form-control" id="other_attachment" name="other_attachment">
                            </div>
                        </div>


                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="designation" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's designation.">
                                    Designation
                                </label>
                                <input type="text" class="form-control" id="designation" name="designation" value="{{ old('designation') }}" placeholder="Enter designation">
                            </div>

                            <div class="col-md-6">
                                <label for="responsibility" class="form-label" data-bs-toggle="tooltip" title="Describe the employee's responsibilities.">
                                    Responsibility
                                </label>
                                <textarea name="responsibility" class="form-control" id="responsibility" rows="2" placeholder="Enter responsibilities">{{ old('responsibility') }}</textarea>
                            </div>

                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="role" class="form-label" data-bs-toggle="tooltip" title="Select the employee's role in the organization.">
                                    Role
                                </label>
                                <select name="role" id="role" class="form-control">
                                    <option value="" disabled selected>Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="team_leader">Team Leader</option>
                                    <option value="employee" selected >Employee</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="salary" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's monthly salary.">
                                    Salary
                                </label>
                                <input type="number" class="form-control" id="salary" name="salary" step="0.01" value="{{ old('salary') }}" placeholder="Enter salary">
                            </div>
                        </div>


                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="check_in_time" class="form-label" data-bs-toggle="tooltip" title="Select the employee's check-in time.">
                                    Check In Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control" id="check_in_time" name="check_in_time" value="{{ old('check_in_time') }}" >
                            </div>
                            <div class="col-md-6">
                                <label for="check_out_time" class="form-label" data-bs-toggle="tooltip" title="Select the employee's check-out time.">
                                    Check Out Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control" id="check_out_time" name="check_out_time" value="{{ old('check_out_time') }}" >
                            </div>

                            <div class="col-md-6">
                                <label for="break" class="form-label" data-bs-toggle="tooltip" title="Break time">
                                    Break duration in a day (min) <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="break" name="break" value="{{ old('break') }}">
                            </div>
                        </div>


                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="office_id" class="form-label" data-bs-toggle="tooltip" title="Select the office where the employee works.">
                                    Office <span class="text-danger">*</span>
                                </label>
                                <select name="office_id" class="form-control" id="office_id" required>
                                    <option value="">Select Office</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="team_leader_id" class="form-label" data-bs-toggle="tooltip" title="Select the team leader for this employee.">
                                    Team Leader
                                </label>
                                <select name="team_leader_id" class="form-control" id="team_leader_id" >
                                    <option value="">Select Team Leader</option>
                                    @foreach($teamLeaders as $leader)
                                        <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <h2>Salary Details</h2>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="salary" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's Basic salary.">
                                    Basic Salary
                                </label>
                                <input type="number" class="form-control" id="basic_salary" name="basic_salary" step="0.01" value="{{ old('basic_salary') }}" placeholder="Basic salary">
                            </div>

                            <div class="col-md-6">
                                <label for="house_rent_allowance" class="form-label" data-bs-toggle="tooltip" title="House Rent Allowance.">
                                    House Rent Allowance
                                </label>
                                <input type="number" class="form-control" id="house_rent_allowance" name="house_rent_allowance" step="0.01" value="{{ old('house_rent_allowance') }}" placeholder="House Rent Allowance">
                            </div>

                            <div class="col-md-6">
                                <label for="transport_allowance" class="form-label" data-bs-toggle="tooltip" title="Transport Allowance">
                                    Transport Allowance
                                </label>
                                <input type="number" class="form-control" id="transport_allowance" name="transport_allowance" step="0.01" value="{{ old('transport_allowance') }}" placeholder="Transport Allowance">
                            </div>

                            <div class="col-md-6">
                                <label for="medical_allowance" class="form-label" data-bs-toggle="tooltip" title="Medical Allowance">
                                    Medical Allowance
                                </label>
                                <input type="number" class="form-control" id="medical_allowance" name="medical_allowance" step="0.01" value="{{ old('medical_allowance') }}" placeholder="Medical Allowance">
                            </div>

                            <div class="col-md-6">
                                <label for="special_allowance" class="form-label" data-bs-toggle="tooltip" title="Special Allowance">
                                    Special Allowance
                                </label>
                                <input type="number" class="form-control" id="special_allowance" name="special_allowance" step="0.01" value="{{ old('special_allowance') }}" placeholder="Special Allowance">
                            </div>

                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-danger btn-lg w-100">Register</button>
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
