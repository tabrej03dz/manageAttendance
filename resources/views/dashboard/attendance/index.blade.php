@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4 d-inline-block">{{ $dates->first()->date->format('Y-M') }} Month</h2>
        <form action="{{ route('attendance.index', ['user' => $user?->id]) }}" class="d-inline-block ml-2">
            @csrf
            <input type="date" name="start" placeholder="Start Date" class="form-control d-inline-block mb-2 mb-sm-0"
                style="width: auto;">
            <input type="date" name="end" placeholder="End Date" class="form-control d-inline-block mb-2 mb-sm-0"
                style="width: auto;">
            <input type="submit" value="Filter" class="btn btn-success mb-2 mb-sm-0">
        </form>
        <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
            class="btn btn-primary ml-2 mb-2 mb-sm-0">Check In</a>
        <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
            class="btn btn-danger ml-2 mb-2 mb-sm-0">Check Out</a> <br>

        @role('super_admin')
            @foreach($users as $u)
                <a href="{{route('attendance.index', ['user' => $u?->id])}}" class="btn btn-success m-2">{{ucfirst($u?->name)}}</a>
            @endforeach
        @endrole

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
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
                        <tr>

                            <td>{{ $d->format('d-[D]') }}</td>
                            <td>{{ $record?->check_in?->format('h:i:s') }}</td>
                            <td>{{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : '' }}</td>
                            <td>
                                @if($record?->check_in_image)
                                <img src="{{ asset('storage/' . $record?->check_in_image) }}" alt="Check-in Image"
                                    class="img-fluid" style="max-width: 100px;">
                                @endif
                            </td>
                            <td>{{ $record?->check_out?->format('h:i:s') }}</td>
                            <td>
                                @if($record?->check_out_image)
                                <img src="{{ asset('storage/' . $record?->check_out_image) }}" alt="Check-out Image"
                                    class="img-fluid" style="max-width: 100px;">
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
@endsection
