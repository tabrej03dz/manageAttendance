@extends('dashboard.layout.root')

@section('content')
    <div class="py-12">
        <div class="content">
            <div class="container-fluid">
                <h2 class="text-center mb-4">Create Office</h2>
                <form action="{{ route('office.store') }}" method="POST">
                    @csrf
                    <div class="card shadow-lg border-danger rounded-3 mb-4">
                        <div class="card-header bg-danger text-white text-center rounded-top">
                            <h3 class="mb-0 py-3">Office Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Office Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="radius" class="form-label">Radius</label>
                                <input type="text" class="form-control" id="radius" name="radius">
                            </div>
                            <div class="form-group mb-3">
                                <label for="number_of_employees" class="form-label">Number of Employee</label>
                                <input type="number" class="form-control" id="number_of_employees" name="number_of_employees">
                            </div>
                            <div class="form-group mb-3">
                                <label for="price_per_employee" class="form-label">Price Per Employee</label>
                                <input type="number" value="59" class="form-control" id="price_per_employee" name="price_per_employee">
                            </div>
                            <div class="col-md-6">
                                <p>Under Radius Required?</p>
                                <div>
                                    <input type="radio" class="" value="1" id="under_radius_required_yes" name="under_radius_required">
                                    <label for="under_radius_required_yes" class="form-label">Yes</label>
                                </div>
                                <div>
                                    <input type="radio" class="" value="0" id="under_radius_required_no" name="under_radius_required" checked>
                                    <label for="under_radius_required_no" class="form-label">No</label>
                                </div>
                            </div>

                        @if($owners)
                            <div class="form-group mb-3">
                                <label for="price_per_employee" class="form-label">Owner</label>
                                <select name="owner_id" id="" class="form-control">
                                    <option value="">--Select Owner--</option>
                                    @foreach($owners as $owner)
                                        <option value="{{$owner->id}}" class="">{{$owner->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                                <input type="number" value="{{auth()->user()->id}}" class="form-control" id="owner_id" name="owner_id" hidden>
                            @endif
                            <div class="text-center">
                                <button type="submit" class="btn btn-danger btn-lg">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
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
            .form-group {
                margin-bottom: 2rem;
                /* Adjust bottom margin for smaller screens */
            }
        }
    </style>
@endsection
