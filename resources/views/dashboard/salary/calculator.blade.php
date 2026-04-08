@extends('dashboard.layout.root')

@section('content')
    <div class="pb-20">
        <div class="content">
            <div class="container-fluid p-4">

                {{-- Filter --}}
                <div class="row align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                    <div class="col-12 col-md-6 mb-2">
                        <form action="{{ route('salary.index') }}" method="GET"
                              class="d-flex flex-column flex-md-row align-items-stretch">
                            <input type="month"
                                   name="month"
                                   value="{{ $month }}"
                                   onchange="this.form.submit()"
                                   class="form-control mb-2 mb-md-0 mr-md-2">

                            <a href="{{ route('salary.index') }}" class="btn btn-info mb-2 ml-2">Clear</a>
                        </form>
                    </div>

                    <div class="col-12 col-md-6 text-center text-md-right">
                        <h5 class="mb-0 font-weight-bold text-primary">
                            Salary Calculator - {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                        </h5>
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

                <div class="table-responsive mt-3" style="max-height: 78vh; overflow-y: auto;">
                    <table id="contentToPrint" class="table table-bordered table-hover align-middle text-center">
                        <thead class="bg-primary text-white sticky-top">
                        <tr>
                            <th class="sticky left-0 bg-primary" style="z-index: 20; min-width: 180px;">Employee</th>
                            <th>Advance</th>
                            <th>Monthly Salary</th>
                            <th>Office Days</th>
                            <th>Present</th>
                            <th>Half Day</th>
                            <th>Paid Leave</th>
                            <th>Unpaid Leave</th>
                            <th>Off</th>
                            <th>Sunday</th>
                            <th>Absent</th>
                            <th>Payable Days</th>
                            <th>Per Day Salary</th>
                            <th>Gross Salary</th>
                            <th>Deduction</th>
                            <th>Net Salary</th>
                        </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $advancePayment = $advancePayments->where('user_id', $user->id)->sum('amount');

                                    $officeDays = 0;
                                    $presentDays = 0;
                                    $halfDays = 0;
                                    $paidLeave = 0;
                                    $unpaidLeave = 0;
                                    $offDays = 0;
                                    $sundayCount = 0;

                                    foreach ($dates as $dateObj) {
                                        $d = \Carbon\Carbon::parse($dateObj->date);

                                        $isSunday = $d->isSunday();
                                        if ($isSunday) {
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

                                    $absentDays = max(
                                        0,
                                        $officeDays - ($presentDays + $halfDays + $paidLeave + $unpaidLeave + $offDays)
                                    );

                                    $employeeSalary = (float) ($user->userSalary->total_salary ?? $user->salary ?? 0);

                                    $payableDays = $presentDays + ($halfDays * 0.5) + $paidLeave + $offDays + $sundayCount;
                                    $perDaySalary = $officeDays > 0 ? ($employeeSalary / $officeDays) : 0;
                                    $grossSalary = $perDaySalary * $payableDays;
                                    $deduction = (float) $advancePayment;
                                    $netSalary = $grossSalary - $deduction;
                                @endphp

                                <tr class="salary-row"
                                    data-office-days="{{ $officeDays }}"
                                    data-present-days="{{ $presentDays }}"
                                    data-half-days="{{ $halfDays }}"
                                    data-paid-leave="{{ $paidLeave }}"
                                    data-unpaid-leave="{{ $unpaidLeave }}"
                                    data-off-days="{{ $offDays }}"
                                    data-sunday-count="{{ $sundayCount }}"
                                    data-absent-days="{{ $absentDays }}"
                                    data-advance="{{ $advancePayment }}">

                                    <td class="fw-bold sticky left-0 bg-light text-left" style="z-index: 10; min-width:180px;">
                                        <div>{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email ?? ($user->phone ?? '') }}</small>
                                    </td>

                                    <td class="advance-cell">{{ number_format($advancePayment, 2, '.', '') }}</td>

                                    <td style="min-width: 160px;">
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
                                    <td class="absent-days">{{ $absentDays }}</td>
                                    <td class="payable-days">{{ number_format($payableDays, 2, '.', '') }}</td>
                                    <td class="per-day-salary">{{ number_format($perDaySalary, 2, '.', '') }}</td>
                                    <td class="gross-salary">{{ number_format($grossSalary, 2, '.', '') }}</td>
                                    <td class="deduction">{{ number_format($deduction, 2, '.', '') }}</td>
                                    <td class="net-salary font-weight-bold text-success">{{ number_format($netSalary, 2, '.', '') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-center py-4">No employees found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 p-3 bg-light rounded shadow-sm">
                    <strong>Formula:</strong><br>
                    Payable Days = Present + (Half Day × 0.5) + Paid Leave + Off + Sunday<br>
                    Gross Salary = Per Day Salary × Payable Days<br>
                    Net Salary = Gross Salary - Advance
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

        function recalculateRow(row) {
            const monthlySalaryInput = row.querySelector('.monthly-salary');

            const officeDays   = toNumber(row.dataset.officeDays);
            const presentDays  = toNumber(row.dataset.presentDays);
            const halfDays     = toNumber(row.dataset.halfDays);
            const paidLeave    = toNumber(row.dataset.paidLeave);
            const offDays      = toNumber(row.dataset.offDays);
            const sundayCount  = toNumber(row.dataset.sundayCount);
            const advance      = toNumber(row.dataset.advance);

            const monthlySalary = toNumber(monthlySalaryInput.value);

            const payableDays = presentDays + (halfDays * 0.5) + paidLeave + offDays + sundayCount;
            const perDaySalary = officeDays > 0 ? (monthlySalary / officeDays) : 0;
            const grossSalary = perDaySalary * payableDays;
            const deduction = advance;
            const netSalary = grossSalary - deduction;

            row.querySelector('.payable-days').textContent = formatNumber(payableDays);
            row.querySelector('.per-day-salary').textContent = formatNumber(perDaySalary);
            row.querySelector('.gross-salary').textContent = formatNumber(grossSalary);
            row.querySelector('.deduction').textContent = formatNumber(deduction);
            row.querySelector('.net-salary').textContent = formatNumber(netSalary);
        }

        const rows = document.querySelectorAll('.salary-row');

        rows.forEach(function (row) {
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