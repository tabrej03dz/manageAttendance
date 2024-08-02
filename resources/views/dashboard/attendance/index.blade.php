@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">{{$dates->first()->date->format('Y-M')}} Month</h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>Date</th>
                <th>Check-in Time</th>
                <th>Check-in Image</th>
                <th>Check-out Time</th>
                <th>Check-out Image</th>
                <th>Working Hours</th>
                <th>Check-in/out</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dates as $dateObj)
                @php
                    $d = \Carbon\Carbon::parse($dateObj->date); // Access the 'date' property and convert to Carbon instance
                $record = $attendanceRecords->first(function ($record) use ($d) {
                    return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                });

                @endphp
                <tr>
                    <td>{{ $d->format('d-[D]') }}</td>
                    <td>{{$record?->check_in->format('h:i:s')}}</td>
                    <td></td>
                    <td>{{$record?->check_out?->format('h:i:s')}}</td>
                    <td></td>
                    <td>{{$record?->duration}}</td>
                    <td>
                        <a href="{{route('attendance.check_in')}}" class="btn btn-primary">Check In</a>
                        <a href="{{route('attendance.check_out')}}" class="btn btn-danger">Check Out</a>
                    </td>
                </tr>
            @endforeach
            <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>

@endsection
