<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'                 => ['nullable', 'digits:10', 'regex:/^[6-9][0-9]{9}$/', 'unique:users,phone'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required'         => 'Name is required.',
            'email.required'        => 'Email address is required.',
            'email.email'           => 'Please enter a valid email address.',
            'email.unique'          => 'This email address is already registered.',
            'phone.digits'          => 'The mobile number must contain exactly 10 digits.',
            'phone.regex'           => 'The mobile number must start with 6, 7, 8, or 9.',
            'phone.unique'          => 'This mobile number is already registered.',
            'password.required'     => 'Password is required.',
            'password.min'          => 'The password must be at least 6 characters.',
            'password.confirmed'    => 'The password confirmation does not match.',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name'     => trim($validated['name']),
                'email'    => strtolower(trim($validated['email'])),
                'phone'    => !empty($validated['phone']) ? trim($validated['phone']) : null,
                'password' => Hash::make($validated['password']),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('employee');
            }

            $token = $user->createToken('authToken')->plainTextToken;

            DB::commit();

            $user->loadMissing('office');

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
                'token'   => $token,
                'user'    => $this->prepareUserData($user),
            ], 201);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('User registration failed.', [
                'email'   => $validated['email'] ?? null,
                'phone'   => $this->maskPhone($validated['phone'] ?? null),
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code'    => 'REGISTRATION_FAILED',
                'message' => 'Unable to register the user. Please try again.',
            ], 500);
        }
    }

    /**
     * Send a login OTP to an active user.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => [
                'required',
                'digits:10',
                'regex:/^[6-9][0-9]{9}$/',
            ],
        ], [
            'phone.required' => 'Mobile number is required.',
            'phone.digits'   => 'The mobile number must contain exactly 10 digits.',
            'phone.regex'    => 'The mobile number must start with 6, 7, 8, or 9.',
        ]);

        $phone = trim($validated['phone']);

        $user = User::query()
            ->where('phone', $phone)
            ->first();

        if (!$user) {
            return response()->json([
                'success'         => false,
                'code'            => 'INVALID_LOGIN',
                'logout_required' => false,
                'message'         => 'No account was found for this mobile number.',
            ], 422);
        }

        if ((string) $user->status !== '1') {
            return response()->json([
                'success'         => false,
                'code'            => 'ACCOUNT_INACTIVE',
                'logout_required' => false,
                'message'         => 'This user account is inactive.',
            ], 403);
        }

        $otp = random_int(100000, 999999);
        $otpExpiresAt = now()->addMinutes(5);

        $otpData = [
            'otp' => Hash::make((string) $otp),
        ];

        if (Schema::hasColumn('users', 'otp_expires_at')) {
            $otpData['otp_expires_at'] = $otpExpiresAt;
        }

        try {
            $user->forceFill($otpData)->save();
        } catch (Throwable $exception) {
            Log::error('Unable to save login OTP.', [
                'user_id' => $user->id,
                'phone'   => $this->maskPhone($phone),
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success'         => false,
                'code'            => 'OTP_SAVE_FAILED',
                'logout_required' => false,
                'message'         => 'Unable to generate the OTP. Please try again.',
            ], 500);
        }

        $smsSent = $this->sendLoginOtp($phone, $otp);

        if (!$smsSent) {
            $clearData = ['otp' => null];

            if (Schema::hasColumn('users', 'otp_expires_at')) {
                $clearData['otp_expires_at'] = null;
            }

            try {
                $user->forceFill($clearData)->save();
            } catch (Throwable $exception) {
                Log::error('Unable to clear OTP after SMS failure.', [
                    'user_id' => $user->id,
                    'message' => $exception->getMessage(),
                ]);
            }

            return response()->json([
                'success'         => false,
                'code'            => 'OTP_SEND_FAILED',
                'logout_required' => false,
                'message'         => 'Unable to send the OTP. Please try again.',
            ], 503);
        }

        $response = [
            'success'      => true,
            'message'      => 'OTP sent successfully.',
            'otp_required' => true,
            'user_id'      => $user->id,
            'expires_in'   => 300,
        ];

        // Never expose OTP in production, even when APP_DEBUG is accidentally enabled.
        if (app()->environment(['local', 'testing'])) {
            $response['otp'] = $otp;
        }

        return response()->json($response, 200);
    }

    /**
     * Verify the login OTP and generate a Sanctum token.
     */
    public function verifyLoginOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'otp'     => ['required', 'digits:6'],
        ], [
            'user_id.required' => 'User ID is required.',
            'user_id.integer'  => 'The user ID must be valid.',
            'user_id.exists'   => 'The selected user does not exist.',
            'otp.required'     => 'OTP is required.',
            'otp.digits'       => 'The OTP must contain exactly 6 digits.',
        ]);

        $user = User::find($validated['user_id']);

        if (!$user) {
            return response()->json([
                'success'         => false,
                'code'            => 'INVALID_USER',
                'logout_required' => false,
                'message'         => 'The user account could not be found.',
            ], 422);
        }

        if ((string) $user->status !== '1') {
            return response()->json([
                'success'         => false,
                'code'            => 'ACCOUNT_INACTIVE',
                'logout_required' => false,
                'message'         => 'This user account is inactive.',
            ], 403);
        }

        if (empty($user->otp)) {
            return response()->json([
                'success'         => false,
                'code'            => 'OTP_NOT_FOUND',
                'logout_required' => false,
                'message'         => 'No active OTP was found. Please request a new OTP.',
            ], 422);
        }

        if (
            Schema::hasColumn('users', 'otp_expires_at')
            && !empty($user->otp_expires_at)
            && now()->greaterThan($user->otp_expires_at)
        ) {
            $user->forceFill([
                'otp'            => null,
                'otp_expires_at' => null,
            ])->save();

            return response()->json([
                'success'         => false,
                'code'            => 'OTP_EXPIRED',
                'logout_required' => false,
                'message'         => 'The OTP has expired. Please request a new OTP.',
            ], 422);
        }

        // Supports the new hashed OTP and any temporary legacy plain-text OTP.
        $storedOtp = (string) $user->otp;
        $enteredOtp = (string) $validated['otp'];

        $isValidOtp = Hash::check($enteredOtp, $storedOtp)
            || hash_equals($storedOtp, $enteredOtp);

        if (!$isValidOtp) {
            return response()->json([
                'success'         => false,
                'code'            => 'INVALID_OTP',
                'logout_required' => false,
                'message'         => 'The OTP you entered is incorrect.',
            ], 422);
        }

        $clearData = ['otp' => null];

        if (Schema::hasColumn('users', 'otp_expires_at')) {
            $clearData['otp_expires_at'] = null;
        }

        try {
            $user->forceFill($clearData)->save();
        } catch (Throwable $exception) {
            Log::error('Unable to clear the verified OTP.', [
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success'         => false,
                'code'            => 'LOGIN_COMPLETION_FAILED',
                'logout_required' => false,
                'message'         => 'The OTP was verified, but login could not be completed.',
            ], 500);
        }

        return $this->sendLoginSuccessResponse($user);
    }

    /**
     * Authenticate the user with an existing Sanctum token.
     */
    public function tokenLogin(Request $request): JsonResponse
    {
        $rawToken = $request->input('token') ?: $request->bearerToken();

        if (empty($rawToken)) {
            return response()->json([
                'success'         => false,
                'code'            => 'TOKEN_REQUIRED',
                'logout_required' => true,
                'message'         => 'An authentication token is required.',
            ], 401);
        }

        $plainToken = trim((string) $rawToken);

        if (str_starts_with(strtolower($plainToken), 'bearer ')) {
            $plainToken = trim(substr($plainToken, 7));
        }

        $accessToken = PersonalAccessToken::findToken($plainToken);
        $user = $accessToken?->tokenable;

        if (!$user) {
            return response()->json([
                'success'         => false,
                'code'            => 'INVALID_TOKEN',
                'logout_required' => true,
                'message'         => 'The authentication token is invalid or expired.',
            ], 401);
        }

        if ((string) $user->status !== '1') {
            return response()->json([
                'success'         => false,
                'code'            => 'ACCOUNT_INACTIVE',
                'logout_required' => true,
                'message'         => 'This user account is inactive.',
            ], 403);
        }

        $user->loadMissing('office');

        return response()->json([
            'success' => true,
            'message' => 'Token authentication was successful.',
            'token'   => $plainToken,
            'user'    => $this->prepareUserData($user),
        ], 200);
    }

    /**
     * Log out from the current device.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'         => false,
                'code'            => 'UNAUTHENTICATED',
                'logout_required' => true,
                'message'         => 'You are not authenticated.',
            ], 401);
        }

        $currentToken = $user->currentAccessToken();

        if ($currentToken instanceof PersonalAccessToken) {
            $currentToken->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Delete the authenticated user's account.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ], [
            'password.required' => 'Password is required.',
            'password.min'      => 'The password must be at least 6 characters.',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'         => false,
                'code'            => 'UNAUTHENTICATED',
                'logout_required' => true,
                'message'         => 'You are not authenticated.',
            ], 401);
        }

        if (empty($user->password) || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success'         => false,
                'code'            => 'INVALID_PASSWORD',
                'logout_required' => false,
                'message'         => 'The password you entered is incorrect.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $userId = $user->id;

            $user->tokens()->delete();
            $user->delete();

            DB::commit();

            Log::info('User account deleted successfully.', [
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully.',
            ], 200);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Account deletion failed.', [
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code'    => 'ACCOUNT_DELETE_FAILED',
                'message' => 'Unable to delete the account. Please try again.',
            ], 500);
        }
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'         => false,
                'code'            => 'UNAUTHENTICATED',
                'logout_required' => true,
                'message'         => 'You are not authenticated.',
            ], 401);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'min:6'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Current password is required.',
            'current_password.min'      => 'The current password must be at least 6 characters.',
            'password.required'         => 'New password is required.',
            'password.min'              => 'The new password must be at least 6 characters.',
            'password.confirmed'        => 'The password confirmation does not match.',
        ]);

        if (empty($user->password) || !Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success'         => false,
                'code'            => 'INVALID_CURRENT_PASSWORD',
                'logout_required' => false,
                'message'         => 'The current password is incorrect.',
            ], 422);
        }

        if (Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success'         => false,
                'code'            => 'PASSWORD_NOT_CHANGED',
                'logout_required' => false,
                'message'         => 'The new password must be different from the current password.',
            ], 422);
        }

        try {
            $user->forceFill([
                'password' => Hash::make($validated['password']),
            ])->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.',
            ], 200);
        } catch (Throwable $exception) {
            Log::error('Password change failed.', [
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code'    => 'PASSWORD_CHANGE_FAILED',
                'message' => 'Unable to change the password. Please try again.',
            ], 500);
        }
    }

    /**
     * Update the authenticated user's profile photo.
     */
    public function profilePhotoUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.required' => 'A profile photo is required.',
            'photo.image'    => 'The uploaded file must be an image.',
            'photo.mimes'    => 'The photo must be a JPG, JPEG, PNG, or WEBP file.',
            'photo.max'      => 'The profile photo must not exceed 2 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code'    => 'VALIDATION_ERROR',
                'message' => 'The profile photo could not be validated.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'         => false,
                'code'            => 'UNAUTHENTICATED',
                'logout_required' => true,
                'message'         => 'You are not authenticated.',
            ], 401);
        }

        $oldPhoto = $user->photo;
        $newPhoto = null;

        try {
            $newPhoto = $request->file('photo')->store('profile_photos', 'public');

            $user->forceFill([
                'photo' => $newPhoto,
            ])->save();

            if (
                !empty($oldPhoto)
                && $oldPhoto !== $newPhoto
                && Storage::disk('public')->exists($oldPhoto)
            ) {
                Storage::disk('public')->delete($oldPhoto);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully.',
                'data'    => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'photo'     => $user->photo,
                    'photo_url' => Storage::disk('public')->url($user->photo),
                ],
            ], 200);
        } catch (Throwable $exception) {
            if (
                !empty($newPhoto)
                && Storage::disk('public')->exists($newPhoto)
            ) {
                Storage::disk('public')->delete($newPhoto);
            }

            Log::error('Profile photo update failed.', [
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code'    => 'PHOTO_UPDATE_FAILED',
                'message' => 'Unable to update the profile photo. Please try again.',
            ], 500);
        }
    }

    /**
     * Generate the standard successful login response.
     */
    private function sendLoginSuccessResponse(User $user): JsonResponse
    {
        try {
            $token = $user->createToken('authToken')->plainTextToken;

            $user->loadMissing('office');

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token'   => $token,
                'user'    => $this->prepareUserData($user),
            ], 200);
        } catch (Throwable $exception) {
            Log::error('Unable to create the login response.', [
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success'         => false,
                'code'            => 'TOKEN_CREATION_FAILED',
                'logout_required' => false,
                'message'         => 'Login verification succeeded, but the authentication token could not be created.',
            ], 500);
        }
    }

    /**
     * Prepare a safe and consistent user response.
     */
    private function prepareUserData(User $user): array
    {
        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->values()->all()
            : [];

        $permissions = method_exists($user, 'getAllPermissions')
            ? $user->getAllPermissions()->pluck('name')->values()->all()
            : [];

        $userData = $user->makeHidden([
            'password',
            'remember_token',
            'otp',
            'otp_expires_at',
            'roles',
            'permissions',
        ])->toArray();

        $userData['roles'] = $roles;
        $userData['permissions'] = $permissions;

        return $userData;
    }

    /**
     * Send login OTP through the configured SMS provider.
     */
    private function sendLoginOtp(string $phone, int $otp): bool
    {
        $message = "Dear Customer, {$otp} this is your login verification OTP. Please do not share with anyone. Best Regards, Real Victory Groups https://realvictorygroups.com/";

        $baseUrl = trim((string) env(
            'KUTILITY_URL',
            'https://kutility.com/api/v2/sendsms'
        ));

        $apiKey = trim((string) env('KUTILITY_KEY'));

        if ($baseUrl === '' || $apiKey === '') {
            Log::error('SMS configuration is missing.', [
                'has_url' => $baseUrl !== '',
                'has_key' => $apiKey !== '',
            ]);

            return false;
        }

        $parameters = [
            'key'         => $apiKey,
            'campaign'    => '12754',
            'routeid'     => '7',
            'type'        => 'text',
            'contacts'    => $phone,
            'senderid'    => 'RVGRPS',
            'msg'         => $message,
            'template_id' => '1707178031266790425',
            'pe_id'       => '1701164032595209992',
        ];

        $separator = str_contains($baseUrl, '?') ? '&' : '?';
        $url = rtrim($baseUrl, '&?') . $separator . http_build_query($parameters);

        try {
            $response = Http::connectTimeout(5)
                ->timeout(10)
                ->retry(2, 500, throw: false)
                ->acceptJson()
                ->get($url);

            Log::info('Login OTP SMS provider response.', [
                'phone'      => $this->maskPhone($phone),
                'status'     => $response->status(),
                'successful' => $response->successful(),
                'body'       => mb_substr($response->body(), 0, 1000),
            ]);

            return $response->successful();
        } catch (Throwable $exception) {
            Log::error('Login OTP SMS dispatch failed.', [
                'phone'   => $this->maskPhone($phone),
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mask a mobile number before writing it to logs.
     */
    private function maskPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = trim($phone);

        if (strlen($phone) <= 4) {
            return '****';
        }

        return str_repeat('*', strlen($phone) - 4) . substr($phone, -4);
    }
}