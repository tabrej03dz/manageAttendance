@extends('dashboard.layout.root')

@section('content')
    <div class="pb-20">
        <div class="content">
            <div class="container-fluid p-4">
                <!-- Header Section -->

                <!-- Filter and Action Buttons -->
                <div class="row align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                    <div class="col-12 col-md-6 mb-2">
                        <form action="{{ route('salary.index') }}" method="GET"
                              class="d-flex flex-column flex-md-row align-items-stretch">
                            @csrf
                            <input type="month" name="month" value="{{$month}}" placeholder="Start Date"
                                   class="form-control mb-2 mb-md-0 mr-md-2">
                            <input type="submit" value="Filter" class="btn btn-success text-white mb-2">
                            <a href="{{ route('salary.index') }}" class="btn btn-info mb-2 ml-2">Clear</a>
                        </form>
                    </div>
                    <div class="col-12 col-md-6 text-center text-md-right">

                    </div>
                </div>

            </div>

            <div id="">

                <div class="container">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="mb-2 mb-md-0 text-primary font-weight-bold">Records</h4>
                        <h4 class="mb-2 mb-md-0 text-secondary font-weight-bold">{{ $dates->first()->date->format('M-Y') }}
                        </h4>
                    </div>

                    <!-- Attendance Table -->
                    <div class="table-responsive mt-3" style="max-height: 75vh; overflow-y: auto;">

                        <!-- Attendance Table (Web View) -->
                        <div class="table-responsive text-xs">
                            <!-- Attendance Table (Web View) -->
                            <div class="table-responsive mt-3 d-md-block">
                                <div class="table-responsive mt-3" style="max-height: 100vh; overflow-y: auto;">
                                    <table id="contentToPrint" class="table table-bordered table-hover align-middle text-center">
                                        <!-- Fixed Header -->
                                        <thead class="bg-primary text-white sticky-top">
                                        <tr>
                                            <th class="sticky left-0 bg-primary" style="z-index: 20;">Employees</th> <!-- Sticky first column -->
                                            <th>Advance Payment</th>
                                            <th>Employee Salary</th>
                                            <th>Office Days</th>
                                            <th>Working Days</th>
                                            <th>Paid Leave</th>
                                            <th>Unpaid Leave</th>
                                            <th>Deduction</th>
{{--                                            <th>Late Deduction</th>--}}
{{--                                            <th>Other Deduction</th>--}}

                                            @php
                                                $officeDays = 0;
                                                $sundayCount = 0;
                                            @endphp
{{--                                            @foreach ($dates as $dateObj)--}}
{{--                                                @php--}}
{{--                                                    $d = \Carbon\Carbon::parse($dateObj->date);--}}
{{--                                                    $isSunday = $d->format('D') === 'Sun';--}}
{{--                                                    if ($isSunday) {--}}
{{--                                                        $sundayCount++;--}}
{{--                                                    }else{--}}
{{--                                                        $officeDays++;--}}
{{--                                                    }--}}
{{--                                                @endphp--}}
{{--                                            @endforeach--}}
                                                <th>Day wise Salary</th>
                                                <th>Hour wise Salary</th>
                                                <th>Status</th>
                                                <th>Paid Amount</th>
                                        </tr>
                                        </thead>
                                        <!-- Table Body -->
                                        <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td class="fw-bold sticky left-0 bg-light" style="z-index: 10;">{{ $user->name }}</td> <!-- Sticky first column -->
                                                @php
                                                    $d = \Carbon\Carbon::parse($dates->first()->date);
                                                    $advancePayment = $advancePayments
                                                        ->where('user_id', $user->id)
                                                        ->filter(function ($item) use ($d) {
                                                            return $item->date->format('Y') == $d->format('Y') &&
                                                                   $item->date->format('m') == $d->format('m');
                                                        })
                                                        ->sum('amount');                                                @endphp
                                                <td>{{$advancePayment}}</td>
                                                <td>{{ round($user->userSalary ? $user->userSalary->total_salary : $user->salary) }}</td>

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
                                                    $officeDays = 0;
                                                    $sundayCount = 0;
                                                @endphp
                                                @foreach ($dates as $dateObj)
                                                    @php
                                                        $d = \Carbon\Carbon::parse($dateObj->date);

                                                        $d = \Carbon\Carbon::parse($dateObj->date);
                                                        $isSunday = $d->format('D') === 'Sun';
                                                        if ($isSunday) {
                                                            $sundayCount++;
                                                        }else{
                                                            $officeDays++;
                                                        }

                                                        $record = $attendanceRecords
                                                            ->where('user_id', $user->id)
                                                            ->first(function ($record) use ($d) {
                                                                return $record->check_in->format('Y-m-d') === $d->format('Y-m-d');
                                                            });

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
                                                            if ($record->check_out && Carbon\Carbon::parse($record?->check_out)->format('H:i') < Carbon\Carbon::parse($user->check_out_time)->format('H:i')) {
                                                            $goneBeforeTimeCount++;
                                                               $checkOutTime = Carbon\Carbon::parse($record?->check_out)->format('H:i'); // Convert datetime to time (H:i:s)
                                                               $userCheckOutTime = Carbon\Carbon::parse($user->check_out_time); // Already a time

                                                               $goneBeforeTime += Carbon\Carbon::createFromFormat('H:i', $checkOutTime)->diffInMinutes($userCheckOutTime);
                                                           }
                                                            $workingDuration += $record->duration;

                                                        }

                                                        $leave = App\Models\Leave::whereDate('start_date', '<=', $d)
                                                            ->whereDate('end_date', '>=', $d)
                                                            ->where(['user_id' => $user->id])
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
                                                            ->where('office_id', $user->office_id)->where('is_off', '1')
                                                            ->first();
                                                        if ($off) {
                                                            $offDays++;
                                                        }
                                                    @endphp

                                                @endforeach


                                                @if(($d < \Carbon\Carbon::today()) && auth()->user()->hasRole('admin|super_admin|owner') && $user->salary > 0)
                                                @php
                                                    $userSalary = App\Models\Salary::where('user_id', $user->id)->where('month', $d)->first();
                                                    if (!$userSalary) {
                                                        $oneDaySalary = ($user->userSalary ?  $user->userSalary->total_salary : $user->salary) / 30;
                                                        $oneHourSalary = $oneDaySalary / ($user->office_time / 60)  ;

                                                        // Calculate salaries

                                                        $salary = (($workingDays * $oneDaySalary) + ($sundayCount * $oneDaySalary) + ($offDays * $oneDaySalary) + (($halfDayCount * $oneDaySalary) / 2) + ($paidLeave * $oneDaySalary));
                                                        $durationSalary = ($oneDaySalary * $sundayCount) + (($workingDuration / 60) * $oneHourSalary) + ($offDays * $oneDaySalary);

                                                        if ($user->userSalary){
                                                        $providentFund = ($user->userSalary->total_salary * $user->userSalary->provident_fund)/100;
                                                        $esic = ($user->userSalary->total_salary * $user->userSalary->employee_state_insurance_corporation)/100;
                                                        }else{
                                                            $providentFund = 0;
                                                            $esic = 0;
                                                        }

                                                        // Create the salary record
                                                        $userSalary = App\Models\Salary::create([
                                                            'user_id' => $user->id,
                                                            'month' => $d,
                                                            'day_wise_salary' => $salary,
                                                            'hour_wise_salary' => $durationSalary,
                                                            'status' => 'unpaid',
                                                            'basic_salary' => $user->userSalary ? $user->userSalary->basic_salary : $user->salary,
                                                            'house_rent_allowance' => $user->userSalary ? $user->userSalary->house_rent_allowance : 0,
                                                            'transport_allowance' => $user->userSalary ? $user->userSalary->transport_allowance : 0,
                                                            'medical_allowance' => $user->userSalary ? $user->userSalary->medical_allowance : 0,
                                                            'special_allowance' => $user->userSalary ? $user->userSalary->special_allowance : 0,
                                                            'dearness_allowance' => $user->userSalary ? $user->userSalary->dearness_allowance : 0,
                                                            'relieving_charge' => $user->userSalary ? $user->userSalary->relieving_charge : 0,
                                                            'additional_allowance' => $user->userSalary ? $user->userSalary->additional_allowance : 0,
                                                            'provident_fund' => $providentFund,
                                                            'employee_state_insurance_corporation' => $esic,
                                                            'advance' => $advancePayment,
                                                            'total_salary' => $salary - ($providentFund + $esic),
                                                            //'paid_leave' => $paidLeave,
                                                            //'unpaid_leave' => $unpaidLeave,
                                                            //'office_days' => $dates->count() - ($sundayCount + $offDays),
                                                            //'office_closed_days' => $offDays,
                                                            //'working_days' => $workingDays,
                                                        ]);
                                                    }
                                                @endphp

                                                @if($userSalary)
                                                    <td>{{ $userSalary->office_days }}</td>
                                                    <td>{{ $userSalary->working_days }}</td>
                                                    <td>{{ $userSalary->paid_leave }}</td>
                                                    <td>{{ $userSalary->unpaid_leave }}</td>
                                                    <td>{{ ($userSalary->provident_fund + $userSalary->employee_state_insurance_corporation) }}</td>
{{--                                                    <td>{{ $userSalary->late_deduction }}</td>--}}
{{--                                                    <td>{{ $userSalary->other_deduction }}</td>--}}
                                                    <td>{{ number_format($userSalary->day_wise_salary - ($userSalary->provident_fund + $userSalary->employee_state_insurance_corporation), 2) }}</td>
                                                    <td>{{ number_format($userSalary->hour_wise_salary - ($userSalary->provident_fund + $userSalary->employee_state_insurance_corporation), 2) }}</td>
                                                    <td>{{ $userSalary->status }}</td>
                                                    <td>
                                                        <form action="{{ route('salary.pay', ['salary' => $userSalary->id]) }}" method="post" class="form-inline">
                                                            @csrf
                                                            <div class="form-group">
                                                                <input type="number" name="paid_amount" class="form-control mr-2" placeholder="Paid Amount" value="{{ $userSalary->paid_amount ?? '' }}">
                                                            </div>
                                                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                                                        </form>
                                                        <a href="{{route('salary.slip', ['salary' => $userSalary->id])}}" class="btn btn-warning">Salary Slip</a>
                                                    </td>
                                                @else
                                                    <td>Salary not generated yet</td>
                                                @endif
                                            @endif

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
