@extends('dashboard.layout.root')
@section('content')
    <!-- CSS for responsive cards -->
    <style>
        .responsive-card {
            width: 100%;
            /* This ensures the card takes up full width of the column */
        }

        /* For extra small and small screens: make cards square */
        @media (max-width: 575.98px),
        (min-width: 576px) and (max-width: 767.98px) {
            .responsive-card {
                aspect-ratio: 1 / 1;
                /* Ensures square shape */
                min-height: 140px;
            }
        }

        /* For medium and large screens: allow flexible card height */
        @media (min-width: 768px) {
            .responsive-card {
                height: auto;
                /* Cards will grow based on content */
            }
        }

        /* General Navbar Styling */
        .navbar-dark {
            padding-bottom: 10px;
            /* Adds space at the bottom */
        }

        /* Link Styling */
        .navbar-dark .nav-link {
            position: relative;
            padding: 10px 15px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            z-index: 1;
            /* Ensures no overlapping */
        }

        /* Hover & Focus States */
        .navbar-dark .nav-link:hover,
        .navbar-dark .nav-link:focus {
            background-color: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
            border-radius: 10px;
        }

        /* Icon Styling */
        .navbar-dark .nav-link i {
            font-size: 1.75rem;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        /* Active Icon Effect */
        .navbar-dark .nav-link.active i {
            color: #ffffff;
            transform: translateY(-5px);
        }

        /* Small text styling */
        .navbar-dark .nav-link .small {
            font-size: 0.85rem;
            opacity: 0.8;
            letter-spacing: 0.5px;
        }
    </style>
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 shadow-sm rounded" style="padding: 15px;">
        <div class="d-flex align-items-center">
            <!-- Smaller Placeholder Avatar -->
            <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : 'https://via.placeholder.com/40' }}"
                alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px;">
            <h4 class="fw-bold mb-0 ml-2" style="font-size: 1.2rem;">{{ auth()->user()->name }}</h4>
        </div>
        @if (auth()->user()->is_accepted == '0')
            <a href="{{ route('policy.read') }}" class="btn btn-outline-danger btn-sm text-decoration-none">Read Policy</a>
        @endif
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm text-decoration-none">LOGOUT</button>
        </form>
    </div>

    @php
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            $employeeIds = $employees->pluck('id');
            $todayCheckIn = \App\Models\AttendanceRecord::whereIn('user_id', $employeeIds)
                ->whereDate('check_in', today())
                ->get();
            $leaves = App\Models\Leave::whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->whereIn('user_id', $employeeIds)
                ->where('status', 'approved')
                ->get();
        }
        if ($user->hasRole('admin')) {
            $employees = $user->office->users;
            $employeeIds = $employees->pluck('id');
            $todayCheckIn = \App\Models\AttendanceRecord::whereIn('user_id', $employeeIds)
                ->whereDate('check_in', today())
                ->get();
            $leaves = App\Models\Leave::whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->whereIn('user_id', $employeeIds)
                ->where('status', 'approved')
                ->get();
        }
    @endphp

    <!-- Check In/Check Out Section -->
    <div class="container mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                            class="btn btn-primary w-100">Check In</a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
                            class="btn btn-danger w-100">Check Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            @role('super_admin|admin')
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $employees->count() }}</h3>

                                <p>All Users</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('employee.index') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    @role('super_admin')
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $offices->count() }}</h3>

                                    <p>Offices</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="{{ route('office.index') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                    @endrole
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $todayCheckIn->count() }}</h3>

                                <p>Check In Today</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="{{ route('attendance.day-wise') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $leaves->count() }}</h3>

                                <p>Users on leave</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            @endrole

            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $data['days'] - ($data['sundays'] + $data['offs']) }}</h3>

                            <p>Office Days</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ route('employee.index') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->

                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $data['records'] }}</h3>

                            <p>Working Days</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('attendance.index') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->

                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $data['sundays'] }}</h3>

                            <p>Sunday</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('attendance.index') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $data['offs'] }}</h3>

                            <p>Offs of the month</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="container pb-20">

                <div class="row row-cols-2 row-cols-md-3 g-4">
                    <div class="col">
                        <a href="{{ route('leave.create') }}" class="text-decoration-none text-reset d-block">
                            <div class="card shadow border-0 h-100 responsive-card">
                                <div
                                    class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                    <i class="fas fa-calendar-alt text-danger mb-3" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">Leave Requests</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('leave.index') }}" class="text-decoration-none text-reset d-block">
                            <div class="card shadow border-0 h-100 responsive-card">
                                <div
                                    class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                    <i class="fas fa-file-alt text-danger mb-3" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">Leaves</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <form action="{{ route('leave.index') }}">
                            <input type="hidden" value="approved" name="status">
                            <button type="submit" class="text-decoration-none text-reset d-block w-100"
                                style="background:none; border:none; padding:0; outline:none;"
                                onfocus="this.style.outline='none';">
                                <div class="card shadow border-0 h-100 responsive-card">
                                    <div
                                        class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <i class="fas fa-check-square text-danger mb-3" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">Approve Leave</p>
                                    </div>
                                </div>
                            </button>
                        </form>
                    </div>
                    <div class="col">
                        <a href="{{ route('attendance.day-wise') }}" class="text-decoration-none text-reset d-block">
                            <div class="card shadow border-0 h-100 responsive-card">
                                <div
                                    class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                    <i class="fas fa-id-card text-danger mb-3" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">Attendance Records</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" class="text-decoration-none text-reset d-block">
                            <div class="card shadow border-0 h-100 responsive-card">
                                <div
                                    class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                    <i class="fas fa-pencil-alt text-danger mb-3" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">Edit Records</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" class="text-decoration-none text-reset d-block">
                            <div class="card shadow border-0 h-100 responsive-card">
                                <div
                                    class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                    <i class="fas fa-user-circle text-danger mb-3" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">User Profiles</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- New Cards -->
                    <div class="col">
                        <a href="#" class="text-decoration-none text-reset d-block">
                            <div class="card shadow border-0 h-100 responsive-card">
                                <div
                                    class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                    <i class="fas fa-clock text-danger mb-3" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">Work Hours</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" class="text-decoration-none text-reset d-block">
                            <div class="card shadow border-0 h-100 responsive-card">
                                <div
                                    class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                    <i class="fas fa-user text-danger mb-3" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">Employee Profiles</p>
                                </div>
                            </div>
                        </a>

                        <!-- /.row -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
