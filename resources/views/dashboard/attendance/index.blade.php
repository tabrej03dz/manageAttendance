@extends('dashboard.layout.root')

@section('content')
    <div class="content">
        <div class="container-fluid p-4">
            <!-- Header Section -->

            <!-- Filter and Action Buttons -->
            <div class="row align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                <div class="col-12 col-md-6 mb-2">
                    <form action="{{ route('attendance.index', ['user' => $user?->id]) }}" method="GET"
                        class="d-flex flex-column flex-md-row align-items-stretch">
                        @csrf
                        <input type="date" name="start" placeholder="Start Date"
                            class="form-control mb-2 mb-md-0 mr-md-2">
                        <input type="date" name="end" placeholder="End Date"
                            class="form-control mb-2 mb-md-0 mr-md-2">
                        <input type="submit" value="Filter" class="btn btn-success text-white mb-2">
                        <a href="{{route('attendance.index')}}" class="btn btn-info mb-2 ml-2">Clear</a>
                    </form>
                </div>
                <div class="col-12 col-md-6 text-center text-md-right">
                    <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                        class="btn btn-primary text-white mb-2 mb-md-0 mr-md-2">Check In</a>
                    <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
                        class="btn btn-danger text-white mb-2 mb-md-0 mr-md-2">Check Out</a>
                    <button class="btn btn-warning text-white mb-2 mb-md-0 mr-md-2" onclick="printDivAsPDF()">Download as PDF</button>
                </div>
            </div>
            @role('super_admin|admin')

                <!-- Employee List -->
                <div class="mb-4">
                    <div class="row">
                        @foreach ($users as $u)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-2">
                                <a href="{{ route('attendance.index', ['user' => $u?->id]) }}"
                                    class="btn {{$u?->id == $user?->id ? 'btn-success font-weight-bold' : 'btn-outline-success'}}  w-100 text-truncate font-weight-bold">{{ ucfirst($u?->name) }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endrole
        </div>

        <div id="contentToPrint">

            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h4 class="mb-2 mb-md-0 text-primary font-weight-bold">Records</h4>
                    <h4 class="mb-2 mb-md-0 text-secondary font-weight-bold">{{ $dates->first()->date->format('M-Y') }}
                    </h4>
                </div>

                <!-- Attendance Table -->
                <div class="table-responsive">


                    <!-- Attendance Accordion (Mobile View) -->
                    <div class="accordion d-md-none" id="attendanceAccordion">
                        @foreach ($dates as $index => $dateObj)
                            @php
                                $d = \Carbon\Carbon::parse($dateObj->date);
                                $record = $attendanceRecords->first(function ($record) use ($d) {
                                    return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                                });
                                $isSunday = $d->format('D') === 'Sun';
                            @endphp
                            <div
                                class="card mb-3 border-0 rounded-lg shadow-sm {{ $record ? ($record->late ? 'bg-danger' : 'bg-success') : 'bg-white' }}">
                                <div
                                    class="card-header bg-dark text-white d-flex justify-content-between align-items-center p-3 rounded-top">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-secondary mr-3"></i>
                                        <h6 class="mb-0 font-weight-bold">{{ $d->format('d M Y') }}</h6>
                                    </div>
                                    <button class="btn btn-link text-secondary p-0" style="margin-left: auto; padding-right: 10px;"
                                        type="button" data-toggle="collapse" data-target="#collapse{{ $index }}"
                                        aria-expanded="true" aria-controls="collapse{{ $index }}">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>

                                <div id="collapse{{ $index }}"
                                    class="collapse @if ($index == 0) show @endif"
                                    aria-labelledby="heading{{ $index }}" data-parent="#attendanceAccordion">
                                    <div class="card-body p-3">
                                        <div class="d-flex flex-column mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user-clock text-secondary mr-3"></i>
                                                <p class="mb-0"><strong>Check-in:</strong>
                                                    {{ $record?->check_in?->format('h:i A') ?? 'N/A' }}</p>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user-clock text-secondary mr-3"></i>
                                                <p class="mb-0"><strong>Check-out:</strong>
                                                    {{ $record?->check_out?->format('h:i A') ?? 'N/A' }}</p>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-clock text-secondary mr-3"></i>
                                                <p class="mb-0"><strong>Working Hours:</strong>
                                                    {{ $record?->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : 'N/A' }}
                                                </p>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-map-marker-alt text-secondary mr-3"></i>
                                                <p class="mb-0"><strong>Check-in Distance:</strong>
                                                    {{ $record?->check_in_distance ?? 'N/A' }}</p>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-map-marker-alt text-secondary mr-3"></i>
                                                <p class="mb-0"><strong>Check-out Distance:</strong>
                                                    {{ $record?->check_out_distance ?? 'N/A' }}</p>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-calendar-day text-secondary mr-3"></i>
                                                <p class="mb-0"><strong>Day Type:</strong> {{ $record?->day_type }}</p>
                                            </div>
                                            <div class="d-flex justify-content-around mt-3">
                                                @if ($record?->check_in_image)
                                                    <div class="text-center">
                                                        <a href="{{ asset('storage/' . $record->check_in_image) }}"
                                                            target="_blank">
                                                            <img src="{{ asset('storage/' . $record->check_in_image) }}"
                                                                alt="Check-in Image" class="img-fluid rounded shadow-sm"
                                                                style="max-width: 70px; height: auto;">
                                                        </a>
                                                        <small class="text-secondary d-block mt-1">Check-in</small>
                                                    </div>
                                                @endif
                                                @if ($record?->check_out_image)
                                                    <div class="text-center">
                                                        <a href="{{ asset('storage/' . $record->check_out_image) }}"
                                                            target="_blank">
                                                            <img src="{{ asset('storage/' . $record->check_out_image) }}"
                                                                alt="Check-out Image" class="img-fluid rounded shadow-sm"
                                                                style="max-width: 70px; height: auto;">
                                                        </a>
                                                        <small class="text-secondary d-block mt-1">Check-out</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Attendance Table (Web View) -->
                    <div class="table-responsive mt-3 d-none d-md-block">
                        <table class="table table-striped table-bordered">
                        <thead class="thead-dark sticky-top bg-white">
                            <tr>
                                <th>Date</th>
                                <th>Check-in Time</th>
                                <th>Late</th>
                                <th>Check-in Image</th>
                                <th>Check-out Time</th>
                                <th>Check-out Image</th>
                                <th>Working Hours</th>
                                <th>Day Type</th>
                                <th>Check-in Distance</th>
                                <th>Check-out Distance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $officeDays = 0;
                                $workingDays = 0;
                                $leaveDays = 0;
                                $offDays = 0;
                            @endphp

                            @foreach ($dates as $dateObj)
                                @php

                                    $d = \Carbon\Carbon::parse($dateObj->date);
                                    $record = $attendanceRecords->first(function ($record) use ($d) {
                                        return $record->created_at->format('Y-m-d') === $d->format('Y-m-d');
                                    });
                                    $isSunday = $d->format('D') === 'Sun';


                                    if($record){
                                        $workingDays++;
                                    }

                                    $leave = App\Models\Leave::whereDate('start_date', '<=', $d)
                                        ->whereDate('end_date', '>=', $d)->where(['user_id' => $user ? $user->id : auth()->user()->id, 'status' => 'approved'])
                                        ->first();
                                    if($leave){
                                        $leaveDays++;
                                    }
                                    $off = App\Models\Off::whereDate('date', $d)->where('office_id', auth()->user()->office_id)->first();
                                    if($off){
                                        $offDays++;
                                    }
                                    if(!$isSunday){
                                        $officeDays++;
                                    }
                                @endphp
                                @if($leave)
                                    <tr>
                                        <td>{{ $d->format('d-[D]') }}</td>
                                        <td colspan="2"><strong>Leave Type:</strong></td>
                                        <td colspan="3">{{$leave->leave_type}}</td>
                                        <td colspan="2"><strong>Reason:</strong></td>
                                        <td colspan="3">{{$leave->reason}}</td>
                                    </tr>
                                @else
                                    <tr
                                        class="{{ $isSunday ? 'bg-info' : '' }}">
                                        <td>{{ $off ? $d->format('d-[D]').' '. $off?->title.' (OFF)' : $d->format('d-[D]')}}</td>
                                        <td class="{{$record?->late ? 'text-danger' : ''}}" >{{ $record?->check_in?->format('h:i:s A') }}</td>
                                        <td class="{{$record?->late ? 'text-danger' : ''}}" >{{ $record?->late ? App\Http\Controllers\HomeController::getTime($record->late) : '' }}
                                        </td>
                                        <td>
                                            @if ($record?->check_in_image)
                                                <a href="{{ asset('storage/' . $record->check_in_image) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $record->check_in_image) }}"
                                                         alt="Check-in Image" class="img-fluid rounded shadow-sm"
                                                         style="max-width: 70px; height: auto;">
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $record?->check_out?->format('h:i:s A') }}</td>
                                        <td>
                                            @if ($record?->check_out_image)
                                                <a href="{{ asset('storage/' . $record->check_out_image) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $record->check_out_image) }}"
                                                         alt="Check-out Image" class="img-fluid rounded shadow-sm"
                                                         style="max-width: 70px; height: auto;">
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $record?->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : '' }}
                                        </td>
                                        <td>{{ $record?->day_type }}</td>
                                        <td class="{{$record?->check_in_distance > 100 ? 'text-danger' : ''}}" >{{ $record?->check_in_distance }}</td>
                                        <td {{$record?->check_out_distance > 100 ? 'text-danger' : ''}}>{{ $record?->check_out_distance }}</td>
                                    </tr>
                                @endif

                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Office Days:</th>
                        <th>{{$officeDays - $offDays}}</th>
                        <th>Working Days:</th>
                        <th>{{$workingDays}}</th>
                        <th>Leaves:</th>
                        <th>{{$leaveDays}}</th>
                    </tr>
                </thead>
            </table>
        </div>
        </div>
    </div>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


    <script>
        function printDivAsPDF() {
            const { jsPDF } = window.jspdf;

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
