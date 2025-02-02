<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Off;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $offices = [];

//            if ($user->hasRole('super_admin')) {
//                // Fetch all offices for super admin
//                $offices = Office::all();
//            } elseif ($user->hasRole('owner')) {
//                // Fetch offices owned by the user
//                $offices = Office::where('owner_id', $user->id)->get();
//            } else {
//                // Fetch the specific office associated with the user
//                $offices = Office::where('id', $user->office_id)->get();
//            }
            if ($user->hasRole('owner')){
                $offices = $user->offices;
            }else{
                $offices = Office::all();
            }

            // Return the list of offices as a JSON response
            return response()->json([
                'message' => 'Offices retrieved successfully.',
                'offices' => $offices,
            ], 200);

        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric',
            'address' => 'required',
            'number_of_employees' => 'nullable|integer',
        ]);

        try {
            // Create the office
            $office = Office::create($validatedData);

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Office created successfully.',
                'office' => $office,
            ], 201);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric',
            'number_of_employees' => 'nullable|integer',
        ]);

        try {
            // Find the office by ID
            $office = Office::find($id);

            if (!$office) {
                return response()->json(['error' => 'Office not found.'], 404);
            }

            // Update the office with validated data
            $office->update($validatedData);

            // Return a success response
            return response()->json([
                'message' => 'Office updated successfully.',
                'office' => $office,
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Find the office by ID
            $office = Office::find($id);

            // Check if the office exists
            if (!$office) {
                return response()->json(['error' => 'Office not found.'], 404);
            }

            // Delete the office
            $office->delete();

            // Return a success response
            return response()->json([
                'message' => 'Office deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }




}
