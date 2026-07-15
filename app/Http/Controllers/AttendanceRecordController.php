<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


class AttendanceRecordController extends Controller
{


private function activeOfficeId(Request $request): ?int
{
    $user = $request->user();

    if (!$user) {
        return null;
    }

    /*
     * Sabse pehle session me selected office dekhenge.
     */
    $sessionOfficeId = $request->session()->get('active_office_id');

    if ($sessionOfficeId && (int) $sessionOfficeId > 0) {
        return (int) $sessionOfficeId;
    }

    /*
     * Normal admin/employee ka assigned office.
     */
    if ($user->office_id && (int) $user->office_id > 0) {
        return (int) $user->office_id;
    }

    /*
     * Owner ke paas users.office_id nahi ho sakta.
     * Isliye uska first owned office default select hoga.
     */
    if ($user->hasRole('owner')) {
        return Office::query()
            ->where('owner_id', $user->id)
            ->orderBy('id')
            ->value('id');
    }

    /*
     * Super admin ke liye first office fallback.
     */
    if ($user->hasRole('super_admin')) {
        return Office::query()
            ->orderBy('id')
            ->value('id');
    }

    return null;
}

private function allowedOfficeIds(Request $request): array
{
    $user = $request->user();

    if (!$user) {
        return [];
    }

    /*
     * Super admin kisi bhi office ko access kar sakta hai.
     */
    if ($user->hasRole('super_admin')) {
        return Office::query()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    /*
     * Owner sirf apne offices access kar sakta hai.
     */
    if ($user->hasRole('owner')) {
        return Office::query()
            ->where('owner_id', $user->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    /*
     * Office switch permission wale user owner ke offices access kar sakte hain.
     */
    if (
        $user->can('switch offices') ||
        $user->can('switch office')
    ) {
        $currentOffice = $user->office;

        if ($currentOffice && $currentOffice->owner_id) {
            return Office::query()
                ->where('owner_id', $currentOffice->owner_id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }
    }

    /*
     * Normal admin/employee sirf assigned office.
     */
    return $user->office_id
        ? [(int) $user->office_id]
        : [];
}

private function selectedOfficeId(Request $request): ?int
{
    $activeOfficeId = $this->activeOfficeId($request);
    $allowedOfficeIds = $this->allowedOfficeIds($request);

    if (
        $activeOfficeId &&
        in_array((int) $activeOfficeId, $allowedOfficeIds, true)
    ) {
        return (int) $activeOfficeId;
    }

    /*
     * Session office invalid ho to allowed offices ka first office.
     */
    return !empty($allowedOfficeIds)
        ? (int) $allowedOfficeIds[0]
        : null;
}

private function officeEmployeeIds(Request $request): array
{
    $officeId = $this->selectedOfficeId($request);

    if (!$officeId) {
        return [];
    }

    return User::query()
        ->where('office_id', $officeId)
        ->where('status', '1')
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->toArray();
}


    // public function index(Request $request)
    // {
    //     if ($request->month) {
    //         $month = $request->month;
    //         $startOfMonth = Carbon::parse($request->month . '-01')->startOfMonth();
    //         $endOfMonth = Carbon::parse($request->month . '-01')->endOfMonth();
    //     } else {
    //         $month = Carbon::now()->format('Y-m');
    //         $startOfMonth = Carbon::now()->startOfMonth();
    //         $endOfMonth = Carbon::now()->endOfMonth();
    //     }

    //     $officeId = $this->activeOfficeId($request);
    //     $employeeIds = $this->officeEmployeeIds($request);

    //     if (empty($employeeIds)) {
    //         $dates = collect();
    //         $attendanceRecords = collect();
    //         $users = collect();
    //         $user = null;

    //         return view('dashboard.attendance.index', compact('dates', 'attendanceRecords', 'users', 'user', 'month'));
    //     }

    //     $user = null;

    //     if ($request->employee) {
    //         $user = User::where('office_id', $officeId)
    //             ->where('id', $request->employee)
    //             ->first();
    //     }

    //     // normal employee only self
    //     if (!$request->user()->hasRole('super_admin') && !$request->user()->hasRole('owner') && !$request->user()->hasRole('admin')) {
    //         $user = $request->user();
    //     }

    //     $dates = new Collection();
    //     for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
    //         $dates->push((object)[
    //             'date' => $date->copy(),
    //         ]);
    //     }

    //     $attendanceRecords = AttendanceRecord::query()
    //         ->whereBetween('check_in', [
    //             $startOfMonth->copy()->startOfDay(),
    //             $endOfMonth->copy()->endOfDay()
    //         ])
    //         ->whereIn('user_id', $user ? [$user->id] : $employeeIds)
    //         ->orderBy('check_in')
    //         ->get();

    //     $users = User::where('office_id', $officeId)
    //         ->where('status', '1')
    //         ->orderBy('name')
    //         ->get();

    //     return view('dashboard.attendance.index', compact('dates', 'attendanceRecords', 'users', 'user', 'month'));
    // }


public function index(Request $request)
{
    $loggedInUser = $request->user();

    /*
    |--------------------------------------------------------------------------
    | Selected month
    |--------------------------------------------------------------------------
    */
    $month = $request->filled('month')
        ? $request->month
        : now()->format('Y-m');

    try {
        $selectedMonth = Carbon::createFromFormat('Y-m', $month);
    } catch (\Throwable $e) {
        $month = now()->format('Y-m');
        $selectedMonth = Carbon::createFromFormat('Y-m', $month);
    }

    $startOfMonth = $selectedMonth->copy()->startOfMonth();
    $endOfMonth = $selectedMonth->copy()->endOfMonth();

    /*
    |--------------------------------------------------------------------------
    | Selected office
    |--------------------------------------------------------------------------
    */
    $officeId = $this->selectedOfficeId($request);

    /*
    |--------------------------------------------------------------------------
    | Role check
    |--------------------------------------------------------------------------
    */
    $isManagement = $loggedInUser->hasAnyRole([
        'super_admin',
        'owner',
        'admin',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Employee dropdown
    |--------------------------------------------------------------------------
    */
    if ($isManagement) {
        if ($officeId) {
            $users = User::query()
                ->where('office_id', $officeId)
                ->where('status', '1')
                ->orderBy('name')
                ->get();
        } else {
            $users = collect();
        }
    } else {
        $users = collect([$loggedInUser]);
    }

    /*
    |--------------------------------------------------------------------------
    | Selected employee
    |--------------------------------------------------------------------------
    */
    $user = null;

    if ($isManagement) {
        if ($request->filled('employee')) {
            /*
             * Employee dropdown me maujood employee hi select ho sakega.
             */
            $user = $users->first(function ($employee) use ($request) {
                return (int) $employee->id === (int) $request->employee;
            });
        }

        /*
         * Employee select nahi kiya to first active employee.
         */
        if (!$user) {
            $user = $users->first();
        }
    } else {
        $user = $loggedInUser;
    }

    /*
    |--------------------------------------------------------------------------
    | Month dates
    |--------------------------------------------------------------------------
    */
    $dates = collect();

    for (
        $date = $startOfMonth->copy();
        $date->lte($endOfMonth);
        $date->addDay()
    ) {
        $dates->push((object) [
            'date' => $date->copy(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance records
    |--------------------------------------------------------------------------
    */
    $attendanceRecords = collect();

    if ($user) {
        $attendanceRecords = AttendanceRecord::query()
            ->with([
                'user',
                'checkInBy',
                'checkOutBy',
                'breaks',
            ])
            ->where('user_id', $user->id)
            ->whereBetween('check_in', [
                $startOfMonth->copy()->startOfDay(),
                $endOfMonth->copy()->endOfDay(),
            ])
            ->orderBy('check_in')
            ->get();
    }

    return view('dashboard.attendance.index', compact(
        'dates',
        'attendanceRecords',
        'users',
        'user',
        'month'
    ));
}



    public function checkIn(Request $request, User $user = null) {
        $request->validate([
            'image' => '',
            'latitude' => '',
            'longitude' => '',
            'distance' => '',
        ]);

        if ($user == null){
            $user = auth()->user();
        }
        if ($user->status == '0' || $user->office->status == 'inactive'){
            return back()->with('error', 'You are inactive user');
        }
        if ($user->location_required === 'yes' && (!$request->latitude || !$request->longitude)) {
            return view('dashboard.settingInstruction');
        }

        // new check
        $activeOfficeId = $request->user()->activeOfficeId();

        if ($activeOfficeId && (int) $user->office_id !== (int) $activeOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }


        // Fetch today's attendance record
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())
            ->where('user_id', $user->id)
            ->first();
        if ($record === null) {
            // Create a new attendance record
            $attendanceRecord = AttendanceRecord::create([
                'user_id' => $user->id,
                'check_in' => Carbon::now(),
                'duration' => $user->office_time / 2, // Set initial duration
                'check_in_distance' => $request->distance ?? null,
                'day_type' => '__',
                'check_in_note' => $request->note ?? null,
                'check_in_latitude' => $request->latitude ?? null,
                'check_in_longitude' => $request->longitude ?? null,
                'check_in_by' => auth()->user()->id,
            ]);
            // Check if the user is late
            if (now()->format('H:i') > $user->check_in_time->addMinutes(5)->format('H:i')) {
                $attendanceRecord->late = Carbon::now()->diffInMinutes(Carbon::parse($user->check_in_time));
            }
            // Handle image upload
            if ($request->hasFile('image')) {
                try {
                    $file = $request->file('image')->store('public/images');
                    $attendanceRecord->check_in_image = str_replace('public/', '', $file);
                    $attendanceRecord->save();
                } catch (\Exception $e) {
                    Log::error('Image upload failed: ' . $e->getMessage());
                    return back()->with('error', 'Failed to upload image. Please try again.');
                }
            }
            // Save the attendance record
            $attendanceRecord->save();
            $message = 'Checked in successfully';
            $messageType = 'success';


            if ($attendanceRecord->late) {

                $type = 'check_in_note';
                $time = HomeController::getTime($attendanceRecord->late);

                $message = "You are {$time} late.\nPlease share the reason for late check-in.";

                // 🔥 Sirf message ko session me bhejo
                session()->flash('message', $message);

                return redirect()->route('attendance.reason.form', [
                    'type' => $type,
                    'record' => $attendanceRecord
                ]);
            }




        }else{
            $message = 'Today you has checked in already';
            $messageType = 'error';
        }
        // Redirect to attendance index with success message
        $message1 = '';
        if ($user->office->under_radius_required == '1') {
            if ($request->distance > $user->office->radius) {
                if ($request->distance >= 1000) {
                    $distance = round($request->distance / 1000, 2) . ' km';
                } else {
                    $distance = round($request->distance) . ' m';
                }

                $required = $user->office->radius >= 1000
                    ? ($user->office->radius / 1000) . ' km'
                    : $user->office->radius . ' m';

                $message1 = "You are {$distance} away from the office, You should be under {$required}";
                // return back()->with('error', $message1);
            }
        }
        return redirect('attendance/day-wise')->with([$messageType => $message , 'error' => $message1]);
    }

    public function checkOut(Request $request, User $user = null){
        $request->validate([
            'image' => '',
            'latitude' => '',
            'longitude' => '',
            'distance' => '',
        ]);
        if ($user == null){
            $user = auth()->user();
        }
        if ($user->status == '0' || $user->office->status == 'inactive'){
            return back()->with('error', 'You are inactive user');
        }
        if ($user->location_required === 'yes' && (!$request->latitude || !$request->longitude)) {
            return view('dashboard.settingInstruction');
        }


        //  new checks
        $activeOfficeId = $request->user()->activeOfficeId();

        if ($activeOfficeId && (int) $user->office_id !== (int) $activeOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }



        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $user->id)->first();
        if ($record){
            $duration = Carbon::now()->diffInMinutes($record->check_in);
            $record->update(['check_out' => Carbon::now(), 'duration' => $duration, 'check_out_distance' => $request->distance, 'day_type' => '__', 'check_out_latitude' => $request->latitude, 'check_out_longitude' => $request->longitude, 'check_out_by' => auth()->user()->id]);
        }else{
            //  $duration = $user->office_time / 2;
            //  $record = AttendanceRecord::create(['user_id' => $user->id, 'check_out' => Carbon::now(), 'duration' => $duration , 'check_out_distance' => $request->distance, 'check_out_latitude' => $request->latitude, 'check_out_longitude' => $request->logitude, 'check_out_by' => auth()->user()->id]);
            return back()->with('error', 'Error, You can\'t check-out without check-in');
        }
        if ($request->file('image')){
            $file = $request->file('image')->store('public/images');
            $record->check_out_image = str_replace('public/', '', $file);
            $record->save();
        }
//        if (!$request->latitude || !$request->longitude){
//            return view('dashboard.settingInstruction');
//        }

        if (Carbon::parse($record->check_out)->format('H:i:s') < Carbon::parse($user->check_out_time)->format('H:i:s') ){
            $checkOutTime = Carbon::parse($record->check_out)->format('H:i:s');
            $userCheckOutTime = Carbon::parse($user->check_out_time);
            $time = Carbon::createFromFormat('H:i:s', $checkOutTime)->diffInMinutes($userCheckOutTime);
            $before = HomeController::getTime($time);
            $message = "You are checking out {$before} earlier than your scheduled time.\nPlease share the reason for early check-out.";
            session()->flash('message', $message);
            $type = 'check_out_note';
            return redirect()->route('attendance.reason.form', ['type' => $type, 'record' => $record]);
        }



        $diffMinutes = Carbon::parse($record->check_in)->diffInMinutes($record->check_out);

        // Required working minutes: based on user’s defined shift timing
        $requiredMinutes = Carbon::parse($user->check_in_time)
            ->diffInMinutes(Carbon::parse($user->check_out_time));

        // Remaining minutes (to complete required time)
        $remainingMinutes = max(0, $requiredMinutes - $diffMinutes);

        // Format check-in time for display
        $checkInTime = Carbon::parse($record->check_in)->format('h:i A');

        // If user is checking out before completing required working time
        if ($diffMinutes < $requiredMinutes) {

            // Convert required minutes to hours/min (for message)
            $reqHrs  = floor($requiredMinutes / 60);
            $reqMins = $requiredMinutes % 60;

            $message = "You are checking out before completing {$reqHrs} hours {$reqMins} minutes.\n"
                . "Check-in Time: {$checkInTime}\n"
                . "Completed: " . floor($diffMinutes / 60) . " hours " . ($diffMinutes % 60) . " minutes\n"
                . "Remaining: " . floor($remainingMinutes / 60) . " hours " . ($remainingMinutes % 60) . " minutes\n"
                . "Please provide the reason for early check-out.";

            session()->flash('message', $message);

            $type = 'check_out_note';

            return redirect()->route('attendance.reason.form', [
                'type' => $type,
                'record' => $record
            ]);
        }




        $message1 = '';
        if ($user->office->under_radius_required == '1') {
            if ($request->distance > $user->office->radius) {
                if ($request->distance >= 1000) {
                    $distance = round($request->distance / 1000, 2) . ' km';
                } else {
                    $distance = round($request->distance) . ' m';
                }

                $required = $user->office->radius >= 1000
                    ? ($user->office->radius / 1000) . ' km'
                    : $user->office->radius . ' m';

                $message1 = "You are {$distance} away from the office, You should be under {$required}";
                // return back()->with('error', $message1);
            }
        }

        return redirect('attendance/day-wise')->with(['success' => 'checked out successfully', 'error' => $message1]);
    }

    public function form($formType, User $user = null){

//        if (auth()->user()->is_accepted == '0'){
//            return back()->with('error', 'You don\'t have accepted policy, Please read policies!');
//        }
        return view('dashboard.attendance.form', compact('formType', 'user'));
    }


    // public function dayWise(Request $request)
    // {
    //     $date = $request->date ?? today();

    //     // office-wise + hierarchical employee list
    //     $employees = HomeController::employeeList();

    //     // collection ensure
    //     $employees = collect($employees);

    //     // sirf selected office ke employees ke ids
    //     $employeeIds = $employees->pluck('id')->toArray();

    //     // selected date ki attendance
    //     $attendances = AttendanceRecord::whereDate('created_at', $date)
    //         ->whereIn('user_id', $employeeIds)
    //         ->get()
    //         ->keyBy('user_id');

    //     // attendance flag attach
    //     $employees = $employees->map(function ($employee) use ($attendances) {
    //         $employee->has_attendance = $attendances->has($employee->id);
    //         return $employee;
    //     });

    //     // status filter
    //     if ($request->filled('status')) {
    //         $employees = $employees->where('status', $request->status)->values();
    //     }

    //     // attendance wale upar, lekin hierarchy bhi zyada na toote
    //     $employees = $employees->sortByDesc(function ($employee) {
    //         return $employee->has_attendance;
    //     })->values();

    //     // paginate
    //     $perPage = 20;
    //     $currentPage = Paginator::resolveCurrentPage();

    //     $paginatedEmployees = new LengthAwarePaginator(
    //         $employees->forPage($currentPage, $perPage)->values(),
    //         $employees->count(),
    //         $perPage,
    //         $currentPage,
    //         ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
    //     );

    //     return view('dashboard.attendance.dayWise', [
    //         'employees' => $paginatedEmployees,
    //         'date' => $date,
    //     ]);
    // }

    public function dayWise(Request $request)
    {
        $date = $request->date ?? today();

        // office-wise + hierarchical employee list
        $employees = HomeController::employeeList();

        // collection ensure + inactive employees remove
        $employees = collect($employees)
            ->filter(function ($employee) {
                return isset($employee->status) && $employee->status === '1';
            })
            ->values();

        // sirf active employees ke ids
        $employeeIds = $employees->pluck('id')->toArray();

        // selected date ki attendance
        $attendances = AttendanceRecord::whereDate('created_at', $date)
            ->whereIn('user_id', $employeeIds)
            ->get()
            ->keyBy('user_id');

        // attendance flag attach
        $employees = $employees->map(function ($employee) use ($attendances) {
            $employee->has_attendance = $attendances->has($employee->id);
            return $employee;
        });

        // status filter
        if ($request->filled('status')) {
            $employees = $employees->where('status', $request->status)->values();
        }

        // attendance wale upar
        $employees = $employees->sortByDesc(function ($employee) {
            return $employee->has_attendance;
        })->values();

        // paginate
        $perPage = 20;
        $currentPage = Paginator::resolveCurrentPage();

        $paginatedEmployees = new LengthAwarePaginator(
            $employees->forPage($currentPage, $perPage)->values(),
            $employees->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => request()->query()
            ]
        );

        return view('dashboard.attendance.dayWise', [
            'employees' => $paginatedEmployees,
            'date' => $date,
        ]);
    }


    public function addNote(Request $request, AttendanceRecord $record){
        $request->validate([
            'note' => 'required',
        ]);
        $record->note = $request->note;
        $record->noted_by = auth()->user()->id;
        $record->save();
        return back()->with('success', 'Note Added successfully');
    }

    public function userNote(Request $request,AttendanceRecord $record, $type){
        $request->validate([
            'note' => 'required|min:6',
        ]);
        if ($type == 'check_in_note'){
            $record->check_in_note = $request->note;
        }else{
            $record->check_out_note = $request->note;
        }
        $record->save();
        return redirect('attendance/day-wise');
    }

    public function userNoteResponse(AttendanceRecord $record, $type, $status){
        if ($type == 'check_in_note'){
            $record->check_in_note_status = $status;
            $record->check_in_note_response_by = auth()->user()->id;
        }else{
            $record->check_out_note_status = $status;
            $record->check_out_note_response_by = auth()->user()->id;
        }
        $record->save();
        return back();
    }

    public function reasonFormLoad($type, AttendanceRecord $record){
        return view('dashboard.attendance.noteForm', compact('type',  'record'));
    }

    // public function manualEntryForm(){
    //     $employees = HomeController::employeeList();
    //     return view('dashboard.attendance.manualEntryForm', compact('employees'));
    // }

    public function manualEntryForm(Request $request)
    {
        $officeId = $this->activeOfficeId($request);

        $employees = User::where('office_id', $officeId)
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        return view('dashboard.attendance.manualEntryForm', compact('employees'));
    }

    // public function store(Request $request){
    //     $request->validate([
    //         'employee_id' => 'required',
    //         'date' => 'required',
    //         'check_in' => 'required',
    //         'check_out' => 'required',
    //     ]);


    //     AttendanceRecord::create([
    //         'user_id' => $request->employee_id,
    //         'check_in' => $request->date.' '.$request->check_in,
    //         'check_out' => $request->date.' '.$request->check_out,
    //     ]);

    //     return back()->with('success', 'Attendance record created successfully');
    // }


    public function store(Request $request)
    {
        $officeId = $this->activeOfficeId($request);

        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'required',
            'check_out' => 'required',
        ]);

        $employee = User::where('office_id', $officeId)
            ->where('id', $request->employee_id)
            ->firstOrFail();

        AttendanceRecord::create([
            'user_id' => $employee->id,
            'check_in' => $request->date . ' ' . $request->check_in,
            'check_out' => $request->date . ' ' . $request->check_out,
        ]);

        return back()->with('success', 'Attendance record created successfully');
    }
}
