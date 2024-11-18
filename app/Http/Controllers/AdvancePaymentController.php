<?php

namespace App\Http\Controllers;

use App\Models\AdvancePayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class AdvancePaymentController extends Controller
{
    public function index(Request $request){
        $month = $request->month ? Carbon::make($request->month)->month : Carbon::today()->month;
        $year = $request->month ? Carbon::make($request->month)->year : Carbon::today()->year;
//        dd($month, $year);
        $payments = AdvancePayment::whereMonth('date', $month)->whereYear('date', $year)->get();
        return view('dashboard.advancePayment.index', compact('payments'));
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
}
