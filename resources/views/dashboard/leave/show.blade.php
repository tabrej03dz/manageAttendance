@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ $leave->user->name }} Requested for Leave</h4>
            </div>
            <div class="card-body">
                <div class="mb-3 flex justify-content-between">
                    <strong>Start Date:</strong>
                    <p class="text-muted">{{ $leave->start_date }}</p>
                    <strong>End Date:</strong>
                    <p class="text-muted">{{ $leave->end_date }}</p>
                </div>
                <div class="mb-3 flex justify-content-between">
                    <strong>Leave Type:</strong>
                    <p class="text-muted">{{ ucfirst($leave->leave_type) }}</p>

                    <strong>Subject:</strong>
                    <p class="text-muted">{{ ucfirst($leave->subject) }}</p>

                </div>
                <div class="mb-3">
                    <strong>Reason:</strong>
                    <p class="text-muted">{{ $leave->reason }}</p>
                </div>

                <div class="mb-3">
                    <strong class="d-block mb-2">Images:</strong>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($leave->images as $image)
                            <a href="{{ asset('storage' . $image->path) }}" target="_blank">
                            <div class="border rounded p-1 shadow-sm" style="width: 200px;">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Leave Image" class="img-fluid rounded">
                            </div>
                            </a>
                        @endforeach
                    </div>
                </div>

@role('super_admin|owner|admin')
                <form action="{{ route('leave.response', $leave) }}" method="post" class="p-3 border rounded shadow-sm bg-white">
                    @csrf

                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Response:</label>
                        <select name="status" id="status" class="form-select form-control">
                            <option value="" selected disabled>Select</option>
                            <option value="approved" {{ $leave->status == 'approved' ? 'selected' : '' }}>Approve</option>
                            <option value="rejected" {{ $leave->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Type:</label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="approve_as" id="paid" value="paid"
                                    {{ $leave->approve_as == 'paid' ? 'checked' : '' }}>
                                <label for="paid" class="form-check-label">Paid</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="approve_as" id="unpaid" value="unpaid"
                                    {{ $leave->approve_as == 'unpaid' ? 'checked' : '' }}>
                                <label for="unpaid" class="form-check-label">Unpaid</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="response" class="form-label fw-bold">Reason:</label>
                        <textarea name="response" id="response" class="form-control" rows="4" placeholder="Enter your reason">{{ $leave->response }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
@endrole
            </div>
        </div>
    </div>
@endsection
