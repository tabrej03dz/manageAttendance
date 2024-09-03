@extends('dashboard.layout.root')

@section('content')
    <div class="content">
        <div class="container-fluid p-4">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Employees</h2>
                <h2 class="mb-0">{{ $dates->first()->date->format('Y-M') }} Month</h2>
            </div>

            <!-- Filter and Action Buttons -->
            <div class="d-flex align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                <form action="{{ route('attendance.index', ['user' => $user?->id]) }}" method="GET" class="d-flex">
                    @csrf
                    <input type="date" name="start" placeholder="Start Date" class="form-control mb-2 mr-2"
                        style="width: auto; padding: 0.5rem; border-radius: 0.375rem;">
                    <input type="date" name="end" placeholder="End Date" class="form-control mb-2 mr-2"
                        style="width: auto; padding: 0.5rem; border-radius: 0.375rem;">
                    <input type="submit" value="Filter" class="btn btn-success text-white mb-2 mr-2">
                </form>
                <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}" class="btn btn-primary mb-2 mr-2"
                    style="color: white !important;">Check In</a>
                <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}" class="btn btn-danger mb-2"
                    style="color: white !important;">Check Out</a>
            </div>

            @role('super_admin')
                <!-- Employee List -->
                <div class="mb-4">
                    <div class="row">
                        @foreach ($users as $u)
                            <div class="col-sm-4 col-md-3 col-lg-2 mb-2">
                                <a href="{{ route('attendance.index', ['user' => $u?->id]) }}"
                                    class="btn btn-outline-success w-100 text-truncate">{{ ucfirst($u?->name) }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endrole

            <!-- Attendance Table -->
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark sticky top-0 z-10 bg-white">
                        <tr>
                            <th>Date</th>
                            <th>Check-in Time</th>
                            <th>Late</th>
                            <th>Check-in Image</th>
                            <th>Check-out Time</th>
                            <th>Check-out Image</th>
                            <th>Working Hours</th>
                            <th>Day Type</th>
                            <th>Check-in Distance</th>
                            <th>Check-out Distance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dates as $dateObj)
                            @php
                                $d = \Carbon\Carbon::parse($dateObj->date);
                                $record = $attendanceRecords->first(function ($record) use ($d) {
                                    return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                                });
                            @endphp
                            <tr class="{{ $d->format('D') == 'Sun' ? 'bg-red-100' : '' }} hover:bg-gray-100">
                                <td>{{ $d->format('d-[D]') }}</td>
                                <td>{{ $record?->check_in?->format('h:i:s A') }}</td>
                                <td>{{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : '' }}
                                </td>
                                <td>
                                    @if ($record?->check_in_image)
                                        <a href="{{ asset('storage/' . $record->check_in_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $record->check_in_image) }}"
                                                alt="Check-in Image" class="img-fluid"
                                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.375rem;">
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $record?->check_out?->format('h:i:s A') }}</td>
                                <td>
                                    @if ($record?->check_out_image)
                                        <a href="{{ asset('storage/' . $record->check_out_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $record->check_out_image) }}"
                                                alt="Check-out Image" class="img-fluid"
                                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.375rem;">
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $record?->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : '' }}
                                </td>
                                <td>{{ $record?->day_type }}</td>
                                <td>{{ $record?->check_in_distance }}</td>
                                <td>{{ $record?->check_out_distance }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
