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
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-head">
                    <h2 class="text-center mb-4">Employee Registration</h2>
                </div>
                <div class="card-body">
                    <form action="{{route('employee.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{old('phone')}}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Full Address</label>
                                <textarea name="address" class="form-control" id="address" cols="30" rows="2">{{old('address')}}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" >
                            </div>
                            <div class="col-md-6">
                                <label for="joining_date" class="form-label">Joining Date</label>
                                <input type="date" class="form-control" id="joining_date" name="joining_date" value="{{old('date')}}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="designation" name="designation" value="{{old('designation')}}">
                            </div>

                            <div class="col-md-6">
                                <label for="responsibility" class="form-label">Responsibility</label>
                                <textarea name="responsibility" class="form-control" id="responsibility" cols="30" rows="2">{{ old('responsibility') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="designation" class="form-label">Designation</label>
                                <select name="role" id="" class="form-control">
                                    <option value="">Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="team_leader">Team Leader</option>
                                    <option value="employee">Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="salary" class="form-label">Salary</label>
                                <input type="number" class="form-control" id="salary" name="salary" step="0.01" value="{{old('salary')}}">
                            </div>

                            <div class="col-md-6">
                                <label for="check_in_time" class="form-label">Check In Time</label>
                                <input type="time" class="form-control" id="check_in_time" name="check_in_time" value="{{old('check_in_time')}}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="check_out_time" class="form-label">Check Out Time</label>
                                <input type="time" class="form-control" id="check_out_time" name="check_out_time" value="{{old('check_out_time')}}">
                            </div>
                            <div class="col-md-6">
                                <label for="office_id" class="form-label">Office</label>
                                <select name="office_id" class="form-control" id="office_id">
                                    <option value="">Select Office</option>
                                    @foreach($offices as $office)
                                        <option value="{{$office->id}}">{{$office->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="team_leader_id" class="form-label">Team Leader</label>
                                <select name="team_leader_id" class="form-control" id="team_leader_id">
                                    <option value="">Select Team Leader</option>
                                    @foreach($teamLeaders as $leader)
                                        <option value="{{$leader->id}}">{{$leader->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
