<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // Get the authenticated user
        $employees = HomeController::employeeList($user); // Fetch the employee list

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'user' => $user,
                'employees' => $employees,
            ]
        ], 200); // Return a 200 HTTP status code
    }
}
