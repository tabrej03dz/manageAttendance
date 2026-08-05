<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRoster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;


class EmployeeRosterController extends Controller
{
    // private function activeOfficeId(Request $request): ?int
    // {
    //     return $request->user()?->activeOfficeId();
    // }

    // private function allowedOfficeIds(Request $request): array
    // {
    //     $user = $request->user();

    //     if (!$user) {
    //         return [];
    //     }

    //     if (
    //         $user->hasRole('super_admin') ||
    //         $user->hasRole('owner') ||
    //         $user->hasRole('admin')
    //     ) {
    //         $officeId = $user->activeOfficeId();
    //         return $officeId ? [(int) $officeId] : [];
    //     }

    //     if ($user->office_id) {
    //         return [(int) $user->office_id];
    //     }

    //     return [];
    // }

    // private function officeEmployeesQuery(Request $request)
    // {
    //     $officeIds = $this->allowedOfficeIds($request);

    //     return User::query()->when(!empty($officeIds), function ($q) use ($officeIds) {
    //         $q->whereIn('office_id', $officeIds);
    //     }, function ($q) {
    //         $q->whereRaw('1 = 0');
    //     });
    // }

    // private function sortEmployeesHierarchically($employees)
    // {
    //     $employees = collect($employees);

    //     $grouped = $employees->groupBy('team_leader_id');
    //     $sorted = collect();

    //     $appendChildren = function ($leaderId) use (&$appendChildren, $grouped, &$sorted) {
    //         if (!isset($grouped[$leaderId])) {
    //             return;
    //         }

    //         foreach ($grouped[$leaderId]->sortBy('name') as $employee) {
    //             $sorted->push($employee);
    //             $appendChildren($employee->id);
    //         }
    //     };

    //     if (isset($grouped[null])) {
    //         foreach ($grouped[null]->sortBy('name') as $employee) {
    //             $sorted->push($employee);
    //             $appendChildren($employee->id);
    //         }
    //     }

    //     $remaining = $employees->whereNotIn('id', $sorted->pluck('id'));

    //     foreach ($remaining->sortBy('name') as $employee) {
    //         $sorted->push($employee);
    //         $appendChildren($employee->id);
    //     }

    //     return $sorted->unique('id')->values();
    // }

    // private function rosterEditPermissionNames(): array
    // {
    //     return [
    //         'edit roster',
    //         'edit roaster',
    //         'edit raoster',
    //     ];
    // }

    // private function canEditRoster($user): bool
    // {
    //     if (!$user) {
    //         return false;
    //     }

    //     foreach ($this->rosterEditPermissionNames() as $permission) {
    //         if ($user->can($permission)) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }

    // private function employeeBelongsToAllowedOffice(Request $request, int $employeeId): bool
    // {
    //     return $this->officeEmployeesQuery($request)
    //         ->where('id', $employeeId)
    //         ->exists();
    // }

    // private function rosterEditErrorMessage(): string
    // {
    //     return 'Aapke paas roster edit permission nahi hai.';
    // }

    // private function ownRosterErrorMessage(): string
    // {
    //     return 'Aap apna roster status change nahi kar sakte. Sirf dusre employees ka kar sakte hain.';
    // }

    // private function officeErrorMessage(): string
    // {
    //     return 'This employee does not belong to your allowed office.';
    // }

    // public function index(Request $request)
    // {
    //     $request->validate([
    //         'month' => ['nullable', 'date_format:Y-m'],
    //     ]);

    //     $month = $request->month ?: now()->format('Y-m');

    //     $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    //     $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    //     $user = $request->user();
    //     $activeOfficeId = $user?->activeOfficeId();
    //     $canEditRoster = $this->canEditRoster($user);

    //     if (!$user || !$activeOfficeId) {
    //         $employees = collect();
    //     } elseif ($user->hasRole('super_admin') || $user->hasRole('admin') || $user->hasRole('owner')) {
    //         $employees = User::where('office_id', $activeOfficeId)
    //             ->where('status', '1')
    //             ->select([
    //                 'id',
    //                 'name',
    //                 'email',
    //                 'photo',
    //                 'office_id',
    //                 'team_leader_id',
    //             ])
    //             ->get();

    //         $employees = $this->sortEmployeesHierarchically($employees);
    //     } elseif ($user->hasRole('team_leader')) {
    //         $employees = $user->getAllTeamMembers()
    //             ->filter(function ($member) use ($activeOfficeId) {
    //                 return (int) $member->office_id === (int) $activeOfficeId
    //                     && (string) $member->status === '1';
    //             });

    //         if (
    //             (int) $user->office_id === (int) $activeOfficeId &&
    //             (string) $user->status === '1'
    //         ) {
    //             $employees->push($user);
    //         }

    //         $employees = $this->sortEmployeesHierarchically(
    //             $employees->unique('id')->values()
    //         );
    //     } else {
    //         if (
    //             (int) $user->office_id === (int) $activeOfficeId &&
    //             (string) $user->status === '1'
    //         ) {
    //             $employees = collect([$user]);
    //         } else {
    //             $employees = collect();
    //         }
    //     }

    //     $employeeIds = $employees->pluck('id');

    //     $rosters = EmployeeRoster::query()
    //         ->whereBetween('duty_date', [
    //             $start->toDateString(),
    //             $end->toDateString(),
    //         ])
    //         ->whereIn('employee_id', $employeeIds)
    //         ->get()
    //         ->groupBy('employee_id');

    //     $days = [];

    //     foreach (CarbonPeriod::create($start, $end) as $date) {
    //         $days[] = [
    //             'date'     => $date->toDateString(),
    //             'day'      => $date->format('d'),
    //             'day_name' => $date->format('D'),
    //             'is_today' => $date->isToday(),
    //         ];
    //     }

    //     $rows = [];

    //     foreach ($employees as $employee) {
    //         $employeeRosters = $rosters
    //             ->get($employee->id, collect())
    //             ->keyBy(function ($item) {
    //                 return Carbon::parse($item->duty_date)->toDateString();
    //             });

