{{-- @extends('dashboard.layout.root')

@section('content') --}}
{{--    <div> --}}
{{--        <div class="content-header"> --}}
{{--            <div class="container-fluid"> --}}
{{--                <div class="row mb-2"> --}}
{{--                    <div class="col-sm-6"> --}}
{{--                        <h1 class="m-0 text-dark">User Profile</h1> --}}
{{--                    </div> --}}
{{--                    <div class="col-sm-6"> --}}
{{--                        <ol class="breadcrumb float-sm-right"> --}}
{{--                            <li class="breadcrumb-item"><a href="#">Home</a></li> --}}
{{--                            <li class="breadcrumb-item active">User Profile</li> --}}
{{--                        </ol> --}}
{{--                    </div> --}}
{{--                </div> --}}
{{--            </div> --}}
{{--        </div> --}}

{{--        <div class="content"> --}}
{{--            <div class="container-fluid"> --}}
{{--                <div class="row"> --}}
{{--                    <!-- User Info Section --> --}}
{{--                    <div class="col-lg-4 col-md-6 col-12 mb-4"> --}}
{{--                        <div class="card card-primary card-outline"> --}}
{{--                            <div class="card-body text-center"> --}}
{{--                                Profile Picture (Optional) --}}
{{--                                <img src="{{ asset('storage/' . $user->photo) }}" --}}
{{--                                    class="profile-user-img img-fluid img-circle mb-3" alt="User profile picture"> --}}

{{--                                <h3 class="profile-username">{{ $user->name }}</h3> --}}
{{--                                <p class="text-muted">{{ $user->designation }}</p> --}}

{{--                                <ul class="list-group list-group-unbordered mb-3"> --}}
{{--                                    <li class="list-group-item"> --}}
{{--                                        <b>Email</b> <span --}}
{{--                                            class="float-md-right d-block d-md-inline">{{ $user->email }}</span> --}}
{{--                                    </li> --}}
{{--                                    <li class="list-group-item"> --}}
{{--                                        <b>Phone</b> <span --}}
{{--                                            class="float-md-right d-block d-md-inline">{{ $user->phone }}</span> --}}
{{--                                    </li> --}}
{{--                                    <li class="list-group-item"> --}}
{{--                                        <b>Salary</b> <span --}}
{{--                                            class="float-md-right d-block d-md-inline">{{ $user->salary }}</span> --}}
{{--                                    </li> --}}
{{--                                    <li class="list-group-item"> --}}
{{--                                        <b>Designation</b> <span --}}
{{--                                            class="float-md-right d-block d-md-inline">{{ $user->designation }}</span> --}}
{{--                                    </li> --}}
{{--                                </ul> --}}
{{--                            </div> --}}
{{--                        </div> --}}
{{--                    </div> --}}

