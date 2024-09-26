<?php

namespace App\Http\Controllers;

use App\Models\Off;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OffController extends Controller
{
    public function index(){
        $offs = Off::whereDate('date', '>=', Carbon::today())->where('office_id', auth()->user()->office_id)->get();
        return view('dashboard.off.index', compact('offs'));
    }
    public function create(){
        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
        }else{
            $offices = null;
        }
        return view('dashboard.off.create', compact('offices'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required',
            'date' => 'required|date',
            'description' => '',
            'office_id' => 'sometimes',
        ]);

        $status = Off::create($request->all() + ['office_id' => $request->office_id ?? auth()->user()->office_id]);
        if ($status) {
            request()->session()->flash('success', 'Off Created successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('off');
    }

    public function edit(Off $off){
        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
        }else{
            $offices = null;
        }
        return view('dashboard.off.edit', compact('offices', 'off'));
    }

    public function update(Request $request, Off $off){
        $request->validate([
            'title' => 'required',
            'date' => 'required|date',
            'description' => '',
            'office_id' => 'sometimes',
        ]);
        $status = $off->update($request->all());
        if ($status){
            request()->session()->flash('success', 'Updated successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('off');
    }

    public function delete(Off $off){
        $status = $off->delete();
        if ($status){
            \request()->session()->flash('success', 'Deleted successfully');
        }else{
            \request()->session()->flash('error', 'Failed, Try again!');
        }
    }
}