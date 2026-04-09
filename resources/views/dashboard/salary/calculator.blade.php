@extends('dashboard.layout.root')

@section('content')
    <style>
        .salary-table-wrapper {
            max-height: 78vh;
            overflow: auto;
            position: relative;
        }

        #contentToPrint {
            border-collapse: separate;
            border-spacing: 0;
            min-width: max-content;
        }

        #contentToPrint thead th {
            position: sticky;
            top: 0;
            z-index: 15;
            white-space: nowrap;
        }

        /* 1st sticky column: Employee */
        #contentToPrint .sticky-col-1 {
            position: sticky;
            left: 0;
            z-index: 20;
            min-width: 210px;
            white-space: nowrap;
        }

        /* 2nd sticky column: Monthly Salary */
        #contentToPrint .sticky-col-2 {
            position: sticky;
            left: 210px; /* Employee column width */
            z-index: 19;
            min-width: 160px;
            white-space: nowrap;
        }

        /* header colors keep proper */
        #contentToPrint thead .sticky-col-1,
        #contentToPrint thead .sticky-col-2 {
            background: #007bff !important;
            color: #fff !important;
        }

        /* body colors keep proper */
        #contentToPrint tbody .sticky-col-1 {
            background: #f8f9fa !important;
        }

        #contentToPrint tbody .sticky-col-2 {
            background: #ffffff !important;
        }

        /* prevent overlap visual issue */
        #contentToPrint th,
        #contentToPrint td {
            vertical-align: middle;
        }

        #contentToPrint tbody .sticky-col-1,
        #contentToPrint tbody .sticky-col-2 {
            box-shadow: 1px 0 0 #dee2e6;
        }
    </style>

    <div class="pb-20"
         data-apply-late-deduction="{{ $applyLateDeduction ? 1 : 0 }}"
         data-apply-early-exit-deduction="{{ $applyEarlyExitDeduction ? 1 : 0 }}"
         data-late-day-threshold="{{ $lateDayThreshold }}">
        <div class="content">
            <div class="container-fluid p-4">

                {{-- Filter --}}
                <div class="row align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                    <div class="col-12">
                        <form action="{{ route('salary.index') }}" method="GET"
                              class="d-flex flex-column flex-md-row align-items-stretch flex-wrap">

                            <input type="month"
                                   name="month"
                                   value="{{ $month }}"
                                   class="form-control mb-2 mb-md-0 mr-md-2"
                                   style="max-width: 220px;">

                            <input type="number"
                                   name="late_day_threshold"
                                   value="{{ $lateDayThreshold }}"
                                   min="1"
                                   class="form-control mb-2 mb-md-0 mr-md-2"
                                   style="max-width: 260px;"
                                   placeholder="Late days threshold">

                            <button type="submit" class="btn btn-primary mb-2 mr-2">
                                Filter
                            </button>

                            @if($applyLateDeduction)
                                <input type="hidden" name="apply_early_exit" value="{{ $applyEarlyExitDeduction ? 1 : 0 }}">
                                <button type="submit" name="apply_late" value="0" class="btn btn-warning mb-2 mr-2">
                                    Remove Late Deduction
                                </button>
                            @else
                                <input type="hidden" name="apply_early_exit" value="{{ $applyEarlyExitDeduction ? 1 : 0 }}">
                                <button type="submit" name="apply_late" value="1" class="btn btn-danger mb-2 mr-2">
                                    Apply Late Deduction
                                </button>
                            @endif

                            @if($applyEarlyExitDeduction)
                                <input type="hidden" name="apply_late" value="{{ $applyLateDeduction ? 1 : 0 }}">
                                <button type="submit" name="apply_early_exit" value="0" class="btn btn-warning mb-2 mr-2">
                                    Remove Early Exit Deduction
                                </button>
                            @else
                                <input type="hidden" name="apply_late" value="{{ $applyLateDeduction ? 1 : 0 }}">
                                <button type="submit" name="apply_early_exit" value="1" class="btn btn-dark mb-2 mr-2">
                                    Apply Early Exit Deduction
                                </button>
                            @endif

                            <a href="{{ route('salary.index') }}" class="btn btn-info mb-2">
                                Clear
                            </a>
                        </form>
                    </div>
                </div>

            </div>

            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                    <h4 class="mb-2 mb-md-0 text-primary font-weight-bold">Employee Salary Sheet</h4>
                    <h4 class="mb-2 mb-md-0 text-secondary font-weight-bold">
                        {{ \Carbon\Carbon::parse($month . '-01')->format('M - Y') }}
                    </h4>
                </div>

                <div class="mb-3">
                    <div class="alert alert-secondary mb-2">
                        <strong>Late Rule:</strong>
                        {{ $lateDayThreshold }} late days = 1 day salary deduction
                    </div>

                    @if($applyLateDeduction)
                        <div class="alert alert-danger mb-2">
                            <strong>Late deduction is applied.</strong>
                            {{ $lateDayThreshold }} late days par 1 day salary deduct ho rahi hai.
                        </div>
                    @else
                        <div class="alert alert-light border mb-2">
                            Late deduction abhi apply nahi hai.
                        </div>
                    @endif

                    @if($applyEarlyExitDeduction)
                        <div class="alert alert-warning mb-0">
                            <strong>Early exit deduction is applied.</strong>
                            Time se pahle jane ke minutes ke hisaab se deduction ho raha hai.
                        </div>
                    @else
                        <div class="alert alert-light border mb-0">
                            Early exit deduction abhi apply nahi hai.
                        </div>
                    @endif
                </div>

                <div class="salary-table-wrapper mt-3">
                    <table id="contentToPrint" class="table table-bordered table-hover align-middle text-center">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th class="sticky-col-1" style="z-index: 21;">Employee</th>
                            <th>Advance</th>
                            <th class="sticky-col-2" style="z-index: 20;">Monthly Salary</th>
                            <th>Office Days</th>
                            <th>Present</th>
                            <th>Half Day</th>
                            <th>Paid Leave</th>
                            <th>Unpaid Leave</th>
                            <th>Off</th>
                            <th>Total Sunday</th>
                            <th>Payable Sunday</th>
                            <th>Absent</th>
                            <th>Late Days</th>
                            <th>Late Time</th>
                            <th>Late Salary Days</th>
                            <th>Early Exit Days</th>
                            <th>Early Exit Time</th>
                            <th>Payable Days</th>
                            <th>Per Day Salary</th>
                            <th>Late Deduction</th>
                            <th>Early Exit Deduction</th>
                            <th>Gross Salary</th>
                            <th>Total Deduction</th>
                            <th>Net Salary</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse ($users as $user)
                            @php
                                $advancePayment = (float) $advancePayments->where('user_id', $user->id)->sum('amount');

                                $officeDays = 0;
                                $presentDays = 0;
                                $halfDays = 0;
                                $paidLeave = 0;
                                $unpaidLeave = 0;
                                $offDays = 0;
                                $sundayCount = 0;

                                $lateDays = 0;
                                $lateMinutes = 0;

                                $earlyExitDays = 0;
                                $earlyExitMinutes = 0;

                                $officeMinutesPerDay = (int) ($user->office_time ?? 0);

                                if ($officeMinutesPerDay <= 0 && !empty($user->check_in_time) && !empty($user->check_out_time)) {
                                    $officeMinutesPerDay = \Carbon\Carbon::parse($user->check_in_time)
                                        ->diffInMinutes(\Carbon\Carbon::parse($user->check_out_time));
                                }

                                if ($officeMinutesPerDay <= 0) {
                                    $officeMinutesPerDay = 540;
                                }

                                foreach ($dates as $dateObj) {
                                    $d = \Carbon\Carbon::parse($dateObj->date);

                                    if ($d->isSunday()) {
                                        $sundayCount++;
                                    } else {
                                        $officeDays++;
                                    }

                                    $record = $attendanceRecords
                                        ->where('user_id', $user->id)
                                        ->first(function ($record) use ($d) {
                                            if (!$record->check_in) {
                                                return false;
                                            }

                                            return \Carbon\Carbon::parse($record->check_in)->format('Y-m-d') === $d->format('Y-m-d');
                                        });

                                    $leave = \App\Models\Leave::whereDate('start_date', '<=', $d)
                                        ->whereDate('end_date', '>=', $d)
                                        ->where('user_id', $user->id)
                                        ->where('status', 'approved')
                                        ->first();

                                    $off = \App\Models\Off::whereDate('date', $d)
                                        ->where('office_id', $user->office_id)
                                        ->where('is_off', '1')
                                        ->first();

                                    if ($record) {
                                        if ($record->check_in && $record->check_out) {
                                            $presentDays++;
                                        } else {
                                            $halfDays++;
                                        }

                                        if ((int) ($record->late ?? 0) > 0) {
                                            $lateDays++;
                                            $lateMinutes += (int) $record->late;
                                        }

                                        if (!empty($record->check_out) && !empty($user->check_out_time)) {
                                            $actualCheckOut = \Carbon\Carbon::parse($record->check_out);

                                            $expectedCheckOut = \Carbon\Carbon::parse(
                                                $d->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($user->check_out_time)->format('H:i:s')
                                            );

                                            if ($actualCheckOut->lt($expectedCheckOut)) {
                                                $earlyExitDays++;
                                                $earlyExitMinutes += $actualCheckOut->diffInMinutes($expectedCheckOut);
                                            }
                                        }
                                    }

                                    if ($leave) {
                                        if ($leave->approve_as === 'paid') {
                                            $paidLeave++;
                                        } else {
                                            $unpaidLeave++;
                                        }
                                    }

                                    if ($off) {
                                        $offDays++;
                                    }
                                }

                                $payableSunday = min((int) floor($presentDays / 6), $sundayCount);

                                $absentDays = max(
                                    0,
                                    $officeDays - ($presentDays + $halfDays + $paidLeave + $unpaidLeave + $offDays)
                                );

                                $employeeSalary = (float) ($user->userSalary->total_salary ?? $user->salary ?? 0);

                                $payableDays = $presentDays + ($halfDays * 0.5) + $paidLeave + $offDays + $payableSunday;

                                $perDaySalary = $employeeSalary > 0 ? round($employeeSalary / 30, 2) : 0;
                                $grossSalary = round($perDaySalary * $payableDays, 2);

                                $perMinuteSalary = $officeMinutesPerDay > 0
                                    ? ($perDaySalary / $officeMinutesPerDay)
                                    : 0;

                                $lateSalaryDays = $lateDayThreshold > 0
                                    ? floor($lateDays / $lateDayThreshold)
                                    : 0;

                                $lateDeduction = $applyLateDeduction
                                    ? round($lateSalaryDays * $perDaySalary, 2)
                                    : 0;

                                $earlyExitDeduction = $applyEarlyExitDeduction
                                    ? round($perMinuteSalary * $earlyExitMinutes, 2)
                                    : 0;

                                $totalDeduction = round($advancePayment + $lateDeduction + $earlyExitDeduction, 2);
                                $netSalary = round($grossSalary - $totalDeduction, 2);

                                $lateHours = floor($lateMinutes / 60);
                                $lateRemainMinutes = $lateMinutes % 60;
                                $lateTimeFormatted = sprintf('%02d:%02d', $lateHours, $lateRemainMinutes);

                                $earlyExitHours = floor($earlyExitMinutes / 60);
                                $earlyExitRemainMinutes = $earlyExitMinutes % 60;
                                $earlyExitFormatted = sprintf('%02d:%02d', $earlyExitHours, $earlyExitRemainMinutes);
                            @endphp

                            <tr class="salary-row"
                                data-present-days="{{ $presentDays }}"
                                data-half-days="{{ $halfDays }}"
                                data-paid-leave="{{ $paidLeave }}"
                                data-off-days="{{ $offDays }}"
                                data-sunday-count="{{ $sundayCount }}"
                                data-advance="{{ $advancePayment }}"
                                data-late-days="{{ $lateDays }}"
                                data-late-minutes="{{ $lateMinutes }}"
                                data-early-exit-days="{{ $earlyExitDays }}"
                                data-early-exit-minutes="{{ $earlyExitMinutes }}"
                                data-office-minutes-per-day="{{ $officeMinutesPerDay }}">

                                <td class="fw-bold sticky-col-1 text-left" style="z-index: 11;">
                                    <div>{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email ?? ($user->phone ?? '') }}</small>
                                </td>

                                <td class="advance-cell">{{ number_format($advancePayment, 2, '.', '') }}</td>

                                <td class="sticky-col-2" style="z-index: 10;">
                                    <input type="number"
                                           class="form-control monthly-salary text-center"
                                           step="0.01"
                                           min="0"
                                           value="{{ number_format($employeeSalary, 2, '.', '') }}">
                                </td>

                                <td class="office-days">{{ $officeDays }}</td>
                                <td class="present-days">{{ $presentDays }}</td>
                                <td class="half-days">{{ $halfDays }}</td>
                                <td class="paid-leave">{{ $paidLeave }}</td>
                                <td class="unpaid-leave">{{ $unpaidLeave }}</td>
                                <td class="off-days">{{ $offDays }}</td>
                                <td class="sunday-count">{{ $sundayCount }}</td>
                                <td class="payable-sunday">{{ $payableSunday }}</td>
                                <td class="absent-days">{{ $absentDays }}</td>
                                <td class="late-days">{{ $lateDays }}</td>
                                <td class="late-time">{{ $lateTimeFormatted }}</td>
                                <td class="late-salary-days">{{ $lateSalaryDays }}</td>
                                <td class="early-exit-days">{{ $earlyExitDays }}</td>
                                <td class="early-exit-time">{{ $earlyExitFormatted }}</td>
                                <td class="payable-days">{{ number_format($payableDays, 2, '.', '') }}</td>
                                <td class="per-day-salary">{{ number_format($perDaySalary, 2, '.', '') }}</td>
                                <td class="late-deduction">{{ number_format($lateDeduction, 2, '.', '') }}</td>
                                <td class="early-exit-deduction">{{ number_format($earlyExitDeduction, 2, '.', '') }}</td>
                                <td class="gross-salary">{{ number_format($grossSalary, 2, '.', '') }}</td>
                                <td class="total-deduction">{{ number_format($totalDeduction, 2, '.', '') }}</td>
                                <td class="net-salary font-weight-bold text-success">{{ number_format($netSalary, 2, '.', '') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="24" class="text-center py-4">No employees found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 p-3 bg-light rounded shadow-sm">
                    <strong>Formula:</strong><br>
                    Per Day Salary = Monthly Salary / 30<br>
                    Payable Sunday = Present Days / 6 (maximum actual Sundays in month)<br>
                    Payable Days = Present + (Half Day × 0.5) + Paid Leave + Off + Payable Sunday<br><br>

                    <strong>Late Rule:</strong><br>
                    Entered Late Threshold = {{ $lateDayThreshold }}<br>
                    Late Salary Days = floor(Late Days / Late Threshold)<br>
                    Late Deduction = Late Salary Days × Per Day Salary<br><br>

                    <strong>Early Exit Rule:</strong><br>
                    Early Exit Time = users.check_out_time - attendance_records.check_out<br>
                    Early Exit Deduction = (Per Day Salary / Office Minutes Per Day) × Total Early Exit Minutes<br><br>

                    Total Deduction = Advance + Late Deduction + Early Exit Deduction<br>
                    Net Salary = Gross Salary - Total Deduction
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        function toNumber(value) {
            if (value === null || value === undefined || value === '') {
                return 0;
            }

            value = String(value).replace(/,/g, '').trim();
            const num = parseFloat(value);
            return isNaN(num) ? 0 : num;
        }

        function formatNumber(value) {
            return toNumber(value).toFixed(2);
        }

        function formatMinutesToHHMM(totalMinutes) {
            totalMinutes = Math.max(0, parseInt(totalMinutes || 0, 10));
            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;
            return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
        }

        function recalculateRow(row) {
            const monthlySalaryInput = row.querySelector('.monthly-salary');

            const wrapper = document.querySelector('[data-apply-late-deduction]');
            const applyLateDeduction = Number(wrapper.dataset.applyLateDeduction) === 1;
            const applyEarlyExitDeduction = Number(wrapper.dataset.applyEarlyExitDeduction) === 1;
            const lateDayThreshold = Math.max(1, parseInt(wrapper.dataset.lateDayThreshold || 1, 10));

            const presentDays = toNumber(row.dataset.presentDays);
            const halfDays = toNumber(row.dataset.halfDays);
            const paidLeave = toNumber(row.dataset.paidLeave);
            const offDays = toNumber(row.dataset.offDays);
            const sundayCount = toNumber(row.dataset.sundayCount);
            const advance = toNumber(row.dataset.advance);
            const lateDays = toNumber(row.dataset.lateDays);
            const lateMinutes = toNumber(row.dataset.lateMinutes);
            const earlyExitMinutes = toNumber(row.dataset.earlyExitMinutes);
            const officeMinutesPerDay = toNumber(row.dataset.officeMinutesPerDay);

            const monthlySalary = toNumber(monthlySalaryInput.value);

            const payableSunday = Math.min(Math.floor(presentDays / 6), sundayCount);
            const payableDays = presentDays + (halfDays * 0.5) + paidLeave + offDays + payableSunday;

            const perDaySalary = monthlySalary > 0 ? Number((monthlySalary / 30).toFixed(2)) : 0;
            const grossSalary = Number((perDaySalary * payableDays).toFixed(2));

            const perMinuteSalary = officeMinutesPerDay > 0
                ? (perDaySalary / officeMinutesPerDay)
                : 0;

            const lateSalaryDays = Math.floor(lateDays / lateDayThreshold);

            const lateDeduction = applyLateDeduction
                ? Number((lateSalaryDays * perDaySalary).toFixed(2))
                : 0;

            const earlyExitDeduction = applyEarlyExitDeduction
                ? Number((perMinuteSalary * earlyExitMinutes).toFixed(2))
                : 0;

            const totalDeduction = Number((advance + lateDeduction + earlyExitDeduction).toFixed(2));
            const netSalary = Number((grossSalary - totalDeduction).toFixed(2));

            row.querySelector('.payable-sunday').textContent = payableSunday;
            row.querySelector('.payable-days').textContent = formatNumber(payableDays);
            row.querySelector('.late-time').textContent = formatMinutesToHHMM(lateMinutes);
            row.querySelector('.late-salary-days').textContent = lateSalaryDays;
            row.querySelector('.early-exit-time').textContent = formatMinutesToHHMM(earlyExitMinutes);
            row.querySelector('.per-day-salary').textContent = formatNumber(perDaySalary);
            row.querySelector('.late-deduction').textContent = formatNumber(lateDeduction);
            row.querySelector('.early-exit-deduction').textContent = formatNumber(earlyExitDeduction);
            row.querySelector('.gross-salary').textContent = formatNumber(grossSalary);
            row.querySelector('.total-deduction').textContent = formatNumber(totalDeduction);
            row.querySelector('.net-salary').textContent = formatNumber(netSalary);
        }

        document.querySelectorAll('.salary-row').forEach(function (row) {
            const input = row.querySelector('.monthly-salary');

            recalculateRow(row);

            input.addEventListener('input', function () {
                recalculateRow(row);
            });

            input.addEventListener('change', function () {
                recalculateRow(row);
            });
        });
    });
</script>

@endsection