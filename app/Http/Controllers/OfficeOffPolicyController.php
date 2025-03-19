<?php

namespace App\Http\Controllers;

use App\Models\OfficeOffPolicy;
use Illuminate\Http\Request;

class OfficeOffPolicyController extends Controller
{
    public function index(){
        $offPolicies = OfficeOffPolicy::where('office_id', auth()->user()->id)->get();
        return view('dashboard.offPolicy.index', compact('offPolicies'));
    }

    public function create(){
        return view('dashboard.offPolicy.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'weekly_off_policy' => 'required|array',
        ]);

        OfficeOffPolicy::create([
            'office_id' => $request->office_id,
            'weekly_off_policy' => json_encode($request->weekly_off_policy),
        ]);

        return redirect()->back()->with('success', 'Leave policy created successfully!');
    }
}
