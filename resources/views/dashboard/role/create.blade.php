@extends('dashboard.layout.root')

@section('content')
    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    @endif
    <div class="py-12">
        <div class="content">
            <div class="container-fluid">
                <h2 class="text-center mb-4">Create Role</h2>
                <form action="{{ route('role.store') }}" method="POST">
                    @csrf
                    <div class="card shadow-lg border-danger rounded-3 mb-4">
                        <div class="card-header bg-danger text-white text-center rounded-top">
                            <h3 class="mb-0 py-3">Create Role</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}">
                            </div>

                            @if(auth()->user()->hasRole('super_admin|owner'))
                            <div class="form-group col-md-6 mb-3">
                                <label for="name" class="form-label">Office for the role</label>
                                <select name="office" id="" class="form-control">
                                    <option value="">Select Office</option>
                                    @foreach($offices as $office)
                                        <option value="{{$office->name}}">{{$office->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                                <input type="text"  id="name" name="office" value="{{auth()->user()->office->name}}" hidden>
                            @endif
                            </div>

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
