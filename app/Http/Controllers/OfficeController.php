<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index(){
        $offices = Office::all();
        return view('dashboard.office.index', compact('offices'));
    }

    public function create(){
        return view('dashboard.office.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => '',
        ]);

        Office::create($request->all());
        return redirect('office')->with('success', 'Office Created successfully');
    }

    public function edit(Office $office){
        return view('dashboard.office.edit', compact('office'));
    }

    public function update(Request $request, Office $office){
        $request->validate([
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => '',
        ]);

        $office->update($request->all());

        return redirect('office')->with('success', 'Updated successfully');
    }

    public function delete(Office $office){
        $office->delete();
        return back()->with('success', 'Deleted successfully');
    }
}
