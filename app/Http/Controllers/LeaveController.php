<?php

namespace App\Http\Controllers;


use App\Mail\LeaveRequest;
use App\Mail\LeaveResponse;
use App\Models\LeaveImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Mail;


class LeaveController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();
        $query = Leave::query();
//        if ($user->hasRole('super_admin')) {
//            $query->where(function ($q) {
//                $q->whereDate('start_date', '>=', today())
//                    ->orWhereDate('end_date', '>=', today());
//            });
//        }
//        elseif($user->hasRole('owner')){
//            $officeIds = $user->offices()->pluck('id');
//            $userIds = User::whereIn('office_id', $officeIds)->pluck('id');
//            $query->whereIn('user_id', $userIds)
//                ->where(function ($q) {
//                    $q->whereDate('start_date', '>=', today())
//                        ->orWhereDate('end_date', '>=', today());
//                });
//
//        }
//        elseif ($user->hasRole('admin')) {
//            $userIds = $user->office->users->pluck('id');
//            $query->whereIn('user_id', $userIds)
//                ->where(function ($q) {
//                    $q->whereDate('start_date', '>=', today())
//                        ->orWhereDate('end_date', '>=', today());
//                });
//        }
//
//
//        elseif ($user->hasRole('team_leader')) {
//            $userIds = $user->members->pluck('id');
//            $query->whereIn('user_id', $userIds)
//                ->where(function ($q) {
//                    $q->whereDate('start_date', '>=', today())
//                        ->orWhereDate('end_date', '>=', today());
//                });
//        }else{
//
//            $query->where('user_id', $user->id)
//                ->where(function ($q) {
//                    $q->whereDate('start_date', '>=', today())
//                        ->orWhereDate('end_date', '>=', today());
//                });
//        }
        $employeesIds = HomeController::employeeList()->pluck('id');
//        dd($employeesIds);
        $query->whereIn('user_id', $employeesIds);
        if ($request->status){
            $query->where('status', $request->status);
        }
        $leaves = $query->orderBy('start_date', 'desc')->get();
        return view('dashboard.leave.index', compact('leaves'));
    }


    public function create(){
        return view('dashboard.leave.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'is_paid' => 'required',
            'start_date'=> 'required',
            'end_date' => '',
            'reason' => '',
            'image.*' => 'required',
        ]);
        $user = auth()->user();

        if ($request->start_date && $request->end_date){
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            // Calculate the difference in days
            $dayCount = $startDate->diffInDays($endDate);
        }else{
            $dayCount = null;
        }
        $leave = Leave::create($request->all() + ['user_id' => $user->id, 'office_id' => $user->office->id, 'day_count' => $dayCount ?? 1]);

        if ($request->hasFile('image')){
            foreach ($request->image as $image){
                $path = $image->store('public/leave');
                LeaveImage::create([
                    'leave_id' => $leave->id,
                    'path' => str_replace('public', '', $path),
                ]);
            }
        }

        $admin = User::where('office_id', $user->office->id)
            ->whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })
            ->first();
        $superAdmin = User::role('super_admin')->first();

        $teamLeader = $user->teamLeader;
        Mail::to($superAdmin->email)->send(new LeaveRequest($leave));
        if($admin){
            Mail::to($admin->email)->send(new LeaveRequest($leave));
        }

        if ($teamLeader){
            Mail::to($teamLeader->email)->send(new LeaveRequest($leave));
        }

        return redirect('userprofile/' . $user->id)->with('success', 'Leave request taken successfully and notification sent to admin.');
    }

    public function status(Leave $leave, $status, $type = null){
        $leave->update(['status' => $status, 'responses_by' => auth()->user()->id, 'approve_as' => $type]);
        Mail::to($leave->user->email1 ?? $leave->user->email)->send(new LeaveResponse($leave));
        return back()->with('success', 'Status updated successfully');
    }

    public function show($id){
        $leave = Leave::find($id);
        return view('dashboard.leave.show', compact('leave'));
    }

    public function response(Request $request, Leave $leave){
//        dd($request->all());
        $leave->update($request->all() + ['responses_by' => auth()->user()->id]);
        Mail::to($leave->user->email1 ?? $leave->user->email)->send(new LeaveResponse($leave));
        return back()->with('success', 'operation successfully');
    }
}
