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
                        <h2 class="mb-0 py-3">Create Note</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('note.store') }}" method="POST" >
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="title" class="form-label" data-bs-toggle="tooltip" title="Please enter your full title.">
                                        Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Enter your full name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="description" class="form-label" data-bs-toggle="tooltip" title="Please enter your full description.">
                                        Description
                                    </label>
                                    <textarea name="description" class="form-control" id="description" rows="2" placeholder="Enter your full description">{{ old('description') }}</textarea>
                                </div>

                            </div>

                            <div class="row mb-4">

                                <div class="col-md-6">
                                    <label for="office_id" class="form-label" data-bs-toggle="tooltip" title="Please enter your full description.">
                                        Office
                                    </label>
                                    <select name="office_id" id="office_id" class="form-control">
                                        <option value="">Select office to show note</option>
                                        @foreach($offices as $office)
                                            <option value="{{$office->id}}">{{$office->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="role" class="form-label" data-bs-toggle="tooltip" title="Please enter your full description.">
                                        Role
                                    </label>
                                    <select name="role" id="role" class="form-control">
                                        <option value="">Select role</option>
                                    </select>

                                </div>
                                <div class="col-md-6">
                                    <label for="employee" class="form-label" data-bs-toggle="tooltip" title="Please enter your full description.">
                                        Employees
                                    </label>
                                    <select name="employees[]" id="employee" multiple class="form-control">
                                        <option value="all">Select All</option>
{{--                                        @foreach($employees as $employee)--}}
{{--                                                <option value="{{$employee->id}}">{{$employee->name}}</option>--}}
{{--                                        @endforeach--}}
                                    </select>
                                </div>


                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-danger btn-lg w-100">Submit</button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#office_id').change(function() {
                const officeId = $(this).val();


                if (officeId) {
                    $.ajax({
                        url: `get-office-role-and-employee/${officeId}`,
                        type: 'GET',
                        success: function(data) {
                            console.log('Response:', data); // Debugging

                            // Populate Employees Dropdown
                            $('#employee').empty().append('<option value="">Select employee to show note</option>');
                            $('#employee').append('<option value="all">Select All</option>');
                            data.employees.forEach(employee => {
                                $('#employee').append(`<option value="${employee.id}">${employee.name}</option>`);
                            });

                            // Populate Roles Dropdown
                            $('#role').empty().append('<option value="">Select role to show note</option>');
                            data.roles.forEach(role => {
                                $('#role').append(`<option value="${role.name}">${role.name}</option>`);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', error); // Debugging
                            alert('Error fetching data.');
                        }
                    });
                } else {
                    $('#employee, #role').empty().append('<option value="">Select option</option>');
                }
            });
        });

    </script>

@endsection