    //         $items = [];

    //         foreach ($days as $day) {
    //             $record = $employeeRosters->get($day['date']);

    //             $items[] = [
    //                 'date'   => $day['date'],
    //                 'status' => $record->status ?? 'working',
    //             ];
    //         }

    //         $rows[] = [
    //             'employee' => $employee,
    //             'items'    => $items,
    //         ];
    //     }

    //     return view('rosters.index', compact('month', 'days', 'rows', 'canEditRoster'));
    // }

    // public function ajaxUpsert(Request $request)
    // {
    //     $request->validate([
    //         'employee_id' => ['required', 'exists:users,id'],
    //         'duty_date'   => ['required', 'date'],
    //         'status'      => ['required', Rule::in(['working', 'off', 'half_day', 'leave'])],
    //     ]);

    //     $authUser = $request->user();
    //     $employeeId = (int) $request->employee_id;

    //     if (!$this->canEditRoster($authUser)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $this->rosterEditErrorMessage(),
    //         ], 403);
    //     }

    //     if ($authUser && $employeeId === (int) $authUser->id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $this->ownRosterErrorMessage(),
    //         ], 403);
    //     }

    //     if (!$this->employeeBelongsToAllowedOffice($request, $employeeId)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $this->officeErrorMessage(),
    //         ], 403);
    //     }

    //     $roster = EmployeeRoster::updateOrCreate(
    //         [
    //             'employee_id' => $employeeId,
    //             'duty_date'   => $request->duty_date,
    //         ],
    //         [
    //             'status'     => $request->status,
    //             'created_by' => $authUser?->id,
    //         ]
    //     );

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Roster saved successfully.',
    //         'data'    => [
    //             'id'          => $roster->id,
    //             'employee_id' => $roster->employee_id,
    //             'duty_date'   => $roster->duty_date,
    //             'status'      => $roster->status,
    //         ],
    //     ]);
    // }

    // /**
    //  * Save single roster
    //  * POST: /employee-rosters/store
    //  */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'employee_id' => ['required', 'exists:users,id'],
    //         'duty_date'   => ['required', 'date'],
    //         'status'      => ['required', Rule::in(['working', 'off', 'half_day', 'leave'])],
    //         'shift_name'  => ['nullable', 'string', 'max:100'],
    //         'start_time'  => ['nullable', 'date_format:H:i'],
    //         'end_time'    => ['nullable', 'date_format:H:i', 'after:start_time'],
    //         'note'        => ['nullable', 'string'],
    //     ]);

    //     $authUser = $request->user();
    //     $employeeId = (int) $request->employee_id;

    //     if (!$this->canEditRoster($authUser)) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->rosterEditErrorMessage()])
    //             ->withInput();
    //     }

    //     if ($authUser && $employeeId === (int) $authUser->id) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->ownRosterErrorMessage()])
    //             ->withInput();
    //     }

    //     if (!$this->employeeBelongsToAllowedOffice($request, $employeeId)) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->officeErrorMessage()])
    //             ->withInput();
    //     }

    //     EmployeeRoster::updateOrCreate(
    //         [
    //             'employee_id' => $employeeId,
    //             'duty_date'   => $request->duty_date,
    //         ],
    //         [
    //             'status'     => $request->status,
    //             'shift_name' => $request->shift_name,
    //             'start_time' => $request->start_time,
    //             'end_time'   => $request->end_time,
    //             'note'       => $request->note,
    //             'created_by' => $authUser?->id,
    //         ]
    //     );

    //     return redirect()
    //         ->back()
    //         ->with('success', 'Roster saved successfully.');
    // }

    // /**
    //  * Bulk roster save
    //  * POST: /employee-rosters/bulk-store
    //  */
    // public function bulkStore(Request $request)
    // {
    //     $request->validate([
    //         'employee_id'         => ['required', 'exists:users,id'],
    //         'items'               => ['required', 'array', 'min:1'],
    //         'items.*.duty_date'   => ['required', 'date'],
    //         'items.*.status'      => ['required', Rule::in(['working', 'off', 'half_day', 'leave'])],
    //         'items.*.shift_name'  => ['nullable', 'string', 'max:100'],
    //         'items.*.start_time'  => ['nullable', 'date_format:H:i'],
    //         'items.*.end_time'    => ['nullable', 'date_format:H:i'],
    //         'items.*.note'        => ['nullable', 'string'],
    //     ]);

    //     $authUser = $request->user();
    //     $employeeId = (int) $request->employee_id;

    //     if (!$this->canEditRoster($authUser)) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->rosterEditErrorMessage()])
    //             ->withInput();
    //     }

    //     if ($authUser && $employeeId === (int) $authUser->id) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->ownRosterErrorMessage()])
    //             ->withInput();
    //     }

    //     if (!$this->employeeBelongsToAllowedOffice($request, $employeeId)) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->officeErrorMessage()])
    //             ->withInput();
    //     }

    //     foreach ($request->items as $item) {
    //         if (
    //             !empty($item['start_time']) &&
    //             !empty($item['end_time']) &&
    //             strtotime($item['end_time']) <= strtotime($item['start_time'])
    //         ) {
    //             return redirect()
    //                 ->back()
    //                 ->withErrors([
    //                     'end_time' => 'End time must be greater than start time for date ' . $item['duty_date']
    //                 ])
    //                 ->withInput();
    //         }

    //         EmployeeRoster::updateOrCreate(
    //             [
    //                 'employee_id' => $employeeId,
    //                 'duty_date'   => $item['duty_date'],
    //             ],
    //             [
    //                 'status'     => $item['status'],
    //                 'shift_name' => $item['shift_name'] ?? null,
    //                 'start_time' => $item['start_time'] ?? null,
    //                 'end_time'   => $item['end_time'] ?? null,
    //                 'note'       => $item['note'] ?? null,
    //                 'created_by' => $authUser?->id,
    //             ]
    //         );
    //     }

