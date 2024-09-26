@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Leave List</h2>
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Leave Type</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Response By</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <!-- Example row -->
                @foreach($leaves as $leave)

                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$leave->user->name}}</td>
                        <td>{{$leave->start_date}}</td>
                        <td>{{$leave->end_date}}</td>
                        <td>{{$leave->leave_type}}</td>
                        <td>{{$leave->reason}}</td>

                        <td><span class="badge bg-{{$leave->status == 'approved' ? 'success' : 'warning'}}">{{$leave->status}}</span></td>
                        <td>{{$leave->responsesBy?->name}}</td>
                        <td>
                            <a href="{{route('leave.status', ['leave' => $leave->id, 'status' => 'approved'])}}" class="btn btn-primary btn-sm">Approve</a>
                            <a href="{{route('leave.status', ['leave' => $leave->id, 'status' => 'rejected'])}}" class="btn btn-warning btn-sm">Reject</a>
{{--                            <a href="#" class="btn btn-danger btn-sm">Delete</a>--}}
                        </td>
                    </tr>
                @endforeach
                <!-- Repeat rows for each leave record -->
                </tbody>
            </table>
        </div>
    </div>
@endsection
