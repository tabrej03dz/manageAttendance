@extends('dashboard.layout.root')

@section('title', 'Day-wise Attendance')

@push('styles')
<style>
    .day-attendance-page{font-family:'Inter',sans-serif;color:#0f172a}
    .day-attendance-hero{position:relative;overflow:hidden;border:1px solid #312e81;border-radius:24px;background:linear-gradient(135deg,#0f172a 0%,#172554 52%,#312e81 100%);box-shadow:0 20px 45px rgba(15,23,42,.24);isolation:isolate}
    .day-attendance-hero:before{content:'';position:absolute;width:290px;height:290px;right:-90px;top:-125px;border-radius:999px;background:rgba(99,102,241,.32);z-index:-1}
    .day-attendance-hero:after{content:'';position:absolute;width:230px;height:230px;left:38%;bottom:-155px;border-radius:999px;background:rgba(6,182,212,.18);z-index:-1}
    .day-panel,.day-filter-panel{border:1px solid #dbe3ee;border-radius:20px;background:#fff;box-shadow:0 12px 32px rgba(15,23,42,.10)}
    .day-filter-panel{border-color:#cbd5e1;border-radius:18px;box-shadow:0 8px 24px rgba(15,23,42,.08)}
    .day-filter-label{display:block;margin-bottom:7px;color:#334155;font-size:12px;font-weight:800;letter-spacing:.04em;text-transform:uppercase}
    .day-filter-control{width:100%;min-height:46px;border:1px solid #cbd5e1!important;border-radius:12px!important;background:#fff!important;color:#0f172a!important;padding:10px 13px!important;font-size:14px;font-weight:700;outline:none;box-shadow:none!important}
    .day-filter-control:focus{border-color:#4f46e5!important;box-shadow:0 0 0 4px rgba(79,70,229,.12)!important}
    .day-button{min-height:46px;display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;padding:10px 16px;font-size:14px;font-weight:800;text-decoration:none!important;transition:.2s}
    .day-button:hover{transform:translateY(-1px)}
    .day-button-primary{background:linear-gradient(135deg,#4338ca,#6366f1);color:#fff!important;box-shadow:0 10px 20px rgba(79,70,229,.22)}
    .day-button-secondary{border:1px solid #cbd5e1;background:#f8fafc;color:#334155!important}
    .day-table-wrap{max-height:72vh;overflow:auto;border:1px solid #dbe3ee;border-radius:16px;background:#fff}
    .day-table{width:100%;min-width:1650px;border-collapse:separate;border-spacing:0}
    .day-table thead th{position:sticky;top:0;z-index:5;border-bottom:1px solid #cbd5e1;background:#eaf0f8!important;color:#334155!important;padding:13px 14px;font-size:11px;font-weight:900;letter-spacing:.06em;text-align:left;text-transform:uppercase;white-space:nowrap}
    .day-table tbody td{border-bottom:1px solid #e8edf3;background:#fff;color:#334155;padding:13px 14px;font-size:13px;font-weight:600;vertical-align:middle}
    .day-table tbody tr:nth-child(even) td{background:#f8fafc}
    .day-table tbody tr:hover td{background:#eef2ff!important}
    .employee-cell{min-width:210px}.employee-name{color:#0f172a;font-size:14px;font-weight:900}
    .employee-status-note{display:block;margin-top:5px;color:#be123c;font-size:11px;font-weight:800;white-space:normal}
    .day-photo{width:44px;height:44px;border:2px solid #fff;border-radius:12px;object-fit:cover;box-shadow:0 4px 12px rgba(15,23,42,.18)}
    .note-pill{display:inline-flex;align-items:center;gap:5px;max-width:230px;border:1px solid #e2e8f0;border-radius:999px;background:#f8fafc;color:#334155;padding:5px 9px;font-size:11px;font-weight:800;white-space:normal}
    .mini-action{width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:9px;color:#fff!important;text-decoration:none!important;box-shadow:0 5px 12px rgba(15,23,42,.14)}
    .mini-action-approve{background:#059669}.mini-action-reject{background:#e11d48}.mini-action-note{background:#d97706}
    .popup-overlay{position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(2,6,23,.68);backdrop-filter:blur(4px)}
    .popup-overlay.is-open{display:flex}.popup-card{width:100%;max-width:460px;border:1px solid #dbe3ee;border-radius:20px;background:#fff;box-shadow:0 28px 70px rgba(2,6,23,.35)}
    .context-menu{position:absolute;z-index:9998;display:none;min-width:270px;border:1px solid #dbe3ee;border-radius:16px;background:#fff;padding:14px;box-shadow:0 18px 45px rgba(15,23,42,.22)}
    .context-menu.is-open{display:block}.context-action{display:flex;align-items:center;gap:9px;width:100%;border-radius:11px;padding:10px 12px;color:#fff!important;font-size:13px;font-weight:800;text-decoration:none!important}
    .day-table-wrap::-webkit-scrollbar{width:9px;height:9px}.day-table-wrap::-webkit-scrollbar-thumb{border-radius:999px;background:linear-gradient(90deg,#6366f1,#0891b2)}.day-table-wrap::-webkit-scrollbar-track{background:#e2e8f0}
    @media(max-width:640px){.day-attendance-hero{border-radius:18px}.day-button{width:100%}}
</style>
@endpush

@section('content')
<div class="day-attendance-page space-y-6 pb-10">

<section class="day-attendance-hero p-6 sm:p-8">
    <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-bold text-white">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Live Day-wise Attendance
            </div>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Day-wise Attendance</h1>
            <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">
                Selected date पर सभी employees का check-in, check-out, notes, images और distance देखें।
            </p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-4 text-white">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Selected Date</p>
            <p class="mt-1 text-xl font-extrabold">
                {{ \Carbon\Carbon::parse(request('date', $date ?? now()->toDateString()))->format('d F Y') }}
            </p>
        </div>
    </div>
</section>

<section class="day-filter-panel p-5 sm:p-6">
    <form action="{{ route('attendance.day-wise') }}" method="GET">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
            <div class="md:col-span-8">
                <label for="attendance-date" class="day-filter-label">Select Date</label>
                <input type="date" id="attendance-date" name="date"
                       value="{{ request('date', $date ?? now()->toDateString()) }}"
                       class="day-filter-control">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="day-button day-button-primary w-full">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
            <div class="md:col-span-2">
                <a href="{{ route('attendance.day-wise') }}" class="day-button day-button-secondary w-full">
                    <i class="fas fa-rotate-left"></i> Clear
                </a>
            </div>
        </div>
    </form>
</section>

<!-- Attendance Records Section -->
<section class="day-panel overflow-hidden">
    <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Employee Attendance Records</h2>
            <p class="mt-1 text-sm font-medium text-slate-500">Right click on an employee row for leave and half-day actions.</p>
        </div>
        <span class="inline-flex w-max items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-extrabold text-indigo-700">
            <i class="fas fa-users"></i> {{ $employees->total() }} Employees
        </span>
    </div>
    <div class="p-4 sm:p-6">
        <div class="day-table-wrap">
            <table class="day-table">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    @foreach (['Name', 'office', 'Check-In', 'Check-in Image','Check-in Note', 'Check-out Time', 'Check-out Image', 'Check-out Note', 'Working Hours', 'Check-in Distance', 'Check-out Distance', 'Add Note'] as $header)
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                            {{ $header }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    @php
                                        //$record = \App\Models\AttendanceRecord::where('user_id', $employee->id)
                                        //    ->whereDate('created_at', $date)
                                        //    ->first();

                                        $record = \App\Models\AttendanceRecord::where('user_id', $employee->id)
                                        ->where(function ($query) use ($date) {
                                            $query->whereDate('check_in', $date)
                                                  ->orWhereDate('check_out', $date);
                                        })
                                        ->first();

                                        $leave = App\Models\Leave::whereDate('start_date', '<=', $date)
                                            ->whereDate('end_date', '>=', $date)
                                            ->where(['user_id' => $employee->id])
                                            ->first();
                                        $halfDayRecord = App\Models\HalfDay::where('date', $date)->where('user_id', $employee->id)->first();

                                    @endphp
{{--                                    @if ($leave)--}}
{{--                                        <tr class="hover:bg-gray-50">--}}
{{--                                            <td class="px-4 py-4 text-sm text-gray-700">{{ $employee->name }}</td>--}}
{{--                                            <td class="px-4 py-4 text-sm text-gray-700 text-center text-lg" colspan="4">--}}
{{--                                                {{ $leave->leave_type . ' leave' }}</td>--}}
{{--                                            <td class="px-4 py-4 text-sm text-gray-700 text-center text-lg" colspan="4">--}}
{{--                                                {{'Status: '. $leave->status }}</td>--}}
{{--                                        </tr>--}}
{{--                                    @else--}}
                                        <tr oncontextmenu="showModal(event, {{ $employee->id }})">
                                            <td class="employee-cell">
    <span class="employee-name">{{ $employee->name }}</span>
    @if($leave || $halfDayRecord)
        <span class="employee-status-note">
            {{ $leave ? 'Leave: ' . $leave->status : '' }}
            {{ $halfDayRecord ? ' Half Day: ' . $halfDayRecord->status : '' }}
        </span>
    @endif
</td>
                                            <td class="px-4 py-4 text-sm text-gray-700">{{ $employee->office?->name }}</td>
                                            <td
                                                class="px-4 py-4 text-sm text-{{ Carbon\Carbon::parse($record?->check_in)->format('H:i:s') < Carbon\Carbon::parse($employee->check_in_time)->format('H:i:s') ? 'green' : ($record?->late ? 'red' : 'grey') }}-700">
                                                {{ $record?->check_in?->format('h:i:s A') }}
                                            </td>
                                            <td class="px-4 py-4">
                                                @if ($record && $record->check_in_image)
                                                    @php
                                                        $imagePath = ltrim($record->check_in_image, '/');

                                                        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                                                            $checkInImageUrl = $imagePath;
                                                        } else {
                                                            $cleanPath = str_replace('storage/', '', $imagePath);

                                                            $currentFilePath = public_path('storage/' . $cleanPath);

                                                            if (file_exists($currentFilePath)) {
                                                                $checkInImageUrl = asset('storage/' . $cleanPath);
                                                            } else {
                                                                $checkInImageUrl = 'https://attendance.realvictorygroups.com/storage/' . $cleanPath;
                                                            }
                                                        }
                                                    @endphp

                                                    <a href="{{ $checkInImageUrl }}" target="_blank">
                                                        <img src="{{ $checkInImageUrl }}"
                                                            alt="Check-in Image"
                                                            class="day-photo">
                                                    </a>
                                                @endif
                                            </td>

                                            <td
                                                class="px-4 py-4 text-sm text-grey-700">
                                                @if($record?->check_in_note)
                                                    <span title="{{$record?->check_in_note_status}}" class="note-pill" >{{$record?->check_in_note}}
                                                        @if($record->check_in_note && $record->check_in_note_status == 'rejected')
                                                            <i class="fas fa-times text-danger" style="margin-left: 5px;"></i>
                                                        @elseif($record->check_in_note && $record->check_in_note_status == 'approved')
                                                            <i class="fas fa-check text-success" style="margin-left: 5px;"></i>
                                                        @elseif($record->check_in_note && $record->check_in_note_status == 'pending')
                                                            <i class="text-warning" style="margin-left: 5px;">P</i>
                                                        @endif
                                                    </span>
                                                    <div class="mt-2 flex space-x-2">
                                                        @can('approve late message')
                                                            @if($record->check_in_note_status != 'approved')
                                                                <a title="Approve"
                                                                href="{{ route('attendance.user.note.response', ['record' => $record->id, 'type' => 'check_in_note', 'status' => 'approved']) }}"
                                                                class="mini-action mini-action-approve">
                                                                    <i class="fas fa-check-circle"></i>

                                                                </a>
                                                            @endif
                                                        @endcan

                                                        @can('reject late message')
                                                    @if($record->check_in_note_status != 'rejected')
                                                            <a title="Reject"
                                                            href="{{ route('attendance.user.note.response', ['record' => $record->id, 'type' => 'check_in_note', 'status' => 'rejected']) }}"
                                                            class="mini-action mini-action-reject">
                                                                <i class="fas fa-times-circle"></i>

                                                            </a>
                                                        @endif
                                                            @endcan
                                                    </div>
                                                @endif

                                            </td>
                                            <td
                                                class="px-4 py-4 text-sm text-{{ Carbon\Carbon::parse($record?->check_out)->format('H:i:s') > Carbon\Carbon::parse($employee->check_out_time)->format('H:i:s') ? 'green' : 'red' }}-700">
                                                {{ $record?->check_out?->format('h:i:s A') }}
                                            </td>
                                            <td class="px-4 py-4">
                                                @if ($record && $record->check_out_image)
                                                    @php
                                                        $imagePath = ltrim($record->check_out_image, '/');

                                                        // Agar DB me full URL saved hai
                                                        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                                                            $checkOutImageUrl = $imagePath;
                                                        } else {
                                                            // Agar path me storage/ already saved hai to remove kar do
                                                            $cleanPath = str_replace('storage/', '', $imagePath);

                                                            // Current domain/server me image file check karo
                                                            $currentFilePath = public_path('storage/' . $cleanPath);

                                                            if (file_exists($currentFilePath)) {
                                                                // Current domain se image
                                                                $checkOutImageUrl = asset('storage/' . $cleanPath);
                                                            } else {
                                                                // Current me nahi mila to old domain se image
                                                                $checkOutImageUrl = 'https://attendance.realvictorygroups.com/storage/' . $cleanPath;
                                                            }
                                                        }
                                                    @endphp

                                                    <a href="{{ $checkOutImageUrl }}" target="_blank">
                                                        <img src="{{ $checkOutImageUrl }}"
                                                            alt="Check-out Image"
                                                            class="day-photo">
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                            @if($record?->check_out_note)
                                                <span title="{{ $record?->check_out_note_status }}" class="note-pill">
                                                    {{ $record?->check_out_note }}

                                                    @if($record?->check_out_note && $record->check_out_note_status == 'rejected')
                                                        <i class="fas fa-times text-danger" style="margin-left: 5px;"></i>
                                                    @elseif($record?->check_out_note && $record->check_out_note_status == 'approved')
                                                        <i class="fas fa-check text-success" style="margin-left: 5px;"></i>
                                                    @elseif($record?->check_out_note && $record->check_out_note_status == 'pending')
                                                        <i class="text-warning" style="margin-left: 5px;">P</i>
                                                    @endif
                                                </span>

                                                @can('approve before going message|reject before going message')
                                                <div class="mt-2 flex space-x-2">
                                                    @can('approve before going message')
                                                    @if($record->check_out_note_status != 'approved')
                                                        <a title="Approve"
                                                        href="{{ route('attendance.user.note.response', ['record' => $record->id, 'type' => 'check_out_note', 'status' => 'approved']) }}"
                                                        class="mini-action mini-action-approve">
                                                            <i class="fas fa-check-circle"></i>

                                                        </a>
                                                    @endif
                                                    @endcan

                                                    @can('reject before going message')
                                                    @if($record->check_out_note_status != 'rejected')
                                                        <a title="Reject"
                                                        href="{{ route('attendance.user.note.response', ['record' => $record->id, 'type' => 'check_out_note', 'status' => 'rejected']) }}"
                                                        class="mini-action mini-action-reject">
                                                            <i class="fas fa-times-circle"></i>

                                                        </a>
                                                    @endif
                                                        @endcan
                                                </div>
                                                @endcan
                                            @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                {{ $record?->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : 'N/A' }}
                                            </td>
                                            <td
                                                class="px-4 py-4 text-sm text-{{ $record?->check_in_distance > 100 ? 'red' : 'gray' }}-700">
                                                @php
                                                    if ($record?->check_in_latitude && $record->check_in_longitude){
                                                        $check_in_latitude = App\Http\Controllers\HomeController::latitudeInDMS($record->check_in_latitude);
                                                        $check_in_longitude = App\Http\Controllers\HomeController::longitudeInDMS($record->check_in_longitude);
                                                    }

                                                    if ($record?->check_out_latitude && $record->check_out_longitude){
                                                        $check_out_latitude = App\Http\Controllers\HomeController::latitudeInDMS($record->check_out_latitude);
                                                        $check_out_longitude = App\Http\Controllers\HomeController::longitudeInDMS($record->check_out_longitude);
                                                    }

                                                @endphp
                                                @if ($record?->check_in_latitude && $record->check_in_longitude)
                                                <a href="{{'https://www.google.com/maps/place/'.$check_in_latitude.'+'.$check_in_longitude.'/@'.$record->check_in_latitude.','.$record->check_out_longitude.',17z/data=!4m4!3m3!8m2!3d26.5004167!4d80.2878611?authuser=0&entry=ttu&g_ep=EgoyMDI0MTAyMC4xIKXMDSoASAFQAw%3D%3D'}}" target="_blank">
                                                {{ round($record?->check_in_distance) }} m
                                                </a>
                                                @else
                                                {{ round($record?->check_in_distance) }} m
                                                @endif
                                            </td>
                                            <td
                                                class="px-4 py-4 text-sm text-sm text-{{ $record?->check_out_distance > 100 ? 'red' : 'gray' }}">
                                                @if($record?->check_out_latitude && $record->check_out_longitude)
                                                <a href="{{'https://www.google.com/maps/place/'.$check_out_latitude.'+'.$check_out_longitude.'/@'.$record->check_out_latitude.','.$record->check_out_longitude.',17z/data=!4m4!3m3!8m2!3d26.5004167!4d80.2878611?authuser=0&entry=ttu&g_ep=EgoyMDI0MTAyMC4xIKXMDSoASAFQAw%3D%3D'}}" target="_blank">
                                                {{ round($record?->check_out_distance) }} m
                                                </a>
                                                @else

                                                    {{ round($record?->check_out_distance) }} m
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                @if($record)
                                                @can('add note')
                                                <!-- Trigger Button -->
                                                <button type="button" title="Add Note" class="mini-action mini-action-note"
        data-note-url="{{ route('attendance.note', ['record' => $record->id]) }}"
        data-note-text="{{ e($record->note ?? '') }}"
        onclick="openNotePopup(this)">
        <i class="fas fa-note-sticky"></i>
    </button>
                                                @endcan
                                                
                                                @endif
                                            </td>
                                        </tr>
{{--                                    @endif--}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
        </div>
        <div class="mt-5">{{ $employees->appends(request()->query())->links() }}</div>
    </div>
</section>
</div>

<!-- Context Menu -->
<div id="customModal" class="context-menu">
    <button type="button" onclick="closeModal()" class="absolute right-2 top-1 text-xl font-bold text-slate-400 hover:text-rose-600">&times;</button>
    <h2 class="mb-3 pr-6 text-sm font-extrabold text-slate-900">Quick Actions</h2>
    <div class="space-y-2">
        <a id="leaveLink" href="#" class="context-action bg-gradient-to-r from-rose-500 to-rose-600">
            <i class="fas fa-calendar-minus"></i> Request For Leave
        </a>
        <a id="halfDayLink" href="#" class="context-action bg-gradient-to-r from-amber-500 to-orange-500">
            <i class="fas fa-clock"></i> Request For Half Day
        </a>
    </div>
</div>

<div id="notePopup" class="popup-overlay" aria-hidden="true">
    <div class="popup-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">Add Attendance Note</h3>
                <p class="mt-1 text-xs font-medium text-slate-500">Write or update the note for this attendance record.</p>
            </div>
            <button type="button" onclick="closeNotePopup()" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-rose-50 hover:text-rose-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="notePopupForm" method="POST" action="">
            @csrf
            <div class="p-5">
                <label for="attendance-note-text" class="day-filter-label">Note</label>
                <textarea id="attendance-note-text" rows="5" name="note" class="day-filter-control min-h-[130px]" placeholder="Write your note here..."></textarea>
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeNotePopup()" class="day-button day-button-secondary">Close</button>
                @role('super_admin|admin')
                    <button type="submit" class="day-button bg-amber-500 text-white hover:bg-amber-600">
                        <i class="fas fa-save"></i> Save Note
                    </button>
                @endrole
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openNotePopup(button) {
        const popup = document.getElementById('notePopup');
        const form = document.getElementById('notePopupForm');
        const textarea = document.getElementById('attendance-note-text');

        form.action = button.dataset.noteUrl || '';
        textarea.value = button.dataset.noteText || '';
        popup.classList.add('is-open');
        popup.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        setTimeout(() => textarea.focus(), 100);
    }

    function closeNotePopup() {
        const popup = document.getElementById('notePopup');
        popup.classList.remove('is-open');
        popup.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function showModal(event, employeeId) {
        event.preventDefault();
        const modal = document.getElementById('customModal');
        document.getElementById('leaveLink').href = `/leave/create/${employeeId}`;
        document.getElementById('halfDayLink').href = `/half-day/create/${employeeId}`;

        const menuWidth = 280;
        const viewportRight = window.scrollX + window.innerWidth;
        const left = Math.min(event.pageX, viewportRight - menuWidth - 16);

        modal.style.left = `${Math.max(12, left)}px`;
        modal.style.top = `${event.pageY}px`;
        modal.classList.add('is-open');
    }

    function closeModal() {
        document.getElementById('customModal').classList.remove('is-open');
    }

    document.addEventListener('click', function (event) {
        const contextMenu = document.getElementById('customModal');
        const notePopup = document.getElementById('notePopup');

        if (contextMenu && !contextMenu.contains(event.target)) closeModal();
        if (notePopup && event.target === notePopup) closeNotePopup();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
            closeNotePopup();
        }
    });
</script>
@endpush

@endsection