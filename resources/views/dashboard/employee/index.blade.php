@extends('dashboard.layout.root')
@section('content')
    <div class="container mt-5">
        <h2> Employees</h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Image</th>

            </tr>
            </thead>
            <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$employee->name}}</td>
                    <td>{{$employee->email}}</td>
                    <td>{{$employee->phone}}</td>
                    <td><img src="{{asset('storage/'. $employee->photo)}}" alt=""></td>
                    <td></td>
                </tr>
            @endforeach
            <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>
@endsection
