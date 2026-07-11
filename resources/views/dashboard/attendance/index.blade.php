{{-- Records Page  --}}

@extends('dashboard.layout.root')

@section('title', 'Attendance Records')

@push('styles')
<style>
    .attendance-records-page {
        font-family: 'Inter', sans-serif;
        color: #0f172a;
    }

    .attendance-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #312e81;
        border-radius: 24px;
        background: linear-gradient(135deg, #0f172a 0%, #172554 52%, #312e81 100%);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.24);
    }

    .attendance-hero::before {
        content: '';
        position: absolute;
        width: 280px;
        height: 280px;
        right: -80px;
        top: -120px;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.32);
    }

    .attendance-hero::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        left: 35%;
        bottom: -150px;
        border-radius: 999px;
        background: rgba(6, 182, 212, 0.18);
    }

    .attendance-panel {
        border: 1px solid #dbe3ee;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.10);
    }

    .filter-panel {
        border: 1px solid #cbd5e1;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .filter-label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .filter-control {
        width: 100%;
        min-height: 46px;
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 10px 13px !important;
        font-size: 14px;
        font-weight: 600;
        outline: none;
        box-shadow: none !important;
    }

    .filter-control:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, .12) !important;
    }

    .record-button {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        border-radius: 12px;
        padding: 10px 17px;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none !important;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .record-button:hover {
        transform: translateY(-1px);
    }

    .button-filter {
        background: linear-gradient(135deg, #4338ca, #6366f1);
        color: #ffffff !important;
        box-shadow: 0 10px 20px rgba(79, 70, 229, .22);
    }

    .button-clear {
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #334155 !important;
    }

    .button-pdf {
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #ffffff !important;
        box-shadow: 0 10px 20px rgba(217, 119, 6, .20);
    }

    .records-table-wrap {
        border: 1px solid #dbe3ee;
        border-radius: 16px;
        overflow: auto;
        background: #ffffff;
        max-height: 70vh;
    }

    .records-table {
        width: 100%;
        min-width: 1900px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .records-table thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        border-bottom: 1px solid #cbd5e1;
        background: #eaf0f8 !important;
        color: #334155 !important;
        padding: 13px 14px;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .06em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .records-table tbody td {
        border-bottom: 1px solid #e8edf3;
        background: #ffffff;
        color: #334155;
        padding: 13px 14px;
        font-size: 13px;
        font-weight: 600;
        vertical-align: middle;
        white-space: nowrap;
    }

    .records-table tbody tr:nth-child(even) td {
        background: #f8fafc;
    }

    .records-table tbody tr:hover td {
        background: #eef2ff !important;
    }

    .attendance-photo {
        width: 42px;
        height: 42px;
        border: 2px solid #ffffff;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .18);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        max-width: 220px;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #f8fafc;
        color: #334155;
        padding: 5px 9px;
        font-size: 11px;
        font-weight: 800;
        white-space: normal;
    }

    .date-cell {
        min-width: 150px;
        color: #0f172a !important;
        font-weight: 800 !important;
    }

    .day-note {
        display: block;
        margin-top: 5px;
        white-space: normal;
    }

    .summary-panel {
        border: 1px solid #c7d2fe;
        border-radius: 18px;
        background: linear-gradient(135deg, #eef2ff, #f8fafc);
        box-shadow: 0 10px 25px rgba(79, 70, 229, .10);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .summary-item {
        border: 1px solid #dbe3ee;
        border-radius: 14px;
        background: #ffffff;
        padding: 14px;
    }

    .summary-item span:first-child {
        display: block;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .summary-item span:last-child {
        display: block;
        margin-top: 5px;
        color: #0f172a;
        font-size: 19px;
        font-weight: 900;
    }

    .empty-value {
        color: #94a3b8;
    }

    .records-scroll::-webkit-scrollbar,
    .records-table-wrap::-webkit-scrollbar {
        width: 9px;
        height: 9px;
    }

    .records-scroll::-webkit-scrollbar-thumb,
    .records-table-wrap::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: linear-gradient(90deg, #6366f1, #0891b2);
    }

    .records-scroll::-webkit-scrollbar-track,
    .records-table-wrap::-webkit-scrollbar-track {
        background: #e2e8f0;
    }

    @media (max-width: 1024px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .attendance-hero {
            border-radius: 18px;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .record-button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="attendance-records-page space-y-6 pb-10">



<section class="attendance-hero p-6 sm:p-8">
    <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-bold text-white">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Attendance Management
            </div>

            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Attendance Records
            </h1>

            <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">
                Employee attendance, check-in, check-out, breaks, distance और monthly salary summary एक जगह देखें।
            </p>
        </div>

        <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-4 text-white">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Selected Month</p>
            <p class="mt-1 text-xl font-extrabold">
                {{ !empty($month) ? \Carbon\Carbon::parse($month . '-01')->format('F Y') : now()->format('F Y') }}
            </p>
        </div>
    </div>
</section>

<form action="{{ route('attendance.index') }}" method="GET" class="filter-panel p-5 sm:p-6">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end">
        @role('super_admin|admin|owner')
            <div class="lg:col-span-5">
                <label for="employee-select" class="filter-label">Select Employee</label>
                <select id="employee-select" name="employee" class="filter-control">
                    <option value="">Select an employee</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ (string) $u->id === (string) $user?->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endrole

        <div class="lg:col-span-4">
            <label for="month-selector" class="filter-label">Select Month</label>
            <input type="month"
                   id="month-selector"
                   name="month"
                   value="{{ $month ?? now()->format('Y-m') }}"
                   class="filter-control">
        </div>

        <div class="flex flex-col gap-2 sm:flex-row lg:col-span-3">
            <button type="submit" class="record-button button-filter flex-1">
                <i class="fas fa-filter"></i>
                Filter
            </button>

            <a href="{{ route('attendance.index') }}" class="record-button button-clear flex-1">
                <i class="fas fa-rotate-left"></i>
                Clear
            </a>
        </div>
    </div>
</form>









    <!-- Attendance Records Section -->
    <section class="attendance-panel overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Monthly Attendance Details</h2>
                <p class="mt-1 text-sm font-medium text-slate-500">
                    {{ $user?->name ?? auth()->user()->name }} की day-wise attendance details।
                </p>
            </div>

            <span class="inline-flex w-max items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-extrabold text-indigo-700">
                <i class="fas fa-calendar-days"></i>
                {{ $dates->count() }} Days
            </span>
        </div>

        <div class="p-4 sm:p-6">
            <!-- Attendance Table -->
            <div class="records-table-wrap records-scroll">
                <table class="records-table">
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
                            <tbody>

                            @php
                                $workingDays = 0;
                                $leaveDays = 0;
                                $paidLeave = 0;
                                $unpaidLeave = 0;
                                $offDays = 0;
                                $lateCount = 0;
                                $lateTime = 0;
                                $goneBeforeTime = 0;
                                $goneBeforeTimeCount = 0;
                                $halfDayCount = 0;
                                $workingDuration = 0;
                                $sundayCount = 0;
                                $officeDays = 0;
                            @endphp

                                @foreach ($dates as $dateObj)
                                @php
                                        $currentUser = $user ?? auth()->user();
                                        $d = \Carbon\Carbon::parse($dateObj->date);
                                        //dd($d->toDateString());
                                        $record = $attendanceRecords
                                            ->where('user_id', $currentUser->id)
                                            ->first(function ($record) use ($d) {
                                                return $record->check_in->format('Y-m-d') == $d->format('Y-m-d');
                                            });

                                        if($d->format('[D]') == '[Sun]'){
                                            $sundayCount++;
                                        }
                                        if ($record) {
                                            if($record->check_in && $record->check_out){
                                                $workingDays++;

                                            }else{
                                                $halfDayCount++;
                                            }
                                            if ($record->late) {
                                                $lateCount++;
                                                $lateTime += $record->late;
                                            }
                                            if ($record->check_out && Carbon\Carbon::parse($record?->check_out)->format('H:i') < Carbon\Carbon::parse($currentUser->check_out_time)->format('H:i')) {
                                            $goneBeforeTimeCount++;
                                                $checkOutTime = Carbon\Carbon::parse($record?->check_out)->format('H:i'); // Convert datetime to time (H:i:s)
                                                $userCheckOutTime = Carbon\Carbon::parse($currentUser->check_out_time); // Already a time

                                                $goneBeforeTime += Carbon\Carbon::createFromFormat('H:i', $checkOutTime)->diffInMinutes($userCheckOutTime);
                                            }
                                            $workingDuration += $record->duration;

                                        }

                                        $leave = App\Models\Leave::whereDate('start_date', '<=', $d)
                                            ->whereDate('end_date', '>=', $d)
                                            ->where(['user_id' => $currentUser->id])
                                            ->first();
                                        if ($leave) {
                                            if($leave->approve_as == 'paid'){
                                                        $paidLeave++;
                                                    }else{
                                                        $unpaidLeave++;
                                                    }
                                                    $leaveDays++;
                                        }
                                        $off = App\Models\Off::whereDate('date', $d)
                                            ->where('office_id', $currentUser->office_id)->where('is_off', '1')
                                            ->first();
                                        if ($off) {
                                            $offDays++;
                                        }
                                        $halfDayRecord = App\Models\HalfDay::where('date', $d)->where('user_id', $currentUser->id)->first();
                                        if($halfDayRecord){
                                            $halfDayCount++;
                                        }
                                @endphp

{{--                                    @if ($leave && !$record)--}}
                                        <!-- Sample Data Row -->
{{--                                        <tr>--}}
{{--                                            <td class="px-4 py-4 text-sm text-gray-700">--}}
{{--                                                {{ $off ? $d->format('d-[D]') . ' ' . $off?->title . ' (OFF)' : $d->format('d-[D]') }}--}}
{{--                                            </td>--}}
{{--                                            <td class="px-4 py-4 text-sm text-gray-700 text-center text-lg" colspan="8">--}}
{{--                                                {{ $leave->leave_type . ' leave' }}</td>--}}

{{--                                        </tr>--}}
{{--                                    @else--}}

                                        <!-- Sample Data Row -->
                                        <tr>
                                            <td class="date-cell">
                                                {{ $off ? $d->format('d-[D]') . ' ' . $off?->title . ' (OFF)' : $d->format('d-[D]') }}
                                                <span class="text-red-700">{{$leave ? 'Leave: '.$leave->status : ''}}</span>
                                                @if($halfDayRecord)
                                                <span class="text-red-700">{{  'Half Day: '.$halfDayRecord->status }}</span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-sm text-{{ Carbon\Carbon::parse($record?->check_in)->format('H:i:s') < Carbon\Carbon::parse($record?->user->check_in_time)->format('H:i:s') ? 'green' : ($record?->late ? 'red' : 'grey') }}-700">
                                                {{ $record?->check_in?->format('h:i:s A') }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                @if ($record && $record->check_in_image)
                                                    @php
                                                        $imagePath = ltrim($record->check_in_image, '/');

                                                        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                                                            $checkInImageUrl = $imagePath;
                                                        } else {
                                                            $cleanPath = str_replace('storage/', '', $imagePath);

                                                            if (file_exists(public_path('storage/' . $cleanPath))) {
                                                                $checkInImageUrl = asset('storage/' . $cleanPath);
                                                            } else {
                                                                $checkInImageUrl = 'https://attendance.realvictorygroups.com/storage/' . $cleanPath;
                                                            }
                                                        }
                                                    @endphp

                                                    <a href="{{ $checkInImageUrl }}" target="_blank">
                                                        <img src="{{ $checkInImageUrl }}"
                                                            alt="Check-in"
                                                            class="attendance-photo">
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                <span title="{{$record?->check_in_note_status}}" class="status-pill" >{{$record?->check_in_note}}
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
                                                @if ($record && $record->check_out_image)
                                                    @php
                                                        $imagePath = ltrim($record->check_out_image, '/');

                                                        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                                                            $checkOutImageUrl = $imagePath;
                                                        } else {
                                                            $cleanPath = str_replace('storage/', '', $imagePath);

                                                            if (file_exists(public_path('storage/' . $cleanPath))) {
                                                                $checkOutImageUrl = asset('storage/' . $cleanPath);
                                                            } else {
                                                                $checkOutImageUrl = 'https://attendance.realvictorygroups.com/storage/' . $cleanPath;
                                                            }
                                                        }
                                                    @endphp

                                                    <a href="{{ $checkOutImageUrl }}" target="_blank">
                                                        <img src="{{ $checkOutImageUrl }}"
                                                            alt="Check-out"
                                                            class="attendance-photo">
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                <span title="{{$record?->check_out_note_status}}" class="status-pill" >{{$record?->check_out_note}}
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
{{--                                            <td class="px-4 py-4 text-sm text-gray-700">--}}
{{--                                                {{ round($record?->check_out_distance) }} m</td>--}}
{{--                                            <td class="px-4 py-4 text-sm text-gray-700">--}}
{{--                                                {{ round($record?->check_out_distance) }} m</td>--}}

                                            <td
                                                class="px-4 py-4 text-sm text-{{ $record?->check_in_distance > 100 ? 'red' : 'gray' }}-700">
                                                @php
                                                    if ($record?->check_in_latitude && $record->check_in_longitude){
                                                        $check_in_latitude = App\Http\Controllers\HomeController::latitudeInDMS($record->check_in_latitude);
                                                        $check_in_longitude = App\Http\Controllers\HomeController::longitudeInDMS($record->check_in_longitude);
                                                    }

                                                    if ($record?->check_out_latitude && $record->check_out_longitude){
                                                        $check_out_latitude = App\Http\Controllers\HomeController::latitudeInDMS($record->check_out_latitude);
                                                        $check_out_longitude = App\Http\Controllers\HomeController::longitudeInDMS($record->check_out_longitude);
                                                    }

                                                @endphp
                                                @if ($record?->check_in_latitude && $record->check_in_longitude)
                                                <a href="{{'https://www.google.com/maps/place/'.$check_in_latitude.'+'.$check_in_longitude.'/@'.$record->check_in_latitude.','.$record->check_out_longitude.',17z/data=!4m4!3m3!8m2!3d26.5004167!4d80.2878611?authuser=0&entry=ttu&g_ep=EgoyMDI0MTAyMC4xIKXMDSoASAFQAw%3D%3D'}}" target="_blank">
                                                {{ round($record?->check_in_distance) }} m
                                                </a>
                                                @else
                                                {{ round($record?->check_in_distance) }} m
                                                @endif
                                            </td>
                                            <td
                                                class="px-4 py-4 text-sm text-sm text-{{ $record?->check_out_distance > 100 ? 'red' : 'gray' }}">
                                                @if($record?->check_out_latitude && $record->check_out_longitude)
                                                <a href="{{'https://www.google.com/maps/place/'.$check_out_latitude.'+'.$check_out_longitude.'/@'.$record->check_out_latitude.','.$record->check_out_longitude.',17z/data=!4m4!3m3!8m2!3d26.5004167!4d80.2878611?authuser=0&entry=ttu&g_ep=EgoyMDI0MTAyMC4xIKXMDSoASAFQAw%3D%3D'}}" target="_blank">
                                                {{ round($record?->check_out_distance) }} m
                                                </a>
                                                @else

                                                    {{ round($record?->check_out_distance) }} m
                                                @endif
                                            </td>


                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $record?->checkInBy?->name }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $record?->checkOutBy?->name }}</td>
                                            @php
                                                $breaks = $record?->breaks ?? collect();
                                            @endphp
                                            <td>
                                                @forelse($breaks as $attendanceBreak)
                                                    @php
                                                        $startTime = $attendanceBreak->start_time
                                                            ? \Carbon\Carbon::parse($attendanceBreak->start_time)
                                                            : null;

                                                        $endTime = $attendanceBreak->end_time
                                                            ? \Carbon\Carbon::parse($attendanceBreak->end_time)
                                                            : null;
                                                    @endphp

                                                    <span class="status-pill mb-1">
                                                        <i class="fas fa-mug-hot text-amber-600"></i>
                                                        {{ $startTime && $endTime
                                                            ? $startTime->diffInMinutes($endTime) . ' min'
                                                            : 'Break running'
                                                        }}
                                                    </span>
                                                @empty
                                                    <span class="empty-value">—</span>
                                                @endforelse
                                            </td>

                                            <td>
                                                @if($record)
                                                <a href="{{ route('correctionNote.create', ['record' => $record->id]) }}"
                                                   class="inline-flex items-center gap-1 rounded-lg bg-violet-50 px-3 py-2 text-xs font-extrabold text-violet-700 hover:bg-violet-100">
                                                    <i class="fas fa-pen-to-square"></i>
                                                    Correction Note
                                                </a>
                                                @endif
                                            </td>

                                        </tr>
{{--                                    @endif--}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @php
                    $advancePayment = App\Models\AdvancePayment::whereMonth('date', \Carbon\Carbon::parse($month . '-01')->month)
                        ->whereYear('date', \Carbon\Carbon::parse($month . '-01')->year)
                        ->where('user_id', $currentUser->id)
                        ->sum('amount');
                        $condition = ($d < \Carbon\Carbon::today()) && auth()->user()->hasRole(['admin', 'super_admin']) && (($currentUser->salary) > 0)
                    @endphp
                    @if ($condition)
                        @php

                            // Retrieve existing salary record for the month
                            $userSalary = App\Models\Salary::where('user_id', $currentUser->id)
                                                        ->where('month', $d)
                                                        ->first();

                            if (!$userSalary) {
                                // Ensure salary and office_time are not null to avoid division errors
                                $dailySalary = $currentUser->salary ? $currentUser->salary / 30 : 0;
                                $hourlySalary = ($currentUser->office_time && $dailySalary > 0) ? $dailySalary / ($currentUser->office_time / 60) : 0;

                                // Calculate salaries
                                $salary = (($workingDays * $dailySalary) +
                                        ($sundayCount * $dailySalary) +
                                        ($offDays * $dailySalary) +
                                        (($halfDayCount * $dailySalary) / 2) +
                                        ($paidLeave * $dailySalary));

                                $durationSalary = (($workingDuration / 60) * $hourlySalary) +
                                                (($sundayCount + $offDays) * $dailySalary);

                                // Create the salary record
                                $userSalary = App\Models\Salary::create([
                                    'user_id' => $currentUser->id,
                                    'month' => $d,
                                    'day_wise_salary' => $salary,
                                    'hour_wise_salary' => $durationSalary,
                                    'status' => 'unpaid'
                                ]);
                            }
                        @endphp
                    @endif

            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" class="record-button button-pdf" onclick="printDivAsPDF()">
                    <i class="fas fa-file-pdf"></i>
                    Download Summary PDF
                </button>
            </div>

            <!-- Summary Information -->
            <div class="summary-panel mt-6 p-5 sm:p-6" id="printDiv">
                <div class="mb-5">
                    <h3 class="text-xl font-extrabold text-slate-900">Summary Information</h3>
                    <p class="mt-1 text-sm font-medium text-slate-600">
                        Monthly attendance and salary overview
                    </p>
                </div>

                <div class="summary-grid">
                    <div class="summary-item">
                        <span>Office Days</span>
                        <span>{{ $dates->count() - ($offDays + $sundayCount) }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Working Days</span>
                        <span>{{ $workingDays }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Half Days</span>
                        <span>{{ $halfDayCount }}</span>
                    </div>

                    <div class="summary-item">
                        <span>Leaves</span>
                        <span>{{ $leaveDays }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Salary</span>
                        <span>₹{{ number_format((float) $currentUser->salary, 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Generated Salary</span>
                        <span>
                            @if($condition)
                                {{ $userSalary ? '₹' . number_format((float) $userSalary->day_wise_salary, 2) : 'Not generated' }}
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="summary-item">
                        <span>Advance Payment</span>
                        <span>₹{{ number_format((float) $advancePayment, 2) }}</span>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
         function printDivAsPDF() {
             const {
                 jsPDF
             } = window.jspdf;

             // Get the div element
             var element = document.getElementById('printDiv');

             // Use html2canvas to capture the div as an image
             html2canvas(element).then(canvas => {
                 var imgData = canvas.toDataURL('image/png');

                 var pdf = new jsPDF('p', 'mm', 'a4');
                 var pageHeight = pdf.internal.pageSize.getHeight();
                 var imgWidth = 190;
                 var imgHeight = (canvas.height * imgWidth) / canvas.width;

                 var heightLeft = imgHeight;
                 var position = 10;

                 // Add the image to the PDF page by page
                 pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                 heightLeft -= pageHeight;

                 while (heightLeft > 0) {
                     position = heightLeft - imgHeight;
                     pdf.addPage();
                     pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                     heightLeft -= pageHeight;
                 }

                 // Save the PDF
                 pdf.save('{{ $month }}.pdf');
             });
         }
     </script>


@endsection