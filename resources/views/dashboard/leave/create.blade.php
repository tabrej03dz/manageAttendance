{{-- @extends('dashboard.layout.root')

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

@endsection --}}


@extends('dashboard.layout.root')

@section('content')
    <div class="container pt-4 pb-4 px-4 px-md-6 bg-gray-200 border border-danger shadow-sm rounded">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0 text-center fw-bold">Leave Request</h5>
        </div>

        <form>
            <!-- Leave Type Selection -->
            <div class="mt-4">
                <div>
                    <label for="leaveType" class="mb-2 font-semibold text-gray-700">Leave Type</label>
                </div>
                <select
                    class="mb-4 form-select bg-white border border-danger rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-500 transition duration-150 ease-in-out hover:bg-gray-50 text-gray-700 py-2 px-3 w-100"
                    id="leaveType" required>
                    <option value="" disabled selected>Select leave type</option>
                    <option value="annual">Annual Leave</option>
                    <option value="sick">Sick Leave</option>
                    <option value="personal">Personal Leave</option>
                </select>
            </div>

            <!-- Date Pickers for From Date and To Date -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="fromDate" class="form-label font-weight-bold text-dark">From Date</label>
                    <input type="date" class="form-control border border-danger rounded" id="fromDate" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="toDate" class="form-label font-weight-bold text-dark">To Date</label>
                    <input type="date" class="form-control border border-danger rounded" id="toDate" required>
                </div>
            </div>

            <!-- Duration Selection -->
            <div class="mb-4">
                <label class="form-label">Duration</label>
                <div class="btn-group w-100" role="group">
                    <label class="btn btn-outline-danger" for="fullDay">Full Day</label>
                    <label class="btn btn-outline-danger" for="an">AN</label>
                    <label class="btn btn-outline-danger" for="fn">FN</label>
                </div>
            </div>

            <!-- Reason for Leave -->
            <div class="mb-4">
                <label for="reason" class="form-label">Reason for Leave</label>
                <textarea class="form-control border border-danger" id="reason" rows="3" required></textarea>
            </div>

            <!-- Comp Off Selection -->
            <div class="mb-4">
                <label class="form-label">Is it a Comp Off?</label>
                <div class="btn-group w-100" role="group">
                    <label class="btn btn-outline-danger" for="Yes">Yes</label>
                    <label class="btn btn-outline-danger" for="No">No</label>
                </div>
            </div>

            <!-- Employee Code and Name -->
            <div class="mb-4">
                <label for="employeeCode" class="form-label">Employee Code</label>
                <input type="text" class="form-control border border-danger" id="employeeCode" required>
            </div>
            <div class="mb-4">
                <label for="employeeName" class="form-label">Employee Name</label>
                <input type="text" class="form-control border border-danger" id="employeeName" required>
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <label for="phoneNumber" class="form-label">Applicant's Phone Number</label>
                <input type="tel" class="form-control border border-danger" id="phoneNumber" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-danger w-100 fw-bold">Submit</button>
        </form>
    </div>
@endsection

