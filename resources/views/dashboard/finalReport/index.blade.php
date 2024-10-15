 @extends('dashboard.layout.root')

 @section('content')
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
                             <input type="date" name="start" placeholder="Start Date"
                                 class="form-control mb-2 mb-md-0 mr-md-2">
                             <input type="date" name="end" placeholder="End Date"
                                 class="form-control mb-2 mb-md-0 mr-md-2">
                             <input type="submit" value="Filter" class="btn btn-success text-white mb-2">
                             <a href="{{ route('attendance.index') }}" class="btn btn-info mb-2 ml-2">Clear</a>
                         </form>
                     </div>
                     <div class="col-12 col-md-6 text-center text-md-right">
                         <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                             class="btn btn-primary text-white mb-2 mb-md-0 mr-md-2">Check In</a>
                         <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
                             class="btn btn-danger text-white mb-2 mb-md-0 mr-md-2">Check Out</a>
                         <button class="btn btn-warning text-white mb-2 mb-md-0 mr-md-2" onclick="printDivAsPDF()">Download
                             as PDF</button>
                     </div>
                 </div>

             </div>

             <div id="contentToPrint">

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
                                    <table class="table table-bordered table-hover align-middle text-center">
                                         <!-- Fixed Header -->
                                         <thead class="bg-primary text-white sticky-top">
                                         <tr>
                                             <th class="sticky left-0 bg-primary" style="z-index: 20;">Employees</th> <!-- Sticky first column -->
                                             <th>Dates</th>
                                             @php
                                                 $officeDays = 0;
                                             @endphp
                                             @foreach ($dates as $dateObj)
                                                 @php
                                                     $d = \Carbon\Carbon::parse($dateObj->date);
                                                     $isSunday = $d->format('D') === 'Sun';
                                                     if (!$isSunday) {
                                                         $officeDays++;
                                                     }
                                                 @endphp
                                                 <th>{{ $d->format('d-[D]') }}</th>
                                             @endforeach
                                             <th>Office Days</th>
                                             <th>Working Days</th>
                                             <th>Leaves</th>
                                             <th>Late Count</th>
                                             <th>Late in time</th>
                                             <th>Gone Before Time</th>
                                         </tr>
                                         </thead>
                                         <!-- Table Body -->
                                         <tbody>
                                         @foreach ($users as $user)
                                             <tr>
                                                 <td class="fw-bold sticky left-0 bg-light" style="z-index: 10;">{{ $user->name }}</td> <!-- Sticky first column -->
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
                                                     $offDays = 0;
                                                     $lateCount = 0;
                                                     $lateTime = 0;
                                                     $goneBeforeTime = 0;
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
                                                             $workingDays++;
                                                             if ($record->late) {
                                                                 $lateCount++;
                                                                 $lateTime += $record->late;
                                                             }
                                                             if (Carbon\Carbon::parse($record?->check_out)->format('H:i:s') < Carbon\Carbon::parse($user->check_out_time)->format('H:i:s')){
                                                                 $checkOutTime = Carbon\Carbon::parse($record?->check_out)->format('H:i:s');
                                                                 $userCheckOutTime = Carbon\Carbon::parse($user->check_out_time);
                                                                 $goneBeforeTime += Carbon\Carbon::createFromFormat('H:i:s', $checkOutTime)->diffInMinutes($userCheckOutTime);

                                                             }
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
                                                     @if ($leave)
                                                         <td class="bg-warning text-dark">Leave</td>
                                                     @elseif ($off)
                                                         <td class="bg-info text-dark">{{ $off->title }}</td>
                                                     @else
                                                         <td>
                                                             <div class="d-flex flex-column">
                                                                 <span class="badge bg-light text-dark" style="color: {{ Carbon\Carbon::parse($record?->check_in)->format('H:i:s') < Carbon\Carbon::parse($user->check_in_time)->format('H:i:s') ? 'green' : ($record?->late ? 'red' : 'grey') }}!important;">{{ $record?->check_in?->format('h:i:s A') ?? '-' }}</span>
                                                                 <hr class="my-1">
                                                                 <span class="badge bg-light text-dark" style="color: {{ Carbon\Carbon::parse($record?->check_out)->format('H:i:s') > Carbon\Carbon::parse($user->check_out_time)->format('H:i:s') ? 'green' : 'red' }}!important;">{{ $record?->check_out?->format('h:i:s A') ?? '-' }}</span>
                                                             </div>
                                                         </td>
                                                     @endif
                                                 @endforeach
                                                 <td class="fw-bold">{{ $officeDays - $offDays }}</td>
                                                 <td>{{ $workingDays }}</td>
                                                 <td>{{ $leaveDays }}</td>
                                                 <td>{{ $lateCount }}</td>
                                                 <td>{{ $lateTime ? App\Http\Controllers\HomeController::getTime($lateTime) : 'N/A' }}</td>
                                                 <td>{{ $goneBeforeTime ? App\Http\Controllers\HomeController::getTime($goneBeforeTime) : 'N/A' }}</td>
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

                 var pdf = new jsPDF('p', 'mm', 'a4');
                 var pageHeight = pdf.internal.pageSize.getHeight();
                 var imgWidth = 210; // A4 width in mm
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
 @endsection
