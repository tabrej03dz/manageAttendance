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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

use Illuminate\Support\Facades\Http;


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


    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'phone' => [
    //             'required',
    //             'digits:10',
    //             'regex:/^[6-9][0-9]{9}$/',
    //         ],
    //     ], [
    //         'phone.required' => 'Mobile number required hai.',
    //         'phone.digits'   => 'Mobile number 10 digit ka hona chahiye.',
    //         'phone.regex'    => 'Mobile number 6, 7, 8 ya 9 se start hona chahiye.',
    //     ]);

    //     $user = User::where('phone', $request->phone)
    //         ->where('status', '1')
    //         ->first();

    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'Invalid mobile number ya user inactive hai.',
    //         ], 401);
    //     }

    //     $otp = $user->phone == '8737934656' ? 123456 : rand(100000, 999999);

    //     // update() ki jagah forceFill use karo, fillable issue nahi aayega
    //     $user->forceFill([
    //         'otp' => $otp,
    //     ])->save();

    //     $this->sendLoginOtp($user->phone, $otp);

    //     return response()->json([
    //         'message' => 'OTP sent successfully',
    //         'otp_required' => true,
    //         'user_id' => $user->id,

    //         // Testing ke liye abhi uncomment rakh sakte ho
    //         'otp' => $otp,
    //     ], 200);
    // }


    public function login(Request $request)
    {
        $user = null;

        try {
            /*
            |--------------------------------------------------------------------------
            | Login API Call Log
            |--------------------------------------------------------------------------
            */
            Log::info('LOGIN API CALLED', [
                'api_name'       => 'Login API',
                'route_name'     => optional($request->route())->getName(),
                'route_uri'      => optional($request->route())->uri(),
                'request_method' => $request->method(),
                'request_url'    => $request->fullUrl(),
                'request_path'   => $request->path(),

                'request_data' => [
                    'phone' => $request->input('phone'),
                ],

                'headers' => [
                    'accept'       => $request->header('Accept'),
                    'content_type' => $request->header('Content-Type'),
                    'authorization_received' => $request->hasHeader('Authorization'),
                    'app_version'  => $request->header('App-Version'),
                    'device_id'    => $request->header('Device-Id'),
                    'platform'     => $request->header('Platform'),
                ],

                'ip_address'   => $request->ip(),
                'forwarded_ip' => $request->header('X-Forwarded-For'),
                'user_agent'   => $request->userAgent(),
                'request_time' => now()->toDateTimeString(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */
            $validated = $request->validate([
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

            Log::info('LOGIN API: Validation successful', [
                'phone'       => $validated['phone'],
                'masked_phone' => $this->maskPhone($validated['phone']),
                'ip_address'  => $request->ip(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Find User Without Status Filter
            |--------------------------------------------------------------------------
            | पहले user निकाल रहे हैं, ताकि inactive user की details भी log हो सकें।
            */
            $user = User::where('phone', $validated['phone'])->first();

            if (!$user) {
                Log::warning('LOGIN API FAILED: User not found', [
                    'failure_reason' => 'Is mobile number se koi user record nahi mila.',
                    'phone'          => $validated['phone'],
                    'masked_phone'   => $this->maskPhone($validated['phone']),
                    'ip_address'     => $request->ip(),
                    'user_agent'     => $request->userAgent(),
                    'request_url'    => $request->fullUrl(),
                    'request_time'   => now()->toDateTimeString(),
                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid mobile number ya user inactive hai.',
                ], 401);
            }

            /*
            |--------------------------------------------------------------------------
            | User Status Check
            |--------------------------------------------------------------------------
            */
            if ((string) $user->status !== '1') {
                Log::warning('LOGIN API FAILED: User inactive', [
                    'failure_reason' => 'User account inactive hai.',
                    'user_id'        => $user->id,
                    'user_name'      => $user->name,
                    'user_email'     => $user->email,
                    'user_phone'     => $user->phone,
                    'user_status'    => $user->status,
                    'office_id'      => $user->office_id,
                    'ip_address'     => $request->ip(),
                    'user_agent'     => $request->userAgent(),
                    'request_url'    => $request->fullUrl(),
                    'request_time'   => now()->toDateTimeString(),
                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid mobile number ya user inactive hai.',
                ], 401);
            }

            $userLogDetails = [
                'user_id'      => $user->id,
                'user_name'    => $user->name,
                'user_email'   => $user->email,
                'user_phone'   => $user->phone,
                'masked_phone' => $this->maskPhone($user->phone),
                'user_status'  => $user->status,
                'office_id'    => $user->office_id,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
            ];

            Log::info(
                'LOGIN API: Active user found',
                $userLogDetails
            );

            /*
            |--------------------------------------------------------------------------
            | Generate OTP
            |--------------------------------------------------------------------------
            */
            $otp = $user->phone === '8737934656'
                ? 123456
                : random_int(100000, 999999);

            Log::info(
                'LOGIN API: OTP generated',
                array_merge($userLogDetails, [
                    'otp' => app()->environment('local', 'testing')
                        ? $otp
                        : 'hidden',
                ])
            );

            /*
            |--------------------------------------------------------------------------
            | Save OTP
            |--------------------------------------------------------------------------
            */
            try {
                $user->forceFill([
                    'otp' => $otp,
                ])->save();

                Log::info(
                    'LOGIN API: OTP saved successfully',
                    array_merge($userLogDetails, [
                        'otp' => app()->environment('local', 'testing')
                            ? $otp
                            : 'hidden',
                    ])
                );
            } catch (Throwable $exception) {
                Log::error(
                    'LOGIN API ERROR: Failed to save OTP',
                    array_merge($userLogDetails, [
                        'failure_reason' => 'Database me OTP save nahi ho paya.',
                        'error_type'     => get_class($exception),
                        'error_message'  => $exception->getMessage(),
                        'error_code'     => $exception->getCode(),
                        'error_file'     => $exception->getFile(),
                        'error_line'     => $exception->getLine(),
                        'trace'          => $exception->getTraceAsString(),
                    ])
                );

                return response()->json([
                    'status'  => false,
                    'message' => 'OTP save nahi ho paya.',
                    'error'   => app()->environment('local')
                        ? $exception->getMessage()
                        : null,
                ], 500);
            }

            /*
            |--------------------------------------------------------------------------
            | SMS API Call Start Log
            |--------------------------------------------------------------------------
            */
            Log::info(
                'LOGIN API: SMS OTP API call started',
                array_merge($userLogDetails, [
                    'sms_provider_url' => env('KUTILITY_URL'),
                    'sms_campaign'     => '12754',
                    'sms_route_id'     => '7',
                    'sms_sender_id'    => 'RVGRPS',
                    'request_time'     => now()->toDateTimeString(),
                ])
            );

            /*
            |--------------------------------------------------------------------------
            | Send OTP
            |--------------------------------------------------------------------------
            */
            try {
                $smsResponse = $this->sendLoginOtp($user->phone, $otp);

                Log::info(
                    'LOGIN API: SMS OTP API call completed',
                    array_merge($userLogDetails, [
                        'sms_response'  => $this->prepareLogValue($smsResponse),
                        'response_time' => now()->toDateTimeString(),
                    ])
                );
            } catch (Throwable $exception) {
                Log::error(
                    'LOGIN API ERROR: SMS OTP API call failed',
                    array_merge($userLogDetails, [
                        'failure_reason' => 'SMS provider API se OTP send nahi hua.',
                        'sms_provider_url' => env('KUTILITY_URL'),
                        'error_type'     => get_class($exception),
                        'error_message'  => $exception->getMessage(),
                        'error_code'     => $exception->getCode(),
                        'error_file'     => $exception->getFile(),
                        'error_line'     => $exception->getLine(),
                        'trace'          => $exception->getTraceAsString(),
                    ])
                );

                /*
                |--------------------------------------------------------------------------
                | SMS Fail होने पर OTP Clear
                |--------------------------------------------------------------------------
                */
                try {
                    $user->forceFill([
                        'otp' => null,
                    ])->save();

                    Log::warning(
                        'LOGIN API: OTP cleared after SMS failure',
                        $userLogDetails
                    );
                } catch (Throwable $clearException) {
                    Log::error(
                        'LOGIN API ERROR: Failed to clear OTP after SMS failure',
                        array_merge($userLogDetails, [
                            'error_type'    => get_class($clearException),
                            'error_message' => $clearException->getMessage(),
                            'error_file'    => $clearException->getFile(),
                            'error_line'    => $clearException->getLine(),
                            'trace'         => $clearException->getTraceAsString(),
                        ])
                    );
                }

                return response()->json([
                    'status'  => false,
                    'message' => 'OTP send nahi ho paya. Please dobara try karein.',
                    'error'   => app()->environment('local')
                        ? $exception->getMessage()
                        : null,
                ], 500);
            }

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */
            $response = [
                'status'       => true,
                'message'      => 'OTP sent successfully',
                'otp_required' => true,
                'user_id'      => $user->id,
            ];

            if (app()->environment('local', 'testing')) {
                $response['otp'] = $otp;
            }

            Log::info(
                'LOGIN API SUCCESS: OTP request completed successfully',
                array_merge($userLogDetails, [
                    'http_status'   => 200,
                    'response_time' => now()->toDateTimeString(),
                ])
            );

            return response()->json($response, 200);

        } catch (ValidationException $exception) {
            /*
            |--------------------------------------------------------------------------
            | Validation Error में phone से user details निकालें
            |--------------------------------------------------------------------------
            */
            $validationUser = null;
            $phone = $request->input('phone');

            if (!empty($phone)) {
                try {
                    $validationUser = User::where('phone', $phone)->first();
                } catch (Throwable $ignoredException) {
                    // Original validation error को प्राथमिकता देंगे।
                }
            }

            Log::warning('LOGIN API FAILED: Validation error', [
                'failure_reason' => 'Login request validation failed.',
                'user_id'        => $validationUser?->id,
                'user_name'      => $validationUser?->name,
                'user_email'     => $validationUser?->email,
                'user_phone'     => $validationUser?->phone ?? $phone,
                'user_status'    => $validationUser?->status,
                'office_id'      => $validationUser?->office_id,
                'validation_errors' => $exception->errors(),
                'request_method' => $request->method(),
                'request_url'    => $request->fullUrl(),
                'ip_address'     => $request->ip(),
                'user_agent'     => $request->userAgent(),
                'request_time'   => now()->toDateTimeString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $exception->errors(),
            ], 422);

        } catch (Throwable $exception) {
            /*
            |--------------------------------------------------------------------------
            | Unexpected Exception
            |--------------------------------------------------------------------------
            */
            Log::critical('LOGIN API CRITICAL: Unexpected exception', [
                'user_id'        => $user?->id,
                'user_name'      => $user?->name,
                'user_email'     => $user?->email,
                'user_phone'     => $user?->phone ?? $request->input('phone'),
                'user_status'    => $user?->status,
                'office_id'      => $user?->office_id,
                'request_method' => $request->method(),
                'request_url'    => $request->fullUrl(),
                'request_path'   => $request->path(),
                'ip_address'     => $request->ip(),
                'user_agent'     => $request->userAgent(),
                'request_time'   => now()->toDateTimeString(),
                'error_type'     => get_class($exception),
                'error_message'  => $exception->getMessage(),
                'error_code'     => $exception->getCode(),
                'error_file'     => $exception->getFile(),
                'error_line'     => $exception->getLine(),
                'trace'          => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Login process ke dauran unexpected error aaya.',
                'error'   => app()->environment('local')
                    ? $exception->getMessage()
                    : null,
            ], 500);
        }
    }




public function verifyLoginOtp(Request $request)
{
    // 1. Validate
    $request->validate([
        'user_id' => 'required|integer',
        'otp'     => 'required|digits:6',
    ]);

    // 2. User find
    $user = User::find($request->user_id);

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found.',
        ], 404);
    }

    // 3. User active check
    if ((string) $user->status !== '1') {
        return response()->json([
            'status' => false,
            'message' => 'User inactive hai.',
        ], 401);
    }

    // 4. OTP check
    if (empty($user->otp)) {
        return response()->json([
            'status' => false,
            'message' => 'OTP not found. Please login again.',
        ], 422);
    }

    // 5. OTP match
    if ((string) $user->otp !== (string) $request->otp) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid OTP.',
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | OTP MATCHED
    |--------------------------------------------------------------------------
    */

    // 6. Purane tokens delete
    $user->tokens()->delete();

    // 7. New Sanctum token
    $token = $user
        ->createToken('mobile-app')
        ->plainTextToken;

    // 8. Office load
    $user->load('office');

    // 9. Roles
    $roles = $user
        ->getRoleNames()
        ->values();

    // 10. Permissions
    $permissions = $user
        ->getAllPermissions()
        ->pluck('name')
        ->values();

    // 11. OTP clear
    $user->forceFill([
        'otp' => null,
    ])->save();

    // 12. Clean user data
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

    // 13. Response
    return response()->json([
        'status' => true,
        'message' => 'OTP verified successfully.',
        'token' => $token,
        'token_type' => 'Bearer',
        'user' => $userData,
        'roles' => $roles,
        'permissions' => $permissions,
    ], 200);
}


    // private function sendLoginSuccessResponse($user)
    // {
    //     $token = $user->createToken('authToken')->plainTextToken;

    //     $user->load('office');

    //     $roles = method_exists($user, 'getRoleNames')
    //         ? $user->getRoleNames()->values()
    //         : [];

    //     $permissions = method_exists($user, 'getAllPermissions')
    //         ? $user->getAllPermissions()->pluck('name')->values()
    //         : [];

    //     $userData = $user->makeHidden([
    //         'roles',
    //         'permissions',
    //         'password',
    //         'remember_token',
    //         'otp',
    //     ])->toArray();

    //     $userData['roles'] = $roles;
    //     $userData['permissions'] = $permissions;

    //     return response()->json([
    //         'message' => 'Login successful',
    //         'user'  => $userData,
    //         'token' => $token,
    //     ], 200);
    // }

    private function sendLoginSuccessResponse(
    Request $request,
    User $user
) {
    $deviceName = trim(
        (string) (
            $request->input('device_name')
            ?: $request->header('Device-Name')
            ?: 'mobile-app'
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Optional - Purane same-device tokens remove
    |--------------------------------------------------------------------------
    */

    $user->tokens()
        ->where('name', $deviceName)
        ->delete();

    /*
    |--------------------------------------------------------------------------
    | Create Sanctum Token
    |--------------------------------------------------------------------------
    */

    $token = $user
        ->createToken($deviceName)
        ->plainTextToken;

    /*
    |--------------------------------------------------------------------------
    | Office Relation
    |--------------------------------------------------------------------------
    */

    $user->load('office');

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    $roles = method_exists($user, 'getRoleNames')
        ? $user->getRoleNames()->values()->all()
        : [];

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    $permissions = method_exists($user, 'getAllPermissions')
        ? $user->getAllPermissions()
            ->pluck('name')
            ->values()
            ->all()
        : [];

    /*
    |--------------------------------------------------------------------------
    | User Response
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

    $userData['roles']       = $roles;
    $userData['permissions'] = $permissions;

    return response()->json([
        'status'     => true,
        'success'    => true,
        'message'    => 'Login successful.',
        'token'      => $token,
        'token_type' => 'Bearer',
        'user'       => $userData,
    ], 200);
}


    // public function logout(Request $request){
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'message' => 'Logged out successfully',
    //     ], 200);
    // }

    public function logout(Request $request)
    {
        $currentToken = $request->user()
            ->currentAccessToken();

        if ($currentToken) {
            $currentToken->delete();
        }

        return response()->json([
            'status' => true,
            'success' => true,
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


    // private function sendLoginOtp($phone, $otp)
    // {
    //     $msg = "Dear Customer, {$otp} this is your login verification OTP. Please do not share with anyone. Best Regards, Real Victory Groups https://realvictorygroups.com/";

    //     $url = env('KUTILITY_URL') . http_build_query([
    //         'key' => env('KUTILITY_KEY'),
    //         'campaign' => '12754',
    //         'routeid' => '7',
    //         'type' => 'text',
    //         'contacts' => $phone,
    //         'senderid' => 'RVGRPS',
    //         'msg' => $msg,
    //         'template_id' => '1707178031266790425',
    //         'pe_id' => '1701164032595209992',
    //     ]);

    //     @file_get_contents($url);
    // }


    private function sendLoginOtp(string $phone, int|string $otp): array
{
    $baseUrl = trim((string) env('KUTILITY_URL'));
    $key     = trim((string) env('KUTILITY_KEY'));

    if ($baseUrl === '') {
        throw new \RuntimeException('KUTILITY_URL .env me configured nahi hai.');
    }

    if ($key === '') {
        throw new \RuntimeException('KUTILITY_KEY .env me configured nahi hai.');
    }

    $message = "Dear Customer, {$otp} this is your login verification OTP. Please do not share with anyone. Best Regards, Real Victory Groups https://realvictorygroups.com/";

    $params = [
        'key'         => $key,
        'campaign'    => '12754',
        'routeid'     => '7',
        'type'        => 'text',
        'contacts'    => $phone,
        'senderid'    => 'RVGRPS',
        'msg'         => $message,
        'template_id' => '1707178031266790425',
        'pe_id'       => '1701164032595209992',
    ];

    /*
    |--------------------------------------------------------------------------
    | URL Build
    |--------------------------------------------------------------------------
    |
    | Agar KUTILITY_URL already ? par end hota hai to direct params lagenge.
    | Otherwise ? automatically add hoga.
    |
    */

    if (str_contains($baseUrl, '?')) {
        $separator = str_ends_with($baseUrl, '?') || str_ends_with($baseUrl, '&')
            ? ''
            : '&';
    } else {
        $separator = '?';
    }

    $url = $baseUrl . $separator . http_build_query($params);

    Log::info('KUTILITY SMS REQUEST', [
        'phone' => $this->maskPhone($phone),
        'url'   => $baseUrl,
    ]);

    try {

        $response = Http::timeout(15)
            ->connectTimeout(10)
            ->get($url);

        $body = trim($response->body());

        Log::info('KUTILITY SMS RESPONSE', [
            'phone'       => $this->maskPhone($phone),
            'status_code' => $response->status(),
            'body'        => $body,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'SMS API HTTP error: ' .
                $response->status() .
                ' Response: ' .
                $body
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Provider Failure Detection
        |--------------------------------------------------------------------------
        |
        | Provider kabhi HTTP 200 ke saath bhi error body de sakta hai.
        |
        */

        $lowerBody = strtolower($body);

        $failureWords = [
            'invalid',
            'failed',
            'failure',
            'error',
            'unauthorized',
            'incorrect',
            'expired',
        ];

        foreach ($failureWords as $failureWord) {

            if (str_contains($lowerBody, $failureWord)) {

                throw new \RuntimeException(
                    'SMS provider rejected request: ' . $body
                );
            }
        }

        return [
            'success'     => true,
            'status_code' => $response->status(),
            'body'        => $body,
        ];

    } catch (Throwable $exception) {

        Log::error('KUTILITY SMS ERROR', [
            'phone'         => $this->maskPhone($phone),
            'error_message' => $exception->getMessage(),
            'error_file'    => $exception->getFile(),
            'error_line'    => $exception->getLine(),
        ]);

        throw $exception;
    }
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







//     private function maskPhone(?string $phone): ?string
// {
//     if (!$phone) {
//         return null;
//     }

//     $phone = trim($phone);

//     if (strlen($phone) < 4) {
//         return '****';
//     }

//     return str_repeat('*', max(strlen($phone) - 4, 0))
//         . substr($phone, -4);
// }

private function prepareLogValue(mixed $value): mixed
{
    if (is_null($value) || is_scalar($value) || is_array($value)) {
        return $value;
    }

    if ($value instanceof \Illuminate\Http\JsonResponse) {
        return [
            'status_code' => $value->getStatusCode(),
            'data'        => $value->getData(true),
        ];
    }

    if ($value instanceof \Illuminate\Http\Client\Response) {
        return [
            'status_code' => $value->status(),
            'successful'  => $value->successful(),
            'body'        => $value->body(),
        ];
    }

    if (is_object($value) && method_exists($value, 'toArray')) {
        return $value->toArray();
    }

    return (string) $value;
}



private function maskPhone(?string $phone): ?string
{
    if (!$phone) {
        return null;
    }

    $phone = trim($phone);

    if (strlen($phone) < 4) {
        return '****';
    }

    return str_repeat('*', max(strlen($phone) - 4, 0))
        . substr($phone, -4);
}

}