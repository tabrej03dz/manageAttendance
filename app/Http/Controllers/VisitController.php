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
}
