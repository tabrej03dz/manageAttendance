@extends('dashboard.layout.root')

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        .break:hover .position-absolute {
            display: block !important;
        }

        /* ========== PDF VIEW – Balanced spacing & clear text ========== */
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
    </style>

    <div class="pb-20">
        <div class="content">
            <div class="container-fluid p-4">
                <!-- Filter and Action Buttons -->
                <div class="row align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                    <div class="col-12 col-md-6 mb-2">
                        <form action="{{ route('reports.index') }}" method="GET"
                              class="d-flex flex-column flex-md-row align-items-stretch">
                            @csrf
                            <input type="month"
                                   name="month"
                                   onchange="this.form.submit()"
                                   value="{{ $month }}"
                                   placeholder="Start Date"
                                   class="form-control mb-2 mb-md-0 mr-md-2">

                            <select name="status"
                                    onchange="this.form.submit()"
                                    class="form-control mb-2 mb-md-0 mr-md-2">
                                <option value="">Status</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>

                            <a href="{{ route('reports.index') }}" class="btn btn-info mb-2 ml-2">Clear</a>
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
                </div>
            </div>

            <div>
                <div class="container">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="mb-2 mb-md-0 text-primary font-weight-bold">Records</h4>
                        <h4 class="mb-2 mb-md-0 text-secondary font-weight-bold">
                            {{ $dates->first()->date->format('M-Y') }}
                        </h4>
                    </div>

                    @php
                        $officeDays = 0;
                        $sundayCount = 0;
                        foreach ($dates as $dateObj) {
                            $d = \Carbon\Carbon::parse($dateObj->date);
                            $isSunday = $d->format('D') === 'Sun';
                            if ($isSunday) {
                                $sundayCount++;
                            } else {
                                $officeDays++;
                            }
                        }

                        // PDF ke liye 10-10 dates ka chunk
                        $dateChunks = $dates->chunk(10);
                    @endphp

                        <!-- ========== 1) ORIGINAL FULL TABLE (WEB + EXCEL VIEW) ========== -->
                    <div class="table-responsive mt-3" style="max-height: 75vh; overflow-y: auto;" id="webTableWrapper">
                        <div class="table-responsive text-xs">
                            <div class="table-responsive mt-3 d-md-block">
                                <div class="table-responsive mt-3" style="max-height: 100vh; overflow-y: auto;">

                                    <table id="mainTable"
                                           class="table table-bordered table-hover align-middle text-center">
                                        <thead class="bg-primary text-white sticky-top">
                                        <tr>
                                            <th class="sticky left-0 bg-primary" style="z-index: 20;">Employees</th>
                                            <th>Dates</th>
                                            @foreach ($dates as $dateObj)
                                                @php
                                                    $d = \Carbon\Carbon::parse($dateObj->date);
                                                @endphp
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
                                                <td class="fw-bold sticky left-0 bg-light" style="z-index: 10;">
                                                    {{ $user?->name }}
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="badge bg-success">Check-in</span>
                                                        <hr class="my-1">
                                                        <span class="badge bg-danger">Check-out</span>
                                                    </div>
                                                </td>

                                                @foreach ($dates as $dateObj)
                                                    @php
                                                        $d = \Carbon\Carbon::parse($dateObj->date);

                                                        $record = $attendanceRecords
                                                            ->where('user_id', $user->id)
                                                            ->first(function ($record) use ($d) {
                                                                return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                                                            });

                                                        if ($record) {
                                                            if ($record->check_in && $record->check_out) {
                                                                $workingDays++;
                                                            } else {
                                                                $halfDayCount++;
                                                            }
                                                            if ($record->late) {
                                                                $lateCount++;
                                                                $lateTime += $record->late;
                                                            }
                                                            if (
                                                                $record->check_out &&
                                                                \Carbon\Carbon::parse($record?->check_out)->format('H:i') <
                                                                    \Carbon\Carbon::parse($user->check_out_time)->format('H:i')
                                                            ) {
                                                                $goneBeforeTimeCount++;
                                                                $checkOutTime = \Carbon\Carbon::parse($record?->check_out)->format('H:i');
                                                                $userCheckOutTime = \Carbon\Carbon::parse($user->check_out_time);
                                                                $goneBeforeTime += \Carbon\Carbon::createFromFormat('H:i', $checkOutTime)
                                                                    ->diffInMinutes($userCheckOutTime);
                                                            }
                                                        }

                                                        $leave = \App\Models\Leave::whereDate('start_date', '<=', $d)
                                                            ->whereDate('end_date', '>=', $d)
                                                            ->where('user_id', $user->id)
                                                            ->first();
                                                        if ($leave) {
                                                            if ($leave->approve_as == 'paid') {
                                                                $paidLeave++;
                                                            } else {
                                                                $unpaidLeave++;
                                                            }
                                                            $leaveDays++;
                                                        }

                                                        $off = \App\Models\Off::whereDate('date', $d)
                                                            ->where('office_id', $user->office_id)
                                                            ->where('is_off', '1')
                                                            ->first();
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
                                                                      style="color: {{ \Carbon\Carbon::parse($record?->check_in)->format('H:i:s') < \Carbon\Carbon::parse($user->check_in_time)->format('H:i:s') ? 'green' : ($record?->late ? 'red' : 'grey') }}!important;">
                                                                    {{ $record?->check_in?->format('h:i:s A') ?? '-' }}
                                                                </span>
                                                                <hr class="my-1">
                                                                <span title="{{ $record?->check_out_note }}"
                                                                      class="badge bg-light text-dark"
                                                                      style="color: {{ \Carbon\Carbon::parse($record?->check_out)->format('H:i:s') > \Carbon\Carbon::parse($user->check_out_time)->format('H:i:s') ? 'green' : 'red' }}!important;">
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

                                                @if (\Carbon\Carbon::parse($dates->last()->date)->lt(\Carbon\Carbon::today()) &&
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
                        </div>
                    </div>

                    <!-- ========== 2) PRINT VIEW – PER USER, 10-10 DATES + SUMMARY ========== -->
                    <div id="contentToPrint" class="mt-4 d-none">
                        <h4 class="text-center mb-3">
                            Monthly Attendance – {{ $dates->first()->date->format('M-Y') }}
                        </h4>

                        @foreach($users as $user)
                            @php
                                // Summary counters for PRINT
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

                            <div class="print-block">
                                <h5 class="pb-2"
                                    style="text-transform: uppercase; border-bottom: 1px solid #333;">
                                    {{ $user?->name }}
                                    <span style="font-weight: 600; font-size: 11px; margin-bottom: 4px; color:#555;">
                                        ({{ $dates->first()->date->format('M-Y') }})
                                    </span>
                                </h5>
                            </div>

                            {{-- per user parts (10-10 dates) --}}
                            @foreach($dateChunks as $chunkIndex => $dateChunk)
                                <div class="print-block" style="border-bottom: 1px dashed #ccc;">
                                    <h6 class="mb-1">
                                        Part {{ $chunkIndex + 1 }} :
                                        {{ \Carbon\Carbon::parse($dateChunk->first()->date)->format('d M') }}
                                        – {{ \Carbon\Carbon::parse($dateChunk->last()->date)->format('d M Y') }}
                                    </h6>

                                    <table class="table table-bordered table-hover align-middle text-center"
                                           style="margin-bottom: 0;">
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
                                        {{-- Check-in row (employee name rowspan="2") --}}
                                        <tr>

                                            <td><strong>C-In</strong></td>
                                            @foreach($dateChunk as $dateObj)
                                                @php
                                                    $d = \Carbon\Carbon::parse($dateObj->date);

                                                    $record = $attendanceRecords
                                                        ->where('user_id', $user->id)
                                                        ->first(function ($record) use ($d) {
                                                            return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                                                        });

                                                    $off = \App\Models\Off::whereDate('date', $d)
                                                        ->where('office_id', $user->office_id)
                                                        ->where('is_off', '1')
                                                        ->first();

                                                    $leave = \App\Models\Leave::whereDate('start_date', '<=', $d)
                                                        ->whereDate('end_date', '>=', $d)
                                                        ->where('user_id', $user->id)
                                                        ->first();

                                                    // Summary counters
                                                    if ($record) {
                                                        if ($record->check_in && $record->check_out) {
                                                            $p_workingDays++;
                                                        } else {
                                                            $p_halfDayCount++;
                                                        }
                                                        if ($record->late) {
                                                            $p_lateCount++;
                                                            $p_lateTime += $record->late;
                                                        }
                                                        if (
                                                            $record->check_out &&
                                                            \Carbon\Carbon::parse($record?->check_out)->format('H:i') <
                                                                \Carbon\Carbon::parse($user->check_out_time)->format('H:i')
                                                        ) {
                                                            $p_goneBeforeTimeCount++;
                                                            $checkOutTime = \Carbon\Carbon::parse($record?->check_out)->format('H:i');
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

                                                    $record = $attendanceRecords
                                                        ->where('user_id', $user->id)
                                                        ->first(function ($record) use ($d) {
                                                            return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                                                        });

                                                    $off = \App\Models\Off::whereDate('date', $d)
                                                        ->where('office_id', $user->office_id)
                                                        ->where('is_off', '1')
                                                        ->first();

                                                    $leave = \App\Models\Leave::whereDate('start_date', '<=', $d)
                                                        ->whereDate('end_date', '>=', $d)
                                                        ->where('user_id', $user->id)
                                                        ->first();

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

                            {{-- SUMMARY BLOCK for this user --}}
                            <div class="print-block mb-4">
                                <table class="table table-bordered table-sm text-center">
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
                                        @if (\Carbon\Carbon::parse($dates->last()->date)->lt(\Carbon\Carbon::today()) &&
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
                                        @if (\Carbon\Carbon::parse($dates->last()->date)->lt(\Carbon\Carbon::today()) &&
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
                        @endforeach
                    </div> {{-- /#contentToPrint --}}

                    {{-- ========== 3) HIDDEN TABLE FOR EXCEL EXPORT (WITH ROWSPAN NAME) ========== --}}
                    <table id="excelTable" class="d-none">
                        <thead>
                        <tr>
                            <th>Type</th>
                            @foreach($dates as $dateObj)
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
                                foreach ($dates as $dateObj) {
                                    $d = \Carbon\Carbon::parse($dateObj->date);

                                    $record = $attendanceRecords
                                        ->where('user_id', $user->id)
                                        ->first(function ($record) use ($d) {
                                            return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                                        });

                                    $off = \App\Models\Off::whereDate('date', $d)
                                        ->where('office_id', $user->office_id)
                                        ->where('is_off', '1')
                                        ->first();

                                    $leave = \App\Models\Leave::whereDate('start_date', '<=', $d)
                                        ->whereDate('end_date', '>=', $d)
                                        ->where('user_id', $user->id)
                                        ->first();

                                    // IN text
                                    $inText = '-';
                                    if ($off) {
                                        $inText = 'OFF';
                                    } elseif ($leave) {
                                        $inText = $leave->approve_as == 'paid' ? 'Paid Leave' : 'Leave';
                                    } elseif ($record && $record->check_in) {
                                        $inText = $record->check_in->format('h:i A');
                                    }

                                    // OUT text
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

                            {{-- Row 1: Employee name (rowspan 2) + Check-in --}}
                            <tr>

                                <td>C-In</td>
                                @foreach($excelIn as $val)
                                    <td>{{ $val }}</td>
                                @endforeach
                            </tr>

                            {{-- Row 2: Check-out --}}
                            <tr>
                                <td>C-Out</td>
                                @foreach($excelOut as $val)
                                    <td>{{ $val }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
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

    {{-- PDF – A4 portrait, block-wise, improved progress bar --}}
    <script>
        function printDivAsPDF() {
            const { jsPDF } = window.jspdf;

            const webWrapper = document.getElementById('webTableWrapper');
            const printDiv   = document.getElementById('contentToPrint');

            if (!printDiv) {
                alert('Print container not found');
                return;
            }

            const blocks = printDiv.querySelectorAll('.print-block');
            if (!blocks.length) {
                alert('No blocks to print');
                return;
            }

            // Progress overlay elements
            const overlay = document.getElementById('pdfProgressOverlay');
            const bar     = document.getElementById('pdfProgressBar');
            const text    = document.getElementById('pdfProgressText');

            const oldTitle = document.title;
            document.title = "Generating PDF...";

            // Web table hide, print-view show
            if (webWrapper) webWrapper.classList.add('d-none');
            printDiv.classList.remove('d-none');

            // Overlay show
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

            // A4 PORTRAIT = 210 x 297 mm
            const pdf = new jsPDF("p", "mm", "a4");

            const pageWidth  = 210;
            const pageHeight = 297;

            const marginX = 10;
            const marginY = 10;

            const usableWidth  = pageWidth  - marginX * 2;
            const usableHeight = pageHeight - marginY * 2;

            let currentY = marginY;

            const CANVAS_OPTIONS = {
                scale: 1,
                useCORS: true,
                logging: false
            };

            const totalBlocks = blocks.length;

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

            const processBlock = (index) => {
                if (index >= totalBlocks) {
                    // Done
                    pdf.save("{{ $dates->first()->date->format('M-Y') }}.pdf");

                    // View reset
                    if (webWrapper) webWrapper.classList.remove('d-none');
                    printDiv.classList.add('d-none');
                    document.title = oldTitle;

                    // Overlay hide
                    if (overlay) {
                        overlay.classList.remove('d-flex');
                        overlay.classList.add('d-none');
                    }
                    return;
                }

                const el = blocks[index];

                // Start of this block => show live progress
                updateProgress(index);

                html2canvas(el, CANVAS_OPTIONS).then(canvas => {
                    const imgData   = canvas.toDataURL('image/jpeg', 0.9);
                    const imgWidth  = usableWidth;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;

                    if (currentY + imgHeight > pageHeight - marginY) {
                        pdf.addPage();
                        currentY = marginY;
                    }

                    pdf.addImage(imgData, "JPEG", marginX, currentY, imgWidth, imgHeight);
                    currentY += imgHeight + 3;

                    // Block successfully done
                    updateProgress(index + 1);

                    setTimeout(() => processBlock(index + 1), 0);
                }).catch(() => {
                    // Even if this block fails, move progress forward
                    updateProgress(index + 1);
                    setTimeout(() => processBlock(index + 1), 0);
                });
            };

            processBlock(0);
        }
    </script>

    {{-- Excel – export using hidden excelTable (employee name rowspan) --}}
    <script>
        function exportToExcel() {
            let table = document.getElementById("excelTable"); // 🔹 yahi table export hoga

            if (!table) {
                alert("Table not found for export.");
                return;
            }

            let workbook  = XLSX.utils.book_new();
            let worksheet = XLSX.utils.table_to_sheet(table);

            XLSX.utils.book_append_sheet(workbook, worksheet, "Attendance");
            XLSX.writeFile(workbook, "attendance_{{ $dates->first()->date->format('M-Y') }}.xlsx");
        }
    </script>
@endsection
