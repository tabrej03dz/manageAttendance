<?php

namespace App\Http\Controllers;

use App\Models\HalfDay;
use Illuminate\Http\Request;

class HalfDayController extends Controller
{
    public function index(){
        $halfDays = HalfDay::orderBy('created_at', 'desc')->paginate(20);
        return view('dashboard.half-day.index', compact('halfDays'));
    }
}
