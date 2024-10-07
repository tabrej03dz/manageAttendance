@extends('dashboard.layout.root')

@section('content')
    <div class="container pt-4 pb-4 px-4 px-md-6 bg-gray-200 border border-danger shadow-sm rounded">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0 text-center fw-bold">Add Information</h5>
        </div>

        <form action="{{route('info.store')}}" method="post">
            @csrf

            <!-- Employee Code and Name -->
            <div class="mb-4">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control border border-danger" name="phone" id="phone" required>
            </div>
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control border border-danger" name="email" id="email" required>
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <label for="address" class="form-label">Address</label>
                <input type="tel" class="form-control border border-danger" name="address" id="address" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-danger w-100 fw-bold">Submit</button>
        </form>
    </div>
@endsection
