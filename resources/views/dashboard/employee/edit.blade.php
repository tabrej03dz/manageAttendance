@extends('dashboard.layout.root')
@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2 class="text-center mb-4">Edit records</h2>
            </div>
            <div class="card-body">
                <form action="{{route('employee.update', ['employee' => $employee->id])}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{$employee->name ?? ''}}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{$employee->email ?? ''}}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{$employee->phone ?? ''}}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Full Address</label>
                            <textarea name="address" class="form-control" id="address" cols="30" rows="2">{{$employee->address ?? ''}}</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="photo" class="form-label">Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo">
                        </div>
                        <div class="col-md-6">
                            <label for="joining_date" class="form-label">Joining Date</label>
                            <input type="text" class="form-control" id="joining_date" name="joining_date" value="{{$employee->joining_date?? ''}}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" value="{{$employee->designation?? ''}}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="responsibility" class="form-label">Responsibility</label>
                            <textarea name="responsibility" class="form-control" id="responsibility" cols="30" rows="2">{{$employee->responsibility ?? ''}}</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" class="form-control" id="salary" value="{{$employee->salary}}" name="salary" step="0.01">
                        </div>

                        <div class="col-md-6">
                            <label for="check_in_time" class="form-label">Check In Time</label>
                            <input type="time" class="form-control" id="check_in_time" name="check_in_time" value="{{$employee->check_in_time ?? ''}}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="check_out_time" class="form-label">Check Out Time</label>
                            <input type="time" class="form-control" id="check_out_time" name="check_out_time" value="{{$employee->check_out_time ?? ''}}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Re Enter Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
