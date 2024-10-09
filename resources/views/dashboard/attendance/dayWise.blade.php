{{--@extends('dashboard.layout.root')--}}

{{--@section('content')--}}
{{--    <div class="content">--}}
{{--        <div class="container-fluid">--}}
{{--            --}}{{-- <h2 class="mb-4 d-inline-block">{{ $dates->first()->date->format('Y-M') }} Month</h2> --}}
{{--            <form action="{{ route('attendance.day-wise') }}" method="GET" class="d-inline-block ml-2">--}}
{{--                @csrf--}}
{{--                <input type="date" name="date" placeholder="Date" class="form-control d-inline-block mb-2 mb-sm-0"--}}
{{--                    style="width: auto;">--}}
{{--                <input type="submit" value="Filter" class="btn btn-success mb-2 mb-sm-0">--}}
{{--                <a href="{{ route('attendance.day-wise') }}" class="btn btn-info ">Clear</a>--}}
{{--            </form>--}}

{{--            <div class="table-responsive mt-3">--}}
{{--                <table class="table table-striped table-bordered">--}}
{{--                    <thead class="thead-dark">--}}
{{--                        <tr>--}}
{{--                            <th>#</th>--}}
{{--                            <th>Name</th>--}}
{{--                            <th>Check-in Time</th>--}}
{{--                            <th>Late</th>--}}
{{--                            <th>Check-in Image</th>--}}
{{--                            <th>Check-out Time</th>--}}
{{--                            <th>Check-out Image</th>--}}
{{--                            <th>Working Hours</th>--}}
{{--                            <th>Day Type</th>--}}
{{--                            <th>Check-in Distance</th>--}}
{{--                            <th>Check-out Distance</th>--}}
{{--                        </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                        @foreach ($employees as $employee)--}}

{{--                            @php--}}
{{--                                $record = \App\Models\AttendanceRecord::where('user_id', $employee->id)--}}
{{--                                    ->whereDate('created_at', $date)--}}
{{--                                    ->first();--}}
{{--                            @endphp--}}
{{--                            <tr>--}}
{{--                                <td>{{ $loop->iteration }}</td>--}}
{{--                                <td>{{ $employee->name }}</td>--}}
{{--                                <td>{{ $record?->check_in?->format('h:i:s A') }}</td>--}}
{{--                                <td>{{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : '' }}--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    @if ($record?->check_in_image)--}}
{{--                                        <a href="{{ asset('storage/' . $record->check_in_image) }}" target="_blank">--}}
{{--                                            <img src="{{ asset('storage/' . $record->check_in_image) }}"--}}
{{--                                                alt="Check-in Image" class="img-fluid" style="max-width: 100px;">--}}
{{--                                        </a>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                                <td>{{ $record?->check_out?->format('h:i:s A') }}</td>--}}
{{--                                <td>--}}
{{--                                    @if ($record?->check_out_image)--}}
{{--                                        <a href="{{ asset('storage/' . $record->check_out_image) }}" target="_blank">--}}
{{--                                            <img src="{{ asset('storage/' . $record->check_out_image) }}"--}}
{{--                                                alt="Check-out Image" class="img-fluid" style="max-width: 100px;">--}}
{{--                                        </a>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                                <td>{{ $record?->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : '' }}--}}
{{--                                </td>--}}
{{--                                <td>{{ $record?->day_type }}</td>--}}
{{--                                <td>{{ $record?->check_in_distance }}</td>--}}
{{--                                <td>{{ $record?->check_out_distance }}</td>--}}
{{--                            </tr>--}}
{{--                        @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}
















{{-- Attandance Records --}}

@extends('dashboard.layout.root')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Include Flatpickr CSS for the date picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <!-- Mobile View -->
        <div class="md:hidden space-y-4">
            <div class="space-y-4">
                <div class="flex flex-col w-full">
                    <label for="to-date-mobile" class="mb-1 text-sm font-medium text-gray-700">Select Date:</label>
                    <input type="text" id="to-date-mobile" name="date"
                        class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Select To Date">
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <button
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out w-full">Filter</button>
                <button
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out w-full">Clear</button>
            </div>
        </div>

        <!-- Web View (enhanced) -->
        <div class="hidden md:block">
            <form action="{{route('attendance.day-wise')}}">
                <div class="flex items-end space-x-4">
                    <div class="flex-grow flex space-x-4">
                        <div class="flex-1">
                            <label for="to-date-web" class="block mb-1 text-sm font-medium text-gray-700">Select Date:</label>
                            <input type="text" id="to-date-web" name="date"
                                class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Select To Date">
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">Filter</button>
                        <button
                            class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out">Clear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- Attendance Records Section -->
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="container mx-auto px-6">
            <!-- Full width on web, max width on mobile -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden w-full md:max-w-4xl mx-auto">
                <div class="p-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Attendance Records</h2>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow-sm">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-In
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Image
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Time
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Image
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Late
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Working Hours
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Day Type
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Distance
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Distance
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($employees as $employee)
                                @php
                                    $record = \App\Models\AttendanceRecord::where('user_id', $employee->id)
                                        ->whereDate('created_at', $date)
                                        ->first();

                                        $leave = App\Models\Leave::whereDate('start_date', '<=', $date)
                                        ->whereDate('end_date', '>=', $date)->where(['user_id' => $employee->id, 'status' => 'approved'])
                                        ->first();

                                @endphp
                                <!-- Sample Data Row -->
                                @if($leave)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            {{$employee->name}}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700 text-center text-lg" colspan="8" >{{ $leave->leave_type.' leave'}}</td>
                                    </tr>
                                @else
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        {{$employee->name}}
                                    </td>
                                    @if(Carbon\Carbon::parse($record?->check_in)->format('H:i:s') < Carbon\Carbon::parse($employee->check_in_time)->format('H:i:s'))
                                        <td class="px-4 py-4 text-sm text-green-700">
                                            {{ $record?->check_in?->format('h:i:s A') }}
                                        </td>
                                    @else
                                        <td class="px-4 py-4 text-sm text-{{$record?->late ? 'red' : 'grey'}}-700">
                                            {{ $record?->check_in?->format('h:i:s A') }}
                                        </td>
                                    @endif

                                    <td class="px-4 py-4">
                                    @if($record)
                                        <img src="{{$record?->check_in_image ? asset('storage/'.$record->check_in_image) : 'https://via.placeholder.com/50'}}" alt="Check-in Image"
                                            class="w-12 h-12 object-cover rounded-lg shadow-sm">
                                    @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-{{Carbon\Carbon::parse($record?->check_out)->format('H:i:s') > Carbon\Carbon::parse($employee->check_out_time)->format('H:i:s') ? 'red' : 'green'}}-700">
                                        {{ $record?->check_out?->format('h:i:s A') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($record)
                                        <a href="{{asset('storage/'.$record->check_out_image)}} " target="_blank">
                                        <img src="{{ $record->check_out_image ? asset('storage/'.$record->check_out_image) : 'https://via.placeholder.com/50'}}" alt="Check-in Image"
                                            class="w-12 h-12 object-cover rounded-lg shadow-sm"></a>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-green-800">
                                            {{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : '' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        {{ $record?->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : '' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        {{$record?->day_type}}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-{{$record?->check_in_distance > 100 ? 'red' : 'grey'}}-700">
                                        {{round($record?->check_in_distance)}}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-{{$record?->check_in_distance > 100 ? 'red' : 'grey'}}-700">
                                        {{round($record?->check_out_distance)}}
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination (if required) -->
{{--                    <div class="mt-6 flex justify-between">--}}
{{--                        <button class="text-sm text-red-600 hover:text-red-800 flex items-center">--}}
{{--                            <span class="material-icons text-red-600 mr-2">chevron_left</span>Previous--}}
{{--                        </button>--}}
{{--                        <button class="text-sm text-red-600 hover:text-red-800 flex items-center">--}}
{{--                            Next<span class="material-icons text-red-600 ml-2">chevron_right</span>--}}
{{--                        </button>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Bar with Hover Effects & Elevated Design -->
    <nav class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl">
        <div class="flex justify-around py-4">
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">home</span>
                <span class="text-xs text-white mt-1">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">list</span>
                <span class="text-xs text-white mt-1">My Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">folder</span>
                <span class="text-xs text-white mt-1">Records</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">access_time</span>
                <span class="text-xs text-white mt-1">Attendance</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">account_circle</span>
                <span class="text-xs text-white mt-1">Account</span>
            </a>
        </div>
    </nav>

    <!-- Initialize Flatpickr for date inputs -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#from-date-mobile, #to-date-mobile, #from-date-web, #to-date-web", {
                dateFormat: "Y-m-d",
                allowInput: true,
                altInput: true,
                altFormat: "F j, Y",
                showMonths: 1
            });
        });
    </script>
@endsection
