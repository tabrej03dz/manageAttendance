<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    public function ownerPlan(Owner $owner){
        $plans = $owner->plans;
        return view('dashboard.plan.index', compact('plans'));
    }
}
