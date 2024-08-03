@extends('dashboard.layout.root')
@section('content')
    <div class="container mt-5">
        <h2> Employees</h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Check-in Time</th>
                <th>Check-in Image</th>
                <th>Check-out Time</th>
                <th>Check-out Image</th>
                <th>Working Hours</th>
                <th>Check-in/out</th>
            </tr>
            </thead>
            <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$employee->name}}</td>
                    <td>{{$employee->email}}</td>
                    <td>{{$record?->check_out?->format('h:i:s')}}</td>
                    <td><img src="{{asset('storage/'. $record?->check_out_image)}}" alt=""></td>
                    <td>{{$record?->duration}}</td>
                </tr>
            @endforeach
            <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>
@endsection
