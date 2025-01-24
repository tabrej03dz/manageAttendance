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


    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $appointment = RequestDemo::query();

        if (!empty($keyword)) {
            $appointment->where('title', 'like', "%$keyword%");
        }

        // Order records by the latest created_at
        $appointmentData = $appointment->orderBy('created_at', 'desc')->paginate(15);

        return view('request.index', compact('appointmentData'));
    }


    public function store(RequestDemoRequest $request)
    {

        $requestDemo = RequestDemo::create($request->all());


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

    public function delete(RequestDemo $appointment)
    {
        $appointment->delete(); // Delete the record
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

}
