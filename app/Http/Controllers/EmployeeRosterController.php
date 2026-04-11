<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRoster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EmployeeRosterController extends Controller
{
    /**
     * Roster list page
     * GET: /employee-rosters
     */
    // public function index(Request $request)
    // {
    //     $request->validate([
    //         'month' => ['nullable', 'date_format:Y-m'],
    //     ]);

    //     $month = $request->month ?: now()->format('Y-m');

    //     $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    //     $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    //     $employees = User::query()
    //         ->where('status', '1')
    //         ->orderBy('name')
    //         ->get(['id', 'name', 'email', 'photo']);

    //     $rosters = EmployeeRoster::query()
    //         ->whereBetween('duty_date', [$start->toDateString(), $end->toDateString()])
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
    //         $employeeRosters = $rosters->get($employee->id, collect())->keyBy(function ($item) {
    //             return Carbon::parse($item->duty_date)->toDateString();
    //         });

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

    //     return view('rosters.index', compact('month', 'days', 'rows'));
    // }

    //     public function ajaxUpsert(Request $request)
    // {
    //     $request->validate([
    //         'employee_id' => ['required', 'exists:users,id'],
    //         'duty_date'   => ['required', 'date'],
    //         'status'      => ['required', Rule::in(['working', 'off', 'half_day', 'leave'])],
    //     ]);

    //     $roster = EmployeeRoster::updateOrCreate(
    //         [
    //             'employee_id' => $request->employee_id,
    //             'duty_date'   => $request->duty_date,
    //         ],
    //         [
    //             'status'     => $request->status,
    //             'created_by' => auth()->id(),
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


    private function activeOfficeId(Request $request): ?int
    {
        return $request->user()?->activeOfficeId();
    }

    private function allowedOfficeIds(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        if ($user->hasRole('super_admin')) {
            $officeId = $user->activeOfficeId();
            return $officeId ? [$officeId] : [];
        }

        if ($user->hasRole('owner')) {
            $officeId = $user->activeOfficeId();
            return $officeId ? [$officeId] : [];
        }

        if ($user->hasRole('admin')) {
            return $user->office_id ? [$user->office_id] : [];
        }

        if ($user->office_id) {
            return [$user->office_id];
        }

        return [];
    }

    private function officeEmployeesQuery(Request $request)
    {
        $officeIds = $this->allowedOfficeIds($request);

        return User::query()->when(!empty($officeIds), function ($q) use ($officeIds) {
            $q->whereIn('office_id', $officeIds);
        }, function ($q) {
            $q->whereRaw('1 = 0');
        });
    }

    private function sortEmployeesHierarchically($employees)
    {
        $employees = collect($employees);

        $grouped = $employees->groupBy('team_leader_id');
        $sorted = collect();

        $appendChildren = function ($leaderId) use (&$appendChildren, $grouped, &$sorted) {
            if (!isset($grouped[$leaderId])) {
                return;
            }

            foreach ($grouped[$leaderId]->sortBy('name') as $employee) {
                $sorted->push($employee);
                $appendChildren($employee->id);
            }
        };

        if (isset($grouped[null])) {
            foreach ($grouped[null]->sortBy('name') as $employee) {
                $sorted->push($employee);
                $appendChildren($employee->id);
            }
        }

        $remaining = $employees->whereNotIn('id', $sorted->pluck('id'));

        foreach ($remaining->sortBy('name') as $employee) {
            $sorted->push($employee);
            $appendChildren($employee->id);
        }

        return $sorted->unique('id')->values();
    }

    public function index(Request $request)
    {
        $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = $request->month ?: now()->format('Y-m');

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // employee list same style as EmployeeController
        $employeesQuery = $this->officeEmployeesQuery($request);

        // default active employees only
        $employeesQuery->where('status', '1');

        $employees = $employeesQuery
            ->select(['id', 'name', 'email', 'photo', 'office_id', 'team_leader_id'])
            ->get();

        // same hierarchy order
        $employees = $this->sortEmployeesHierarchically($employees);

        $rosters = EmployeeRoster::query()
            ->whereBetween('duty_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->groupBy('employee_id');

        $days = [];
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $days[] = [
                'date'     => $date->toDateString(),
                'day'      => $date->format('d'),
                'day_name' => $date->format('D'),
                'is_today' => $date->isToday(),
            ];
        }

        $rows = [];

        foreach ($employees as $employee) {
            $employeeRosters = $rosters->get($employee->id, collect())->keyBy(function ($item) {
                return Carbon::parse($item->duty_date)->toDateString();
            });

            $items = [];
            foreach ($days as $day) {
                $record = $employeeRosters->get($day['date']);

                $items[] = [
                    'date'   => $day['date'],
                    'status' => $record->status ?? 'working',
                ];
            }

            $rows[] = [
                'employee' => $employee,
                'items'    => $items,
            ];
        }

        return view('rosters.index', compact('month', 'days', 'rows'));
    }

    public function ajaxUpsert(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'duty_date'   => ['required', 'date'],
            'status'      => ['required', Rule::in(['working', 'off', 'half_day', 'leave'])],
        ]);

        // security: selected employee must belong to allowed offices
        $allowedEmployeeIds = $this->officeEmployeesQuery($request)->pluck('id')->toArray();

        if (!in_array((int) $request->employee_id, $allowedEmployeeIds, true)) {
            return response()->json([
                'success' => false,
                'message' => 'This employee does not belong to your allowed office.',
            ], 403);
        }

        $roster = EmployeeRoster::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'duty_date'   => $request->duty_date,
            ],
            [
                'status'     => $request->status,
                'created_by' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Roster saved successfully.',
            'data'    => [
                'id'          => $roster->id,
                'employee_id' => $roster->employee_id,
                'duty_date'   => $roster->duty_date,
                'status'      => $roster->status,
            ],
        ]);
    }







    /**
     * Save single roster
     * POST: /employee-rosters/store
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'duty_date'   => ['required', 'date'],
            'status'      => ['required', Rule::in(['working', 'off', 'half_day', 'leave'])],
            'shift_name'  => ['nullable', 'string', 'max:100'],
            'start_time'  => ['nullable', 'date_format:H:i'],
            'end_time'    => ['nullable', 'date_format:H:i', 'after:start_time'],
            'note'        => ['nullable', 'string'],
        ]);

        EmployeeRoster::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'duty_date'   => $request->duty_date,
            ],
            [
                'status'     => $request->status,
                'shift_name' => $request->shift_name,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'note'       => $request->note,
                'created_by' => auth()->id(),
            ]
        );

        return redirect()
            ->back()
            ->with('success', 'Roster saved successfully.');
    }

    /**
     * Bulk roster save
     * POST: /employee-rosters/bulk-store
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'employee_id'         => ['required', 'exists:users,id'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.duty_date'   => ['required', 'date'],
            'items.*.status'      => ['required', Rule::in(['working', 'off', 'half_day', 'leave'])],
            'items.*.shift_name'  => ['nullable', 'string', 'max:100'],
            'items.*.start_time'  => ['nullable', 'date_format:H:i'],
            'items.*.end_time'    => ['nullable', 'date_format:H:i'],
            'items.*.note'        => ['nullable', 'string'],
        ]);

        foreach ($request->items as $item) {
            if (
                !empty($item['start_time']) &&
                !empty($item['end_time']) &&
                strtotime($item['end_time']) <= strtotime($item['start_time'])
            ) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'end_time' => 'End time must be greater than start time for date ' . $item['duty_date']
                    ])
                    ->withInput();
            }

            EmployeeRoster::updateOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'duty_date'   => $item['duty_date'],
                ],
                [
                    'status'     => $item['status'],
                    'shift_name' => $item['shift_name'] ?? null,
                    'start_time' => $item['start_time'] ?? null,
                    'end_time'   => $item['end_time'] ?? null,
                    'note'       => $item['note'] ?? null,
                    'created_by' => auth()->id(),
                ]
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Bulk roster saved successfully.');
    }

    /**
     * Monthly roster list
     * GET: /employee-rosters/monthly?month=2026-04
     */
    public function monthly(Request $request)
    {
        $request->validate([
            'month'       => ['required', 'date_format:Y-m'],
            'employee_id' => ['nullable', 'exists:users,id'],
        ]);

        $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

        $query = EmployeeRoster::with(['employee:id,name'])
            ->whereBetween('duty_date', [$start->toDateString(), $end->toDateString()]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $records = $query->orderBy('employee_id')
            ->orderBy('duty_date')
            ->get();

        $employees = User::select('id', 'name')
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        return view('rosters.monthly', [
            'records'   => $records,
            'month'     => $request->month,
            'employees' => $employees,
        ]);
    }

    /**
     * Employee monthly roster
     * GET: /employee-rosters/employee/{employee}?month=2026-04
     */
    public function employeeRoster(Request $request, $employee)
    {
        $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $employeeData = User::select('id', 'name')->findOrFail($employee);

        $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

        $records = EmployeeRoster::where('employee_id', $employee)
            ->whereBetween('duty_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('duty_date')
            ->get();

        return view('rosters.employee-monthly', [
            'employee' => $employeeData,
            'records'  => $records,
            'month'    => $request->month,
        ]);
    }

    /**
     * Month grid page
     * GET: /employee-rosters/month-grid?month=2026-04
     */
    public function monthGrid(Request $request)
    {
        $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

        $employees = User::select('id', 'name')
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        $rosters = EmployeeRoster::whereBetween('duty_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('employee_id');

        $days = [];
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $days[] = [
                'date'       => $date->toDateString(),
                'day'        => $date->format('d'),
                'day_name'   => $date->format('D'),
                'day_number' => $date->day,
            ];
        }

        $statusShortMap = [
            'working'  => 'P',
            'off'      => 'O',
            'half_day' => 'H',
            'leave'    => 'L',
        ];

        $rows = [];

        foreach ($employees as $employee) {
            $employeeRosters = $rosters->get($employee->id, collect())->keyBy(function ($item) {
                return Carbon::parse($item->duty_date)->toDateString();
            });

            $items = [];

            foreach ($days as $day) {
                $record = $employeeRosters->get($day['date']);
                $status = $record->status ?? 'working';

                $items[] = [
                    'date'         => $day['date'],
                    'status'       => $status,
                    'status_short' => $statusShortMap[$status] ?? 'P',
                    'shift_name'   => $record->shift_name ?? null,
                    'start_time'   => $record->start_time ?? null,
                    'end_time'     => $record->end_time ?? null,
                    'note'         => $record->note ?? null,
                ];
            }

            $rows[] = [
                'employee_id'   => $employee->id,
                'employee_name' => $employee->name,
                'items'         => $items,
            ];
        }

        return view('rosters.month-grid', [
            'month' => $request->month,
            'days'  => $days,
            'rows'  => $rows,
        ]);
    }

    /**
     * Delete roster
     * POST: /employee-rosters/delete
     */
    public function delete(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'duty_date'   => ['required', 'date'],
        ]);

        $roster = EmployeeRoster::where('employee_id', $request->employee_id)
            ->whereDate('duty_date', $request->duty_date)
            ->first();

        if (!$roster) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Roster not found.']);
        }

        $roster->delete();

        return redirect()
            ->back()
            ->with('success', 'Roster deleted successfully.');
    }

    /**
     * Show today's roster status page
     * GET: /employee-rosters/today-status?employee_id=1
     */
    public function todayStatus(Request $request)
    {
        $request->validate([
            'employee_id' => ['nullable', 'exists:users,id'],
        ]);

        $employeeId = $request->employee_id ?: auth()->id();
        $today = now()->toDateString();

        $roster = EmployeeRoster::with('employee:id,name')
            ->where('employee_id', $employeeId)
            ->whereDate('duty_date', $today)
            ->first();

        $employee = $roster->employee ?? User::select('id', 'name')->find($employeeId);

        $data = [
            'employee_id'  => $employeeId,
            'date'         => $today,
            'status'       => $roster->status ?? 'working',
            'status_short' => match ($roster->status ?? 'working') {
                'off'      => 'O',
                'leave'    => 'L',
                'half_day' => 'H',
                default    => 'P',
            },
            'shift_name'   => $roster->shift_name ?? null,
            'start_time'   => $roster->start_time ?? null,
            'end_time'     => $roster->end_time ?? null,
            'note'         => $roster->note ?? null,
            'employee'     => $employee,
        ];

        return view('rosters.today-status', compact('data'));
    }

    /**
     * Generate full month default working roster
     * POST: /employee-rosters/generate-month
     */
    public function generateMonth(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'month'       => ['required', 'date_format:Y-m'],
            'overwrite'   => ['nullable', 'boolean'],
        ]);

        $overwrite = $request->boolean('overwrite', false);

        $start = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $existing = EmployeeRoster::where('employee_id', $request->employee_id)
                ->whereDate('duty_date', $date->toDateString())
                ->first();

            if ($existing && !$overwrite) {
                continue;
            }

            EmployeeRoster::updateOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'duty_date'   => $date->toDateString(),
                ],
                [
                    'status'     => 'working',
                    'shift_name' => null,
                    'start_time' => null,
                    'end_time'   => null,
                    'note'       => null,
                    'created_by' => auth()->id(),
                ]
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Monthly default roster generated successfully.');
    }
}