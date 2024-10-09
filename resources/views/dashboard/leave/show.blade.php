@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">{{$leave->user->name. ' Requested for leave'}}</h2>
        <div class="table-responsive">
            <table>
                <tr>
                    <td><strong>Start Date:</strong></td>
                    <td>{{$leave->start_date}}</td>
                </tr>
                <tr>
                    <td><strong>End Date:</strong></td>
                    <td>{{$leave->end_date}}</td>
                </tr>
                <tr>
                    <td><strong>Leave Type:</strong></td>
                    <td>{{$leave->leave_type}}</td>
                </tr>
                <tr>
                    <td><strong>Reason:</strong></td>
                    <td>{{$leave->reason}}</td>
                </tr>
                <tr>
                    <td><a href="{{route('leave.status', ['leave' => $leave->id, 'status' => 'approved'])}}" class="btn btn-primary">Approve</a></td>
                    <td><a href="{{route('leave.status', ['leave' => $leave->id, 'status' => 'rejected'])}}" class="btn btn-warning">Reject</a></td>
                </tr>
            </table>
        </div>
    </div>
@endsection
