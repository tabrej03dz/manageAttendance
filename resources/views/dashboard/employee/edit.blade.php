@extends('dashboard.layout.root')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
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
                        <h2 class="mb-0 py-3">Edit Records</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employee.update', ['employee' => $employee->id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $employee->name ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $employee->email ?? '' }}" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ $employee->phone ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Full Address</label>
                                    <textarea name="address" class="form-control" id="address" rows="2">{{ $employee->address ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="photo" class="form-label">Photo</label>
                                    <input type="file" class="form-control" id="photo" name="photo">
                                </div>
                                <div class="col-md-6">
                                    <label for="joining_date" class="form-label">Joining Date</label>
                                    <input type="date" class="form-control" id="joining_date" name="joining_date"
                                        value="{{ $employee->joining_date ?? '' }}">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="designation" class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation"
                                        value="{{ $employee->designation ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="responsibility" class="form-label">Responsibility</label>
                                    <textarea name="responsibility" class="form-control" id="responsibility" rows="2">{{ $employee->responsibility ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="salary" class="form-label">Salary</label>
                                    <input type="number" class="form-control" id="salary" name="salary" step="0.01"
                                        value="{{ $employee->salary }}" >
                                </div>
                                <div class="col-md-6">
                                    <label for="check_in_time" class="form-label">Check In Time</label>
                                    <input type="time" class="form-control" id="check_in_time" name="check_in_time"
                                        value="{{ $employee->check_in_time ? \Carbon\Carbon::parse($employee->check_in_time)->format('H:i') : '' }}">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="check_out_time" class="form-label">Check Out Time</label>
                                    <input type="time" class="form-control" id="check_out_time" name="check_out_time"
                                        value="{{ $employee->check_out_time ? \Carbon\Carbon::parse($employee->check_out_time)->format('H:i') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="office_id" class="form-label">Select Office</label>
                                    <select name="office_id" class="form-control" id="office_id" required>
                                        <option value="">Select Office</option>
                                        @foreach ($offices as $office)
                                            <option value="{{ $office->id }}"
                                                {{ $office->id == $employee->office_id ? 'selected' : '' }}>
                                                {{ $office->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="role" class="form-label">Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="">Select Role</option>
                                        <option value="admin" {{ $employee->hasRole('admin') ? 'selected' : '' }}>Admin
                                        </option>
                                        <option value="employee" {{ $employee->hasRole('employee') ? 'selected' : '' }}>
                                            Employee</option>
                                        <option value="team_leader"
                                            {{ $employee->hasRole('team_leader') ? 'selected' : '' }}>Team Leader</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="team_leader_id" class="form-label">Team Leader</label>
                                    <select name="team_leader_id" class="form-control" id="team_leader_id">
                                        <option value="">Select Team Leader</option>
                                        @foreach ($teamLeaders as $leader)
                                            <option value="{{ $leader->id }}"
                                                {{ $leader->id == $employee->team_leader_id ? 'selected' : '' }}>
                                                {{ $leader->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Re Enter Password</label>
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password">
                                </div>
                            </div>



                            <h2>Salary Details</h2>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="salary" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's Basic salary.">
                                        Basic Salary
                                    </label>
                                    <input type="number" class="form-control" id="basic_salary" name="basic_salary" step="0.01" value="{{ $employee->userSalary?->basic_salary }}" placeholder="Basic salary">
                                </div>

                                <div class="col-md-6">
                                    <label for="house_rent_allowance" class="form-label" data-bs-toggle="tooltip" title="House Rent Allowance.">
                                        House Rent Allowance
                                    </label>
                                    <input type="number" class="form-control" id="house_rent_allowance" name="house_rent_allowance" step="0.01" value="{{ $employee->userSalary?->house_rent_allowance }}" placeholder="House Rent Allowance">
                                </div>

                                <div class="col-md-6">
                                    <label for="transport_allowance" class="form-label" data-bs-toggle="tooltip" title="Transport Allowance">
                                        Transport Allowance
                                    </label>
                                    <input type="number" class="form-control" id="transport_allowance" name="transport_allowance" step="0.01" value="{{ $employee->userSalary?->transport_allowance }}" placeholder="Transport Allowance">
                                </div>

                                <div class="col-md-6">
                                    <label for="medical_allowance" class="form-label" data-bs-toggle="tooltip" title="Medical Allowance">
                                        Medical Allowance
                                    </label>
                                    <input type="number" class="form-control" id="medical_allowance" name="medical_allowance" step="0.01" value="{{ $employee->userSalary?->medical_allowance }}" placeholder="Medical Allowance">
                                </div>

                                <div class="col-md-6">
                                    <label for="special_allowance" class="form-label" data-bs-toggle="tooltip" title="Special Allowance">
                                        Special Allowance
                                    </label>
                                    <input type="number" class="form-control" id="special_allowance" name="special_allowance" step="0.01" value="{{ $employee->userSalary?->special_allowance }}" placeholder="Special Allowance">
                                </div>

                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-danger btn-lg">Update</button>
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
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            /* Focus ring effect */
            border-color: #dc3545;
            /* Border color on focus */
        }

        /* Additional styles for input fields */
        .form-control {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            /* Smooth transition */
        }

        /* Custom height for file input */
        .custom-file-input {
            height: 38px;
            /* Adjust to your preferred height */
            line-height: 1.5;
            /* Adjust line height for vertical alignment */
        }

        .card {
            border-radius: 0.5rem;
            /* Rounded corners for the card */
            border: 2px solid #dc3545;
            /* Card border color */
        }

        .card-header {
            border-top-left-radius: 0.5rem;
            /* Rounded corners for the card header */
            border-top-right-radius: 0.5rem;
            /* Rounded corners for the card header */
        }

        /* Spacing and layout adjustments */
        .form-label {
            font-weight: bold;
            /* Make labels bold */
        }

        /* Media queries for responsiveness */
        @media (max-width: 768px) {
            .row {
                margin-bottom: 2rem;
                /* Adjust bottom margin for smaller screens */
            }
        }
    </style>
@endsection
