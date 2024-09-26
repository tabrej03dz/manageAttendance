@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Apply for Leave</h2>
        <form action="{{route('leave.store')}}" method="POST">
            <!-- Include CSRF token for Laravel -->
            @csrf

            <div class="mb-3">
                <label for="leave_type" class="form-label">Leave Type</label>
                <select class="form-select form-control" id="leave_type" name="leave_type" required>
                    <option value="" selected disabled>Select Leave Type</option>
                    <option value="sick">Sick Leave</option>
                    <option value="vacation">Vacation Leave</option>
                    <option value="casual">Casual Leave</option>
                    <option value="others">Others</option>
                    <!-- Add more leave types as needed -->
                </select>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Reason</label>
                <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Optional reason for leave"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Leave Application</button>
        </form>
    </div>

@endsection
