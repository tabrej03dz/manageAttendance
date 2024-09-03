@extends('dashboard.layout.root')

@section('content')
    <div class="content">
        <div class="container-fluid">
            {{-- <h2 class="mb-4 d-inline-block">{{ $dates->first()->date->format('Y-M') }} Month</h2> --}}
            <form action="{{ route('attendance.day-wise') }}" method="GET" class="d-inline-block ml-2">
                @csrf
                <input type="date" name="date" placeholder="Date" class="form-control d-inline-block mb-2 mb-sm-0"
                    style="width: auto;">
                <input type="submit" value="Filter" class="btn btn-success mb-2 mb-sm-0">
                <a href="{{ route('attendance.day-wise') }}" class="btn btn-info ">Clear</a>
            </form>

            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
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
                        @foreach ($employees as $employee)
                            @php
                                $record = \App\Models\AttendanceRecord::where('user_id', $employee->id)
                                    ->whereDate('created_at', $date)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $record?->check_in?->format('h:i:s A') }}</td>
                                <td>{{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : '' }}
                                </td>
                                <td>
                                    @if ($record?->check_in_image)
                                        <a href="{{ asset('storage/' . $record->check_in_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $record->check_in_image) }}"
                                                alt="Check-in Image" class="img-fluid" style="max-width: 100px;">
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $record?->check_out?->format('h:i:s A') }}</td>
                                <td>
                                    @if ($record?->check_out_image)
                                        <a href="{{ asset('storage/' . $record->check_out_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $record->check_out_image) }}"
                                                alt="Check-out Image" class="img-fluid" style="max-width: 100px;">
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
