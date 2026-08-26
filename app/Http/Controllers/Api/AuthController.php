<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'digits:10',
                'regex:/^[6-9][0-9]{9}$/',
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        /*
         * Default role
         */
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('employee');
        }

        $token = $user
            ->createToken('mobile-app')
            ->plainTextToken;

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->values()
            : collect();

        $permissions = method_exists($user, 'getAllPermissions')
            ? $user->getAllPermissions()
                ->pluck('name')
                ->values()
            : collect();

        $user->load('office');

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully.',

            'token' => $token,
            'token_type' => 'Bearer',

            'user' => $user,

            'roles' => $roles,
            'permissions' => $permissions,
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Login - Send OTP
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => [
                'required',
                'digits:10',
                'regex:/^[6-9][0-9]{9}$/',
            ],
        ], [
            'phone.required' => 'Mobile number required hai.',
            'phone.digits' => 'Mobile number 10 digit ka hona chahiye.',
            'phone.regex' => 'Mobile number 6, 7, 8 ya 9 se start hona chahiye.',
        ]);

        $user = User::where('phone', $data['phone'])
            ->where('status', '1')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid mobile number ya user inactive hai.',
            ], 401);
        }

        /*
         * Testing number
         */
        $otp = $user->phone === '8737934656'
            ? 123456
            : random_int(100000, 999999);

        /*
         * Save OTP in users table
         */
        $user->forceFill([
            'otp' => $otp,
        ])->save();

        /*
         * Send OTP SMS
         */
        try {

            $this->sendLoginOtp(
                $user->phone,
                $otp
            );

        } catch (\Throwable $e) {

            /*
             * SMS fail hua to OTP clear kar do
             */
            $user->forceFill([
                'otp' => null,
            ])->save();

            return response()->json([
                'status' => false,
                'message' => 'OTP send nahi ho paya. Please dobara try karein.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully.',
            'otp_required' => true,
            'user_id' => $user->id,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Verify OTP + Login
    |--------------------------------------------------------------------------
    */
    public function verifyLoginOtp(Request $request)
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                'integer',
            ],

            'otp' => [
                'required',
                'digits:6',
            ],
        ], [
            'user_id.required' => 'User ID required hai.',
            'otp.required' => 'OTP required hai.',
            'otp.digits' => 'OTP 6 digit ka hona chahiye.',
        ]);

        /*
         * Find active user
         */
        $user = User::where('id', $data['user_id'])
            ->where('status', '1')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User nahi mila ya inactive hai.',
            ], 401);
        }

        /*
         * OTP exists?
         */
        if (
            $user->otp === null ||
            trim((string) $user->otp) === ''
        ) {
            return response()->json([
                'status' => false,
                'message' => 'OTP not found. Please login again.',
            ], 422);
        }

        /*
         * OTP match
         */
        if (
            (string) $user->otp !==
            (string) $data['otp']
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | OTP Correct - Generate Token
        |--------------------------------------------------------------------------
        */

        $deviceName = $request->input(
            'device_name',
            'mobile-app'
        );

        $token = $user
            ->createToken($deviceName)
            ->plainTextToken;

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()
                ->values()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = method_exists($user, 'getAllPermissions')
            ? $user->getAllPermissions()
                ->pluck('name')
                ->values()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Office
        |--------------------------------------------------------------------------
        */

        $user->load('office');

        /*
        |--------------------------------------------------------------------------
        | Clear OTP
        |--------------------------------------------------------------------------
        */

        $user->forceFill([
            'otp' => null,
        ])->save();

        /*
        |--------------------------------------------------------------------------
        | Clean User Response
        |--------------------------------------------------------------------------
        */

        $userData = $user
            ->makeHidden([
                'password',
                'remember_token',
                'otp',
                'roles',
                'permissions',
            ])
            ->toArray();

        $userData['roles'] = $roles;
        $userData['permissions'] = $permissions;

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'status' => true,
            'success' => true,

            'message' => 'Login successful.',

            'token' => $token,
            'token_type' => 'Bearer',

            'user' => $userData,

            'roles' => $roles,
            'permissions' => $permissions,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Token Login
    |--------------------------------------------------------------------------
    */
    public function tokenLogin(Request $request)
    {
        $data = $request->validate([
            'token' => [
                'required',
                'string',
            ],
        ]);

        $accessToken = PersonalAccessToken::findToken(
            $data['token']
        );

        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        $user = $accessToken->tokenable;

        if (
            !$user ||
            (string) $user->status !== '1'
        ) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or inactive.',
            ], 401);
        }

        $user->load('office');

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()
                ->values()
            : collect();

        $permissions = method_exists($user, 'getAllPermissions')
            ? $user->getAllPermissions()
                ->pluck('name')
                ->values()
            : collect();

        $userData = $user
            ->makeHidden([
                'password',
                'remember_token',
                'otp',
                'roles',
                'permissions',
            ])
            ->toArray();

        $userData['roles'] = $roles;
        $userData['permissions'] = $permissions;

        return response()->json([
            'status' => true,
            'message' => 'Authenticated successfully.',

            'token' => $data['token'],
            'token_type' => 'Bearer',

            'user' => $userData,

            'roles' => $roles,
            'permissions' => $permissions,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Change Password
    |--------------------------------------------------------------------------
    */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $data = $request->validate([
            'current_password' => [
                'required',
                'string',
                'min:6',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        if (
            !Hash::check(
                $data['current_password'],
                $user->password
            )
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        if (
            Hash::check(
                $data['password'],
                $user->password
            )
        ) {
            return response()->json([
                'status' => false,
                'message' => 'New password cannot be same as current password.',
            ], 422);
        }

        $user->password = Hash::make(
            $data['password']
        );

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully.',
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Photo Update
    |--------------------------------------------------------------------------
    */
    public function profilePhotoUpdate(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'photo' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (
            !empty($user->photo) &&
            Storage::disk('public')->exists($user->photo)
        ) {
            Storage::disk('public')->delete(
                $user->photo
            );
        }

        $path = $request
            ->file('photo')
            ->store(
                'profile_photos',
                'public'
            );

        $user->photo = $path;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile photo updated successfully.',

            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'photo' => $user->photo,
                'photo_url' => asset(
                    'storage/' . $user->photo
                ),
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Account
    |--------------------------------------------------------------------------
    */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $data = $request->validate([
            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ]);

        if (
            !Hash::check(
                $data['password'],
                $user->password
            )
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Password is incorrect.',
            ], 401);
        }

        DB::beginTransaction();

        try {

            $user->tokens()->delete();

            if (
                !empty($user->photo) &&
                Storage::disk('public')->exists($user->photo)
            ) {
                Storage::disk('public')->delete(
                    $user->photo
                );
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Account deleted successfully.',
            ], 200);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Account delete failed.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Send Login OTP
    |--------------------------------------------------------------------------
    */
    private function sendLoginOtp(
        string $phone,
        int $otp
    ): void {
        $message = "Dear Customer, {$otp} this is your login verification OTP. Please do not share with anyone. Best Regards, Real Victory Groups https://realvictorygroups.com/";

        $baseUrl = trim(
            (string) env('KUTILITY_URL')
        );

        $key = trim(
            (string) env('KUTILITY_KEY')
        );

        if (
            $baseUrl === '' ||
            $key === ''
        ) {
            throw new \RuntimeException(
                'SMS configuration missing.'
            );
        }

        /*
         * KUTILITY_URL me agar ? already hai
         * to dobara ? add nahi hoga.
         */
        if (str_contains($baseUrl, '?')) {

            $separator =
                str_ends_with($baseUrl, '?') ||
                str_ends_with($baseUrl, '&')
                    ? ''
                    : '&';

        } else {

            $separator = '?';
        }

        $url = $baseUrl
            . $separator
            . http_build_query([
                'key' => $key,
                'campaign' => '12754',
                'routeid' => '7',
                'type' => 'text',
                'contacts' => $phone,
                'senderid' => 'RVGRPS',
                'msg' => $message,
                'template_id' => '1707178031266790425',
                'pe_id' => '1701164032595209992',
            ]);

        $response = @file_get_contents($url);

        if ($response === false) {
            throw new \RuntimeException(
                'OTP SMS send failed.'
            );
        }
    }
}