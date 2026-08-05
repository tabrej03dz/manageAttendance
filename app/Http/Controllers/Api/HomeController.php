<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public static function employeeList($user)
    {

        if ($user->hasRole('super_admin|admin')) {
            if ($user->hasRole('super_admin')) {
                $employees = User::all();
            } else {
                $office = $user->office;
                $employees = $office->users;
            }
        } elseif ($user->hasRole('owner')) {
            $officeIds = Office::where('owner_id', $user->id)->pluck('id');
            $employees = User::whereIn('office_id', $officeIds)->get();
        } else {
            if ($user->hasRole('team_leader')) {
                $employees = $user->members;
                $record = User::find($user->id);
                $employees->push($record);
            } else {
                $employees = User::where('id', $user->id)->get();
            }
        }

        return $employees;
    }



    // public static function getTime($totalMinutes){
    //     $hours = (int)($totalMinutes/60);
    //     $minutes = $totalMinutes % 60;

    //     return "$hours h, $minutes m";
    // }

    public static function getTime($totalMinutes): string
{
    $totalMinutes = max(0, (int) $totalMinutes);

    $hours = intdiv($totalMinutes, 60);
    $minutes = $totalMinutes % 60;

    return "{$hours} h, {$minutes} m";
}

    // public function dashboard(Request $request)
    // {
    // //        $user = $request->user();
    //     $user = User::find(1);
    //     // Fetch employees
    //     $employees = User::all();

    //     // Determine offices based on user role
    //     if ($user->hasRole('owner')) {
    //         $offices = $user->offices;
    //     } else {
    //         $offices = Office::all();
    //     }

    //     // Check today's attendance record
    //     $todayAttendanceRecord = AttendanceRecord::where('user_id', $user->id)
    //         ->whereDate('created_at', Carbon::today())
    //         ->first();
    //     // Get the latest lunch break for today's attendance record
    //     if ($todayAttendanceRecord) {
    //         $break = LunchBreak::where('attendance_record_id', $todayAttendanceRecord->id)
    //             ->orderBy('created_at', 'desc')
    //             ->first();
    //     } else {
    //         $break = null;
    //     }
    //     // Fetch current month's data
    //     $data = DashboardController::currentMonth(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), $user);

    //     // Return response as JSON
    //     return response()->json([
    //         'offices' => $offices->count(),
    //         'employees' => $employees->count(),
    //         'todayAttendanceRecord' => $todayAttendanceRecord,
    //         'break' => $break,
    //         'data' => $data,
    //     ]);
    // }


// public function dashboard(Request $request)
// {
//     $user = $request->user();

//     if (!$user) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Unauthenticated user.',
//         ], 401);
//     }

//     try {
//         /*
//         |--------------------------------------------------------------------------
//         | Employee list
//         |--------------------------------------------------------------------------
//         */

//         $employees = self::employeeList($user);

//         /*
//         |--------------------------------------------------------------------------
//         | Office list according to role
//         |--------------------------------------------------------------------------
//         */

//         if ($user->hasRole('super_admin')) {
//             $offices = Office::query()->get();
//         } elseif ($user->hasRole('owner')) {
//             $offices = Office::query()
//                 ->where('owner_id', $user->id)
//                 ->get();
//         } elseif ($user->office_id) {
//             $offices = Office::query()
//                 ->where('id', $user->office_id)
//                 ->get();
//         } else {
//             $offices = collect();
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Today's attendance
//         |--------------------------------------------------------------------------
//         */

//         $todayAttendanceRecord = AttendanceRecord::query()
//             ->where('user_id', $user->id)
//             ->whereDate('created_at', Carbon::today())
//             ->latest('id')
//             ->first();

//         /*
//         |--------------------------------------------------------------------------
//         | Latest break of today's attendance
//         |--------------------------------------------------------------------------
//         */

//         $break = null;

//         if ($todayAttendanceRecord) {
//             $break = LunchBreak::query()
//                 ->where(
//                     'attendance_record_id',
//                     $todayAttendanceRecord->id
//                 )
//                 ->latest('id')
//                 ->first();
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Current month dashboard data
//         |--------------------------------------------------------------------------
//         */

//         $startDate = Carbon::now()->startOfMonth();
//         $endDate = Carbon::now()->endOfMonth();

//         $dashboardController = app(DashboardController::class);

//         $data = $dashboardController->currentMonth(
//             $startDate,
//             $endDate,
//             $user
//         );

//         /*
//         |--------------------------------------------------------------------------
//         | If currentMonth returns JsonResponse
//         |--------------------------------------------------------------------------
//         */

//         if ($data instanceof \Illuminate\Http\JsonResponse) {
//             $data = $data->getData(true);
//         }

