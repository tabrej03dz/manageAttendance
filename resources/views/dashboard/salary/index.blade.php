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
                                            @php
                                                $officeDays = 0;
                                                $sundayCount = 0;
                                            @endphp
                                            @foreach ($dates as $dateObj)
                                                @php
                                                    $d = \Carbon\Carbon::parse($dateObj->date);
                                                    $isSunday = $d->format('D') === 'Sun';
                                                    if ($isSunday) {
                                                        $sundayCount++;
                                                    }else{
                                                        $officeDays++;
                                                    }
                                                @endphp
                                            @endforeach
                                                <th>Day wise Salary</th>
                                                <th>Hour wise Salary</th>
                                                <th>Status</th>
                                                <th>Mark as paid</th>
                                        </tr>
                                        </thead>
                                        <!-- Table Body -->
                                        <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td class="fw-bold sticky left-0 bg-light" style="z-index: 10;">{{ $user->name }}</td> <!-- Sticky first column -->
                                                @php
                                                    $workingDays = 0;
                                                    $leaveDays = 0;
                                                    $offDays = 0;
                                                    $lateCount = 0;
                                                    $lateTime = 0;
                                                    $goneBeforeTime = 0;
                                                    $goneBeforeTimeCount = 0;
                                                    $halfDayCount = 0;
                                                    $workingDuration = 0;
                                                @endphp
                                                @foreach ($dates as $dateObj)
                                                    @php
                                                        $d = \Carbon\Carbon::parse($dateObj->date);
                                                        $record = $attendanceRecords
                                                            ->where('user_id', $user->id)
                                                            ->first(function ($record) use ($d) {
                                                                return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
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
                                                            ->where(['user_id' => $user->id, 'status' => 'approved'])
                                                            ->first();
                                                        if ($leave) {
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


                                                @if(($d < \Carbon\Carbon::today()) && auth()->user()->hasRole('admin|super_admin'))
                                                    @php

                                                    $userSalary = App\Models\Salary::where('user_id', $user->id)->where('month', $d)->first();
                                                    if (!$userSalary){
                                                        $oneDaySalary = $user->salary / 30;
                                                        $oneHourSalary = $oneDaySalary/ ($user->office_time/60);
                                                        //$salary = $user->salary - (($leaveDays * $oneDaySalary) + (($halfDayCount * $oneDaySalary)/2) + ((($lateTime + $goneBeforeTime)/$user->office_time) * $oneDaySalary))
                                                       //$salary = (($workingDays * $oneDaySalry) + (($halfDayCount * $oneDaySalary)/2)) - ((($lateTime + $goneBeforeTime)/$user->office_time) * $oneDaySalary);
                                                       $salary = (($workingDays * $oneDaySalary) + ($sundayCount * $oneDaySalary) +  ($offDays * $oneDaySalary) + (($halfDayCount * $oneDaySalary)/2));
                                                        $durationSalary = (($workingDuration/60) * $oneHourSalary) + (($sundayCount + $offDays) * $oneDaySalary);
                                                    $userSalary = App\Models\Salary::create(['user_id' => $user->id, 'month' => $d, 'day_wise_salary' => $salary, 'hour_wise_salary' => $durationSalary, 'status' => 'unpaid']);

                                                    }

                                                    @endphp


                                                    @if($userSalary)
                                                        <td>{{ round($userSalary->day_wise_salary) }}</td>
                                                        <td>{{ round($userSalary->hour_wise_salary) }}</td>
                                                        <td>{{ $userSalary->status }}</td>
                                                        <td>
                                                            @if($userSalary->status == 'paid')
                                                                <a href="#" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a>
                                                            @else
                                                                <a href="{{route('salary.status', ['salary' => $userSalary->id])}}" class="btn btn-primary btn-sm">Mark as Paid</a>
                                                            @endif
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
