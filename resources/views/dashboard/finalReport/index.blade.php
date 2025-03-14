 @extends('dashboard.layout.root')

 @section('content')
     <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
     <style>
         .break:hover .position-absolute {
             display: block !important;
         }
     </style>
     <div class="pb-20">
         <div class="content">
             <div class="container-fluid p-4">
                 <!-- Header Section -->

                 <!-- Filter and Action Buttons -->
                 <div class="row align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                     <div class="col-12 col-md-6 mb-2">
                         <form action="{{ route('reports.index') }}" method="GET"
                             class="d-flex flex-column flex-md-row align-items-stretch">
                             @csrf
                             <input type="month" name="month" value="{{ $month }}" placeholder="Start Date"
                                 class="form-control mb-2 mb-md-0 mr-md-2">
                             <input type="submit" value="Filter" class="btn btn-success text-white mb-2">
                             <a href="{{ route('reports.index') }}" class="btn btn-info mb-2 ml-2">Clear</a>
                         </form>
                     </div>
                     <div class="col-12 col-md-6 text-center text-md-right">
                         <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                             class="btn btn-primary text-white mb-2 mb-md-0 mr-md-2">Check In</a>
                         <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
                             class="btn btn-danger text-white mb-2 mb-md-0 mr-md-2">Check Out</a>
                         <button class="btn btn-warning text-white mb-2 mb-md-0 mr-md-2" onclick="printDivAsPDF()">Download
                             as PDF</button>
                         <button onclick="exportToExcel()">Export to Excel</button>
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
                                     <table id="contentToPrint"
                                         class="table table-bordered table-hover align-middle text-center">
                                         <!-- Fixed Header -->
                                         <thead class="bg-primary text-white sticky-top">
                                             <tr>
                                                 <th class="sticky left-0 bg-primary" style="z-index: 20;">Employees</th>
                                                 <!-- Sticky first column -->
                                                 <th>Dates</th>
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
                                                         } else {
                                                             $officeDays++;
                                                         }
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
                                         <!-- Table Body -->
                                         <tbody>
                                             @foreach ($users as $user)
                                                 <tr>
                                                     <td class="fw-bold sticky left-0 bg-light" style="z-index: 10;">
                                                         {{ $user?->name }}</td> <!-- Sticky first column -->
                                                     <td>
                                                         <div class="d-flex flex-column">
                                                             <span class="badge bg-success">Check-in</span>
                                                             <hr class="my-1">
                                                             <span class="badge bg-danger">Check-out</span>
                                                         </div>
                                                     </td>
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
                                                     @foreach ($dates as $dateObj)
                                                         @php
                                                             $d = \Carbon\Carbon::parse($dateObj->date);
                                                             $record = $attendanceRecords
                                                                 ->where('user_id', $user->id)
                                                                 ->first(function ($record) use ($d) {
                                                                     return $record->created_at->format('Y-m-d') ===
                                                                         $d->format('Y-m-d');
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
                                                                     Carbon\Carbon::parse($record?->check_out)->format(
                                                                         'H:i',
                                                                     ) <
                                                                         Carbon\Carbon::parse(
                                                                             $user->check_out_time,
                                                                         )->format('H:i')
                                                                 ) {
                                                                     $goneBeforeTimeCount++;
                                                                     $checkOutTime = Carbon\Carbon::parse(
                                                                         $record?->check_out,
                                                                     )->format('H:i'); // Convert datetime to time (H:i:s)
                                                                     $userCheckOutTime = Carbon\Carbon::parse(
                                                                         $user->check_out_time,
                                                                     ); // Already a time

                                                                     $goneBeforeTime += Carbon\Carbon::createFromFormat(
                                                                         'H:i',
                                                                         $checkOutTime,
                                                                     )->diffInMinutes($userCheckOutTime);
                                                                 }
                                                             }

                                                             $leave = App\Models\Leave::whereDate(
                                                                 'start_date',
                                                                 '<=',
                                                                 $d,
                                                             )
                                                                 ->whereDate('end_date', '>=', $d)
                                                                 ->where([
                                                                     'user_id' => $user->id,
                                                                 ])
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
                                                                 ->where('office_id', $user->office_id)
                                                                 ->where('is_off', '1')
                                                                 ->first();
                                                             if ($off) {
                                                                 $offDays++;
                                                             }
                                                         @endphp
{{--                                                         @if ($leave)--}}
{{--                                                             <td class="bg-warning text-dark">Leave approved by--}}
{{--                                                                 {{ $leave->responsesBy?->name }}</td>--}}
                                                         @if ($off)
                                                             <td class="bg-info text-dark">{{ $off->title }}</td>
                                                         @else
                                                             <td class="break position-relative">
                                                                 <div class="d-flex flex-column">
                                                                     <span title="{{ $record?->check_in_note }}"
                                                                         class="badge bg-light text-dark"
                                                                         style="color: {{ Carbon\Carbon::parse($record?->check_in)->format('H:i:s') < Carbon\Carbon::parse($user->check_in_time)->format('H:i:s') ? 'green' : ($record?->late ? 'red' : 'grey') }}!important;">
                                                                         {{ $record?->check_in?->format('h:i:s A') ?? '-' }}
                                                                         @if ($record?->check_in_note && $record->check_in_note_status == 'rejected')
                                                                             <i class="fas fa-times text-danger"
                                                                                 style="margin-left: 5px;"></i>
                                                                         @elseif($record?->check_in_note && $record->check_in_note_status == 'approved')
                                                                             <i class="fas fa-check text-success"
                                                                                 style="margin-left: 5px;"></i>
                                                                         @elseif($record?->check_in_note && $record->check_in_note_status == 'pending')
                                                                             <i class="text-warning"
                                                                                 style="margin-left: 5px;">P</i>
                                                                         @endif
                                                                     </span>
                                                                     <hr class="my-1">
                                                                     <span title="{{ $record?->check_out_note }}"
                                                                         class="badge bg-light text-dark"
                                                                         style="color: {{ Carbon\Carbon::parse($record?->check_out)->format('H:i:s') > Carbon\Carbon::parse($user->check_out_time)->format('H:i:s') ? 'green' : 'red' }}!important;">
                                                                         {{ $record?->check_out?->format('h:i:s A') ?? '-' }}
                                                                         @if ($record?->check_out_note && $record->check_out_note_status == 'rejected')
                                                                             <i class="fas fa-times text-danger"
                                                                                 style="margin-left: 5px;"></i>
                                                                         @elseif($record?->check_in_note && $record->check_out_note_status == 'approved')
                                                                             <i class="fas fa-check text-success"
                                                                                 style="margin-left: 5px;"></i>
                                                                         @elseif($record?->check_out_note && $record->check_out_note_status == 'pending')
                                                                             <i class="text-warning"
                                                                                 style="margin-left: 5px;">P</i>
                                                                         @endif
                                                                     </span>
                                                                 </div>
                                                                 @php
                                                                     $breaks = $record?->breaks;
                                                                     if($record){
                                                                         $corrections = \App\Models\CorrectionNote::where('attendance_record_id', $record->id)->get();
                                                                     }else{
                                                                         $corrections = null;
                                                                     }
                                                                 @endphp
                                                                 @if ($breaks || $corrections)
                                                                     <!-- Employee Break Details Table, shown only on hover -->
                                                                     <div class="bg-success p-3 rounded shadow-lg position-absolute translate-middle z-50 d-none mt-2"
                                                                         style="width: auto; background-color: #28a745;">
                                                                         @if($breaks)
                                                                         <table
                                                                             class="table table-bordered table-sm w-auto">
                                                                             <thead>
                                                                                 <tr>
                                                                                     <th>Break Start</th>
                                                                                     <th>Break End</th>
                                                                                     <th>Duration</th>
                                                                                 </tr>
                                                                             </thead>
                                                                             <tbody>
                                                                             @foreach($breaks as $break)
                                                                                 <tr>
                                                                                     <td>{{$break->start_time}}</td>
                                                                                     <td>{{$break->end_time}}</td>
                                                                                     @php
                                                                                         $start_time = \Carbon\Carbon::parse($break->start_time);
                                                                                         $end_time = \Carbon\Carbon::parse($break->end_time);
                                                                                     @endphp
                                                                                     <td>{{$start_time->diffInMinutes($end_time)}}</td>
                                                                                 </tr>
                                                                             @endforeach
                                                                             <!-- Add more rows as needed -->
                                                                             </tbody>
                                                                         </table>
                                                                             <span class="text-red-700">{{$leave ? 'Leave: '.$leave->status : ''}}</span>
                                                                         @endif
                                                                             @if($corrections)
                                                                                 <table
                                                                                     class="table table-bordered table-sm w-auto">
                                                                                     <thead>
                                                                                     <tr>
                                                                                         <th>Note</th>
                                                                                         <th>Action</th>
                                                                                     </tr>
                                                                                     </thead>
                                                                                     <tbody>
                                                                                     @foreach($corrections as $correction)
                                                                                         <tr>
                                                                                             <td>{{$correction->note}}</td>
                                                                                             <td><a href="{{route('correctionNote.create', ['record' => $record->id, 'note' => $correction->id])}}">reply</a></td>

                                                                                         </tr>
                                                                                     @endforeach
                                                                                     <!-- Add more rows as needed -->
                                                                                     </tbody>
                                                                                 </table>
                                                                             @endif
                                                                     </div>
                                                                 @endif

                                                             </td>
                                                         @endif
                                                     @endforeach
                                                     <td class="fw-bold">{{ $officeDays - $offDays }}</td>
                                                     <td>{{ $workingDays }}</td>
                                                     <td>{{ $halfDayCount }}</td>
                                                     <td>{{ $leaveDays }}</td>
                                                     <td>{{ $lateCount }}</td>
                                                     <td>{{ $lateTime ? App\Http\Controllers\HomeController::getTime($lateTime) : 'N/A' }}
                                                     </td>
                                                     <td>{{ $goneBeforeTime ? App\Http\Controllers\HomeController::getTime($goneBeforeTime) : 'N/A' }}
                                                     </td>
                                                     <td>{{ $goneBeforeTimeCount }}</td>
                                                     @if ($d < \Carbon\Carbon::today() && auth()->user()->hasRole('admin|super_admin'))
                                                         @php
                                                             $oneDaySalary = $user->salary / 30;
                                                             //$salary = $user->salary - (($leaveDays * $oneDaySalary) + (($halfDayCount * $oneDaySalary)/2) + ((($lateTime + $goneBeforeTime)/$user->office_time) * $oneDaySalary))
                                                             //$salary = (($workingDays * $oneDaySalry) + (($halfDayCount * $oneDaySalary)/2)) - ((($lateTime + $goneBeforeTime)/$user->office_time) * $oneDaySalary);
                                                             $salary =
                                                                 $workingDays * $oneDaySalary +
                                                                 $sundayCount * $oneDaySalary +
                                                                 $paidLeave * $oneDaySalary +
                                                                 $offDays * $oneDaySalary +
                                                                 $offDays * $oneDaySalary +
                                                                 ($halfDayCount * $oneDaySalary) / 2;
                                                         @endphp
                                                         <td>{{ $salary }}</td>
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



     <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


     <script>
         function printDivAsPDF() {
             const {
                 jsPDF
             } = window.jspdf;

             // Get the div element
             var element = document.getElementById('contentToPrint');

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
                 pdf.save('{{ $dates->first()->date->format('M-Y') }}.pdf');
             });
         }
     </script>



     <script>
         function exportToExcel() {
             let table = document.getElementById("contentToPrint");
             let workbook = XLSX.utils.book_new();
             let worksheet = XLSX.utils.table_to_sheet(table);

             XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
             XLSX.writeFile(workbook, "table_data.xlsx");
         }
     </script>
 @endsection
