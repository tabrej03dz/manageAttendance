@extends('dashboard.layout.root')

@section('title', 'Day-wise Attendance')

@push('styles')
<style>
    .day-attendance-page {
        font-family: 'Inter', sans-serif;
        color: #0f172a;
    }

    .day-attendance-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #312e81;
        border-radius: 24px;
        background: linear-gradient(135deg, #0f172a 0%, #172554 52%, #312e81 100%);
        box-shadow: 0 20px 45px rgba(15, 23, 42, .24);
        isolation: isolate;
    }

    .day-attendance-hero::before {
        content: '';
        position: absolute;
        width: 290px;
        height: 290px;
        right: -90px;
        top: -125px;
        border-radius: 999px;
        background: rgba(99, 102, 241, .32);
        z-index: -1;
    }

    .day-attendance-hero::after {
        content: '';
        position: absolute;
        width: 230px;
        height: 230px;
        left: 38%;
        bottom: -155px;
        border-radius: 999px;
        background: rgba(6, 182, 212, .18);
        z-index: -1;
    }

    .day-panel,
    .day-filter-panel {
        border: 1px solid #dbe3ee;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .10);
    }

    .day-filter-panel {
        border-color: #cbd5e1;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
    }

    .day-filter-label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .day-filter-control {
        width: 100%;
        min-height: 46px;
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        background: #fff !important;
        color: #0f172a !important;
        padding: 10px 13px !important;
        font-size: 14px;
        font-weight: 700;
        outline: none;
        box-shadow: none !important;
    }

    .day-filter-control:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, .12) !important;
    }

    .day-button {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        border-radius: 12px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none !important;
        transition: .2s;
    }

    .day-button:hover {
        transform: translateY(-1px);
    }

    .day-button-primary {
        background: linear-gradient(135deg, #4338ca, #6366f1);
        color: #fff !important;
        box-shadow: 0 10px 20px rgba(79, 70, 229, .22);
    }

    .day-button-secondary {
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #334155 !important;
    }

    .day-table-wrap {
        max-height: 72vh;
        overflow: auto;
        border: 1px solid #dbe3ee;
        border-radius: 16px;
        background: #fff;
    }

    .day-table {
        width: 100%;
        min-width: 1650px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .day-table thead th {
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

    .day-table tbody td {
        border-bottom: 1px solid #e8edf3;
        background: #fff;
        color: #334155;
        padding: 13px 14px;
        font-size: 13px;
        font-weight: 600;
        vertical-align: middle;
    }

    .day-table tbody tr:nth-child(even) td {
        background: #f8fafc;
    }

    .day-table tbody tr:hover td {
        background: #eef2ff !important;
    }

    .employee-cell {
        min-width: 210px;
    }

    .employee-name {
        color: #0f172a;
        font-size: 14px;
        font-weight: 900;
    }

    .employee-status-note {
        display: block;
        margin-top: 5px;
        color: #be123c;
        font-size: 11px;
        font-weight: 800;
        white-space: normal;
    }

    .day-photo {
        width: 44px;
        height: 44px;
        border: 2px solid #fff;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .18);
    }

    .note-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        max-width: 230px;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #f8fafc;
        color: #334155;
        padding: 5px 9px;
        font-size: 11px;
        font-weight: 800;
        white-space: normal;
    }

    .mini-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 9px;
        color: #fff !important;
        text-decoration: none !important;
        box-shadow: 0 5px 12px rgba(15, 23, 42, .14);
    }

    .mini-action-approve {
        background: #059669;
    }

    .mini-action-reject {
        background: #e11d48;
    }

    .mini-action-note {
        background: #d97706;
    }

    .popup-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(2, 6, 23, .68);
        backdrop-filter: blur(4px);
    }

    .popup-overlay.is-open {
        display: flex;
    }

    .popup-card {
        width: 100%;
        max-width: 460px;
        border: 1px solid #dbe3ee;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 28px 70px rgba(2, 6, 23, .35);
    }

    .context-menu {
        position: absolute;
        z-index: 9998;
        display: none;
        min-width: 270px;
        border: 1px solid #dbe3ee;
        border-radius: 16px;
        background: #fff;
        padding: 14px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .22);
    }

    .context-menu.is-open {
        display: block;
    }

    .context-action {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
        border-radius: 11px;
        padding: 10px 12px;
        color: #fff !important;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none !important;
    }

    .empty-state {
        padding: 48px 20px !important;
        text-align: center;
        color: #64748b !important;
    }

    .day-table-wrap::-webkit-scrollbar {
        width: 9px;
        height: 9px;
    }

    .day-table-wrap::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: linear-gradient(90deg, #6366f1, #0891b2);
    }

    .day-table-wrap::-webkit-scrollbar-track {
        background: #e2e8f0;
    }

    @media (max-width: 640px) {
        .day-attendance-hero {
            border-radius: 18px;
        }

        .day-button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $selectedDate = $date ?? now()->toDateString();

    try {
        $formattedSelectedDate = \Carbon\Carbon::parse($selectedDate)->format('d F Y');
    } catch (\Throwable $exception) {
        $formattedSelectedDate = now()->format('d F Y');
    }
@endphp

<div class="day-attendance-page space-y-6 pb-10">

    {{-- Hero section --}}
    <section class="day-attendance-hero p-6 sm:p-8">
        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-bold text-white">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Live Day-wise Attendance
                </div>

                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    Day-wise Attendance
                </h1>

                <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">
                    Selected date पर सभी employees का check-in, check-out, notes, images और distance देखें।
                </p>
            </div>

            <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-4 text-white">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-200">
                    Selected Date
                </p>

                <p class="mt-1 text-xl font-extrabold">
                    {{ $formattedSelectedDate }}
                </p>
            </div>
        </div>
    </section>

    {{-- Filter section --}}
    <section class="day-filter-panel p-5 sm:p-6">
        <form action="{{ route('attendance.day-wise') }}" method="GET">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
                <div class="md:col-span-8">
                    <label for="attendance-date" class="day-filter-label">
                        Select Date
                    </label>

                    <input
                        type="date"
                        id="attendance-date"
                        name="date"
                        value="{{ request('date', $selectedDate) }}"
                        class="day-filter-control"
                    >
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="day-button day-button-primary w-full">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                </div>

                <div class="md:col-span-2">
                    <a
                        href="{{ route('attendance.day-wise') }}"
                        class="day-button day-button-secondary w-full"
                    >
                        <i class="fas fa-rotate-left"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </section>

    {{-- Attendance records --}}
    <section class="day-panel overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">
                    Employee Attendance Records
                </h2>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Right click on an employee row for leave and half-day actions.
                </p>
            </div>

            <span class="inline-flex w-max items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-extrabold text-indigo-700">
                <i class="fas fa-users"></i>
                {{ $employees->total() }} Employees
            </span>
        </div>

        <div class="p-4 sm:p-6">
            <div class="day-table-wrap">
                <table class="day-table">
                    <thead>
                        <tr>
                            @foreach ([
                                'Name',
                                'Office',
                                'Check-In',
                                'Check-in Image',
                                'Check-in Note',
                                'Check-out Time',
                                'Check-out Image',
                                'Check-out Note',
                                'Working Hours',
                                'Check-in Distance',
                                'Check-out Distance',
                                'Add Note'
                            ] as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($employees as $employee)
                            @php
                                $record = $employee->attendance_record ?? null;
                                $leave = $employee->leave_record ?? null;
                                $halfDayRecord = $employee->half_day_record ?? null;

                                /*
                                |--------------------------------------------------------------------------
                                | Check-in formatting
                                |--------------------------------------------------------------------------
                                */

                                $formattedCheckIn = 'N/A';
                                $checkInColorClass = 'text-gray-700';

                                if (!empty($record?->check_in)) {
                                    try {
                                        $actualCheckIn = $record->check_in instanceof \Carbon\CarbonInterface
                                            ? $record->check_in
                                            : \Carbon\Carbon::parse($record->check_in);

                                        $formattedCheckIn = $actualCheckIn->format('h:i:s A');

                                        if (!empty($employee->check_in_time)) {
                                            $expectedCheckIn = \Carbon\Carbon::parse($employee->check_in_time);

                                            if (
                                                $actualCheckIn->format('H:i:s')
                                                <= $expectedCheckIn->format('H:i:s')
                                            ) {
                                                $checkInColorClass = 'text-green-700';
                                            } elseif (!empty($record->late)) {
                                                $checkInColorClass = 'text-red-700';
                                            }
                                        } elseif (!empty($record->late)) {
                                            $checkInColorClass = 'text-red-700';
                                        }
                                    } catch (\Throwable $exception) {
                                        $formattedCheckIn = 'N/A';
                                    }
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | Check-out formatting
                                |--------------------------------------------------------------------------
                                */

                                $formattedCheckOut = 'N/A';
                                $checkOutColorClass = 'text-gray-700';

                                if (!empty($record?->check_out)) {
                                    try {
                                        $actualCheckOut = $record->check_out instanceof \Carbon\CarbonInterface
                                            ? $record->check_out
                                            : \Carbon\Carbon::parse($record->check_out);

                                        $formattedCheckOut = $actualCheckOut->format('h:i:s A');

                                        if (!empty($employee->check_out_time)) {
                                            $expectedCheckOut = \Carbon\Carbon::parse($employee->check_out_time);

                                            $checkOutColorClass =
                                                $actualCheckOut->format('H:i:s')
                                                >= $expectedCheckOut->format('H:i:s')
                                                    ? 'text-green-700'
                                                    : 'text-red-700';
                                        }
                                    } catch (\Throwable $exception) {
                                        $formattedCheckOut = 'N/A';
                                    }
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | Check-in image URL
                                |--------------------------------------------------------------------------
                                */

                                $checkInImageUrl = null;
                                $checkInImageFallback = null;

                                if (!empty($record?->check_in_image)) {
                                    $checkInImagePath = ltrim(
                                        (string) $record->check_in_image,
                                        '/'
                                    );

                                    if (filter_var($checkInImagePath, FILTER_VALIDATE_URL)) {
                                        $checkInImageUrl = $checkInImagePath;
                                    } else {
                                        $checkInCleanPath = ltrim(
                                            preg_replace(
                                                '#^storage/#',
                                                '',
                                                $checkInImagePath
                                            ),
                                            '/'
                                        );

                                        $checkInImageUrl = asset(
                                            'storage/' . $checkInCleanPath
                                        );

                                        $checkInImageFallback =
                                            'https://attendance.realvictorygroups.com/storage/'
                                            . $checkInCleanPath;
                                    }
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | Check-out image URL
                                |--------------------------------------------------------------------------
                                */

                                $checkOutImageUrl = null;
                                $checkOutImageFallback = null;

                                if (!empty($record?->check_out_image)) {
                                    $checkOutImagePath = ltrim(
                                        (string) $record->check_out_image,
                                        '/'
                                    );

                                    if (filter_var($checkOutImagePath, FILTER_VALIDATE_URL)) {
                                        $checkOutImageUrl = $checkOutImagePath;
                                    } else {
                                        $checkOutCleanPath = ltrim(
                                            preg_replace(
                                                '#^storage/#',
                                                '',
                                                $checkOutImagePath
                                            ),
                                            '/'
                                        );

                                        $checkOutImageUrl = asset(
                                            'storage/' . $checkOutCleanPath
                                        );

                                        $checkOutImageFallback =
                                            'https://attendance.realvictorygroups.com/storage/'
                                            . $checkOutCleanPath;
                                    }
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | Distance and Google Maps URLs
                                |--------------------------------------------------------------------------
                                */

                                $checkInDistance = is_numeric($record?->check_in_distance)
                                    ? round((float) $record->check_in_distance)
                                    : null;

                                $checkOutDistance = is_numeric($record?->check_out_distance)
                                    ? round((float) $record->check_out_distance)
                                    : null;

                                $checkInMapUrl = null;

                                if (
                                    !empty($record?->check_in_latitude)
                                    && !empty($record?->check_in_longitude)
                                ) {
                                    $checkInMapUrl = 'https://www.google.com/maps?q='
                                        . urlencode(
                                            $record->check_in_latitude
                                            . ','
                                            . $record->check_in_longitude
                                        );
                                }

                                $checkOutMapUrl = null;

                                if (
                                    !empty($record?->check_out_latitude)
                                    && !empty($record?->check_out_longitude)
                                ) {
                                    $checkOutMapUrl = 'https://www.google.com/maps?q='
                                        . urlencode(
                                            $record->check_out_latitude
                                            . ','
                                            . $record->check_out_longitude
                                        );
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | Working hours
                                |--------------------------------------------------------------------------
                                */

                                $workingHours = 'N/A';

                                if (!empty($record?->duration)) {
                                    try {
                                        $workingHours = \App\Http\Controllers\HomeController::getTime(
                                            $record->duration
                                        );
                                    } catch (\Throwable $exception) {
                                        $workingHours = 'N/A';
                                    }
                                }
                            @endphp

                            <tr oncontextmenu="showModal(event, {{ (int) $employee->id }})">
                                {{-- Employee --}}
                                <td class="employee-cell">
                                    <span class="employee-name">
                                        {{ $employee->name ?? 'N/A' }}
                                    </span>

                                    @if ($leave || $halfDayRecord)
                                        <span class="employee-status-note">
                                            @if ($leave)
                                                Leave: {{ $leave->status ?? 'N/A' }}
                                            @endif

                                            @if ($leave && $halfDayRecord)
                                                <br>
                                            @endif

                                            @if ($halfDayRecord)
                                                Half Day: {{ $halfDayRecord->status ?? 'N/A' }}
                                            @endif
                                        </span>
                                    @endif
                                </td>

                                {{-- Office --}}
                                <td>
                                    {{ data_get($employee, 'office.name', 'N/A') }}
                                </td>

                                {{-- Check-in --}}
                                <td class="{{ $checkInColorClass }}">
                                    {{ $formattedCheckIn }}
                                </td>

                                {{-- Check-in image --}}
                                <td>
                                    @if ($checkInImageUrl)
                                        <a
                                            href="{{ $checkInImageUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <img
                                                src="{{ $checkInImageUrl }}"
                                                alt="Check-in Image"
                                                class="day-photo"
                                                loading="lazy"
                                                @if ($checkInImageFallback)
                                                    onerror="this.onerror=null;this.src='{{ $checkInImageFallback }}';"
                                                @endif
                                            >
                                        </a>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>

                                {{-- Check-in note --}}
                                <td>
                                    @if (!empty($record?->check_in_note))
                                        <span
                                            title="{{ $record->check_in_note_status ?? '' }}"
                                            class="note-pill"
                                        >
                                            {{ $record->check_in_note }}

                                            @if ($record->check_in_note_status === 'rejected')
                                                <i class="fas fa-times text-red-600"></i>
                                            @elseif ($record->check_in_note_status === 'approved')
                                                <i class="fas fa-check text-green-600"></i>
                                            @elseif ($record->check_in_note_status === 'pending')
                                                <span class="font-black text-amber-600">P</span>
                                            @endif
                                        </span>

                                        <div class="mt-2 flex space-x-2">
                                            @can('approve late message')
                                                @if ($record->check_in_note_status !== 'approved')
                                                    <a
                                                        title="Approve"
                                                        href="{{ route('attendance.user.note.response', [
                                                            'record' => $record->id,
                                                            'type' => 'check_in_note',
                                                            'status' => 'approved'
                                                        ]) }}"
                                                        class="mini-action mini-action-approve"
                                                    >
                                                        <i class="fas fa-check-circle"></i>
                                                    </a>
                                                @endif
                                            @endcan

                                            @can('reject late message')
                                                @if ($record->check_in_note_status !== 'rejected')
                                                    <a
                                                        title="Reject"
                                                        href="{{ route('attendance.user.note.response', [
                                                            'record' => $record->id,
                                                            'type' => 'check_in_note',
                                                            'status' => 'rejected'
                                                        ]) }}"
                                                        class="mini-action mini-action-reject"
                                                    >
                                                        <i class="fas fa-times-circle"></i>
                                                    </a>
                                                @endif
                                            @endcan
                                        </div>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>

                                {{-- Check-out --}}
                                <td class="{{ $checkOutColorClass }}">
                                    {{ $formattedCheckOut }}
                                </td>

                                {{-- Check-out image --}}
                                <td>
                                    @if ($checkOutImageUrl)
                                        <a
                                            href="{{ $checkOutImageUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <img
                                                src="{{ $checkOutImageUrl }}"
                                                alt="Check-out Image"
                                                class="day-photo"
                                                loading="lazy"
                                                @if ($checkOutImageFallback)
                                                    onerror="this.onerror=null;this.src='{{ $checkOutImageFallback }}';"
                                                @endif
                                            >
                                        </a>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>

                                {{-- Check-out note --}}
                                <td>
                                    @if (!empty($record?->check_out_note))
                                        <span
                                            title="{{ $record->check_out_note_status ?? '' }}"
                                            class="note-pill"
                                        >
                                            {{ $record->check_out_note }}

                                            @if ($record->check_out_note_status === 'rejected')
                                                <i class="fas fa-times text-red-600"></i>
                                            @elseif ($record->check_out_note_status === 'approved')
                                                <i class="fas fa-check text-green-600"></i>
                                            @elseif ($record->check_out_note_status === 'pending')
                                                <span class="font-black text-amber-600">P</span>
                                            @endif
                                        </span>

                                        @can('approve before going message|reject before going message')
                                            <div class="mt-2 flex space-x-2">
                                                @can('approve before going message')
                                                    @if ($record->check_out_note_status !== 'approved')
                                                        <a
                                                            title="Approve"
                                                            href="{{ route('attendance.user.note.response', [
                                                                'record' => $record->id,
                                                                'type' => 'check_out_note',
                                                                'status' => 'approved'
                                                            ]) }}"
                                                            class="mini-action mini-action-approve"
                                                        >
                                                            <i class="fas fa-check-circle"></i>
                                                        </a>
                                                    @endif
                                                @endcan

                                                @can('reject before going message')
                                                    @if ($record->check_out_note_status !== 'rejected')
                                                        <a
                                                            title="Reject"
                                                            href="{{ route('attendance.user.note.response', [
                                                                'record' => $record->id,
                                                                'type' => 'check_out_note',
                                                                'status' => 'rejected'
                                                            ]) }}"
                                                            class="mini-action mini-action-reject"
                                                        >
                                                            <i class="fas fa-times-circle"></i>
                                                        </a>
                                                    @endif
                                                @endcan
                                            </div>
                                        @endcan
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>

                                {{-- Working hours --}}
                                <td>
                                    {{ $workingHours }}
                                </td>

                                {{-- Check-in distance --}}
                                <td class="{{ $checkInDistance !== null && $checkInDistance > 100 ? 'text-red-700' : 'text-gray-700' }}">
                                    @if ($checkInDistance !== null)
                                        @if ($checkInMapUrl)
                                            <a
                                                href="{{ $checkInMapUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="font-bold underline"
                                            >
                                                {{ $checkInDistance }} m
                                            </a>
                                        @else
                                            {{ $checkInDistance }} m
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>

                                {{-- Check-out distance --}}
                                <td class="{{ $checkOutDistance !== null && $checkOutDistance > 100 ? 'text-red-700' : 'text-gray-700' }}">
                                    @if ($checkOutDistance !== null)
                                        @if ($checkOutMapUrl)
                                            <a
                                                href="{{ $checkOutMapUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="font-bold underline"
                                            >
                                                {{ $checkOutDistance }} m
                                            </a>
                                        @else
                                            {{ $checkOutDistance }} m
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>

                                {{-- Add note --}}
                                <td>
                                    @if ($record)
                                        @can('add note')
                                            <button
                                                type="button"
                                                title="Add Note"
                                                class="mini-action mini-action-note"
                                                data-note-url="{{ route('attendance.note', [
                                                    'record' => $record->id
                                                ]) }}"
                                                data-note-text="{{ e($record->note ?? '') }}"
                                                onclick="openNotePopup(this)"
                                            >
                                                <i class="fas fa-note-sticky"></i>
                                            </button>
                                        @endcan
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="empty-state">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">
                                            <i class="fas fa-calendar-xmark"></i>
                                        </div>

                                        <div>
                                            <p class="font-extrabold text-slate-800">
                                                No employees found
                                            </p>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Selected date या current office के लिए कोई active employee उपलब्ध नहीं है।
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($employees->hasPages())
                <div class="mt-5">
                    {{ $employees->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </section>
</div>

{{-- Context menu --}}
<div id="customModal" class="context-menu">
    <button
        type="button"
        onclick="closeModal()"
        class="absolute right-2 top-1 text-xl font-bold text-slate-400 hover:text-rose-600"
    >
        &times;
    </button>

    <h2 class="mb-3 pr-6 text-sm font-extrabold text-slate-900">
        Quick Actions
    </h2>

    <div class="space-y-2">
        <a
            id="leaveLink"
            href="#"
            class="context-action bg-gradient-to-r from-rose-500 to-rose-600"
        >
            <i class="fas fa-calendar-minus"></i>
            Request For Leave
        </a>

        <a
            id="halfDayLink"
            href="#"
            class="context-action bg-gradient-to-r from-amber-500 to-orange-500"
        >
            <i class="fas fa-clock"></i>
            Request For Half Day
        </a>
    </div>
</div>

{{-- Note popup --}}
<div id="notePopup" class="popup-overlay" aria-hidden="true">
    <div class="popup-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">
                    Add Attendance Note
                </h3>

                <p class="mt-1 text-xs font-medium text-slate-500">
                    Write or update the note for this attendance record.
                </p>
            </div>

            <button
                type="button"
                onclick="closeNotePopup()"
                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-rose-50 hover:text-rose-600"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="notePopupForm" method="POST" action="">
            @csrf

            <div class="p-5">
                <label for="attendance-note-text" class="day-filter-label">
                    Note
                </label>

                <textarea
                    id="attendance-note-text"
                    rows="5"
                    name="note"
                    class="day-filter-control min-h-[130px]"
                    placeholder="Write your note here..."
                ></textarea>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="closeNotePopup()"
                    class="day-button day-button-secondary"
                >
                    Close
                </button>

                @role('super_admin|admin')
                    <button
                        type="submit"
                        class="day-button bg-amber-500 text-white hover:bg-amber-600"
                    >
                        <i class="fas fa-save"></i>
                        Save Note
                    </button>
                @endrole
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openNotePopup(button) {
        const popup = document.getElementById('notePopup');
        const form = document.getElementById('notePopupForm');
        const textarea = document.getElementById('attendance-note-text');

        if (!popup || !form || !textarea) {
            return;
        }

        form.action = button.dataset.noteUrl || '';
        textarea.value = button.dataset.noteText || '';

        popup.classList.add('is-open');
        popup.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(function () {
            textarea.focus();
        }, 100);
    }

    function closeNotePopup() {
        const popup = document.getElementById('notePopup');

        if (!popup) {
            return;
        }

        popup.classList.remove('is-open');
        popup.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function showModal(event, employeeId) {
        event.preventDefault();

        const modal = document.getElementById('customModal');
        const leaveLink = document.getElementById('leaveLink');
        const halfDayLink = document.getElementById('halfDayLink');

        if (!modal || !leaveLink || !halfDayLink) {
            return;
        }

        leaveLink.href = `{{ url('/leave/create') }}/${employeeId}`;
        halfDayLink.href = `{{ url('/half-day/create') }}/${employeeId}`;

        const menuWidth = 280;
        const viewportRight = window.scrollX + window.innerWidth;
        const left = Math.min(
            event.pageX,
            viewportRight - menuWidth - 16
        );

        modal.style.left = `${Math.max(12, left)}px`;
        modal.style.top = `${event.pageY}px`;
        modal.classList.add('is-open');
    }

    function closeModal() {
        const modal = document.getElementById('customModal');

        if (modal) {
            modal.classList.remove('is-open');
        }
    }

    document.addEventListener('click', function (event) {
        const contextMenu = document.getElementById('customModal');
        const notePopup = document.getElementById('notePopup');

        if (
            contextMenu
            && contextMenu.classList.contains('is-open')
            && !contextMenu.contains(event.target)
        ) {
            closeModal();
        }

        if (notePopup && event.target === notePopup) {
            closeNotePopup();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
            closeNotePopup();
        }
    });
</script>
@endpush