    //     return redirect()
    //         ->back()
    //         ->with('success', 'Bulk roster saved successfully.');
    // }

    // /**
    //  * Monthly roster list
    //  * GET: /employee-rosters/monthly?month=2026-04
    //  */
    // public function monthly(Request $request)
    // {
    //     $request->validate([
    //         'month'       => ['required', 'date_format:Y-m'],
    //         'employee_id' => ['nullable', 'exists:users,id'],
    //     ]);

    //     $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
    //     $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

    //     $query = EmployeeRoster::with(['employee:id,name'])
    //         ->whereBetween('duty_date', [$start->toDateString(), $end->toDateString()]);

    //     if ($request->filled('employee_id')) {
    //         $query->where('employee_id', $request->employee_id);
    //     }

    //     $records = $query->orderBy('employee_id')
    //         ->orderBy('duty_date')
    //         ->get();

    //     $employees = User::select('id', 'name')
    //         ->where('status', '1')
    //         ->orderBy('name')
    //         ->get();

    //     return view('rosters.monthly', [
    //         'records'   => $records,
    //         'month'     => $request->month,
    //         'employees' => $employees,
    //     ]);
    // }

    // /**
    //  * Employee monthly roster
    //  * GET: /employee-rosters/employee/{employee}?month=2026-04
    //  */
    // public function employeeRoster(Request $request, $employee)
    // {
    //     $request->validate([
    //         'month' => ['required', 'date_format:Y-m'],
    //     ]);

    //     $employeeData = User::select('id', 'name')->findOrFail($employee);

    //     $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
    //     $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

    //     $records = EmployeeRoster::where('employee_id', $employee)
    //         ->whereBetween('duty_date', [$start->toDateString(), $end->toDateString()])
    //         ->orderBy('duty_date')
    //         ->get();

    //     return view('rosters.employee-monthly', [
    //         'employee' => $employeeData,
    //         'records'  => $records,
    //         'month'    => $request->month,
    //     ]);
    // }

    // /**
    //  * Month grid page
    //  * GET: /employee-rosters/month-grid?month=2026-04
    //  */
    // public function monthGrid(Request $request)
    // {
    //     $request->validate([
    //         'month' => ['required', 'date_format:Y-m'],
    //     ]);

    //     $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
    //     $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

    //     $employees = User::select('id', 'name')
    //         ->where('status', '1')
    //         ->orderBy('name')
    //         ->get();

    //     $rosters = EmployeeRoster::whereBetween('duty_date', [$start->toDateString(), $end->toDateString()])
    //         ->get()
    //         ->groupBy('employee_id');

    //     $days = [];

    //     foreach (CarbonPeriod::create($start, $end) as $date) {
    //         $days[] = [
    //             'date'       => $date->toDateString(),
    //             'day'        => $date->format('d'),
    //             'day_name'   => $date->format('D'),
    //             'day_number' => $date->day,
    //         ];
    //     }

    //     $statusShortMap = [
    //         'working'  => 'P',
    //         'off'      => 'O',
    //         'half_day' => 'H',
    //         'leave'    => 'L',
    //     ];

    //     $rows = [];

    //     foreach ($employees as $employee) {
    //         $employeeRosters = $rosters->get($employee->id, collect())->keyBy(function ($item) {
    //             return Carbon::parse($item->duty_date)->toDateString();
    //         });

    //         $items = [];

    //         foreach ($days as $day) {
    //             $record = $employeeRosters->get($day['date']);
    //             $status = $record->status ?? 'working';

    //             $items[] = [
    //                 'date'         => $day['date'],
    //                 'status'       => $status,
    //                 'status_short' => $statusShortMap[$status] ?? 'P',
    //                 'shift_name'   => $record->shift_name ?? null,
    //                 'start_time'   => $record->start_time ?? null,
    //                 'end_time'     => $record->end_time ?? null,
    //                 'note'         => $record->note ?? null,
    //             ];
    //         }

    //         $rows[] = [
    //             'employee_id'   => $employee->id,
    //             'employee_name' => $employee->name,
    //             'items'         => $items,
    //         ];
    //     }

    //     return view('rosters.month-grid', [
    //         'month' => $request->month,
    //         'days'  => $days,
    //         'rows'  => $rows,
    //     ]);
    // }

    // /**
    //  * Delete roster
    //  * POST: /employee-rosters/delete
    //  */
    // public function delete(Request $request)
    // {
    //     $request->validate([
    //         'employee_id' => ['required', 'exists:users,id'],
    //         'duty_date'   => ['required', 'date'],
    //     ]);

    //     $authUser = $request->user();
    //     $employeeId = (int) $request->employee_id;

    //     if (!$this->canEditRoster($authUser)) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->rosterEditErrorMessage()])
    //             ->withInput();
    //     }

    //     if ($authUser && $employeeId === (int) $authUser->id) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->ownRosterErrorMessage()])
    //             ->withInput();
    //     }

    //     if (!$this->employeeBelongsToAllowedOffice($request, $employeeId)) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->officeErrorMessage()])
    //             ->withInput();
    //     }

    //     $roster = EmployeeRoster::where('employee_id', $employeeId)
    //         ->whereDate('duty_date', $request->duty_date)
    //         ->first();

    //     if (!$roster) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => 'Roster not found.']);
    //     }

    //     $roster->delete();

    //     return redirect()
    //         ->back()
    //         ->with('success', 'Roster deleted successfully.');
    // }

    // /**
    //  * Show today's roster status page
    //  * GET: /employee-rosters/today-status?employee_id=1
    //  */
    // public function todayStatus(Request $request)
    // {
    //     $request->validate([
    //         'employee_id' => ['nullable', 'exists:users,id'],
    //     ]);

    //     $employeeId = $request->employee_id ?: auth()->id();
    //     $today = now()->toDateString();

    //     $roster = EmployeeRoster::with('employee:id,name')
    //         ->where('employee_id', $employeeId)
    //         ->whereDate('duty_date', $today)
    //         ->first();

