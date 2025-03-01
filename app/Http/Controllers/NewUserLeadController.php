<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreNewUserLeadRequest;
use App\Models\NewUserLead;
class NewUserLeadController extends Controller
{
    public function store(StoreNewUserLeadRequest $request)
    {
        dd($request);
        $lead = NewUserLead::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'New user lead created successfully!',
            'data'    => $lead
        ]);
    }
}
