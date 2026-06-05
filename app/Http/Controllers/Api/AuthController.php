<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class AuthController extends Controller
{


    public function register(Request $request)
    {

        // ✅ Validate input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ]);
        


        // ✅ Create user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // ✅ Optional: default role assign
        // Spatie permission use kar rahe ho to
        $user->assignRole('employee'); // change role if needed

        // ✅ Create token
        $token = $user->createToken('authToken')->plainTextToken;

        // ✅ Get roles & permissions
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions();

        // ✅ Response
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
            'roles' => $roles,
            'permissions' => $permissions,
        ], 201);
    }

    // public function login(Request $request)
    // {
    //     // Validate input
    //     $request->validate([
    //         'email' => 'required|string',
    //         'password' => 'required|string|min:6',
    //     ]);

    //     $login = $request->email;
    //     // Attempt to authenticate the user
    //     $user = User::where('email', $login)
    //         ->orWhere('phone', $login)
    //         ->first();
    //     //  return response($user);

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     // Generate a token for the user
    //     $token = $user->createToken('authToken')->plainTextToken;
    //     $user->office = $user->office;
    //     $roles = $user->getRoleNames();
    //     $permissions = $user->getAllPermissions();
    //     return response()->json([
    //         'user' => $user,
    //         'token' => $token,
    //         'roles' => $roles,
    //         'permissions' => $permissions,
    //     ], 200);
    // }


    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required','string'],   // email ya phone dono aayega is field me
            'password' => ['required','string','min:6'],
        ]);

        $login = trim((string) $request->email);

        $user = User::query()
            ->where('email', $login)
            ->orWhere('phone', $login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // ✅ token
        $token = $user->createToken('authToken')->plainTextToken;

        // ✅ office load (agar relation hai)
        $user->load('office');

        // ✅ roles & permissions (names only)
        $roles = $user->getRoleNames()->values(); // ["employee"]
        $permissions = $user->getAllPermissions()->pluck('name')->values(); // ["check-in", "check-out"]

        // ✅ user object ko clean rakho (duplicate relations hide)
        $userData = $user->makeHidden([
            'roles',
            'permissions',
            'password',
            'remember_token',
        ])->toArray();

        // ✅ single place pe roles/permissions attach
        $userData['roles'] = $roles;
        $userData['permissions'] = $permissions;

        return response()->json([
            'user'  => $userData,
            'token' => $token,
        ], 200);
    }


//     public function login(Request $request)
// {
//     $request->validate([
//         'email'    => ['required', 'string'], // email ya phone
//         'password' => ['nullable', 'string', 'min:6'],
//     ]);

//     $login = trim((string) $request->email);

//     $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

//     $user = User::with('office')
//         ->where($field, $login)
//         ->where('status', '1')
//         ->first();

//     if (!$user) {
//         return response()->json([
//             'message' => 'Invalid credentials'
//         ], 401);
//     }

//     $otpEnabled = optional($user->office)->otp_enable == 1;

//     // OTP enabled + phone login = password check nahi hoga
//     if ($otpEnabled && $field === 'phone') {
//         $otp = rand(1000, 9999);

//         $user->update([
//             'otp' => $otp,
//         ]);

//         $this->sendLoginOtp($user->phone, $otp);

//         return response()->json([
//             'message' => 'OTP sent successfully',
//             'otp_required' => true,
//             'user_id' => $user->id,
//         ], 200);
//     }

//     // OTP disabled = email/phone + password login
//     if (!$request->filled('password') || !Hash::check($request->password, $user->password)) {
//         return response()->json([
//             'message' => 'Invalid credentials'
//         ], 401);
//     }

//     return $this->sendLoginSuccessResponse($user);
// }



// public function verifyLoginOtp(Request $request)
// {
//     $request->validate([
//         'user_id' => ['required', 'exists:users,id'],
//         'otp'     => ['required', 'digits:4'],
//     ]);

//     $user = User::with('office')
//         ->where('status', '1')
//         ->find($request->user_id);

//     if (!$user) {
//         return response()->json([
//             'message' => 'Invalid user'
//         ], 401);
//     }

//     if (!$user->otp) {
//         return response()->json([
//             'message' => 'OTP not found. Please login again.'
//         ], 422);
//     }

//     if ((string) $user->otp !== (string) $request->otp) {
//         return response()->json([
//             'message' => 'Invalid OTP'
//         ], 422);
//     }

//     $user->update([
//         'otp' => null,
//     ]);

//     return $this->sendLoginSuccessResponse($user);
// }


//     private function sendLoginSuccessResponse($user)
//     {
//         $token = $user->createToken('authToken')->plainTextToken;

//         $user->load('office');

//         $roles = $user->getRoleNames()->values();

//         $permissions = $user->getAllPermissions()->pluck('name')->values();

//         $userData = $user->makeHidden([
//             'roles',
//             'permissions',
//             'password',
//             'remember_token',
//         ])->toArray();

//         $userData['roles'] = $roles;
//         $userData['permissions'] = $permissions;

//         return response()->json([
//             'user'  => $userData,
//             'token' => $token,
//         ], 200);
//     }


    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    public function deleteAccount(Request $request)
    {
        // ✅ Validate password
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = $request->user();

        // ✅ Check password
        if (!Hash::check($request->password, $user->password)) {

            return response()->json([
                'status' => false,
                'message' => 'Password is incorrect'
            ], 401);

        }

        DB::beginTransaction();

        try {

            // ✅ Delete all tokens (logout everywhere)
            $user->tokens()->delete();

            // ✅ OPTIONAL: Delete related data (if exists in your system)

            // Example:
            // $user->attendanceRecords()->delete();
            // $user->leaveRequests()->delete();
            // $user->notifications()->delete();


            // ✅ Delete user
            $user->delete();


            DB::commit();


            return response()->json([

                'status' => true,
                'message' => 'Account deleted successfully'

            ], 200);


        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([

                'status' => false,
                'message' => 'Account delete failed',
                'error' => $e->getMessage()

            ], 500);

        }

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

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required','string','min:6'],
            'password'         => ['required','string','min:6','confirmed'], // password_confirmation required
        ]);

        // ✅ current password match?
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        // ✅ same password prevent (optional but good)
        if (Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'New password cannot be same as current password',
            ], 422);
        }

        // ✅ update password
        $user->password = Hash::make($data['password']);
        $user->save();

        // ✅ logout from all devices (optional)
        // $user->tokens()->delete();

        // ✅ OR only current token revoke (optional)
        // $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully',
        ], 200);
    }
}
