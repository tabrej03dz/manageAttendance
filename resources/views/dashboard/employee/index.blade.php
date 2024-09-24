@extends('dashboard.layout.root')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <h2 class="mb-4 d-inline-block">Employees</h2>

            @role('super_admin|admin')
            <a href="{{ route('employee.create') }}" class="btn btn-primary ml-2 mb-2 mb-sm-0">Create</a>
            @endrole

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
                            @role('super_admin|admin')
                            <th>Action</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone }}</td>
                                <td><img src="{{ asset('storage/' . $employee->photo) }}" alt=""
                                        style="width: 100px;"></td>
                                <td>{{ $employee->office?->name }}</td>
                                @role('super_admin|admin')
                                <td>
                                    <a href="{{ route('employee.edit', ['employee' => $employee->id]) }}"
                                        class="btn btn-primary">Edit</a>
                                    <a href="{{ route('employee.delete', ['employee' => $employee->id]) }}"
                                        class="btn btn-danger">Delete</a>

                                    <a href="{{ route('employee.profile', ['user' => $employee->id]) }}"
                                       class="btn btn-danger">Profile</a>
                                </td>
                                @endrole
                            </tr>
                        @endforeach
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
