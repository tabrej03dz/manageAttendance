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
            /*
             * Super admin ko sabhi offices ke birthdays dikhenge.
             */
            $birthdayOfficeIds = Office::query()
                ->pluck('id');
        } elseif ($user->hasRole('owner')) {
            /*
             * Owner ko uske owned offices ke birthdays dikhenge.
             */
            $birthdayOfficeIds = Office::query()
                ->where('owner_id', $user->id)
                ->pluck('id');
        } elseif ($user->office_id) {
            /*
             * Normal employee/team leader/admin ke liye current office.
             */
            $currentOffice = Office::query()
                ->select([
                    'id',
                    'name',
                ])
                ->find($user->office_id);

            if ($currentOffice) {
                $currentOfficeName = mb_strtolower(
                    trim((string) $currentOffice->name)
                );

                $specialOfficeNames = [
                    'real victory groups',
                    'rvg development',
                ];

                if (
                    in_array(
                        $currentOfficeName,
                        $specialOfficeNames,
                        true
                    )
                ) {
                    /*
                     * Real Victory Groups aur Rvg Development ke
                     * employees ko dono offices ke birthdays dikhenge.
                     */
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
                    /*
                     * Baaki employees ko sirf apne office ka birthday.
                     */
                    $birthdayOfficeIds = collect([
                        (int) $currentOffice->id,
                    ]);
                }
            }
        }

        $birthdayOfficeIds = $birthdayOfficeIds
            ->map(function ($officeId) {
                return (int) $officeId;
            })
            ->filter(function ($officeId) {
                return $officeId > 0;
            })
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Today's birthday employees
        |--------------------------------------------------------------------------
        */

        if ($birthdayOfficeIds->isEmpty()) {
            $todayBirthdayEmployees = collect();
        } else {
            $todayBirthdayEmployees = User::query()
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
                ->map(function ($birthdayEmployee) use ($today, $user) {
                    /*
                     * IMPORTANT:
                     * $user ko closure ke use() me pass kiya gaya hai.
                     * Isi se Undefined variable $user error fix hota hai.
                     */

                    $birthdayAge = null;
                    $birthdayDate = null;

                    try {
                        $dob = Carbon::parse(
                            $birthdayEmployee->dob
                        );

                        $birthdayAge = $dob->age;
                        $birthdayDate = $dob->toDateString();
                    } catch (\Throwable $exception) {
                        $birthdayAge = null;
                        $birthdayDate = null;
                    }

                    return [
                        'id' => (int) $birthdayEmployee->id,

                        'name' => $birthdayEmployee->name,

                        'email' => $birthdayEmployee->email,

                        'phone' => $birthdayEmployee->phone,

                        'photo' => $birthdayEmployee->photo,

                        'dob' => $birthdayDate,

                        'birthday_age' => $birthdayAge,

                        'designation' =>
                            $birthdayEmployee->designation,

                        'department' => optional(
                            $birthdayEmployee->department
                        )->name,

                        'office_id' =>
                            $birthdayEmployee->office_id
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
        }

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
        | Logged-in user ko list se remove karke sirf dusre employees.
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
        | Logged-in birthday employee data
        |--------------------------------------------------------------------------
        */

        $loggedInBirthdayEmployee = $todayBirthdayEmployees
            ->first(function ($birthdayEmployee) use ($user) {
                return (int) $birthdayEmployee['id'] ===
                    (int) $user->id;
            });

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
         * Future dates include nahi karni hain.
         * Isliye month end ke badle aaj tak ka data.
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
        | Final API response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'status' => true,

            'message' =>
                'Dashboard data fetched successfully.',

            'offices' => $offices->count(),

            'employees' => $employees->count(),

            'todayAttendanceRecord' =>
                $todayAttendanceRecord,

            'break' => $break,

            'data' => $data,

            /*
            |--------------------------------------------------------------------------
            | Birthday response
            |--------------------------------------------------------------------------
            */

            'birthday' => [
                /*
                 * Aaj kisi visible employee ka birthday hai ya nahi.
                 */
                'hasBirthdayToday' =>
                    $todayBirthdayEmployees->isNotEmpty(),

                /*
                 * Logged-in user ka khud ka birthday hai ya nahi.
                 */
                'isUserBirthdayToday' =>
                    $isUserBirthdayToday,

                /*
                 * Logged-in user ka birthday data.
                 * Birthday nahi hai to null.
                 */
                'loggedInBirthdayEmployee' =>
                    $loggedInBirthdayEmployee,

                /*
                 * Total visible birthday employees.
                 */
                'todayBirthdayCount' =>
                    $todayBirthdayEmployees->count(),

                /*
                 * Logged-in user ko hata kar dusre birthday employees.
                 */
                'otherBirthdayCount' =>
                    $otherBirthdayEmployees->count(),

                /*
                 * Birthday ke liye visible offices.
                 */
                'visibleOfficeIds' =>
                    $birthdayOfficeIds->all(),

                /*
                 * Isme logged-in user bhi ho sakta hai.
                 */
                'todayBirthdayEmployees' =>
                    $todayBirthdayEmployees,

                /*
                 * Isme logged-in user nahi hoga.
                 */
                'otherBirthdayEmployees' =>
                    $otherBirthdayEmployees,

                /*
                 * App popup visibility:
                 * sirf logged-in birthday user ke liye true.
                 */
                'showBirthdayPopup' =>
                    $isUserBirthdayToday,

                /*
                 * Team birthday section:
                 * sirf tab true jab kisi dusre employee ka birthday ho.
                 */
                'showTeamBirthdaySection' =>
                    $otherBirthdayEmployees->isNotEmpty(),
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
            'status' => false,

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