    //     $employee = $roster->employee ?? User::select('id', 'name')->find($employeeId);

    //     $data = [
    //         'employee_id'  => $employeeId,
    //         'date'         => $today,
    //         'status'       => $roster->status ?? 'working',
    //         'status_short' => match ($roster->status ?? 'working') {
    //             'off'      => 'O',
    //             'leave'    => 'L',
    //             'half_day' => 'H',
    //             default    => 'P',
    //         },
    //         'shift_name'   => $roster->shift_name ?? null,
    //         'start_time'   => $roster->start_time ?? null,
    //         'end_time'     => $roster->end_time ?? null,
    //         'note'         => $roster->note ?? null,
    //         'employee'     => $employee,
    //     ];

    //     return view('rosters.today-status', compact('data'));
    // }

    // /**
    //  * Generate full month default working roster
    //  * POST: /employee-rosters/generate-month
    //  */
    // public function generateMonth(Request $request)
    // {
    //     $request->validate([
    //         'employee_id' => ['required', 'exists:users,id'],
    //         'month'       => ['required', 'date_format:Y-m'],
    //         'overwrite'   => ['nullable', 'boolean'],
    //     ]);

    //     $authUser = $request->user();
    //     $employeeId = (int) $request->employee_id;

    //     if (!$this->canEditRoster($authUser)) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->rosterEditErrorMessage()])
    //             ->withInput();
    //     }

    //     if ($authUser && $employeeId === (int) $authUser->id) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->ownRosterErrorMessage()])
    //             ->withInput();
    //     }

    //     if (!$this->employeeBelongsToAllowedOffice($request, $employeeId)) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => $this->officeErrorMessage()])
    //             ->withInput();
    //     }

    //     $overwrite = $request->boolean('overwrite', false);

    //     $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
    //     $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

    //     foreach (CarbonPeriod::create($start, $end) as $date) {
    //         $existing = EmployeeRoster::where('employee_id', $employeeId)
    //             ->whereDate('duty_date', $date->toDateString())
    //             ->first();

    //         if ($existing && !$overwrite) {
    //             continue;
    //         }

    //         EmployeeRoster::updateOrCreate(
    //             [
    //                 'employee_id' => $employeeId,
    //                 'duty_date'   => $date->toDateString(),
    //             ],
    //             [
    //                 'status'     => 'working',
    //                 'shift_name' => null,
    //                 'start_time' => null,
    //                 'end_time'   => null,
    //                 'note'       => null,
    //                 'created_by' => $authUser?->id,
    //             ]
    //         );
    //     }

    //     return redirect()
    //         ->back()
    //         ->with('success', 'Monthly default roster generated successfully.');
    // }




