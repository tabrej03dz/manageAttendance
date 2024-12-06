
{{-- Records Page  --}}

@extends('dashboard.layout.root')

@section('content')


    <form action="{{ route('attendance.index', ['user' => $user?->id]) }}">
        <div class="bg-gray-100 p-4 rounded-lg shadow-md">
            <!-- Employee Selection -->
            @role('super_admin|admin')
                <div class="mb-4">
                    <label for="employee-select" class="block mb-1 text-sm font-medium text-gray-700">Select Employee:</label>
                    <select id="employee-select" name="employee"
                        class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select an employee</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" {{ $u->id == $user?->id ? 'selected' : '' }}>{{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endrole

            <!-- Date Filter Section -->
            <div class="md:hidden space-y-4">
                <div class="space-y-4">
                    <div class="flex flex-col w-full">
                        <label for="from-date-mobile" class="mb-1 text-sm font-medium text-gray-700">From:</label>
                        <input type="date" value="{{$monthStart ? \Carbon\Carbon::parse($monthStart)->format('Y-m-d') : ''}}" id="from-date-mobile" name="start"
                            class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Select From Date">
                    </div>
                    <div class="flex flex-col w-full">
                        <label for="to-date-mobile" class="mb-1 text-sm font-medium text-gray-700">To:</label>
                        <input type="date" id="to-date-mobile" value="{{$endOfMonth ? \Carbon\Carbon::parse($endOfMonth)->format('Y-m-d') : ''}}" name="end"
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
                <div class="flex items-end space-x-4">
                    <div class="flex-grow flex space-x-4">
                        <div class="flex-1">
                            <label for="from-date-web" class="block mb-1 text-sm font-medium text-gray-700">From:</label>
                            <input type="date" id="from-date-web" name="start" value="{{ $monthStart ? \Carbon\Carbon::parse($monthStart)->format('Y-m-d') : '' }}"
                                class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Select From Date">
                        </div>
                        <div class="flex-1">
                            <label for="to-date-web" class="block mb-1 text-sm font-medium text-gray-700">To:</label>
                            <input type="date" id="to-date-web" name="end" value="{{ $endOfMonth ? \Carbon\Carbon::parse($endOfMonth)->format('Y-m-d') : '' }}"
                                class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Select To Date">
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">Filter</button>
                        <a href="{{ route('attendance.index') }}"
                            class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out">Clear</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Attendance Records Section -->
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="container mx-auto px-6">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden w-full md:max-w-5xl mx-auto">
                <div class="p-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Attendance Records</h2>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow-sm">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Time</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Late</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Image</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Note</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Time</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Image</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Note</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Working Hours</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Day Type</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Distance</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Distance</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in By</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out By</th>
                                    <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Breaks</th>

                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                                @php
                                    $officeDays = 0;
                                    $workingDays = 0;
                                    $leaveDays = 0;
                                    $offDays = 0;
                                @endphp

                                @foreach ($dates as $dateObj)
                                    @php

                                        $d = \Carbon\Carbon::parse($dateObj->date);
                                        $record = $attendanceRecords->first(function ($record) use ($d) {
                                            return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                                        });
                                        $isSunday = $d->format('D') === 'Sun';

                                        if ($record) {
                                            $workingDays++;
                                        }

                                        $leave = App\Models\Leave::whereDate('start_date', '<=', $d)
                                            ->whereDate('end_date', '>=', $d)
                                            ->where([
                                                'user_id' => $user ? $user->id : auth()->user()->id,
                                                'status' => 'approved',
                                            ])
                                            ->first();
                                        if ($leave) {
                                            $leaveDays++;
                                        }
                                        $off = App\Models\Off::whereDate('date', $d)
                                            ->where('office_id', auth()->user()->office_id)->where('is_off', '1')
                                            ->first();
                                        if ($off) {
                                            $offDays++;
                                        }
                                        if (!$isSunday) {
                                            $officeDays++;
                                        }
                                    @endphp

                                    @if ($leave)
                                        <!-- Sample Data Row -->
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $off ? $d->format('d-[D]') . ' ' . $off?->title . ' (OFF)' : $d->format('d-[D]') }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700 text-center text-lg" colspan="8">
                                                {{ $leave->leave_type . ' leave' }}</td>

                                        </tr>
                                    @else

                                        <!-- Sample Data Row -->
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $off ? $d->format('d-[D]') . ' ' . $off?->title . ' (OFF)' : $d->format('d-[D]') }}
                                            </td>

                                            <td class="px-4 py-4 text-sm text-{{ Carbon\Carbon::parse($record?->check_in)->format('H:i:s') < Carbon\Carbon::parse($record?->user->check_in_time)->format('H:i:s') ? 'green' : ($record?->late ? 'red' : 'grey') }}-700">
                                                {{ $record?->check_in?->format('h:i:s A') }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                @if ($record)
                                                    <img src="{{ asset('storage/' . $record?->check_in_image) }}"
                                                        alt="Check-in" class="w-10 h-10 rounded-full">
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                <span title="{{$record?->check_in_note_status}}" class="badge bg-light text-dark" >{{$record?->check_in_note}}
                                                    @if($record?->check_in_note && $record->check_in_note_status == 'rejected')
                                                        <i class="fas fa-times text-danger" style="margin-left: 5px;"></i>
                                                    @elseif($record?->check_in_note && $record->check_in_note_status == 'approved')
                                                        <i class="fas fa-check text-success" style="margin-left: 5px;"></i>
                                                    @elseif($record?->check_in_note && $record->check_in_note_status == 'pending')
                                                        <i class="text-warning" style="margin-left: 5px;">P</i>
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-{{ Carbon\Carbon::parse($record?->check_out)->format('H:i:s') > Carbon\Carbon::parse($record?->user->check_out_time)->format('H:i:s') ? 'green' : 'red' }}-700">
                                                {{ $record?->check_out?->format('h:i:s A') }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                @if ($record)
                                                    <img src="{{ asset('storage/' . $record?->check_out_image) }}"
                                                        alt="Check-in" class="w-10 h-10 rounded-full">
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                <span title="{{$record?->check_out_note_status}}" class="badge bg-light text-dark" >{{$record?->check_out_note}}
                                                    @if($record?->check_out_note && $record->check_out_note_status == 'rejected')
                                                        <i class="fas fa-times text-danger" style="margin-left: 5px;"></i>
                                                    @elseif($record?->check_out_note && $record->check_out_note_status == 'approved')
                                                        <i class="fas fa-check text-success" style="margin-left: 5px;"></i>
                                                    @elseif($record?->check_out_note && $record->check_out_note_status == 'pending')
                                                        <i class="text-warning" style="margin-left: 5px;">P</i>
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $record?->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : '' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">{{ $record?->day_type }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ round($record?->check_out_distance) }} m</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ round($record?->check_out_distance) }} m</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $record?->checkInBy?->name }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $record?->checkOutBy?->name }}</td>
                                            @php
                                                $breaks = $record?->breaks
                                            @endphp
                                        @if($breaks)
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                            <ul>
                                            @foreach($record?->breaks as $break)
                                                @php
                                                    $start_time = \Carbon\Carbon::parse($break->start_time);
                                                    $end_time = \Carbon\Carbon::parse($break->end_time);
                                                @endphp
                                                <li>{{ $start_time->diffInMinutes($end_time) }}</li>
                                                <hr>
                                            @endforeach
                                            </ul>
                                            </td>
                                        @endif

                                            <td>
                                                @if($record)
                                                <a href="{{route('correctionNote.create', ['record' => $record->id])}}">Correction Note</a>
                                                @endif
                                            </td>

                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr class="border-gray-300 border-4 my-6" />
                    <!-- Summary Information -->
                    <div class="mt-6 bg-red-50 p-4 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-800">Summary Information</h3>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Office Days:</span>
                                <span class="font-bold text-gray-800">{{ $officeDays }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Working Days:</span>
                                <span class="font-bold text-gray-800">{{ $workingDays }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Leaves:</span>
                                <span class="font-bold text-gray-800">{{ $leaveDays }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


@endsection
