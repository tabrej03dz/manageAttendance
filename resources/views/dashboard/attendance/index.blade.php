@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4 d-inline-block">{{$dates->first()->date->format('Y-M')}} Month</h2>
        <form action="{{route('attendance.index')}}" class="d-inline-block ml-2">
            @csrf
            <input type="date" name="start" placeholder="Start Date">
            <input type="date" name="end" placeholder="End Date">
            <input type="submit" value="Filter" class="btn btn-success">
        </form>
        <a href="{{route('attendance.form', ['form_type' => 'check_in'])}}" class="btn btn-primary ml-2">Check In</a>
        <a href="{{route('attendance.form', ['form_type' => 'check_out'])}}" class="btn btn-danger ml-2">Check Out</a>
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
                    <td>
                        <img src="{{asset('storage/'. $record?->check_in_image)}}" alt="">
                    </td>
                    <td>{{$record?->check_out?->format('h:i:s')}}</td>
                    <td><img src="{{asset('storage/'. $record?->check_out_image)}}" alt=""></td>
                    <td>{{$record?->duration}}</td>
                </tr>
            @endforeach
            <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>

@endsection
