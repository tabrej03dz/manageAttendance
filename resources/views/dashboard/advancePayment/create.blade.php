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
                        <h2 class="mb-0 py-3">Edit Plan</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('advance.store') }}" method="POST" >
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Title*</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Ex-5" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount*</label>
                                    <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount')  }}" placeholder="Ex-399" required>
                                </div>
                            </div>

                            <div class="row mb-4">


                                <div class="col-12">
                                    <label for="price" class="form-label">Employee*</label>
                                    <select name="user_id" id="" class="form-control" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{$employee->id}}">{{$employee->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="" cols="30" rows="3">{{old('title')}}</textarea>
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
