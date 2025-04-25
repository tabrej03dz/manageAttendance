<?php

namespace App\Http\Controllers;

use App\Models\HalfDay;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HalfDayController extends Controller
{
    public function index(Request $request, $id = null){
        $query = HalfDay::query();
        if ($request->status){
            $status = $request->status;
            $query->where('status', $status);
        }else{
            $status = '';
        }
        $month = $request->month ? Carbon::createFromFormat('Y-m', $request->month) : Carbon::today();

        if ($id){
            $query->where('id', $id);
        }else{
            $query->whereMonth('date', $month->format('m'))
                ->whereYear('date', $month->format('Y'));
        }
        $halfDays = $query->get();
        $month = $month->format('Y-m');

        return view('dashboard.half-day.index', compact('halfDays', 'month', 'status'));
    }

    public function create(){
        return view('dashboard.half-day.create');
    }

    public function store(Request $request){
        $request->validate([
            'date' => 'required|date',
            'reason' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $halfDay = HalfDay::create([
            'user_id' => auth()->user()->id,
            'date' => $request->date ?? today(),
            'reason' => $request->reason,
        ]);
        if ($request->hasFile('image')){
            $halfDay->update(['image'=> str_replace('public/', '', $request->file('image')->store('public/images'))]);
        }

        return redirect('half-day')->with('success', 'Requested successfully');
    }

    public function status(Request $request, HalfDay $halfDay){
        $halfDay->update([
            'status' => $request->status,
            'respond_by' => auth()->user()->id,
        ]);

        return back()->with('success', $request->status.' successfully');
    }
}
