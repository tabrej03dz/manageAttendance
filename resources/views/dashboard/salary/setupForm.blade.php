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
                        <h2 class="mb-0 py-3">{{$employee->name}}'s Salary Details</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('salary.informationStore', ['employee' => $employee->id, 'userSalary' => $userSalary ? $userSalary->id : '']) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="salary" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's Basic Pay.">
                                        Basic Pay
                                    </label>
                                    <input type="number" class="form-control" id="basic_salary" name="basic_salary" step="0.01" value="{{ old('basic_salary') }}" placeholder="Basic salary">


                                    <label for="dearness_allowance" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's D.A.">
                                        Dearness Allowance (D.A)
                                    </label>
                                    <input type="number" class="form-control" id="dearness_allowance" name="dearness_allowance" step="0.01" value="{{ old('dearness_allowance') }}" placeholder="Basic salary">

                                    <label for="relieving_charge" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's Relieving Charge.">
                                        Relieving Charge
                                    </label>
                                    <input type="number" class="form-control" id="relieving_charge" name="relieving_charge" step="0.01" value="{{ old('relieving_charge') }}" placeholder="Basic salary">

                                    <label for="additional_allowance" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's Additional Allowance.">
                                        Additional Allowance
                                    </label>
                                    <input type="number" class="form-control" id="additional_allowance" name="additional_allowance" step="0.01" value="{{ old('additional_allowance') }}" placeholder="Basic salary">

                                </div>

                                <div class="col-md-6">
                                    <label for="provident_fund" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's Provident Fund %.">
                                        Provident Fund %
                                    </label>
                                    <input type="number" class="form-control" id="provident_fund" name="provident_fund" step="0.01" value="{{ old('provident_fund') }}" placeholder="Basic salary">

                                    <label for="employee_state_insurance_corporation" class="form-label" data-bs-toggle="tooltip" title="Enter the employee's ESIC %.">
                                        ESIC %
                                    </label>
                                    <input type="number" class="form-control" id="employee_state_insurance_corporation" name="employee_state_insurance_corporation" step="0.01" value="{{ old('employee_state_insurance_corporation') }}" placeholder="Basic salary">
                                </div>

                                <div class="text-center mt-4">
                                <button type="submit" class="btn btn-danger btn-lg w-100">Register</button>
                                </div>
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
