@extends('dashboard.layout.root')

@section('content')
    <div class="content">
        <div class="container-fluid p-4">
            <!-- Header Section -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <h2 class="mb-2 mb-md-0">Employees</h2>
                <h2 class="mb-2 mb-md-0">{{ $dates->first()->date->format('Y-M') }} Month</h2>
            </div>

            <!-- Filter and Action Buttons -->
            <div class="row align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                <div class="col-12 col-md-6 mb-2">
                    <form action="{{ route('attendance.index', ['user' => $user?->id]) }}" method="GET" class="d-flex flex-column flex-md-row">
                        @csrf
                        <input type="date" name="start" placeholder="Start Date" class="form-control mb-2 mr-md-2" style="flex: 1;">
                        <input type="date" name="end" placeholder="End Date" class="form-control mb-2 mr-md-2" style="flex: 1;">
                        <input type="submit" value="Filter" class="btn btn-success text-white mb-2">
                    </form>
                </div>
                <div class="col-12 col-md-6 text-center text-md-right">
                    <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}" class="btn btn-primary text-white mb-2 mb-md-0 mr-md-2">Check In</a>
                    <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}" class="btn btn-danger text-white mb-2">Check Out</a>
                </div>
            </div>

            @role('super_admin')
                <!-- Employee List -->
                <div class="mb-4">
                    <div class="row">
                        @foreach ($users as $u)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-2">
                                <a href="{{ route('attendance.index', ['user' => $u?->id]) }}" class="btn btn-outline-success w-100 text-truncate">{{ ucfirst($u?->name) }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endrole

            <!-- Attendance Table -->
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark sticky-top bg-white">
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
                                <td>{{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : '' }}</td>
                                <td>
                                    @if ($record?->check_in_image)
                                        <a href="{{ asset('storage/' . $record->check_in_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $record->check_in_image) }}" alt="Check-in Image" class="img-fluid" style="max-width: 100px; height: auto; object-fit: cover; border-radius: 0.375rem;">
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $record?->check_out?->format('h:i:s A') }}</td>
                                <td>
                                    @if ($record?->check_out_image)
                                        <a href="{{ asset('storage/' . $record->check_out_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $record->check_out_image) }}" alt="Check-out Image" class="img-fluid" style="max-width: 100px; height: auto; object-fit: cover; border-radius: 0.375rem;">
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $record?->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : '' }}</td>
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
