<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(){

        $visits = Visit::all();
        return view('dashboard.visit.index');
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
        $photo = $request->file('photo')->store('visits', 'public');
        $expenseAttachment = $request->file('expense_attachment')->store('expenses', 'public');
        $status = Visit::create([
            'address' => $request->address,
            'expense' => $request->expense ?? null,
            'description' => $request->description,
            'photo' => str_replace('public/', '', $photo),
            'expense_attachment' => str_replace('public/', '', $expenseAttachment),
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
        ]);
        if ($status){
            request()->session()->flash('success', 'Visit created successfully');
        }
    }
}
