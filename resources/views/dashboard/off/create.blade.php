@extends('dashboard.layout.root')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
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
                <div class="card shadow-lg border-danger rounded-3 mb-4">
                    <div class="card-header bg-danger text-white text-center rounded-top">
                        <h2 class="mb-0 py-3">Create Holiday</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('off.store') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Holiday Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                @if ($offices)
                                    <div class="col-md-6">
                                        <label for="office_id" class="form-label">Select Office</label>
                                        <select name="office_id" id="office_id" class="form-control">
                                            <option value="">Select Office</option>
                                            @foreach ($offices as $office)
                                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-danger btn-lg">Submit</button>
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
