<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OldRecordController extends Controller
{
    private function activeOfficeId(Request $request): ?int
    {
        return $request->user()?->activeOfficeId();
    }

    private function officeEmployeeIds(Request $request): array
    {
        $officeId = $this->activeOfficeId($request);

        if (!$officeId) {
            return [];
        }

        return User::where('office_id', $officeId)->pluck('id')->toArray();
    }

    public function index(Request $request)
    {
        $cutoffDate = Carbon::now()->subYear()->startOfDay();

        $officeId = $this->activeOfficeId($request);
        $employeeIds = $this->officeEmployeeIds($request);

        $users = User::where('office_id', $officeId)
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        $query = AttendanceRecord::with(['user', 'checkInBy', 'checkOutBy'])
            ->where('created_at', '<', $cutoffDate);

        if (!empty($employeeIds)) {
            $query->whereIn('user_id', $employeeIds);
        }

        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $records = $query->latest('created_at')->paginate(25)->withQueryString();

        $totalOldRecords = AttendanceRecord::where('created_at', '<', $cutoffDate)
            ->when(!empty($employeeIds), fn ($q) => $q->whereIn('user_id', $employeeIds))
            ->count();

        return view('dashboard.old_attendance.index', compact(
            'records',
            'users',
            'cutoffDate',
            'totalOldRecords'
        ));
    }

    public function show(Request $request, AttendanceRecord $record)
    {
        $cutoffDate = Carbon::now()->subYear()->startOfDay();

        if ($record->created_at >= $cutoffDate) {
            abort(404);
        }

        $employeeIds = $this->officeEmployeeIds($request);

        if (!empty($employeeIds) && !in_array($record->user_id, $employeeIds)) {
            abort(403);
        }

        $record->load(['user', 'checkInBy', 'checkOutBy', 'notedBy']);

        return view('dashboard.old_attendance.show', compact('record'));
    }

    public function destroy(Request $request, AttendanceRecord $record)
    {
        $cutoffDate = Carbon::now()->subYear()->startOfDay();

        if ($record->created_at >= $cutoffDate) {
            return back()->with('error', 'Only records older than 1 year can be deleted.');
        }

        $employeeIds = $this->officeEmployeeIds($request);

        if (!empty($employeeIds) && !in_array($record->user_id, $employeeIds)) {
            abort(403);
        }

        $this->deleteRecordImages($record);

        $record->delete();

        return redirect()->route('old-attendance.index')
            ->with('success', 'Old attendance record deleted successfully.');
    }

    public function destroyOldRecords(Request $request)
    {
        $cutoffDate = Carbon::now()->subYear()->startOfDay();
        $employeeIds = $this->officeEmployeeIds($request);

        $query = AttendanceRecord::where('created_at', '<', $cutoffDate);

        if (!empty($employeeIds)) {
            $query->whereIn('user_id', $employeeIds);
        }

        $records = $query->get();

        foreach ($records as $record) {
            $this->deleteRecordImages($record);
            $record->delete();
        }

        return redirect()->route('old-attendance.index')
            ->with('success', $records->count() . ' old attendance records deleted successfully.');
    }

    private function deleteRecordImages(AttendanceRecord $record): void
    {
        if ($record->check_in_image && Storage::disk('public')->exists($record->check_in_image)) {
            Storage::disk('public')->delete($record->check_in_image);
        }

        if ($record->check_out_image && Storage::disk('public')->exists($record->check_out_image)) {
            Storage::disk('public')->delete($record->check_out_image);
        }
    }
}
