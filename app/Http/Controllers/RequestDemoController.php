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
            $appointment->where('owner_name', 'like', "%$keyword%");
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
        }

        Mail::to('realvictorygroups@gmail.com')->send(new AdminNewRequestDemoMail($requestDemo));

        return response()->json(['success' => true, 'message' => 'Request demo submitted successfully!']);
    }


    public function delete(RequestDemo $appointment)
    {
        $appointment->delete(); // Delete the record
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

}