//         return response()->json([
//             'status'                => true,
//             'message'               => 'Dashboard data fetched successfully.',
//             'offices'               => $offices->count(),
//             'employees'             => $employees->count(),
//             'todayAttendanceRecord' => $todayAttendanceRecord,
//             'break'                 => $break,
//             'data'                  => $data,
//         ], 200);
//     } catch (\Throwable $exception) {
//         \Log::error('API dashboard error', [
//             'user_id' => $user->id,
//             'message' => $exception->getMessage(),
//             'file'    => $exception->getFile(),
//             'line'    => $exception->getLine(),
//             'trace'   => $exception->getTraceAsString(),
//         ]);

//         return response()->json([
//             'status'  => false,
//             'message' => 'Dashboard data could not be loaded.',
//             'error'   => config('app.debug')
//                 ? $exception->getMessage()
//                 : null,
//             'line'    => config('app.debug')
//                 ? $exception->getLine()
//                 : null,
//         ], 500);
//     }
// }



public function dashboard(Request $request)
{
    $user = $request->user();

    if (! $user) {
        return response()->json([
            'status'  => false,
            'message' => 'Unauthenticated user.',
        ], 401);
    }

    try {
        /*
        |--------------------------------------------------------------------------
        | Today
        |--------------------------------------------------------------------------
        */

        $today = now();

        /*
        |--------------------------------------------------------------------------
        | Employee list
        |--------------------------------------------------------------------------
        */

        $employees = self::employeeList($user);

        /*
        |--------------------------------------------------------------------------
        | Office list according to role
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('super_admin')) {
            $offices = Office::query()
                ->orderBy('name')
                ->get();
        } elseif ($user->hasRole('owner')) {
            $offices = Office::query()
                ->where('owner_id', $user->id)
                ->orderBy('name')
                ->get();
        } elseif ($user->office_id) {
            $offices = Office::query()
                ->where('id', $user->office_id)
                ->get();
        } else {
            $offices = collect();
        }

        /*
        |--------------------------------------------------------------------------
        | Birthday visible office IDs
        |--------------------------------------------------------------------------
        |
        | Normal employee:
        | - Apne office ke birthdays dekhega.
        |
        | Special offices:
        | - Real Victory Groups
        | - Rvg Development
        |
        | In dono offices ke employees ek dusre office ke birthdays bhi dekhenge.
        |
        */

        $birthdayOfficeIds = collect();

        if ($user->hasRole('super_admin')) {
            $birthdayOfficeIds = Office::query()
                ->pluck('id');
        } elseif ($user->hasRole('owner')) {
            $birthdayOfficeIds = Office::query()
                ->where('owner_id', $user->id)
                ->pluck('id');
        } elseif ($user->office_id) {
            $currentOffice = Office::query()
                ->select(['id', 'name'])
                ->find($user->office_id);

            if ($currentOffice) {
                $currentOfficeName = mb_strtolower(
                    trim($currentOffice->name)
                );

                $specialOfficeNames = [
                    'real victory groups',
                    'rvg development',
                ];

                if (in_array(
                    $currentOfficeName,
                    $specialOfficeNames,
                    true
                )) {
                    $birthdayOfficeIds = Office::query()
                        ->where(function ($query) {
                            $query
                                ->whereRaw(
                                    'LOWER(TRIM(name)) = ?',
                                    ['real victory groups']
                                )
                                ->orWhereRaw(
                                    'LOWER(TRIM(name)) = ?',
                                    ['rvg development']
                                );
                        })
                        ->pluck('id');
                } else {
                    $birthdayOfficeIds = collect([
                        (int) $currentOffice->id,
                    ]);
                }
            }
        }

        $birthdayOfficeIds = $birthdayOfficeIds
            ->map(fn ($officeId) => (int) $officeId)
            ->filter(fn ($officeId) => $officeId > 0)
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Today's birthday employees
        |--------------------------------------------------------------------------
        */

        $todayBirthdayEmployees = $birthdayOfficeIds->isEmpty()
            ? collect()
            : User::query()
                ->with([
                    'office:id,name',
                    'department:id,name',
                ])
                ->whereIn(
                    'office_id',
                    $birthdayOfficeIds->all()
                )
                ->where('status', '1')
                ->whereNotNull('dob')
                ->whereMonth('dob', $today->month)
                ->whereDay('dob', $today->day)
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'email',
                    'phone',
                    'photo',
                    'dob',
                    'designation',
                    'office_id',
                    'department_id',
                ])
                ->map(function ($birthdayEmployee) use ($today) {
                    $birthdayAge = null;

                    try {
                        $dob = Carbon::parse(
                            $birthdayEmployee->dob
                        );

                        $birthdayAge = $dob->diffInYears($today);
                    } catch (\Throwable $exception) {
                        $birthdayAge = null;
                    }

                    return [
                        'id' => (int) $birthdayEmployee->id,

                        'name' => $birthdayEmployee->name,

                        'email' => $birthdayEmployee->email,

                        'phone' => $birthdayEmployee->phone,

                        'photo' => $birthdayEmployee->photo,

                        'dob' => $birthdayEmployee->dob
                            ? Carbon::parse(
                                $birthdayEmployee->dob
                            )->toDateString()
                            : null,

                        'birthday_age' => $birthdayAge,

                        'designation' => $birthdayEmployee->designation,

                        'department' => optional(
                            $birthdayEmployee->department
                        )->name,

                        'office_id' => $birthdayEmployee->office_id
                            ? (int) $birthdayEmployee->office_id
                            : null,

                        'office_name' => optional(
                            $birthdayEmployee->office
                        )->name,

                        'is_logged_in_user' =>
                            (int) $birthdayEmployee->id ===
                            (int) $user->id,
                    ];
                })
                ->values();

        /*
        |--------------------------------------------------------------------------
        | Logged-in user's birthday status
        |--------------------------------------------------------------------------
        */

        $isUserBirthdayToday = $todayBirthdayEmployees
            ->contains(function ($birthdayEmployee) use ($user) {
                return (int) $birthdayEmployee['id'] ===
                    (int) $user->id;
            });

        /*
        |--------------------------------------------------------------------------
        | Other birthday employees
        |--------------------------------------------------------------------------
        |
        | Logged-in user ko remove karke sirf dusre birthday employees.
        |
        */

        $otherBirthdayEmployees = $todayBirthdayEmployees
            ->reject(function ($birthdayEmployee) use ($user) {
                return (int) $birthdayEmployee['id'] ===
                    (int) $user->id;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Today's attendance
        |--------------------------------------------------------------------------
        */

        $todayAttendanceRecord = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query
                    ->whereDate(
                        'check_in',
                        $today->toDateString()
                    )
                    ->orWhere(function ($subQuery) use ($today) {
                        $subQuery
                            ->whereNull('check_in')
                            ->whereDate(
                                'created_at',
                                $today->toDateString()
                            );
                    });
            })
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Latest break of today's attendance
        |--------------------------------------------------------------------------
        */

        $break = null;

        if ($todayAttendanceRecord) {
            $break = LunchBreak::query()
                ->where(
                    'attendance_record_id',
                    $todayAttendanceRecord->id
                )
                ->latest('id')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Current month dashboard data
        |--------------------------------------------------------------------------
        */

        $startDate = $today
            ->copy()
            ->startOfMonth()
            ->startOfDay();

        /*
         * Current month ke future days ko include nahi karna hai.
         * Isliye end date aaj tak rakhi gayi hai.
         */
        $endDate = $today
            ->copy()
            ->endOfDay();

        $dashboardController = app(
            DashboardController::class
        );

        $data = $dashboardController->currentMonth(
            $startDate,
            $endDate,
            $user
        );

        /*
        |--------------------------------------------------------------------------
        | If currentMonth returns JsonResponse
        |--------------------------------------------------------------------------
        */

        if ($data instanceof \Illuminate\Http\JsonResponse) {
            $data = $data->getData(true);
        }

        /*
        |--------------------------------------------------------------------------
        | Final response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'status'  => true,
            'message' => 'Dashboard data fetched successfully.',

            'offices' => $offices->count(),

            'employees' => $employees->count(),

            'todayAttendanceRecord' => $todayAttendanceRecord,

            'break' => $break,

            'data' => $data,

            /*
            |--------------------------------------------------------------------------
            | Birthday response
            |--------------------------------------------------------------------------
            */

            'birthday' => [
                'hasBirthdayToday' =>
                    $todayBirthdayEmployees->isNotEmpty(),

                'isUserBirthdayToday' =>
                    $isUserBirthdayToday,

                'todayBirthdayCount' =>
                    $todayBirthdayEmployees->count(),

                'otherBirthdayCount' =>
                    $otherBirthdayEmployees->count(),

                'visibleOfficeIds' =>
                    $birthdayOfficeIds->all(),

                'todayBirthdayEmployees' =>
                    $todayBirthdayEmployees,

                'otherBirthdayEmployees' =>
                    $otherBirthdayEmployees,
            ],
        ], 200);
    } catch (\Throwable $exception) {
        \Log::error('API dashboard error', [
            'user_id' => $user->id,
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'trace'   => $exception->getTraceAsString(),
        ]);

        return response()->json([
            'status'  => false,

            'message' =>
                'Dashboard data could not be loaded.',

            'error' => config('app.debug')
                ? $exception->getMessage()
                : null,

            'line' => config('app.debug')
                ? $exception->getLine()
                : null,
        ], 500);
    }
}

}
