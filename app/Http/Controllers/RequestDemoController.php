<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestDemoRequest;
use App\Mail\AdminNewRequestDemoMail;
use App\Mail\RequestDemoMail;
use App\Models\RequestDemo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RequestDemoController extends Controller
{
    public function index()
    {
        return view('request.index');
    }


    public function store(RequestDemoRequest $request)
    {
        // Debug request data
        // dd($request->all());

        // Store data in the database
        $requestDemo = RequestDemo::create($request->all());

        // Send email if email is provided
        if ($request->has('email') && $request->email) {
            Mail::to($request->email)->send(new RequestDemoMail($requestDemo));
        } else {
            Log::warning('Request submitted without email.', ['request' => $request->all()]);
        }

        // Send email to the admin
        $adminEmail = 'realvictorygroups@gmail.com'; // Replace with the admin's email address
        Mail::to($adminEmail)->send(new AdminNewRequestDemoMail($requestDemo));

        // Redirect with success message
        return redirect()->route('thankyoupage')->with('success', 'Request demo submitted successfully!');
    }
}
