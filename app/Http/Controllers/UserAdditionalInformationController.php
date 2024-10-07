<?php

namespace App\Http\Controllers;

use App\Models\UserAdditionalInformation;
use Illuminate\Http\Request;

class UserAdditionalInformationController extends Controller
{
    public function create(){
        return view('dashboard.user.infoCreate');
    }

    public function store(Request $request){
        $request->validate([
           'phone' => 'required',
           'email' => 'required',
           'address' => 'required',
        ]);
        UserAdditionalInformation::create($request->all() + ['user_id' => auth()->user()->id]);
        return redirect()->route('userprofile', ['user' => auth()->user()->id])->with('success', 'Information stored successfully');
    }
}
