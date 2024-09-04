@extends('dashboard.layout.root')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <h2 class="mb-4 d-inline-block">Employees</h2>
<<<<<<< HEAD
            <a href="{{ route('employee.create') }}"
               class="btn btn-primary ml-2 mb-2 mb-sm-0">Create</a>
    {{--        <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"--}}
    {{--           class="btn btn-danger ml-2 mb-2 mb-sm-0">Check Out</a>--}}
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Image</th>
                    <th>Office</th>
                    <th>Action</th>

                </tr>
                </thead>
                <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{$employee->name}}</td>
                        <td>{{$employee->email}}</td>
                        <td>{{$employee->phone}}</td>
                        <td><img src="{{asset('storage/'. $employee->photo)}}" alt="" style="width: 100px;"></td>
                        <td>{{$employee->office?->name}}</td>
                        <td>
                            <div class="btn-group">
                            <a href="{{route('employee.edit', ['employee' => $employee->id])}}" class="btn btn-primary">Edit</a>
                            <a href="{{route('employee.delete', ['employee' => $employee->id])}}" class="btn btn-danger">Delete</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <!-- Add more rows as needed -->
                </tbody>
            </table>
=======
            <a href="{{ route('employee.create') }}" class="btn btn-primary ml-2 mb-2 mb-sm-0">Create</a>
            
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Image</th>
                            <th>Office</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{$employee->name}}</td>
                                <td>{{$employee->email}}</td>
                                <td>{{$employee->phone}}</td>
                                <td><img src="{{asset('storage/'. $employee->photo)}}" alt="" style="width: 100px;"></td>
                                <td>{{$employee->office?->name}}</td>
                                <td>
                                    <a href="{{route('employee.edit', ['employee' => $employee->id])}}" class="btn btn-primary">Edit</a>
                                    <a href="{{route('employee.delete', ['employee' => $employee->id])}}" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
>>>>>>> 370a38edd89fa80d2e9d8a7d24338d7d27b27eec
        </div>
    </div>
@endsection
