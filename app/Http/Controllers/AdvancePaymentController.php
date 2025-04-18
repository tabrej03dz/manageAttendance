<?php

namespace App\Http\Controllers;

use App\Models\AdvancePayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class AdvancePaymentController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();
        $month = $request->month ? Carbon::make($request->month)->month : Carbon::today()->month;
        $year = $request->month ? Carbon::make($request->month)->year : Carbon::today()->year;
//        dd($month, $year);
        $selectedMonth = $request->month ?? Carbon::today()->format('Y-m');
        if ($user->hasRole('super_admin')){
            $payments = AdvancePayment::whereMonth('date', $month)->whereYear('date', $year)->get();
        }elseif($user->hasRole('owner')){
            $officeIds = $user->offices()->pluck('id');
            $employeeIds = User::whereIn('office_id', $officeIds)->pluck('id');
            $employeeIds->push($user->id);
            $payments = AdvancePayment::whereIn('user_id', $employeeIds)->whereMonth('date', $month)->whereYear('date', $year)->get();
        }elseif($user->hasRole('admin')){
            $employeeIds = $user->office->employees()->pluck('id');
            $employeeIds->push($user->id);
            $payments = AdvancePayment::whereIn('user_id', $employeeIds)->whereMonth('date', $month)->whereYear('date', $year)->get();
        }else{
            $payments = AdvancePayment::where('user_id', $user->id)->whereMonth('date', $month)->whereYear('date', $year)->get();
        }
        return view('dashboard.advancePayment.index', compact('payments', 'selectedMonth'));
    }

    public function create(User $user = null){
        $employees = HomeController::employeeList();
        return view('dashboard.advancePayment.create', compact('employees'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required',
            'description' => '',
            'amount' => 'required',
            'user_id' => 'required',

        ]);
        $status = AdvancePayment::create($request->all() + ['date' => Carbon::today()]);
        if ($status){
            request()->session()->flash('success', 'Advance Payment created successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('advance');
    }

    public function request(){
        return view('dashboard.advancePayment.request');
    }

    public function requestStore(Request $request){
        $request->validate([
            'title' => 'required',
            'amount' => 'required',
            'description'=> '',
        ]);

        AdvancePayment::create($request->all() + ['user_id' => auth()->user()->id, 'date' => Carbon::today()]);

        return redirect('advance');

    }

    public function status(Request $request, AdvancePayment $payment){
        $payment->update(['status' => $request->status, 'paid_by' => auth()->user()->id]);
        return back()->with('success', 'Status updated successfully');
    }
}
