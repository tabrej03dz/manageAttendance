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



    // public function verifyLoginOtp(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => ['required', 'exists:users,id'],
    //         'otp'     => ['required', 'digits:6'],
    //     ]);

    //     $user = User::where('id', $request->user_id)
    //         ->where('status', '1')
    //         ->first();

    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'Invalid user',
    //         ], 401);
    //     }

    //     if (empty($user->otp)) {
    //         return response()->json([
    //             'message' => 'OTP not found. Please login again.',
    //         ], 422);
    //     }

    //     if ((string) $user->otp !== (string) $request->otp) {
    //         return response()->json([
    //             'message' => 'Invalid OTP',
    //         ], 422);
    //     }

    //     $user->forceFill([
    //         'otp' => null,
    //     ])->save();

    //     return $this->sendLoginSuccessResponse($user);
    // }


    // public function verifyLoginOtp(Request $request)
    // {
    //     /*
    //     |--------------------------------------------------------------------------
    //     | Request से user की basic details निकालें
    //     |--------------------------------------------------------------------------
    //     | Validation fail होने पर भी user_id मौजूद हो सकता है, इसलिए पहले user
    //     | resolve किया जा रहा है ताकि पता चले error किस user के side आया।
    //     */
    //     $requestedUserId = $request->input('user_id');

    //     $logUser = null;

    //     if (!empty($requestedUserId) && is_numeric($requestedUserId)) {
    //         try {
    //             $logUser = User::find($requestedUserId);
    //         } catch (Throwable $lookupException) {
    //             Log::error('VERIFY OTP: User details lookup failed', [
    //                 'requested_user_id' => $requestedUserId,
    //                 'request_phone'     => $request->input('phone'),
    //                 'ip_address'        => $request->ip(),
    //                 'user_agent'        => $request->userAgent(),
    //                 'error_type'        => get_class($lookupException),
    //                 'error_message'     => $lookupException->getMessage(),
    //                 'error_file'        => $lookupException->getFile(),
    //                 'error_line'        => $lookupException->getLine(),
    //                 'trace'             => $lookupException->getTraceAsString(),
    //             ]);
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Common user information for every log
    //     |--------------------------------------------------------------------------
    //     */
    //     $userLogDetails = [
    //         'user_id'          => $logUser?->id ?? $requestedUserId,
    //         'user_name'        => $logUser?->name,
    //         'user_email'       => $logUser?->email,
    //         'user_phone'       => $logUser?->phone,
    //         'user_status'      => $logUser?->status,
    //         'user_office_id'   => $logUser?->office_id,
    //         'request_ip'       => $request->ip(),
    //         'request_method'   => $request->method(),
    //         'request_url'      => $request->fullUrl(),
    //         'user_agent'       => $request->userAgent(),
    //         'request_time'     => now()->toDateTimeString(),
    //     ];

    //     try {
    //         Log::info(
    //             'VERIFY OTP: Request received',
    //             array_merge($userLogDetails, [
    //                 'otp_received' => $request->filled('otp'),
    //             ])
    //         );

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Validate Request
    //         |--------------------------------------------------------------------------
    //         */
    //         $validated = $request->validate([
    //             'user_id' => [
    //                 'required',
    //                 'integer',
    //                 'exists:users,id',
    //             ],
    //             'otp' => [
    //                 'required',
    //                 'digits:6',
    //             ],
    //         ], [
    //             'user_id.required' => 'User ID required hai.',
    //             'user_id.integer'  => 'User ID valid nahi hai.',
    //             'user_id.exists'   => 'User nahi mila.',
    //             'otp.required'     => 'OTP required hai.',
    //             'otp.digits'       => 'OTP 6 digit ka hona chahiye.',
    //         ]);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Find Active User
    //         |--------------------------------------------------------------------------
    //         */
    //         $user = User::where('id', $validated['user_id'])
    //             ->where('status', '1')
    //             ->first();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Actual user details update करें
    //         |--------------------------------------------------------------------------
    //         */
    //         if ($user) {
    //             $userLogDetails = array_merge($userLogDetails, [
    //                 'user_id'        => $user->id,
    //                 'user_name'      => $user->name,
    //                 'user_email'     => $user->email,
    //                 'user_phone'     => $user->phone,
    //                 'user_status'    => $user->status,
    //                 'user_office_id' => $user->office_id,
    //             ]);
    //         }

    //         if (!$user) {
    //             /*
    //             |--------------------------------------------------------------------------
    //             | User inactive हो सकता है, इसलिए बिना status condition के निकालें
    //             |--------------------------------------------------------------------------
    //             */
    //             $inactiveUser = User::find($validated['user_id']);

    //             $failedUserDetails = array_merge($userLogDetails, [
    //                 'user_id'        => $inactiveUser?->id ?? $validated['user_id'],
    //                 'user_name'      => $inactiveUser?->name,
    //                 'user_email'     => $inactiveUser?->email,
    //                 'user_phone'     => $inactiveUser?->phone,
    //                 'user_status'    => $inactiveUser?->status,
    //                 'user_office_id' => $inactiveUser?->office_id,
    //                 'failure_reason' => $inactiveUser
    //                     ? 'User account inactive hai.'
    //                     : 'User record nahi mila.',
    //             ]);

    //             Log::warning(
    //                 'VERIFY OTP FAILED: User invalid or inactive',
    //                 $failedUserDetails
    //             );

    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Invalid user ya user inactive hai.',
    //             ], 401);
    //         }

    //         Log::info(
    //             'VERIFY OTP: Active user found',
    //             $userLogDetails
    //         );

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Check OTP Exists
    //         |--------------------------------------------------------------------------
    //         */
    //         if (empty($user->otp)) {
    //             Log::warning(
    //                 'VERIFY OTP FAILED: OTP not found for user',
    //                 array_merge($userLogDetails, [
    //                     'failure_reason' => 'Database me user ka OTP empty ya null hai.',
    //                 ])
    //             );

    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'OTP not found. Please login again.',
    //             ], 422);
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Verify OTP
    //         |--------------------------------------------------------------------------
    //         */
    //         if (!hash_equals((string) $user->otp, (string) $validated['otp'])) {
    //             Log::warning(
    //                 'VERIFY OTP FAILED: Invalid OTP entered by user',
    //                 array_merge($userLogDetails, [
    //                     'failure_reason' => 'User ne invalid OTP enter kiya.',
    //                     'entered_otp'    => app()->environment('local', 'testing')
    //                         ? (string) $validated['otp']
    //                         : 'hidden',
    //                     'stored_otp'     => app()->environment('local', 'testing')
    //                         ? (string) $user->otp
    //                         : 'hidden',
    //                 ])
    //             );

    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Invalid OTP',
    //             ], 422);
    //         }

    //         Log::info(
    //             'VERIFY OTP: OTP matched successfully',
    //             $userLogDetails
    //         );

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Clear Used OTP
    //         |--------------------------------------------------------------------------
    //         */
    //         try {
    //             $user->forceFill([
    //                 'otp' => null,
    //             ])->save();

    //             Log::info(
    //                 'VERIFY OTP: Used OTP cleared successfully',
    //                 $userLogDetails
    //             );
    //         } catch (Throwable $exception) {
    //             Log::error(
    //                 'VERIFY OTP ERROR: Failed to clear used OTP',
    //                 array_merge($userLogDetails, [
    //                     'failure_reason' => 'OTP match hone ke baad database se clear nahi hua.',
    //                     'error_type'     => get_class($exception),
    //                     'error_message'  => $exception->getMessage(),
    //                     'error_code'     => $exception->getCode(),
    //                     'error_file'     => $exception->getFile(),
    //                     'error_line'     => $exception->getLine(),
    //                     'trace'          => $exception->getTraceAsString(),
    //                 ])
    //             );

    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'OTP verify hua lekin login process complete nahi ho paya.',
    //                 'error'   => app()->environment('local')
    //                     ? $exception->getMessage()
    //                     : null,
    //             ], 500);
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Generate Login Success Response
    //         |--------------------------------------------------------------------------
    //         */
    //         try {
    //             $response = $this->sendLoginSuccessResponse($user);

    //             Log::info(
    //                 'VERIFY OTP SUCCESS: User login completed successfully',
    //                 $userLogDetails
    //             );

    //             return $response;
    //         } catch (Throwable $exception) {
    //             Log::error(
    //                 'VERIFY OTP ERROR: Login success response failed',
    //                 array_merge($userLogDetails, [
    //                     'failure_reason' => 'Token, role, permission ya office load karte samay error.',
    //                     'error_type'     => get_class($exception),
    //                     'error_message'  => $exception->getMessage(),
    //                     'error_code'     => $exception->getCode(),
    //                     'error_file'     => $exception->getFile(),
    //                     'error_line'     => $exception->getLine(),
    //                     'trace'          => $exception->getTraceAsString(),
    //                 ])
    //             );

    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Login response generate nahi ho paya.',
    //                 'error'   => app()->environment('local')
    //                     ? $exception->getMessage()
    //                     : null,
    //             ], 500);
    //         }
    //     } catch (ValidationException $exception) {
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Validation fail होने पर दोबारा user details resolve करें
    //         |--------------------------------------------------------------------------
    //         */
    //         $validationUser = null;

    //         if (!empty($requestedUserId) && is_numeric($requestedUserId)) {
    //             try {
    //                 $validationUser = User::find($requestedUserId);
    //             } catch (Throwable $ignoredException) {
    //                 // Main validation error log नीचे किया जाएगा।
    //             }
    //         }

    //         Log::warning('VERIFY OTP FAILED: Validation error', [
    //             'user_id'        => $validationUser?->id ?? $requestedUserId,
    //             'user_name'      => $validationUser?->name,
    //             'user_email'     => $validationUser?->email,
    //             'user_phone'     => $validationUser?->phone,
    //             'user_status'    => $validationUser?->status,
    //             'user_office_id' => $validationUser?->office_id,
    //             'validation_errors' => $exception->errors(),
    //             'failure_reason' => 'Request validation failed.',
    //             'request_ip'     => $request->ip(),
    //             'request_method' => $request->method(),
    //             'request_url'    => $request->fullUrl(),
    //             'user_agent'     => $request->userAgent(),
    //             'request_time'   => now()->toDateTimeString(),
    //         ]);

    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Validation failed.',
    //             'errors'  => $exception->errors(),
    //         ], 422);
    //     } catch (Throwable $exception) {
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Unexpected error के समय latest user details निकालें
    //         |--------------------------------------------------------------------------
    //         */
    //         $exceptionUser = null;

    //         if (!empty($requestedUserId) && is_numeric($requestedUserId)) {
    //             try {
    //                 $exceptionUser = User::find($requestedUserId);
    //             } catch (Throwable $ignoredException) {
    //                 // Original exception को प्राथमिकता दी जाएगी।
    //             }
    //         }

    //         Log::critical('VERIFY OTP CRITICAL: Unexpected exception', [
    //             'user_id'        => $exceptionUser?->id ?? $requestedUserId,
    //             'user_name'      => $exceptionUser?->name,
    //             'user_email'     => $exceptionUser?->email,
    //             'user_phone'     => $exceptionUser?->phone,
    //             'user_status'    => $exceptionUser?->status,
    //             'user_office_id' => $exceptionUser?->office_id,
    //             'request_ip'     => $request->ip(),
    //             'request_method' => $request->method(),
    //             'request_url'    => $request->fullUrl(),
    //             'user_agent'     => $request->userAgent(),
    //             'request_time'   => now()->toDateTimeString(),
    //             'error_type'     => get_class($exception),
    //             'error_message'  => $exception->getMessage(),
    //             'error_code'     => $exception->getCode(),
    //             'error_file'     => $exception->getFile(),
    //             'error_line'     => $exception->getLine(),
    //             'trace'          => $exception->getTraceAsString(),
    //         ]);

    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'OTP verification ke dauran unexpected error aaya.',
    //             'error'   => app()->environment('local')
    //                 ? $exception->getMessage()
    //                 : null,
    //         ], 500);
    //     }
    // }


    public function verifyLoginOtp(Request $request)
    {
        $user = null;
        $requestedUserId = $request->input('user_id');

        try {
            /*
            |--------------------------------------------------------------------------
            | Verify OTP API Call Log
            |--------------------------------------------------------------------------
            */
            Log::info('VERIFY OTP API CALLED', [
                'api_name'       => 'Verify Login OTP API',
                'route_name'     => optional($request->route())->getName(),
                'route_uri'      => optional($request->route())->uri(),
                'request_method' => $request->method(),
                'request_url'    => $request->fullUrl(),
                'request_path'   => $request->path(),

                'request_data' => [
                    'user_id'      => $requestedUserId,
                    'otp_received' => $request->filled('otp'),
                    'otp_length'   => strlen((string) $request->input('otp')),
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
                'user_id' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
                'otp' => [
                    'required',
                    'digits:6',
                ],
            ], [
                'user_id.required' => 'User ID required hai.',
                'user_id.integer'  => 'User ID valid nahi hai.',
                'user_id.exists'   => 'User nahi mila.',
                'otp.required'     => 'OTP required hai.',
                'otp.digits'       => 'OTP 6 digit ka hona chahiye.',
            ]);

            Log::info('VERIFY OTP API: Validation successful', [
                'user_id'      => $validated['user_id'],
                'otp_received' => true,
                'ip_address'   => $request->ip(),
                'request_time' => now()->toDateTimeString(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Find User Without Status Filter
            |--------------------------------------------------------------------------
            */
            $user = User::find($validated['user_id']);

            if (!$user) {
                Log::warning('VERIFY OTP API FAILED: User not found', [
                    'failure_reason' => 'User ID se koi user record nahi mila.',
                    'requested_user_id' => $validated['user_id'],
                    'ip_address'     => $request->ip(),
                    'user_agent'     => $request->userAgent(),
                    'request_url'    => $request->fullUrl(),
                    'request_time'   => now()->toDateTimeString(),
                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid user.',
                ], 401);
            }

            /*
            |--------------------------------------------------------------------------
            | Common User Details
            |--------------------------------------------------------------------------
            */
            $userLogDetails = [
                'user_id'        => $user->id,
                'user_name'      => $user->name,
                'user_email'     => $user->email,
                'user_phone'     => $user->phone,
                'masked_phone'   => $this->maskPhone($user->phone),
                'user_status'    => $user->status,
                'user_office_id' => $user->office_id,
                'ip_address'     => $request->ip(),
                'forwarded_ip'   => $request->header('X-Forwarded-For'),
                'user_agent'     => $request->userAgent(),
                'app_version'    => $request->header('App-Version'),
                'device_id'      => $request->header('Device-Id'),
                'platform'       => $request->header('Platform'),
                'request_time'   => now()->toDateTimeString(),
            ];

            /*
            |--------------------------------------------------------------------------
            | Check User Status
            |--------------------------------------------------------------------------
            */
            if ((string) $user->status !== '1') {
                Log::warning(
                    'VERIFY OTP API FAILED: User inactive',
                    array_merge($userLogDetails, [
                        'failure_reason' => 'User account inactive hai.',
                    ])
                );

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid user ya user inactive hai.',
                ], 401);
            }

            Log::info(
                'VERIFY OTP API: Active user found',
                $userLogDetails
            );

            /*
            |--------------------------------------------------------------------------
            | Check OTP Exists
            |--------------------------------------------------------------------------
            */
            if (empty($user->otp)) {
                Log::warning(
                    'VERIFY OTP API FAILED: OTP not found',
                    array_merge($userLogDetails, [
                        'failure_reason' => 'Database me user ka OTP null ya empty hai.',
                        'stored_otp_exists' => false,
                    ])
                );

                return response()->json([
                    'status'  => false,
                    'message' => 'OTP not found. Please login again.',
                ], 422);
            }

            Log::info(
                'VERIFY OTP API: Stored OTP found',
                array_merge($userLogDetails, [
                    'stored_otp_exists' => true,
                ])
            );

            /*
            |--------------------------------------------------------------------------
            | Verify OTP
            |--------------------------------------------------------------------------
            */
            if (!hash_equals((string) $user->otp, (string) $validated['otp'])) {
                Log::warning(
                    'VERIFY OTP API FAILED: Invalid OTP',
                    array_merge($userLogDetails, [
                        'failure_reason' => 'User ne galat OTP enter kiya.',
                        'entered_otp'    => app()->environment('local', 'testing')
                            ? (string) $validated['otp']
                            : 'hidden',
                        'stored_otp'     => app()->environment('local', 'testing')
                            ? (string) $user->otp
                            : 'hidden',
                    ])
                );

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid OTP',
                ], 422);
            }

            Log::info(
                'VERIFY OTP API: OTP matched successfully',
                $userLogDetails
            );

            /*
            |--------------------------------------------------------------------------
            | Clear Used OTP
            |--------------------------------------------------------------------------
            */
            try {
                $user->forceFill([
                    'otp' => null,
                ])->save();

                Log::info(
                    'VERIFY OTP API: Used OTP cleared successfully',
                    $userLogDetails
                );
            } catch (Throwable $exception) {
                Log::error(
                    'VERIFY OTP API ERROR: Failed to clear OTP',
                    array_merge($userLogDetails, [
                        'failure_reason' => 'OTP verify hone ke baad database se clear nahi hua.',
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
                    'message' => 'OTP verify hua lekin login process complete nahi ho paya.',
                    'error'   => app()->environment('local')
                        ? $exception->getMessage()
                        : null,
                ], 500);
            }

            /*
            |--------------------------------------------------------------------------
            | Login Success Response Call Start
            |--------------------------------------------------------------------------
            */
            Log::info(
                'VERIFY OTP API: Login success response generation started',
                array_merge($userLogDetails, [
                    'response_function' => 'sendLoginSuccessResponse',
                ])
            );

            /*
            |--------------------------------------------------------------------------
            | Generate Token, Roles, Permissions and User Response
            |--------------------------------------------------------------------------
            */
            try {
                $response = $this->sendLoginSuccessResponse(
                $request,
                $user
            );

                Log::info(
                    'VERIFY OTP API SUCCESS: User login completed successfully',
                    array_merge($userLogDetails, [
                        'http_status'      => 200,
                        'token_generated'  => true,
                        'response_time'    => now()->toDateTimeString(),
                    ])
                );

                return $response;
            } catch (Throwable $exception) {
                Log::error(
                    'VERIFY OTP API ERROR: Login success response failed',
                    array_merge($userLogDetails, [
                        'failure_reason' => 'Token, office, roles ya permissions load karte waqt error aaya.',
                        'response_function' => 'sendLoginSuccessResponse',
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
                    'message' => 'Login response generate nahi ho paya.',
                    'error'   => app()->environment('local')
                        ? $exception->getMessage()
                        : null,
                ], 500);
            }

        } catch (ValidationException $exception) {
            /*
            |--------------------------------------------------------------------------
            | Validation Failure User Resolve
            |--------------------------------------------------------------------------
            */
            $validationUser = null;

            if (!empty($requestedUserId) && is_numeric($requestedUserId)) {
                try {
                    $validationUser = User::find($requestedUserId);
                } catch (Throwable $lookupException) {
                    Log::error('VERIFY OTP API: Validation user lookup failed', [
                        'requested_user_id' => $requestedUserId,
                        'error_type'        => get_class($lookupException),
                        'error_message'     => $lookupException->getMessage(),
                        'error_file'        => $lookupException->getFile(),
                        'error_line'        => $lookupException->getLine(),
                    ]);
                }
            }

            Log::warning('VERIFY OTP API FAILED: Validation error', [
                'failure_reason' => 'Verify OTP request validation failed.',
                'user_id'        => $validationUser?->id ?? $requestedUserId,
                'user_name'      => $validationUser?->name,
                'user_email'     => $validationUser?->email,
                'user_phone'     => $validationUser?->phone,
                'masked_phone'   => $this->maskPhone($validationUser?->phone),
                'user_status'    => $validationUser?->status,
                'user_office_id' => $validationUser?->office_id,
                'otp_received'   => $request->filled('otp'),
                'otp_length'     => strlen((string) $request->input('otp')),
                'validation_errors' => $exception->errors(),
                'request_method' => $request->method(),
                'request_url'    => $request->fullUrl(),
                'request_path'   => $request->path(),
                'ip_address'     => $request->ip(),
                'forwarded_ip'   => $request->header('X-Forwarded-For'),
                'user_agent'     => $request->userAgent(),
                'app_version'    => $request->header('App-Version'),
                'device_id'      => $request->header('Device-Id'),
                'platform'       => $request->header('Platform'),
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
            | Unexpected Exception में User Resolve
            |--------------------------------------------------------------------------
            */
            $exceptionUser = $user;

            if (!$exceptionUser && !empty($requestedUserId) && is_numeric($requestedUserId)) {
                try {
                    $exceptionUser = User::find($requestedUserId);
                } catch (Throwable $lookupException) {
                    Log::error('VERIFY OTP API: Exception user lookup failed', [
                        'requested_user_id' => $requestedUserId,
                        'lookup_error'      => $lookupException->getMessage(),
                    ]);
                }
            }

            Log::critical('VERIFY OTP API CRITICAL: Unexpected exception', [
                'user_id'        => $exceptionUser?->id ?? $requestedUserId,
                'user_name'      => $exceptionUser?->name,
                'user_email'     => $exceptionUser?->email,
                'user_phone'     => $exceptionUser?->phone,
                'masked_phone'   => $this->maskPhone($exceptionUser?->phone),
                'user_status'    => $exceptionUser?->status,
                'user_office_id' => $exceptionUser?->office_id,
                'otp_received'   => $request->filled('otp'),
                'request_method' => $request->method(),
                'request_url'    => $request->fullUrl(),
                'request_path'   => $request->path(),
                'ip_address'     => $request->ip(),
                'forwarded_ip'   => $request->header('X-Forwarded-For'),
                'user_agent'     => $request->userAgent(),
                'app_version'    => $request->header('App-Version'),
                'device_id'      => $request->header('Device-Id'),
                'platform'       => $request->header('Platform'),
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
                'message' => 'OTP verification ke dauran unexpected error aaya.',
                'error'   => app()->environment('local')
                    ? $exception->getMessage()
                    : null,
            ], 500);
        }
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

    private function sendLoginSuccessResponse(Request $request, User $user) {

        /*
        |--------------------------------------------------------------------------
        | Device name resolve karein
        |--------------------------------------------------------------------------
        */

        $deviceName = $request->input('device_name')
            ?: $request->header('Device-Name')
            ?: 'mobile-app';

        /*
        |--------------------------------------------------------------------------
        | Sanctum token
        |--------------------------------------------------------------------------
        */

        $token = $user->createToken(
            $deviceName
        )->plainTextToken;

        /*
        |--------------------------------------------------------------------------
        | User relations, roles and permissions
        |--------------------------------------------------------------------------
        */

        $user->load('office');

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->values()
            : collect();

        $permissions = method_exists($user, 'getAllPermissions')
            ? $user->getAllPermissions()
                ->pluck('name')
                ->values()
            : collect();

        $userData = $user->makeHidden([
            'password',
            'remember_token',
            'otp',
            'roles',
            'permissions',
        ])->toArray();

        $userData['roles'] = $roles;
        $userData['permissions'] = $permissions;

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'Login successful.',

            'token' => $token,
            'token_type' => 'Bearer',

            'user' => $userData,
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