{{--                    <!-- Attendance and Summary Section --> --}}
{{--                    <div class="col-lg-8 col-md-6 col-12"> --}}
{{--                        <!-- Attendance Records --> --}}
{{--                        --}}{{--                    <div class="card mb-4"> --}}
{{--                        --}}{{--                        <div class="card-header"> --}}
{{--                        --}}{{--                            <h3 class="card-title">Attendance Records</h3> --}}
{{--                        --}}{{--                        </div> --}}
{{--                        --}}{{--                        <div class="card-body p-0"> --}}
{{--                        --}}{{--                            <div class="table-responsive"> --}}
{{--                        --}}{{--                                <table class="table table-striped table-bordered mb-0"> --}}
{{--                        --}}{{--                                    <thead class="thead-light"> --}}
{{--                        --}}{{--                                        <tr> --}}
{{--                        --}}{{--                                            <th style="width: 10px">#</th> --}}
{{--                        --}}{{--                                            <th>Date</th> --}}
{{--                        --}}{{--                                            <th>Check-in</th> --}}
{{--                        --}}{{--                                            <th>Check-out</th> --}}
{{--                        --}}{{--                                            <th>Hours Worked</th> --}}
{{--                        --}}{{--                                        </tr> --}}
{{--                        --}}{{--                                    </thead> --}}
{{--                        --}}{{--                                    <tbody> --}}
{{--                        --}}{{--                                        <tr> --}}
{{--                        --}}{{--                                            <td>1.</td> --}}
{{--                        --}}{{--                                            <td>2023-08-01</td> --}}
{{--                        --}}{{--                                            <td>09:00 AM</td> --}}
{{--                        --}}{{--                                            <td>06:00 PM</td> --}}
{{--                        --}}{{--                                            <td>9 hrs</td> --}}
{{--                        --}}{{--                                        </tr> --}}
{{--                        --}}{{--                                        <tr> --}}
{{--                        --}}{{--                                            <td>2.</td> --}}
{{--                        --}}{{--                                            <td>2023-08-02</td> --}}
{{--                        --}}{{--                                            <td>09:00 AM</td> --}}
{{--                        --}}{{--                                            <td>05:30 PM</td> --}}
{{--                        --}}{{--                                            <td>8.5 hrs</td> --}}
{{--                        --}}{{--                                        </tr> --}}
{{--                        --}}{{--                                        <tr> --}}
{{--                        --}}{{--                                            <td>3.</td> --}}
{{--                        --}}{{--                                            <td>2023-08-03</td> --}}
{{--                        --}}{{--                                            <td>09:15 AM</td> --}}
{{--                        --}}{{--                                            <td>06:15 PM</td> --}}
{{--                        --}}{{--                                            <td>9 hrs</td> --}}
{{--                        --}}{{--                                        </tr> --}}
{{--                        --}}{{--                                    </tbody> --}}
{{--                        --}}{{--                                </table> --}}
{{--                        --}}{{--                            </div> --}}
{{--                        --}}{{--                        </div> --}}
{{--                        --}}{{--                    </div> --}}

{{--                        <!-- Summary --> --}}
{{--                        <div class="card"> --}}
{{--                            <div class="card-header"> --}}
{{--                                <h3 class="card-title">Summary</h3> --}}
{{--                            </div> --}}
{{--                            <div class="card-body"> --}}
{{--                                <ul class="list-group list-group-unbordered"> --}}
{{--                                    <li class="list-group-item"> --}}
{{--                                        <b>Total Working Days</b> <span class="float-md-right d-block d-md-inline">22</span> --}}
{{--                                    </li> --}}
{{--                                    <li class="list-group-item"> --}}
{{--                                        <b>Total Off Days</b> <span class="float-md-right d-block d-md-inline">8</span> --}}
{{--                                    </li> --}}
{{--                                </ul> --}}
{{--                            </div> --}}
{{--                        </div> --}}
{{--                    </div> --}}
{{--                </div> --}}
{{--            </div> --}}
{{--        </div> --}}
{{--    </div> --}}

{{--    <div class="container mt-5"> --}}
{{--        <h2 class="mb-4">Additional Information</h2> --}}
{{--        <a href="{{ route('info.create') }}" class="btn btn-primary btn-sm">Add Info</a> --}}
{{--        <div class="table-responsive"> --}}
{{--            <table class="table table-hover table-striped table-bordered"> --}}
{{--                <thead class="table-dark"> --}}
{{--                    <tr> --}}
{{--                        <th>#</th> --}}
{{--                        <th>Mobile</th> --}}
{{--                        <th>Email</th> --}}
{{--                        <th>Address</th> --}}
{{--                    </tr> --}}
{{--                </thead> --}}
{{--                <tbody> --}}
{{--                    <!-- Example row --> --}}

{{--                    @foreach ($infos as $info) --}}
{{--                        <tr> --}}
{{--                            <td>{{ $loop->iteration }}</td> --}}
{{--                            <td>{{ $info->phone }}</td> --}}
{{--                            <td>{{ $info->email }}</td> --}}
{{--                            <td>{{ $info->address }}</td> --}}

{{--                        </tr> --}}
{{--                    @endforeach --}}
{{--                    <!-- Repeat rows for each info record --> --}}
{{--                </tbody> --}}
{{--            </table> --}}
{{--        </div> --}}
{{--    </div> --}}

{{--    <div class="container mt-5"> --}}
{{--        <h2 class="mb-4">Leave List</h2> --}}
{{--        <a href="{{ route('leave.create') }}" class="btn btn-primary btn-sm">Request For Leave</a> --}}
{{--        <div class="table-responsive"> --}}
{{--            <table class="table table-hover table-striped table-bordered"> --}}
{{--                <thead class="table-dark"> --}}
{{--                    <tr> --}}
{{--                        <th>#</th> --}}
{{--                        <th>Start Date</th> --}}
{{--                        <th>End Date</th> --}}
{{--                        <th>Leave Type</th> --}}
{{--                        <th>Reason</th> --}}
{{--                        <th>Status</th> --}}
{{--                        <th>Responses By</th> --}}
{{--                    </tr> --}}
{{--                </thead> --}}
{{--                <tbody> --}}
{{--                    <!-- Example row --> --}}
{{--                    @foreach ($leaves as $leave) --}}
{{--                        <tr> --}}
{{--                            <td>{{ $loop->iteration }}</td> --}}
{{--                            <td>{{ $leave->start_date }}</td> --}}
{{--                            <td>{{ $leave->end_date }}</td> --}}
{{--                            <td>{{ $leave->leave_type }}</td> --}}
{{--                            <td>{{ $leave->reason }}</td> --}}
{{--                            <td><span --}}
{{--                                    class="badge bg-{{ $leave->status == 'approved' ? 'success' : 'warning' }}">{{ $leave->status }}</span> --}}
{{--                            </td> --}}
{{--                            <td>{{ $leave->responsesBy?->name }}</td> --}}
{{--                        </tr> --}}
{{--                    @endforeach --}}
{{--                    <!-- Repeat rows for each leave record --> --}}
{{--                </tbody> --}}
{{--            </table> --}}
{{--        </div> --}}
{{--    </div> --}}

{{--    <!-- Change Password --> --}}
{{--    <div class="card mt-4"> --}}
{{--        <div class="card-header"> --}}
{{--            <h3 class="card-title">Change Password</h3> --}}
{{--        </div> --}}
{{--        <div class="card-body"> --}}
{{--            <form action="{{ route('userPassword', ['user' => $user->id]) }}" method="POST"> --}}
{{--                @csrf --}}
{{--                <div class="form-group"> --}}
{{--                    <label for="currentPassword">Current Password</label> --}}
{{--                    <input type="password" class="form-control" id="currentPassword" name="current_password" --}}
{{--                        placeholder="Enter current password"> --}}
{{--                </div> --}}
{{--                <div class="form-group"> --}}
{{--                    <label for="newPassword">New Password</label> --}}
{{--                    <input type="password" class="form-control" id="newPassword" name="new_password" --}}
{{--                        placeholder="Enter new password"> --}}
{{--                </div> --}}
{{--                <div class="form-group"> --}}
{{--                    <label for="confirmPassword">Confirm Password</label> --}}
{{--                    <div class="input-group"> --}}
{{--                        <input type="password" class="form-control" style="width: 90%;" id="confirmPassword" --}}
{{--                            name="confirm_password" placeholder="Confirm new password"> --}}
{{--                        <div class="input-group-append"> --}}
{{--                            <div class="input-group-text"> --}}
{{--                                <span class="fas fa-lock"></span> --}}
{{--                            </div> --}}
{{--                        </div> --}}
{{--                    </div> --}}
{{--                </div> --}}
{{--                <button type="submit" class="btn btn-primary">Update Password</button> --}}
{{--            </form> --}}
{{--        </div> --}}
{{--    </div> --}}






@extends('dashboard.layout.root')

@section('content')
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
    @endif
    <div class="pb-24">

        <!-- Profile Details -->
        <div class="bg-gray-100 min-h-screen">
            <div class="container mx-auto px-4 py-8">
                <!-- Full width on web, max width on mobile -->

                <!-- Header -->
                <div class="bg-red-600 text-white font-semibold text-2xl text-center py-3 shadow-lg rounded-t-lg">
                    Profile Details
                </div>

                <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full md:w-full mx-auto">
                    <form action="{{ route('profile.update', ['user' => $user->id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="p-6">
                            <!-- Profile Picture -->
                            <div class="flex flex-col items-center">
                                <img src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://via.placeholder.com/100' }}"
                                    alt="Profile Avatar" class="w-24 h-24 rounded-full mb-4">
                                <div class="flex justify-center items-center mb-4">
                                    <input type="file"
                                        class="text-gray-600 text-sm w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                        name="photo" />
                                </div>
                            </div>

                            <!-- Personal Details Section -->
                            <div class="space-y-4">
                                <!-- Name Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">person</span>
                                        <input type="text"
                                            class="text-gray-800 font-medium w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            value="{{ $user->name }}" name="name" placeholder="Enter your name"
                                            disabled />
                                    </div>
                                </div>

                                <!-- Designation Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">work</span>
                                        <input type="text"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            value="{{ $user->designation }}" name="designation"
                                            placeholder="Enter your designation" disabled />
                                    </div>
                                </div>

                                <!-- Responsibility Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">work_outline</span>
                                        <input type="text"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            placeholder="Enter Responsibility" name="responsibility" />
                                    </div>
                                </div>

                                <!-- Joining Date Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">calendar_today</span>
                                        <input type="date"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            placeholder="Enter Joining Date" name="joining_date" disabled />
                                    </div>
                                </div>

                                <!-- Email Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">email</span>
                                        <input type="email"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            value="{{ $user->email }}" name="email" placeholder="Enter your email"
                                            disabled />
                                    </div>
                                </div>

                                <!-- Phone Number Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">phone</span>
                                        <input type="text"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            value="{{ $user->phone }}" placeholder="+91-1234567890" name="phone"
                                            disabled />
                                    </div>
                                </div>

                                <!-- Salary Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="text-xl font-bold text-gray-600">₹</span>
                                        <input type="text"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            value="{{ $user->salary }}" placeholder="20000/mnth" name="salary" disabled />
                                    </div>
                                </div>

                                <!-- Pancard Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">credit_card</span>
                                        <input type="text"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            placeholder="Enter Pancard Number" name="pancard" />
                                    </div>
                                </div>

                                <!-- Pancard File -->
                                <div class="mt-6">
                                    <h1 class="ml-6 font-bold text-gray-800">Upload Pancard</h1>
                                    <div
                                        class="ml-6 flex flex-col sm:flex-row items-center justify-start sm:space-x-4 border-b py-4">
                                        <label
                                            class="flex items-center space-x-2 cursor-pointer w-full sm:w-auto bg-red-100 hover:bg-red-200 text-red-600 rounded-lg p-3">
                                            <span class="material-icons">cloud_upload</span>
                                            <span class="text-xs">Choose Pancard Files</span>
                                            <input type="file" name="pancard[]" multiple class="hidden" />
                                        </label>
                                        <span class="text-xs text-gray-500">Accepted formats: .jpg, .png, .pdf</span>
                                    </div>
                                </div>

                                <!-- Aadharcard Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">account_balance</span>
                                        <input type="text"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            placeholder="Enter Aadharcard Number" name="aadharcard" />
                                    </div>
                                </div>

                                <!-- Aadharcard File -->
                                <div class="mt-6">
                                    <h1 class="ml-6 font-bold text-gray-800">Upload Aadharcard</h1>
                                    <div
                                        class="ml-6 flex flex-col sm:flex-row items-center justify-start sm:space-x-4 border-b py-4">
                                        <label
                                            class="flex items-center space-x-2 cursor-pointer w-full sm:w-auto bg-red-100 hover:bg-red-200 text-red-600 rounded-lg p-3">
                                            <span class="material-icons">cloud_upload</span>
                                            <span class="text-xs">Choose Aadharcard Files</span>
                                            <input type="file" name="aadharcard[]" multiple class="hidden" />
                                        </label>
                                        <span class="text-xs text-gray-500">Accepted formats: .jpg, .png, .pdf</span>
                                    </div>
                                </div>

                                <!-- Address Field -->
                                <div class="flex items-center justify-between border-b py-2">
                                    <div class="flex items-center space-x-2 w-full">
                                        <span class="material-icons text-gray-600">home</span>
                                        <input type="text"
                                            class="text-gray-800 w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                            placeholder="Enter Full Address" name="address" />
                                    </div>
                                </div>
                            </div>

                            <!-- Update Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="w-1/2 bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- OFFICE TIMING -->
        <div class="bg-gray-100 flex items-center justify-center">
            <div class="container mx-auto px-4 py-8">

                <!-- Card with shadow and rounded corners -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Header with a red background -->
                    <div class="bg-red-600 text-white font-semibold text-2xl text-center py-2 shadow-md rounded-t-lg">
                        Office Timing
                    </div>
                    <!-- Form Section -->
                    <form action="{{ route('profile.update', ['user' => $user->id]) }}" method="post">
                        @csrf
                        <div class="p-6 space-y-8">

                            <!-- Office Start Time -->
                            <div class="flex flex-col space-y-2">
                                <label for="start_time" class="text-lg font-semibold text-gray-600">Office Start
                                    Time</label>
                                <input type="time" id="start_time" name="start_time" value="{{ $user->start_time }}"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>

                            <!-- Office End Time -->
                            <div class="flex flex-col space-y-2">
                                <label for="end_time" class="text-lg font-semibold text-gray-600">Office End Time</label>
                                <input type="time" id="end_time" name="end_time" value="{{ $user->end_time }}"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>

                            <!-- Break Time (Optional) -->
                            <div class="flex flex-col space-y-2">
                                <label for="break_time" class="text-lg font-semibold text-gray-600">Break Time</label>
                                <input type="time" id="break_time" name="break_time" value="{{ $user->break_time }}"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="w-1/2 bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <!-- Secondary Email -->
        <div class="bg-gray-100 flex items-center justify-center">
            <div class="container mx-auto px-4 py-8">
                <!-- Card with shadow and rounded corners -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">

                    <!-- Header with a red background -->
                    <div class="bg-red-600 text-white font-semibold text-2xl text-center py-2 shadow-md rounded-t-lg">
                        Secondary Email
                    </div>

                    <!-- Form Section -->
                    <form action="{{ route('profile.update', ['user' => $user->id]) }}" method="post">
                        @csrf
                        <div class="p-6 space-y-6">

                            <!-- Secondary Email Field -->
                            <div class="flex flex-col space-y-2">
                                <label for="email1" class="text-lg font-semibold text-gray-600">Enter your secondary
                                    email</label>
                                <input type="email" id="email1" name="email1" value="{{ $user->email1 }}"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="w-1/2 bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Change Password -->
        <div class="bg-gray-100 flex items-center justify-center">
            <div class="container mx-auto px-4 py-8">
                <!-- Card with shadow and rounded corners -->
                <form action="{{ route('userPassword', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full md:w-full mx-auto">

                        <!-- Header with a red background -->
                        <div class="bg-red-600 text-white font-semibold text-2xl text-center py-2 shadow-md rounded-t-lg">
                            Change Password
                        </div>

                        <div class="p-6 space-y-6">

                            <!-- Current Password Field -->
                            <div class="flex flex-col space-y-2">
                                <label for="current-password" class="text-lg font-semibold text-gray-600">Current
                                    Password</label>
                                <input type="password" id="current-password" name="current_password"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                    placeholder="Enter your current password">
                            </div>

                            <!-- New Password Field -->
                            <div class="flex flex-col space-y-2">
                                <label for="new-password" class="text-lg font-semibold text-gray-600">New Password</label>
                                <input type="password" id="newPassword" name="new_password"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                    placeholder="Enter your new password">
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="flex flex-col space-y-2">
                                <label for="confirm-password" class="text-lg font-semibold text-gray-600">Confirm New
                                    Password</label>
                                <input type="password" id="confirm-password" name="confirm_password"
                                    class="w-full text-gray-800 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                    placeholder="Confirm your new password">
                            </div>

                            <!-- Divider -->
                            <div class="flex items-center justify-between border-b py-2"></div>

                            <!-- Update Password Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="w-1/2 bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                                    Update Password
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Log out Button -->
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit"
                class="mt-6 w-3/4 mx-auto flex justify-center items-center bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                <!-- Icon on the left -->
                <span class="material-icons text-white text-lg mr-2">exit_to_app</span>
                LogOut
            </button>
        </form>
    </div>
@endsection
