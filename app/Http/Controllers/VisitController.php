<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(){
        if (auth()->user()->hasRole('super_admin')){
            $visits = Visit::all();
        }elseif(auth()->user()->hasRole('admin')){
            $userIds = auth()->user()->office->users()->pluck('id');
            $visits = Visit::whereIn('user_id', $userIds)->get();
        }else{
            $visits = auth()->user()->visits;
        }
        return view('dashboard.visit.index', compact('visits'));
    }

    public function create(){
        return view('dashboard.visit.create');
    }

    public function store(Request $request){
        $request->validate([
            'address' => 'required',
            'expense' => 'numeric',
            'description' => 'required',
            'photo' => '',
            'expense_attachment' => '',
            'latitude' => '',
            'longitude' => '',
        ]);
//        dd($request->all());

        $visit = Visit::create([
            'address' => $request->address,
            'expense' => $request->expense ?? null,
            'description' => $request->description,
            'user_id' => auth()->user()->id,
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
        ]);
        if ($request->hasFile('photo')){
            $photo = $request->file('photo')->store('visits', 'public');
            $visit->photo = str_replace('public/', '', $photo);
        }
        if ($request->hasFile('expense_attachment')){
            $expenseAttachment = $request->file('expense_attachment')->store('expenses', 'public');
            $visit->expense_attachment = str_replace('public/', '', $expenseAttachment);

        }
        $visit->save();
        return redirect('visit')->with('success');
    }

    public function status(Visit $visit){
        if ($visit->status == 'approved'){
            $visit->update(['status' => 'rejected']);
        }
        else{
            $visit->update(['status' => 'approved']);
        }
        return back()->with('success', 'status changed successfully');
    }

    public function pay(Visit $visit){
        $status = $visit->update(['expense_paid' => '1', 'paid_by' => auth()->user()->id]);

        if ($status){
            request()->session()->flash('success', 'Mark as Paid successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return back();
    }
}
