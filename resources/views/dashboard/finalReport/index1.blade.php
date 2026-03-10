@extends('dashboard.layout.root')

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <style>
        .break:hover .position-absolute {
            display: block !important;
        }

        #contentToPrint table {
            border-collapse: collapse !important;
        }

        #contentToPrint table th,
        #contentToPrint table td {
            padding: 4px 5px !important;
            font-size: 16px !important;
            line-height: 1.3 !important;
            vertical-align: middle !important;
            white-space: nowrap;
        }

        #contentToPrint table th {
            font-weight: 700;
            background: #e5e5e5;
        }

        #contentToPrint h5 {
            margin: 0 0 6px 0 !important;
            padding-bottom: 6px !important;
            font-size: 14px !important;
            font-weight: bold;
        }

        #contentToPrint h6 {
            margin: 0 0 4px 0 !important;
            font-size: 12px !important;
            font-weight: 600;
        }

        #contentToPrint .summary-table td,
        #contentToPrint .summary-table th {
            font-weight: 700 !important;
        }

        .month-section {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            background: #fff;
            margin-bottom: 24px;
        }

        .month-title {
            font-size: 18px;
            font-weight: 700;
            color: #0d6efd;
        }

        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 10;
            background: #fff;
        }

        .sticky-head {
            position: sticky;
            top: 0;
            z-index: 20;
        }


        .ts-wrapper {
            width: 100%;
        }

        .ts-wrapper .ts-control {
            background: #fff !important;
            border: 1px solid #ced4da !important;
            min-height: 38px;
            box-shadow: none !important;
        }

        .ts-wrapper.single .ts-control,
        .ts-wrapper.multi .ts-control {
            background: #fff !important;
        }

        .ts-control input {
            background: #fff !important;
        }

        .ts-dropdown {
            background: #fff !important;
            border: 1px solid #ced4da !important;
            z-index: 9999;
        }


        .report-filter-card {
            background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
            border: 1px solid #e9eef5;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .filter-group {
            min-width: 180px;
            flex: 0 0 180px;
        }

        .filter-group.filter-employee {
            min-width: 240px;
            flex: 0 0 240px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }

        .filter-actions {
            display: flex;
            align-items: end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .report-action-buttons {
            display: flex;
            justify-content: end;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .report-action-buttons .btn {
            min-width: 135px;
            border-radius: 10px;
            font-weight: 600;
        }

        .report-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }

        .report-filter-form .form-control,
        .report-filter-form .ts-wrapper .ts-control {
            min-height: 42px;
            border-radius: 10px !important;
            background: #fff !important;
            border: 1px solid #d7dee7 !important;
            box-shadow: none !important;
        }

        .report-filter-form .form-control:focus,
        .report-filter-form .ts-wrapper.focus .ts-control,
        .report-filter-form .ts-wrapper .ts-control:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.12) !important;
        }

        .ts-wrapper {
            width: 100%;
        }

        .ts-wrapper .ts-control {
            padding: 6px 10px !important;
        }

        .ts-dropdown {
            background: #fff !important;
            border: 1px solid #d7dee7 !important;
            border-radius: 10px !important;
            z-index: 9999;
        }

        @media (max-width: 991.98px) {
            .filter-group,
            .filter-group.filter-employee {
                min-width: 100%;
                flex: 0 0 100%;
            }

            .filter-actions,
            .report-action-buttons {
                width: 100%;
                justify-content: flex-start;
            }

            .report-action-buttons .btn {
                min-width: unset;
                width: 100%;
            }
        }
    </style>

    <div class="pb-20">
        <div class="content">
            <div class="container-fluid p-4">

                {{-- <div class="row align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                    <div class="col-12 col-md-6 mb-2">
                        <form action="{{ route('reports.index') }}" method="GET"
                            class="d-flex flex-column flex-md-row align-items-stretch flex-wrap gap-2">
                            @csrf

                            <div class="mr-md-2 mb-2">
                                <label class="mb-1 d-block">From Month</label>
                                <input type="month"
                                    name="from_month"
                                    value="{{ request('from_month', $fromMonth ?? now()->format('Y-m')) }}"
                                    class="form-control">
                            </div>

                            <div class="mr-md-2 mb-2">
                                <label class="mb-1 d-block">To Month</label>
                                <input type="month"
                                    name="to_month"
                                    value="{{ request('to_month', $toMonth ?? now()->format('Y-m')) }}"
                                    class="form-control">
                            </div>

                            <div class="mr-md-2 mb-2">
                                <label class="mb-1 d-block">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Status</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="mr-md-2 mb-2">
                                <label class="mb-1 d-block">Department</label>
                                <select name="department_id" class="form-control">
                                    <option value="">Choose Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mr-md-2 mb-2">
                                <label class="mb-1 d-block">Employee</label>
                                <select name="employee_id" id="employee_id" class="form-control">
                                    <option value="">Choose Employee</option>
                                    @foreach($allEmployees as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex align-items-end mb-2">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="{{ route('reports.index') }}" class="btn btn-info">Clear</a>
                            </div>
                        </form>
                    </div>

                    <div class="col-12 col-md-6 text-center text-md-right">
                        <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                           class="btn btn-primary text-white mb-2 mb-md-0 mr-md-2">Check In</a>
                        <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
                           class="btn btn-danger text-white mb-2 mb-md-0 mr-md-2">Check Out</a>
                        <button class="btn btn-warning text-white mb-2 mb-md-0 mr-md-2"
                                onclick="printDivAsPDF()">Download as PDF</button>
                        <button onclick="exportToExcel()" class="btn btn-success text-white mb-2 mb-md-0">
                            Export to Excel
                        </button>
                    </div>
                </div> --}}

                <div class="report-filter-card p-3 p-md-4 mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-xl-8">
                            <form action="{{ route('reports.index') }}" method="GET" class="report-filter-form">
                                @csrf

                                <div class="filter-group">
                                    <label class="filter-label">From Month</label>
                                    <input type="month"
                                        name="from_month"
                                        value="{{ request('from_month', $fromMonth ?? now()->format('Y-m')) }}"
                                        class="form-control">
                                </div>

                                <div class="filter-group">
                                    <label class="filter-label">To Month</label>
                                    <input type="month"
                                        name="to_month"
                                        value="{{ request('to_month', $toMonth ?? now()->format('Y-m')) }}"
                                        class="form-control">
                                </div>

                                <div class="filter-group">
                                    <label class="filter-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <label class="filter-label">Department</label>
                                    <select name="department_id" class="form-control">
                                        <option value="">Choose Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="filter-group filter-employee">
                                    <label class="filter-label">Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-control">
                                        <option value="">Choose Employee</option>
                                        @foreach($allEmployees as $emp)
                                            <option value="{{ $emp->id }}"
                                                {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary px-4">Filter</button>
                                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary px-4">Clear</a>
                                </div>
                            </form>
                        </div>

                        <div class="col-12 col-xl-4">
                            <div class="report-action-buttons">
                                <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                                    class="btn btn-primary text-white">
                                    Check In
                                </a>

                                <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
                                    class="btn btn-danger text-white">
                                    Check Out
                                </a>

                                <button class="btn btn-warning text-white" onclick="printDivAsPDF()">
                                    Download PDF
                                </button>

                                <button onclick="exportToExcel()" class="btn btn-success text-white">
                                    Export Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                        <h4 class="mb-2 mb-md-0 text-primary font-weight-bold">Records</h4>
                        <h4 class="mb-2 mb-md-0 text-secondary font-weight-bold">
                            @if($dates->first() && $dates->last())
                                @if($dates->first()->date->format('Y-m') == $dates->last()->date->format('Y-m'))
                                    {{ $dates->first()->date->format('M-Y') }}
                                @else
                                    {{ $dates->first()->date->format('M-Y') }} to {{ $dates->last()->date->format('M-Y') }}
                                @endif
                            @endif
                        </h4>
                    </div>

                    {{-- WEB VIEW --}}
                    <div id="webTableWrapper">
                        @foreach ($monthGroups as $monthGroup)
                            @php
                                $monthDates = $monthGroup->dates;
                                $officeDays = $monthGroup->officeDays;
                                $sundayCount = $monthGroup->sundayCount;
                            @endphp

                            <div class="month-section">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                                    <h5 class="month-title mb-2 mb-md-0">{{ $monthGroup->month_label }}</h5>
                                    <span class="badge bg-secondary">
                                        {{ $monthDates->first()->date->format('d M Y') }} -
                                        {{ $monthDates->last()->date->format('d M Y') }}
                                    </span>
                                </div>

                                <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                                    <table class="table table-bordered table-hover align-middle text-center month-table">
                                        <thead class="bg-primary text-white sticky-head">
                                            <tr>
                                                <th class="bg-primary text-white sticky-col" style="z-index: 30;">Employees</th>
                                                <th>Dates</th>
                                                @foreach ($monthDates as $dateObj)
                                                    @php $d = \Carbon\Carbon::parse($dateObj->date); @endphp
                                                    <th>{{ $d->format('d-[D]') }}</th>
                                                @endforeach
                                                <th>Office Days</th>
                                                <th>Working Days</th>
                                                <th>Half Days</th>
                                                <th>Leaves</th>
                                                <th>Late Count</th>
                                                <th>Late in time</th>
                                                <th>Gone Before Time</th>
                                                <th>Gone Before Time Count</th>
                                                @if (auth()->user()->hasRole('admin|super_admin'))
                                                    <th>Basic Salary</th>
                                                @endif
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($users as $user)
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
                                                @endphp

                                                <tr>
                                                    <td class="fw-bold sticky-col bg-light">
                                                        {{ $user?->name }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="badge bg-success">Check-in</span>
                                                            <hr class="my-1">
                                                            <span class="badge bg-danger">Check-out</span>
                                                        </div>
                                                    </td>

                                                    @foreach ($monthDates as $dateObj)
                                                        @php
                                                            $d = \Carbon\Carbon::parse($dateObj->date);
                                                            $dateKey = $d->format('Y-m-d');

                                                            $record = $attendanceMap->get($user->id . '_' . $dateKey);
                                                            $leave  = $leaveMap->get($user->id . '_' . $dateKey);
                                                            $off    = $offMap->get($user->office_id . '_' . $dateKey);

                                                            if ($record) {
                                                                $workingDays++;

                                                                if (!($record->check_in && $record->check_out)) {
                                                                    $halfDayCount++;
                                                                }

                                                                if ($record->late) {
                                                                    $lateCount++;
                                                                    $lateTime += $record->late;
                                                                }

                                                                if (
                                                                    $record->check_out &&
                                                                    \Carbon\Carbon::parse($record->check_out)->format('H:i') <
                                                                    \Carbon\Carbon::parse($user->check_out_time)->format('H:i')
                                                                ) {
                                                                    $goneBeforeTimeCount++;
                                                                    $checkOutTime = \Carbon\Carbon::parse($record->check_out)->format('H:i');
                                                                    $userCheckOutTime = \Carbon\Carbon::parse($user->check_out_time);
                                                                    $goneBeforeTime += \Carbon\Carbon::createFromFormat('H:i', $checkOutTime)
                                                                        ->diffInMinutes($userCheckOutTime);
                                                                }
                                                            }

                                                            if ($leave) {
                                                                if ($leave->approve_as == 'paid') {
                                                                    $paidLeave++;
                                                                } else {
                                                                    $unpaidLeave++;
                                                                }
                                                                $leaveDays++;
                                                            }

                                                            if ($off) {
                                                                $offDays++;
                                                            }
                                                        @endphp

                                                        @if ($off)
                                                            <td class="bg-info text-dark">{{ $off->title }}</td>
                                                        @else
                                                            <td class="break position-relative">
                                                                <div class="d-flex flex-column">
                                                                    <span title="{{ $record?->check_in_note }}"
                                                                        class="badge bg-light text-dark"
                                                                        style="color: {{ $record && $record->check_in
                                                                            ? (\Carbon\Carbon::parse($record->check_in)->format('H:i:s') < \Carbon\Carbon::parse($user->check_in_time)->format('H:i:s') ? 'green' : ($record->late ? 'red' : 'grey'))
                                                                            : 'grey' }} !important;">
                                                                        {{ $record?->check_in?->format('h:i:s A') ?? '-' }}
                                                                    </span>
                                                                    <hr class="my-1">
                                                                    <span title="{{ $record?->check_out_note }}"
                                                                        class="badge bg-light text-dark"
                                                                        style="color: {{ $record && $record->check_out
                                                                            ? (\Carbon\Carbon::parse($record->check_out)->format('H:i:s') > \Carbon\Carbon::parse($user->check_out_time)->format('H:i:s') ? 'green' : 'red')
                                                                            : 'grey' }} !important;">
                                                                        {{ $record?->check_out?->format('h:i:s A') ?? '-' }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        @endif
                                                    @endforeach

                                                    <td class="fw-bold">{{ $officeDays - $offDays }}</td>
                                                    <td>{{ $workingDays }}</td>
                                                    <td>{{ $halfDayCount }}</td>
                                                    <td>{{ $leaveDays }}</td>
                                                    <td>{{ $lateCount }}</td>
                                                    <td>{{ $lateTime ? App\Http\Controllers\HomeController::getTime($lateTime) : 'N/A' }}</td>
                                                    <td>{{ $goneBeforeTime ? App\Http\Controllers\HomeController::getTime($goneBeforeTime) : 'N/A' }}</td>
                                                    <td>{{ $goneBeforeTimeCount }}</td>

                                                    @if (\Carbon\Carbon::parse($monthDates->last()->date)->lt(\Carbon\Carbon::today()) &&
                                                        auth()->user()->hasRole('admin|super_admin'))
                                                        @php
                                                            $oneDaySalary = $user->salary / 30;
                                                            $salary =
                                                                $workingDays * $oneDaySalary +
                                                                $sundayCount * $oneDaySalary +
                                                                $paidLeave * $oneDaySalary +
                                                                $offDays * $oneDaySalary +
                                                                ($halfDayCount * $oneDaySalary) / 2;
                                                        @endphp
                                                        <td>{{ round($salary) }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- PRINT VIEW --}}
                    <div id="contentToPrint" class="d-none">
                        <h4 class="text-center mb-3">
                            Monthly Attendance –
                            @if($dates->first() && $dates->last())
                                @if($dates->first()->date->format('Y-m') == $dates->last()->date->format('Y-m'))
                                    {{ $dates->first()->date->format('M-Y') }}
                                @else
                                    {{ $dates->first()->date->format('M-Y') }} to {{ $dates->last()->date->format('M-Y') }}
                                @endif
                            @endif
                        </h4>

                        @foreach($monthGroups as $monthGroup)
                            @php
                                $monthDates = $monthGroup->dates;
                                $dateChunks = $monthDates->chunk(10);
                                $officeDays = $monthGroup->officeDays;
                                $sundayCount = $monthGroup->sundayCount;
                            @endphp

                            <div class="mb-4">
                                <h4 class="text-center mb-3">{{ $monthGroup->month_label }}</h4>

                                @foreach($users as $user)
                                    @php
                                        $p_workingDays = 0;
                                        $p_leaveDays = 0;
                                        $p_paidLeave = 0;
                                        $p_unpaidLeave = 0;
                                        $p_offDays = 0;
                                        $p_lateCount = 0;
                                        $p_lateTime = 0;
                                        $p_goneBeforeTime = 0;
                                        $p_goneBeforeTimeCount = 0;
                                        $p_halfDayCount = 0;
                                    @endphp

                                    <div class="employee-block mb-4">
                                        <div class="print-block">
                                            <h5 style="text-transform: uppercase; border-bottom: 1px solid #333;">
                                                {{ $user?->name }}
                                                <span style="font-weight: 600; font-size: 11px; margin-bottom: 4px; color:#555;">
                                                    ({{ $monthGroup->month_label }})
                                                </span>
                                            </h5>
                                        </div>

                                        @foreach($dateChunks as $chunkIndex => $dateChunk)
                                            <div class="print-block" style="border-bottom: 1px dashed #ccc;">
                                                <h6 class="mb-12">
                                                    Part {{ $chunkIndex + 1 }} :
                                                    {{ \Carbon\Carbon::parse($dateChunk->first()->date)->format('d M') }}
                                                    – {{ \Carbon\Carbon::parse($dateChunk->last()->date)->format('d M Y') }}
                                                </h6>

                                                <table class="table table-bordered table-hover align-middle text-center" style="margin-bottom: 0;">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th style="min-width: 70px;">Type</th>
                                                            @foreach($dateChunk as $dateObj)
                                                                @php $d = \Carbon\Carbon::parse($dateObj->date); @endphp
                                                                <th>{{ $d->format('d-[D]') }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- Check-in row --}}
                                                        <tr>
                                                            <td><strong>C-In</strong></td>
                                                            @foreach($dateChunk as $dateObj)
                                                                @php
                                                                    $d = \Carbon\Carbon::parse($dateObj->date);
                                                                    $dateKey = $d->format('Y-m-d');

                                                                    $record = $attendanceMap->get($user->id . '_' . $dateKey);
                                                                    $leave  = $leaveMap->get($user->id . '_' . $dateKey);
                                                                    $off    = $offMap->get($user->office_id . '_' . $dateKey);

                                                                    if ($record) {
                                                                        $p_workingDays++;

                                                                        if (!($record->check_in && $record->check_out)) {
                                                                            $p_halfDayCount++;
                                                                        }

                                                                        if ($record->late) {
                                                                            $p_lateCount++;
                                                                            $p_lateTime += $record->late;
                                                                        }

                                                                        if (
                                                                            $record->check_out &&
                                                                            \Carbon\Carbon::parse($record->check_out)->format('H:i') <
                                                                            \Carbon\Carbon::parse($user->check_out_time)->format('H:i')
                                                                        ) {
                                                                            $p_goneBeforeTimeCount++;
                                                                            $checkOutTime = \Carbon\Carbon::parse($record->check_out)->format('H:i');
                                                                            $userCheckOutTime = \Carbon\Carbon::parse($user->check_out_time);
                                                                            $p_goneBeforeTime += \Carbon\Carbon::createFromFormat('H:i', $checkOutTime)
                                                                                ->diffInMinutes($userCheckOutTime);
                                                                        }
                                                                    }

                                                                    if ($leave) {
                                                                        if ($leave->approve_as == 'paid') {
                                                                            $p_paidLeave++;
                                                                        } else {
                                                                            $p_unpaidLeave++;
                                                                        }
                                                                        $p_leaveDays++;
                                                                    }

                                                                    if ($off) {
                                                                        $p_offDays++;
                                                                    }

                                                                    $cellTextIn = '-';
                                                                    if ($off) {
                                                                        $cellTextIn = 'OFF';
                                                                    } elseif ($leave) {
                                                                        $cellTextIn = $leave->approve_as == 'paid' ? 'Paid Leave' : 'Leave';
                                                                    } elseif ($record && $record->check_in) {
                                                                        $cellTextIn = $record->check_in->format('h:i A');
                                                                    }
                                                                @endphp
                                                                <td>{{ $cellTextIn }}</td>
                                                            @endforeach
                                                        </tr>

                                                        {{-- Check-out row --}}
                                                        <tr>
                                                            <td><strong>C-Out</strong></td>
                                                            @foreach($dateChunk as $dateObj)
                                                                @php
                                                                    $d = \Carbon\Carbon::parse($dateObj->date);
                                                                    $dateKey = $d->format('Y-m-d');

                                                                    $record = $attendanceMap->get($user->id . '_' . $dateKey);
                                                                    $leave  = $leaveMap->get($user->id . '_' . $dateKey);
                                                                    $off    = $offMap->get($user->office_id . '_' . $dateKey);

                                                                    $cellTextOut = '-';
                                                                    if ($off) {
                                                                        $cellTextOut = 'OFF';
                                                                    } elseif ($leave) {
                                                                        $cellTextOut = $leave->approve_as == 'paid' ? 'Paid Leave' : 'Leave';
                                                                    } elseif ($record && $record->check_out) {
                                                                        $cellTextOut = $record->check_out->format('h:i A');
                                                                    }
                                                                @endphp
                                                                <td>{{ $cellTextOut }}</td>
                                                            @endforeach
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach

                                        <div class="print-block mb-4">
                                            <table class="table table-bordered table-sm text-center summary-table">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Office Days</th>
                                                        <th>Working Days</th>
                                                        <th>Half Days</th>
                                                        <th>Leaves</th>
                                                        <th>Paid Leaves</th>
                                                        <th>Late Count</th>
                                                        <th>Late Time</th>
                                                        <th>Gone Before Time</th>
                                                        <th>Gone Before Time Count</th>
                                                        @if (\Carbon\Carbon::parse($monthDates->last()->date)->lt(\Carbon\Carbon::today()) &&
                                                            auth()->user()->hasRole('admin|super_admin'))
                                                            <th>Basic Salary</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $officeDays - $p_offDays }}</td>
                                                        <td>{{ $p_workingDays }}</td>
                                                        <td>{{ $p_halfDayCount }}</td>
                                                        <td>{{ $p_leaveDays }}</td>
                                                        <td>{{ $p_paidLeave }}</td>
                                                        <td>{{ $p_lateCount }}</td>
                                                        <td>{{ $p_lateTime ? App\Http\Controllers\HomeController::getTime($p_lateTime) : 'N/A' }}</td>
                                                        <td>{{ $p_goneBeforeTime ? App\Http\Controllers\HomeController::getTime($p_goneBeforeTime) : 'N/A' }}</td>
                                                        <td>{{ $p_goneBeforeTimeCount }}</td>

                                                        @if (\Carbon\Carbon::parse($monthDates->last()->date)->lt(\Carbon\Carbon::today()) &&
                                                            auth()->user()->hasRole('admin|super_admin'))
                                                            @php
                                                                $p_oneDaySalary = $user->salary / 30;
                                                                $p_salary =
                                                                    $p_workingDays * $p_oneDaySalary +
                                                                    $sundayCount * $p_oneDaySalary +
                                                                    $p_paidLeave * $p_oneDaySalary +
                                                                    $p_offDays * $p_oneDaySalary +
                                                                    ($p_halfDayCount * $p_oneDaySalary) / 2;
                                                            @endphp
                                                            <td>{{ round($p_salary) }}</td>
                                                        @endif
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- EXCEL TABLE --}}
                    <table id="excelTable" class="d-none">
                        @foreach($monthGroups as $monthGroup)
                            @php
                                $monthDates = $monthGroup->dates;
                            @endphp

                            <thead>
                                <tr>
                                    <th colspan="{{ 2 + $monthDates->count() }}">
                                        {{ $monthGroup->month_label }}
                                    </th>
                                </tr>
                                <tr>
                                    <th>Employee</th>
                                    <th>Type</th>
                                    @foreach($monthDates as $dateObj)
                                        @php $d = \Carbon\Carbon::parse($dateObj->date); @endphp
                                        <th>{{ $d->format('d-[D]') }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($users as $user)
                                    @php
                                        $excelIn  = [];
                                        $excelOut = [];

                                        foreach ($monthDates as $dateObj) {
                                            $d = \Carbon\Carbon::parse($dateObj->date);
                                            $dateKey = $d->format('Y-m-d');

                                            $record = $attendanceMap->get($user->id . '_' . $dateKey);
                                            $leave  = $leaveMap->get($user->id . '_' . $dateKey);
                                            $off    = $offMap->get($user->office_id . '_' . $dateKey);

                                            $inText = '-';
                                            if ($off) {
                                                $inText = 'OFF';
                                            } elseif ($leave) {
                                                $inText = $leave->approve_as == 'paid' ? 'Paid Leave' : 'Leave';
                                            } elseif ($record && $record->check_in) {
                                                $inText = $record->check_in->format('h:i A');
                                            }

                                            $outText = '-';
                                            if ($off) {
                                                $outText = 'OFF';
                                            } elseif ($leave) {
                                                $outText = $leave->approve_as == 'paid' ? 'Paid Leave' : 'Leave';
                                            } elseif ($record && $record->check_out) {
                                                $outText = $record->check_out->format('h:i A');
                                            }

                                            $excelIn[]  = $inText;
                                            $excelOut[] = $outText;
                                        }
                                    @endphp

                                    <tr>
                                        <td rowspan="2">{{ $user->name }}</td>
                                        <td>C-In</td>
                                        @foreach($excelIn as $val)
                                            <td>{{ $val }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>C-Out</td>
                                        @foreach($excelOut as $val)
                                            <td>{{ $val }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF Progress Overlay --}}
    <div id="pdfProgressOverlay"
         class="position-fixed w-100 h-100 top-0 start-0 d-none align-items-center justify-content-center"
         style="background: rgba(0,0,0,0.35); z-index: 1050;">
        <div class="bg-white rounded shadow p-4" style="min-width: 260px; max-width: 420px;">
            <h6 class="mb-3">Generating PDF… Please wait</h6>

            <div class="progress" style="height: 8px;">
                <div id="pdfProgressBar"
                     class="progress-bar"
                     role="progressbar"
                     style="width: 0%;"
                     aria-valuenow="0"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>

            <div class="mt-2 text-muted small text-end">
                <span id="pdfProgressText">0%</span>
            </div>
        </div>
    </div>

    {{-- JS Libraries --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        function printDivAsPDF() {
            const { jsPDF } = window.jspdf;

            const webWrapper = document.getElementById('webTableWrapper');
            const printDiv   = document.getElementById('contentToPrint');

            if (!printDiv) {
                alert('Print container not found');
                return;
            }

            const employeeBlocks = printDiv.querySelectorAll('.employee-block');
            if (!employeeBlocks.length) {
                alert('No employee blocks to print');
                return;
            }

            const overlay = document.getElementById('pdfProgressOverlay');
            const bar     = document.getElementById('pdfProgressBar');
            const text    = document.getElementById('pdfProgressText');

            const oldTitle = document.title;
            document.title = "Generating PDF...";

            if (webWrapper) webWrapper.classList.add('d-none');
            printDiv.classList.remove('d-none');

            if (overlay) {
                overlay.classList.remove('d-none');
                overlay.classList.add('d-flex');
            }
            if (bar) {
                bar.style.width = '0%';
                bar.setAttribute('aria-valuenow', '0');
            }
            if (text) {
                text.textContent = '0%';
            }

            const pdf = new jsPDF("p", "mm", "a4");

            const pageWidth  = 210;
            const pageHeight = 297;

            const marginX = 10;
            const marginY = 10;

            const usableWidth  = pageWidth - marginX * 2;
            const usableHeight = pageHeight - marginY * 2;

            const slotsPerPage = 3;
            const slotHeight   = usableHeight / slotsPerPage;

            const CANVAS_OPTIONS = {
                scale: 1,
                useCORS: true,
                logging: false
            };

            const totalBlocks = employeeBlocks.length;

            function updateProgress(doneCount) {
                const percent = Math.min(100, Math.round((doneCount / totalBlocks) * 100));
                if (bar) {
                    bar.style.width = percent + '%';
                    bar.setAttribute('aria-valuenow', percent.toString());
                }
                if (text) {
                    text.textContent = percent + '%';
                }
            }

            const processEmployee = (index) => {
                if (index >= totalBlocks) {
                    pdf.save("attendance_{{ $dates->first()?->date?->format('M-Y') ?? 'report' }}_to_{{ $dates->last()?->date?->format('M-Y') ?? 'report' }}.pdf");

                    if (webWrapper) webWrapper.classList.remove('d-none');
                    printDiv.classList.add('d-none');
                    document.title = oldTitle;

                    if (overlay) {
                        overlay.classList.remove('d-flex');
                        overlay.classList.add('d-none');
                    }
                    return;
                }

                const block = employeeBlocks[index];
                updateProgress(index);

                html2canvas(block, CANVAS_OPTIONS).then(canvas => {
                    const imgData = canvas.toDataURL('image/jpeg', 0.9);

                    const scaleX = usableWidth / canvas.width;
                    const scaleY = slotHeight / canvas.height;
                    const scale  = Math.min(scaleX, scaleY, 1);

                    const imgWidth  = canvas.width * scale;
                    const imgHeight = canvas.height * scale;

                    const employeeIndexOnPage = index % slotsPerPage;

                    if (employeeIndexOnPage === 0 && index !== 0) {
                        pdf.addPage();
                    }

                    const posY = marginY + employeeIndexOnPage * slotHeight + (slotHeight - imgHeight) / 2;
                    const posX = marginX + (usableWidth - imgWidth) / 2;

                    pdf.addImage(imgData, "JPEG", posX, posY, imgWidth, imgHeight);

                    updateProgress(index + 1);
                    setTimeout(() => processEmployee(index + 1), 0);
                }).catch(() => {
                    updateProgress(index + 1);
                    setTimeout(() => processEmployee(index + 1), 0);
                });
            };

            processEmployee(0);
        }
    </script>

    <script>
        function exportToExcel() {
            let table = document.getElementById("excelTable");

            if (!table) {
                alert("Table not found for export.");
                return;
            }

            let workbook  = XLSX.utils.book_new();
            let worksheet = XLSX.utils.table_to_sheet(table);

            XLSX.utils.book_append_sheet(workbook, worksheet, "Attendance");
            XLSX.writeFile(workbook, "attendance_{{ $dates->first()?->date?->format('M-Y') ?? 'report' }}_to_{{ $dates->last()?->date?->format('M-Y') ?? 'report' }}.xlsx");
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new TomSelect('#employee_id', {
                create: false,
                allowEmptyOption: true,
                placeholder: 'Search employee...',
                maxOptions: 2000,
                plugins: ['clear_button'],
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });
    </script>
@endsection