    private function activeOfficeId(Request $request): ?int
    {
        $user = $request->user();

        if (!$user) {
            return null;
        }

        $sessionOfficeId = (int) $request->session()->get(
            'active_office_id',
            0
        );

        if ($sessionOfficeId > 0) {
            return $sessionOfficeId;
        }

        if (method_exists($user, 'activeOfficeId')) {
            $officeId = $user->activeOfficeId();

            if ($officeId) {
                return (int) $officeId;
            }
        }

        return !empty($user->office_id)
            ? (int) $user->office_id
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Allowed office IDs
    |--------------------------------------------------------------------------
    */

    private function allowedOfficeIds(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        $activeOfficeId = $this->activeOfficeId($request);

        if ($activeOfficeId) {
            return [$activeOfficeId];
        }

        if (!empty($user->office_id)) {
            return [(int) $user->office_id];
        }

        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Office employees query
    |--------------------------------------------------------------------------
    */

    private function officeEmployeesQuery(Request $request)
    {
        $officeIds = $this->allowedOfficeIds($request);

        return User::query()
            ->when(
                !empty($officeIds),
                function ($query) use ($officeIds) {
                    $query->whereIn('office_id', $officeIds);
                },
                function ($query) {
                    $query->whereRaw('1 = 0');
                }
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Safe nested hierarchy IDs
    |--------------------------------------------------------------------------
    |
    | Recursion इस्तेमाल नहीं की गई।
    | A -> B -> A जैसी circular hierarchy में infinite loop नहीं होगा।
    |
    */

    private function nestedTeamEmployeeIds(
        int $leaderId,
        ?int $officeId = null
    ): array {
        $result = [$leaderId];
        $visited = [$leaderId => true];
        $currentLeaderIds = [$leaderId];

        $currentLevel = 0;
        $maximumLevels = 100;

        while (
            !empty($currentLeaderIds)
            && $currentLevel < $maximumLevels
        ) {
            $currentLevel++;

            $query = User::query()
                ->whereIn('team_leader_id', $currentLeaderIds)
                ->where('status', '1');

            if ($officeId) {
                $query->where('office_id', $officeId);
            }

            $childIds = $query
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            $nextLeaderIds = [];

            foreach ($childIds as $childId) {
                if ($childId <= 0) {
                    continue;
                }

                if (isset($visited[$childId])) {
                    continue;
                }

                $visited[$childId] = true;
                $result[] = $childId;
                $nextLeaderIds[] = $childId;
            }

            $currentLeaderIds = $nextLeaderIds;
        }

        return array_values(array_unique($result));
    }

    /*
    |--------------------------------------------------------------------------
    | Safe hierarchy sorting
    |--------------------------------------------------------------------------
    |
    | Collection push/prepend और recursive Collection operations नहीं हैं।
    |
    */

    private function sortEmployeesHierarchically(
        Collection $employees,
        ?int $forcedRootId = null
    ): Collection {
        $employees = $employees
            ->filter(function ($employee) {
                return !empty($employee->id);
            })
            ->unique('id')
            ->values();

        if ($employees->isEmpty()) {
            return collect();
        }

        $employeeById = [];

        foreach ($employees as $employee) {
            $employeeById[(int) $employee->id] = $employee;
        }

        $childrenByLeader = [];

        foreach ($employees as $employee) {
            $employeeId = (int) $employee->id;

            $leaderId = !empty($employee->team_leader_id)
                ? (int) $employee->team_leader_id
                : 0;

            /*
             * Self-reference को root बनाएं।
             */
            if ($leaderId === $employeeId) {
                $leaderId = 0;
            }

            /*
             * Current employee collection में leader मौजूद नहीं है
             * तो employee root level पर दिखेगा।
             */
            if (
                $leaderId > 0
                && !array_key_exists($leaderId, $employeeById)
            ) {
                $leaderId = 0;
            }

            if (!isset($childrenByLeader[$leaderId])) {
                $childrenByLeader[$leaderId] = [];
            }

            $childrenByLeader[$leaderId][] = $employee;
        }

        /*
         * हर level पर alphabetical sorting।
         */
        foreach ($childrenByLeader as &$children) {
            usort(
                $children,
                function ($first, $second) {
                    return strcasecmp(
                        (string) ($first->name ?? ''),
                        (string) ($second->name ?? '')
                    );
                }
            );
        }

        unset($children);

        $sortedEmployees = [];
        $visited = [];

        /*
         * Iterative stack traversal।
         */
        $processRoot = function (
            $rootEmployee,
            int $rootLevel = 0
        ) use (
            &$sortedEmployees,
            &$visited,
            $childrenByLeader
        ): void {
            $stack = [
                [
                    'employee' => $rootEmployee,
                    'level' => $rootLevel,
                ],
            ];

            while (!empty($stack)) {
                $current = array_pop($stack);

                $employee = $current['employee'];
                $level = (int) $current['level'];
                $employeeId = (int) $employee->id;

                if (isset($visited[$employeeId])) {
                    continue;
                }

                $visited[$employeeId] = true;

                $employee->hierarchy_level = $level;

                $sortedEmployees[] = $employee;

                $children = $childrenByLeader[$employeeId] ?? [];

                /*
                 * Stack LIFO है, इसलिए reverse order में डालेंगे।
                 */
                for ($index = count($children) - 1; $index >= 0; $index--) {
                    $child = $children[$index];
                    $childId = (int) $child->id;

                    if (isset($visited[$childId])) {
                        continue;
                    }

                    $stack[] = [
                        'employee' => $child,
                        'level' => $level + 1,
                    ];
                }
            }
        };

        /*
         * Team leader page पर logged-in leader सबसे पहले।
         */
        if (
            $forcedRootId
            && isset($employeeById[$forcedRootId])
        ) {
            $processRoot(
                $employeeById[$forcedRootId],
                0
            );
        }

        /*
         * Normal root employees।
         */
        foreach ($childrenByLeader[0] ?? [] as $rootEmployee) {
            $processRoot($rootEmployee, 0);
        }

        /*
         * Broken या circular hierarchy में छूटे हुए employees।
         */
        foreach ($employees as $employee) {
            $employeeId = (int) $employee->id;

            if (!isset($visited[$employeeId])) {
                $processRoot($employee, 0);
            }
        }

        return collect($sortedEmployees)->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Visible employees according to hierarchy
    |--------------------------------------------------------------------------
    */

    private function visibleEmployees(Request $request): Collection
    {
        $user = $request->user();

        if (!$user) {
            return collect();
        }

        $activeOfficeId = $this->activeOfficeId($request);

        if (!$activeOfficeId) {
            return collect();
        }

        $employeeSelect = [
            'id',
            'name',
            'email',
            'photo',
            'office_id',
            'team_leader_id',
            'status',
        ];

        /*
         * Management selected office के सभी active employees।
         */
        if (
            $user->hasRole('super_admin')
            || $user->hasRole('admin')
            || $user->hasRole('owner')
        ) {
            $employees = User::query()
                ->select($employeeSelect)
                ->where('office_id', $activeOfficeId)
                ->where('status', '1')
                ->get();

            return $this->sortEmployeesHierarchically(
                $employees
            );
        }

        /*
         * Team leader खुद और पूरी nested team।
         */
        if ($user->hasRole('team_leader')) {
            if (
                (int) $user->office_id !== (int) $activeOfficeId
                || (string) $user->status !== '1'
            ) {
                return collect();
            }

            $employeeIds = $this->nestedTeamEmployeeIds(
                (int) $user->id,
                $activeOfficeId
            );

            $employees = User::query()
                ->select($employeeSelect)
                ->whereIn('id', $employeeIds)
                ->where('office_id', $activeOfficeId)
                ->where('status', '1')
                ->get();

            return $this->sortEmployeesHierarchically(
                $employees,
                (int) $user->id
            );
        }

        /*
         * Normal employee केवल खुद।
         */
        if (
            (int) $user->office_id === (int) $activeOfficeId
            && (string) $user->status === '1'
        ) {
            $employee = User::query()
                ->select($employeeSelect)
                ->find($user->id);

            if ($employee) {
                $employee->hierarchy_level = 0;

                return collect([$employee]);
            }
        }

        return collect();
    }

    /*
    |--------------------------------------------------------------------------
    | Visible employee IDs
    |--------------------------------------------------------------------------
    */

    private function visibleEmployeeIds(Request $request): array
    {
        return $this->visibleEmployees($request)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Permission helpers
    |--------------------------------------------------------------------------
    */

    private function rosterEditPermissionNames(): array
    {
        return [
            'edit roster',
            'edit roaster',
            'edit raoster',
        ];
    }

    private function canEditRoster($user): bool
    {
        if (!$user) {
            return false;
        }

        foreach ($this->rosterEditPermissionNames() as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    private function employeeBelongsToAllowedHierarchy(
        Request $request,
        int $employeeId
    ): bool {
        return in_array(
            $employeeId,
            $this->visibleEmployeeIds($request),
            true
        );
    }

    private function rosterEditErrorMessage(): string
    {
        return 'Aapke paas roster edit permission nahi hai.';
    }

    private function ownRosterErrorMessage(): string
    {
        return 'Aap apna roster status change nahi kar sakte. Sirf dusre employees ka kar sakte hain.';
    }

    private function officeErrorMessage(): string
    {
        return 'This employee does not belong to your allowed hierarchy.';
    }

    /*
    |--------------------------------------------------------------------------
    | Main roster page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $request->validate([
            'month' => [
                'nullable',
                'date_format:Y-m',
            ],
        ]);

        $month = $request->filled('month')
            ? $request->input('month')
            : now()->format('Y-m');

        try {
            $start = Carbon::createFromFormat(
                'Y-m',
                $month
            )->startOfMonth();

            $end = Carbon::createFromFormat(
                'Y-m',
                $month
            )->endOfMonth();
        } catch (Throwable $exception) {
            $month = now()->format('Y-m');
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        }

        $user = $request->user();
        $canEditRoster = $this->canEditRoster($user);

        /*
         * Safe role-based hierarchy।
         */
        $employees = $this->visibleEmployees($request);

        $employeeIds = $employees
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Roster records
        |--------------------------------------------------------------------------
        */

        $rosterQuery = EmployeeRoster::query()
            ->select([
                'id',
                'employee_id',
                'duty_date',
                'status',
                'shift_name',
                'start_time',
                'end_time',
                'note',
            ])
            ->whereBetween('duty_date', [
                $start->toDateString(),
                $end->toDateString(),
            ]);

        if (empty($employeeIds)) {
            $rosterQuery->whereRaw('1 = 0');
        } else {
            $rosterQuery->whereIn(
                'employee_id',
                $employeeIds
            );
        }

        $rosterRecords = $rosterQuery
            ->orderBy('employee_id')
            ->orderBy('duty_date')
            ->get();

        /*
         * employee_id + duty_date map।
         */
        $rosterMap = [];

        foreach ($rosterRecords as $record) {
            $recordDate = $record->duty_date instanceof Carbon
                ? $record->duty_date->toDateString()
                : Carbon::parse($record->duty_date)->toDateString();

            $rosterMap[
                (int) $record->employee_id
                . '_'
                . $recordDate
            ] = $record;
        }

        /*
        |--------------------------------------------------------------------------
        | Month days
        |--------------------------------------------------------------------------
        */

        $days = [];

        foreach (
            CarbonPeriod::create(
                $start->copy(),
                $end->copy()
            ) as $date
        ) {
            $days[] = [
                'date' => $date->toDateString(),
                'day' => $date->format('d'),
                'day_name' => $date->format('D'),
                'is_today' => $date->isToday(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Grid rows
        |--------------------------------------------------------------------------
        */

        $rows = [];

        foreach ($employees as $employee) {
            $items = [];

            foreach ($days as $day) {
                $mapKey = (int) $employee->id
                    . '_'
                    . $day['date'];

                $record = $rosterMap[$mapKey] ?? null;

                $items[] = [
                    'date' => $day['date'],
                    'status' => $record?->status ?? 'working',
                    'shift_name' => $record?->shift_name,
                    'start_time' => $record?->start_time,
                    'end_time' => $record?->end_time,
                    'note' => $record?->note,
                ];
            }

            $rows[] = [
                'employee' => $employee,
                'items' => $items,
            ];
        }

        return view('rosters.index', [
            'month' => $month,
            'days' => $days,
            'rows' => $rows,
            'canEditRoster' => $canEditRoster,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX single roster save
    |--------------------------------------------------------------------------
    */

    public function ajaxUpsert(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'duty_date' => [
                'required',
                'date',
            ],
            'status' => [
                'required',
                Rule::in([
                    'working',
                    'off',
                    'half_day',
                    'leave',
                ]),
            ],
        ]);

        $authUser = $request->user();
        $employeeId = (int) $validated['employee_id'];

        if (!$this->canEditRoster($authUser)) {
            return response()->json([
                'success' => false,
                'message' => $this->rosterEditErrorMessage(),
            ], 403);
        }

        if (
            $authUser
            && $employeeId === (int) $authUser->id
        ) {
            return response()->json([
                'success' => false,
                'message' => $this->ownRosterErrorMessage(),
            ], 403);
        }

        if (
            !$this->employeeBelongsToAllowedHierarchy(
                $request,
                $employeeId
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => $this->officeErrorMessage(),
            ], 403);
        }

        $roster = EmployeeRoster::query()->updateOrCreate(
            [
                'employee_id' => $employeeId,
                'duty_date' => Carbon::parse(
                    $validated['duty_date']
                )->toDateString(),
            ],
            [
                'status' => $validated['status'],
                'created_by' => $authUser?->id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Roster saved successfully.',
            'data' => [
                'id' => $roster->id,
                'employee_id' => $roster->employee_id,
                'duty_date' => $roster->duty_date,
                'status' => $roster->status,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Save single roster
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'duty_date' => [
                'required',
                'date',
            ],
            'status' => [
                'required',
                Rule::in([
                    'working',
                    'off',
                    'half_day',
                    'leave',
                ]),
            ],
            'shift_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],
            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after:start_time',
            ],
            'note' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $authUser = $request->user();
        $employeeId = (int) $validated['employee_id'];

        if (!$this->canEditRoster($authUser)) {
            return back()
                ->withErrors([
                    'error' => $this->rosterEditErrorMessage(),
                ])
                ->withInput();
        }

        if (
            $authUser
            && $employeeId === (int) $authUser->id
        ) {
            return back()
                ->withErrors([
                    'error' => $this->ownRosterErrorMessage(),
                ])
                ->withInput();
        }

        if (
            !$this->employeeBelongsToAllowedHierarchy(
                $request,
                $employeeId
            )
        ) {
            return back()
                ->withErrors([
                    'error' => $this->officeErrorMessage(),
                ])
                ->withInput();
        }

        EmployeeRoster::query()->updateOrCreate(
            [
                'employee_id' => $employeeId,
                'duty_date' => Carbon::parse(
                    $validated['duty_date']
                )->toDateString(),
            ],
            [
                'status' => $validated['status'],
                'shift_name' => $validated['shift_name'] ?? null,
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'note' => $validated['note'] ?? null,
                'created_by' => $authUser?->id,
            ]
        );

        return back()->with(
            'success',
            'Roster saved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk roster save
    |--------------------------------------------------------------------------
    */

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'items' => [
                'required',
                'array',
                'min:1',
                'max:366',
            ],
            'items.*.duty_date' => [
                'required',
                'date',
            ],
            'items.*.status' => [
                'required',
                Rule::in([
                    'working',
                    'off',
                    'half_day',
                    'leave',
                ]),
            ],
            'items.*.shift_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'items.*.start_time' => [
                'nullable',
                'date_format:H:i',
            ],
            'items.*.end_time' => [
                'nullable',
                'date_format:H:i',
            ],
            'items.*.note' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $authUser = $request->user();
        $employeeId = (int) $validated['employee_id'];

        if (!$this->canEditRoster($authUser)) {
            return back()
                ->withErrors([
                    'error' => $this->rosterEditErrorMessage(),
                ])
                ->withInput();
        }

        if (
            $authUser
            && $employeeId === (int) $authUser->id
        ) {
            return back()
                ->withErrors([
                    'error' => $this->ownRosterErrorMessage(),
                ])
                ->withInput();
        }

        if (
            !$this->employeeBelongsToAllowedHierarchy(
                $request,
                $employeeId
            )
        ) {
            return back()
                ->withErrors([
                    'error' => $this->officeErrorMessage(),
                ])
                ->withInput();
        }

        foreach ($validated['items'] as $item) {
            if (
                !empty($item['start_time'])
                && !empty($item['end_time'])
                && strtotime($item['end_time'])
                    <= strtotime($item['start_time'])
            ) {
                return back()
                    ->withErrors([
                        'end_time' =>
                            'End time must be greater than start time for date '
                            . $item['duty_date'],
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use (
            $validated,
            $employeeId,
            $authUser
        ) {
            foreach ($validated['items'] as $item) {
                EmployeeRoster::query()->updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'duty_date' => Carbon::parse(
                            $item['duty_date']
                        )->toDateString(),
                    ],
                    [
                        'status' => $item['status'],
                        'shift_name' => $item['shift_name'] ?? null,
                        'start_time' => $item['start_time'] ?? null,
                        'end_time' => $item['end_time'] ?? null,
                        'note' => $item['note'] ?? null,
                        'created_by' => $authUser?->id,
                    ]
                );
            }
        });

        return back()->with(
            'success',
            'Bulk roster saved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Monthly roster list
    |--------------------------------------------------------------------------
    */

    public function monthly(Request $request)
    {
        $validated = $request->validate([
            'month' => [
                'required',
                'date_format:Y-m',
            ],
            'employee_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
        ]);

        $start = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->startOfMonth();

        $end = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->endOfMonth();

        $employees = $this->visibleEmployees($request);
        $employeeIds = $employees
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $query = EmployeeRoster::query()
            ->with([
                'employee:id,name',
            ])
            ->whereBetween('duty_date', [
                $start->toDateString(),
                $end->toDateString(),
            ]);

        if (empty($employeeIds)) {
            $query->whereRaw('1 = 0');
        } else {
            $query->whereIn(
                'employee_id',
                $employeeIds
            );
        }

        if (!empty($validated['employee_id'])) {
            $employeeId = (int) $validated['employee_id'];

            if (
                in_array(
                    $employeeId,
                    $employeeIds,
                    true
                )
            ) {
                $query->where('employee_id', $employeeId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $records = $query
            ->orderBy('employee_id')
            ->orderBy('duty_date')
            ->get();

        return view('rosters.monthly', [
            'records' => $records,
            'month' => $validated['month'],
            'employees' => $employees,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Employee monthly roster
    |--------------------------------------------------------------------------
    */

    public function employeeRoster(
        Request $request,
        $employee
    ) {
        $validated = $request->validate([
            'month' => [
                'required',
                'date_format:Y-m',
            ],
        ]);

        $employeeId = (int) $employee;

        if (
            !$this->employeeBelongsToAllowedHierarchy(
                $request,
                $employeeId
            )
        ) {
            abort(
                403,
                'This employee is outside your allowed hierarchy.'
            );
        }

        $employeeData = User::query()
            ->select([
                'id',
                'name',
            ])
            ->findOrFail($employeeId);

        $start = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->startOfMonth();

        $end = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->endOfMonth();

        $records = EmployeeRoster::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('duty_date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->orderBy('duty_date')
            ->get();

        return view('rosters.employee-monthly', [
            'employee' => $employeeData,
            'records' => $records,
            'month' => $validated['month'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Month grid
    |--------------------------------------------------------------------------
    */

    public function monthGrid(Request $request)
    {
        $validated = $request->validate([
            'month' => [
                'required',
                'date_format:Y-m',
            ],
        ]);

        $start = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->startOfMonth();

        $end = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->endOfMonth();

        $employees = $this->visibleEmployees($request);

        $employeeIds = $employees
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $rosterQuery = EmployeeRoster::query()
            ->whereBetween('duty_date', [
                $start->toDateString(),
                $end->toDateString(),
            ]);

        if (empty($employeeIds)) {
            $rosterQuery->whereRaw('1 = 0');
        } else {
            $rosterQuery->whereIn(
                'employee_id',
                $employeeIds
            );
        }

        $rosterRecords = $rosterQuery->get();

        $rosterMap = [];

        foreach ($rosterRecords as $record) {
            $date = Carbon::parse(
                $record->duty_date
            )->toDateString();

            $rosterMap[
                (int) $record->employee_id
                . '_'
                . $date
            ] = $record;
        }

        $days = [];

        foreach (
            CarbonPeriod::create(
                $start->copy(),
                $end->copy()
            ) as $date
        ) {
            $days[] = [
                'date' => $date->toDateString(),
                'day' => $date->format('d'),
                'day_name' => $date->format('D'),
                'day_number' => $date->day,
            ];
        }

        $statusShortMap = [
            'working' => 'P',
            'off' => 'O',
            'half_day' => 'H',
            'leave' => 'L',
        ];

        $rows = [];

        foreach ($employees as $employee) {
            $items = [];

            foreach ($days as $day) {
                $mapKey = (int) $employee->id
                    . '_'
                    . $day['date'];

                $record = $rosterMap[$mapKey] ?? null;
                $status = $record?->status ?? 'working';

                $items[] = [
                    'date' => $day['date'],
                    'status' => $status,
                    'status_short' =>
                        $statusShortMap[$status] ?? 'P',
                    'shift_name' => $record?->shift_name,
                    'start_time' => $record?->start_time,
                    'end_time' => $record?->end_time,
                    'note' => $record?->note,
                ];
            }

            $rows[] = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'hierarchy_level' =>
                    (int) ($employee->hierarchy_level ?? 0),
                'items' => $items,
            ];
        }

        return view('rosters.month-grid', [
            'month' => $validated['month'],
            'days' => $days,
            'rows' => $rows,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete roster
    |--------------------------------------------------------------------------
    */

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'duty_date' => [
                'required',
                'date',
            ],
        ]);

        $authUser = $request->user();
        $employeeId = (int) $validated['employee_id'];

        if (!$this->canEditRoster($authUser)) {
            return back()
                ->withErrors([
                    'error' => $this->rosterEditErrorMessage(),
                ])
                ->withInput();
        }

        if (
            $authUser
            && $employeeId === (int) $authUser->id
        ) {
            return back()
                ->withErrors([
                    'error' => $this->ownRosterErrorMessage(),
                ])
                ->withInput();
        }

        if (
            !$this->employeeBelongsToAllowedHierarchy(
                $request,
                $employeeId
            )
        ) {
            return back()
                ->withErrors([
                    'error' => $this->officeErrorMessage(),
                ])
                ->withInput();
        }

        $deleted = EmployeeRoster::query()
            ->where('employee_id', $employeeId)
            ->whereDate(
                'duty_date',
                $validated['duty_date']
            )
            ->delete();

        if (!$deleted) {
            return back()->withErrors([
                'error' => 'Roster not found.',
            ]);
        }

        return back()->with(
            'success',
            'Roster deleted successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Today status
    |--------------------------------------------------------------------------
    */

    public function todayStatus(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
        ]);

        $employeeId = !empty($validated['employee_id'])
            ? (int) $validated['employee_id']
            : (int) $request->user()->id;

        if (
            !$this->employeeBelongsToAllowedHierarchy(
                $request,
                $employeeId
            )
        ) {
            abort(
                403,
                'This employee is outside your allowed hierarchy.'
            );
        }

        $today = now()->toDateString();

        $roster = EmployeeRoster::query()
            ->with([
                'employee:id,name',
            ])
            ->where('employee_id', $employeeId)
            ->whereDate('duty_date', $today)
            ->first();

        $employee = $roster?->employee
            ?? User::query()
                ->select([
                    'id',
                    'name',
                ])
                ->find($employeeId);

        $status = $roster?->status ?? 'working';

        $data = [
            'employee_id' => $employeeId,
            'date' => $today,
            'status' => $status,
            'status_short' => match ($status) {
                'off' => 'O',
                'leave' => 'L',
                'half_day' => 'H',
                default => 'P',
            },
            'shift_name' => $roster?->shift_name,
            'start_time' => $roster?->start_time,
            'end_time' => $roster?->end_time,
            'note' => $roster?->note,
            'employee' => $employee,
        ];

        return view('rosters.today-status', [
            'data' => $data,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate month
    |--------------------------------------------------------------------------
    */

    public function generateMonth(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'month' => [
                'required',
                'date_format:Y-m',
            ],
            'overwrite' => [
                'nullable',
                'boolean',
            ],
        ]);

        $authUser = $request->user();
        $employeeId = (int) $validated['employee_id'];

        if (!$this->canEditRoster($authUser)) {
            return back()
                ->withErrors([
                    'error' => $this->rosterEditErrorMessage(),
                ])
                ->withInput();
        }

        if (
            $authUser
            && $employeeId === (int) $authUser->id
        ) {
            return back()
                ->withErrors([
                    'error' => $this->ownRosterErrorMessage(),
                ])
                ->withInput();
        }

        if (
            !$this->employeeBelongsToAllowedHierarchy(
                $request,
                $employeeId
            )
        ) {
            return back()
                ->withErrors([
                    'error' => $this->officeErrorMessage(),
                ])
                ->withInput();
        }

        $overwrite = (bool) (
            $validated['overwrite'] ?? false
        );

        $start = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->startOfMonth();

        $end = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->endOfMonth();

        $existingDates = EmployeeRoster::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('duty_date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->pluck('duty_date')
            ->map(function ($date) {
                return Carbon::parse($date)->toDateString();
            })
            ->flip()
            ->all();

        DB::transaction(function () use (
            $start,
            $end,
            $employeeId,
            $authUser,
            $overwrite,
            $existingDates
        ) {
            foreach (
                CarbonPeriod::create(
                    $start->copy(),
                    $end->copy()
                ) as $date
            ) {
                $dateString = $date->toDateString();

                if (
                    isset($existingDates[$dateString])
                    && !$overwrite
                ) {
                    continue;
                }

                EmployeeRoster::query()->updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'duty_date' => $dateString,
                    ],
                    [
                        'status' => 'working',
                        'shift_name' => null,
                        'start_time' => null,
                        'end_time' => null,
                        'note' => null,
                        'created_by' => $authUser?->id,
                    ]
                );
            }
        });

        return back()->with(
            'success',
            'Monthly default roster generated successfully.'
        );
    }
}