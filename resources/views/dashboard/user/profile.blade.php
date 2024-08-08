@extends('dashboard.layout.root')

@section('content')
<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">User Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">User Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- User Info Section -->
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body text-center">
                            <!-- Profile Picture (Optional) -->
                            <!-- <img src="path_to_profile_picture.jpg" class="profile-user-img img-fluid img-circle mb-3" alt="User profile picture"> -->

                            <h3 class="profile-username">John Doe</h3>
                            <p class="text-muted">Software Engineer</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Email</b> <span class="float-md-right d-block d-md-inline">johndoe@example.com</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Phone</b> <span class="float-md-right d-block d-md-inline">+1 (123) 456-7890</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Salary</b> <span class="float-md-right d-block d-md-inline">$50,000</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Designation</b> <span class="float-md-right d-block d-md-inline">Software Engineer</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Attendance and Summary Section -->
                <div class="col-lg-8 col-md-6 col-12">
                    <!-- Attendance Records -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Attendance Records</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>Date</th>
                                            <th>Check-in</th>
                                            <th>Check-out</th>
                                            <th>Hours Worked</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1.</td>
                                            <td>2023-08-01</td>
                                            <td>09:00 AM</td>
                                            <td>06:00 PM</td>
                                            <td>9 hrs</td>
                                        </tr>
                                        <tr>
                                            <td>2.</td>
                                            <td>2023-08-02</td>
                                            <td>09:00 AM</td>
                                            <td>05:30 PM</td>
                                            <td>8.5 hrs</td>
                                        </tr>
                                        <tr>
                                            <td>3.</td>
                                            <td>2023-08-03</td>
                                            <td>09:15 AM</td>
                                            <td>06:15 PM</td>
                                            <td>9 hrs</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Summary</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>Total Working Days</b> <span class="float-md-right d-block d-md-inline">22</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Off Days</b> <span class="float-md-right d-block d-md-inline">8</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Change Password</h3>
    </div>
    <div class="card-body">
        <form>
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password">
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" style="width: 90%;" id="confirmPassword" placeholder="Confirm new password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>
@endsection
