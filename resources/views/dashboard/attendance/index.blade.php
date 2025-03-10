
{{-- Records Page  --}}

@extends('dashboard.layout.root')

@section('content')


<form action="{{ route('attendance.index', ['user' => $user?->id]) }}">
    <div class="bg-gray-100 p-4 rounded-lg shadow-md flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
        @role('super_admin|admin|owner')
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 w-full">
                <label for="employee-select" class="text-sm font-medium text-gray-700">Select Employee:</label>
                <select id="employee-select" name="employee" class="border-gray-300 rounded-md shadow-sm p-2 w-full sm:w-auto focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select an employee</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ $u->id == $user?->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        @endrole
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 w-full">
            <label for="month-selector" class="text-sm font-medium text-gray-700">Select Month:</label>
            <input type="month" id="month-selector" name="month" value="{{ $month ?? '' }}" class="border-gray-300 rounded-md shadow-sm p-2 w-full sm:w-auto focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex flex-col sm:flex-row sm:space-x-2 w-full sm:w-auto">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out w-full sm:w-auto">Filter</button>
            <button class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out w-full sm:w-auto">Clear</button>
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
                                        $record = $attendanceRecords
                                            ->where('user_id', $currentUser->id)
                                            ->first(function ($record) use ($d) {
                                                return $record->check_in->format('Y-m-d') === $d->format('Y-m-d');
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
                                    @endphp

                                    @if ($leave && !$record)
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

                    @php
                    $advancePayment = App\Models\AdvancePayment::whereMonth('date', $month)->where('user_id', $currentUser)->sum('amount');
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

                    <button class="btn btn-warning text-white mb-2 mb-md-0 mr-md-2" onclick="printDivAsPDF()">Download
                             as PDF</button>
                    <hr class="border-gray-300 border-4 my-6" />
                    <!-- Summary Information -->
                    <div class="mt-6 bg-red-50 p-4 rounded-lg shadow-md" id="printDiv">
                        <h3 class="text-lg font-semibold text-gray-800">Summary Information</h3>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Office Days:</span>
                                <span class="font-bold text-gray-800">{{ $dates->count() - ($offDays + $sundayCount) }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Working Days:</span>
                                <span class="font-bold text-gray-800">{{ $workingDays }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Half Days:</span>
                                <span class="font-bold text-gray-800">{{ $halfDayCount }}</span>
                            </div>

                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Leaves:</span>
                                <span class="font-bold text-gray-800">{{ $leaveDays }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Salary:</span>
                                <span class="font-bold text-gray-800">{{ $currentUser->salary }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Generated Salary:</span>
                            @if($condition)
                                <span class="font-bold text-gray-800">{{ $userSalary ? $userSalary->day_wise_salary : 'Salary not generated yet!' }}</span>
                            @endif
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Advance Payment</span>
                                <span class="font-bold text-gray-800">{{ $advancePayment }}</span>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
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

                 var pdf = new jsPDF('p', 'mm', 'a0');
                 var pageHeight = pdf.internal.pageSize.getHeight();
                 var imgWidth = 841; // A4 width in mm
                 var imgHeight = (canvas.height * imgWidth) / canvas.width;

                 var heightLeft = imgHeight;
                 var position = 0;

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
