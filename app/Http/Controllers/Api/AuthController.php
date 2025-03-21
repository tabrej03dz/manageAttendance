<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;






class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        // Attempt to authenticate the user
        $user = User::where('email', $request->email)->first();
//        return response($user);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate a token for the user
        $token = $user->createToken('authToken')->plainTextToken;
        $user->office = $user->office;
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions();
        return response()->json([
            'user' => $user,
            'token' => $token,
            'roles' => $roles,
            'permissions' => $permissions,
        ], 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }


    public function tokenLogin(Request $request)
    {
        // Validate input
        $request->validate([
            'token' => 'required|string',
        ]);
        // Extract the token from the request
        $token = $request->token;


        // Attempt to authenticate the user using the token
        $user = PersonalAccessToken::findToken($token)?->tokenable;


        if (!$user || !Auth::loginUsingId($user->id)) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }

        $user->office = $user->office;
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions();
        return response()->json([
            'user' => $user,
            'message' => 'Authenticated successfully using token',
            'roles' => $roles,
            'permissions' => $permissions,
        ], 200);
    }
}
