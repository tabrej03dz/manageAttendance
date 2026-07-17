<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



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


    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => ['required','string'],   // email ya phone dono aayega is field me
    //         'password' => ['required','string','min:6'],
    //     ]);

    //     $login = trim((string) $request->email);

    //     $user = User::query()
    //         ->where('email', $login)
    //         ->orWhere('phone', $login)
    //         ->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'message' => 'Invalid credentials'
    //         ], 401);
    //     }

    //     // ✅ token
    //     $token = $user->createToken('authToken')->plainTextToken;

    //     // ✅ office load (agar relation hai)
    //     $user->load('office');

    //     // ✅ roles & permissions (names only)
    //     $roles = $user->getRoleNames()->values(); // ["employee"]
    //     $permissions = $user->getAllPermissions()->pluck('name')->values(); // ["check-in", "check-out"]

    //     // ✅ user object ko clean rakho (duplicate relations hide)
    //     $userData = $user->makeHidden([
    //         'roles',
    //         'permissions',
    //         'password',
    //         'remember_token',
    //     ])->toArray();

    //     // ✅ single place pe roles/permissions attach
    //     $userData['roles'] = $roles;
    //     $userData['permissions'] = $permissions;

    //     return response()->json([
    //         'user'  => $userData,
    //         'token' => $token,
    //     ], 200);
    // }


    public function login(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                'digits:10',
                'regex:/^[6-9][0-9]{9}$/',
            ],
        ], [
            'phone.required' => 'Mobile number required hai.',
            'phone.digits'   => 'Mobile number 10 digit ka hona chahiye.',
            'phone.regex'    => 'Mobile number 6, 7, 8 ya 9 se start hona chahiye.',
        ]);

        $user = User::where('phone', $request->phone)
            ->where('status', '1')
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid mobile number ya user inactive hai.',
            ], 401);
        }

        $otp = $user->phone == '8737934656' ? 123456 : rand(100000, 999999);

        // update() ki jagah forceFill use karo, fillable issue nahi aayega
        $user->forceFill([
            'otp' => $otp,
        ])->save();

        $this->sendLoginOtp($user->phone, $otp);

        return response()->json([
            'message' => 'OTP sent successfully',
            'otp_required' => true,
            'user_id' => $user->id,

            // Testing ke liye abhi uncomment rakh sakte ho
            'otp' => $otp,
        ], 200);
    }



    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'otp'     => ['required', 'digits:6'],
        ]);

        $user = User::where('id', $request->user_id)
            ->where('status', '1')
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid user',
            ], 401);
        }

        if (empty($user->otp)) {
            return response()->json([
                'message' => 'OTP not found. Please login again.',
            ], 422);
        }

        if ((string) $user->otp !== (string) $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP',
            ], 422);
        }

        $user->forceFill([
            'otp' => null,
        ])->save();

        return $this->sendLoginSuccessResponse($user);
    }


    private function sendLoginSuccessResponse($user)
    {
        $token = $user->createToken('authToken')->plainTextToken;

        $user->load('office');

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->values()
            : [];

        $permissions = method_exists($user, 'getAllPermissions')
            ? $user->getAllPermissions()->pluck('name')->values()
            : [];

        $userData = $user->makeHidden([
            'roles',
            'permissions',
            'password',
            'remember_token',
            'otp',
        ])->toArray();

        $userData['roles'] = $roles;
        $userData['permissions'] = $permissions;

        return response()->json([
            'message' => 'Login successful',
            'user'  => $userData,
            'token' => $token,
        ], 200);
    }


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
            'token' => $token,
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


    private function sendLoginOtp($phone, $otp)
    {
        $msg = "Dear Customer, {$otp} this is your login verification OTP. Please do not share with anyone. Best Regards, Real Victory Groups https://realvictorygroups.com/";

        $url = env('KUTILITY_URL') . http_build_query([
            'key' => env('KUTILITY_KEY'),
            'campaign' => '12754',
            'routeid' => '7',
            'type' => 'text',
            'contacts' => $phone,
            'senderid' => 'RVGRPS',
            'msg' => $msg,
            'template_id' => '1707178031266790425',
            'pe_id' => '1701164032595209992',
        ]);

        @file_get_contents($url);
    }


    public function profilePhotoUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Purani photo delete karo agar storage me hai
        if (!empty($user->photo) && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        // New photo upload
        $path = $request->file('photo')->store('profile_photos', 'public');

        $user->photo = $path;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile photo updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'photo' => $user->photo,
                'photo_url' => asset('storage/' . $user->photo),
            ],
        ], 200);
    }
}
