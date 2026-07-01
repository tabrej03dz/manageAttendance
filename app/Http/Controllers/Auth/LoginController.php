<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;

// class LoginController extends Controller
// {
//     /*
//     |--------------------------------------------------------------------------
//     | Login Controller
//     |--------------------------------------------------------------------------
//     |
//     | This controller handles authenticating users for the application and
//     | redirecting them to your home screen. The controller uses a trait
//     | to conveniently provide its functionality to your applications.
//     |
//     */

//     use AuthenticatesUsers;

//     /**
//      * Where to redirect users after login.
//      *
//      * @var string
//      */
//     protected $redirectTo = '/home';

//     /**
//      * Create a new controller instance.
//      *
//      * @return void
//      */
//     public function __construct()
//     {
//         $this->middleware('guest')->except('logout');
//         $this->middleware('auth')->only('logout');
//     }


// }


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function username()
    {
        return 'username';
    }

    


    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|string',
    //         'password' => 'nullable|string',
    //     ]);

    //     $input = $request->username;

    //     $field = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

    //     $user = User::with('office')
    //         ->where($field, $input)
    //         ->where('status', '1')
    //         ->first();

    //     if (!$user) {
    //         throw ValidationException::withMessages([
    //             'username' => ['Invalid login details.'],
    //         ]);
    //     }

    //     $otpEnabled = optional($user->office)->otp_enable == 1;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Case 1: Office OTP enabled + user login by phone
    //     | Password check nahi hoga, direct OTP jayega
    //     |--------------------------------------------------------------------------
    //     */
    //     if ($otpEnabled && $field === 'phone') {
    //         $otp = rand(1000, 9999);

    //         session([
    //             'login_otp_user_id' => $user->id,
    //             'login_otp' => $otp,
    //             'login_remember' => $request->boolean('remember'),
    //             'login_otp_time' => now()->timestamp,
    //         ]);

    //         $this->sendLoginOtp($user->phone, $otp);

    //         return redirect()->route('login.otp')
    //             ->with('success', 'OTP sent to your registered mobile number.');
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Case 2: Office OTP disabled
    //     | Email/phone + password se direct login
    //     |--------------------------------------------------------------------------
    //     */
    //     if (!$request->filled('password') || !Hash::check($request->password, $user->password)) {
    //         throw ValidationException::withMessages([
    //             'username' => ['Invalid login details.'],
    //         ]);
    //     }

    //     Auth::login($user, $request->boolean('remember'));

    //     $request->session()->regenerate();

    //     return redirect()->intended($this->redirectPath());
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

    $phone = $request->phone;

    $user = User::where('phone', $phone)
        ->where('status', '1')
        ->first();

    if (!$user) {
        throw ValidationException::withMessages([
            'phone' => ['Invalid mobile number ya user inactive hai.'],
        ]);
    }

    // Testing ke liye direct login
    Auth::login($user);

    $request->session()->regenerate();

    return redirect()->intended('/home')
        ->with('success', 'Login successful.');
}

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

    //     $phone = $request->phone;

    //     $user = User::where('phone', $phone)
    //         ->where('status', '1')
    //         ->first();

    //     if (!$user) {
    //         throw ValidationException::withMessages([
    //             'phone' => ['Invalid mobile number ya user inactive hai.'],
    //         ]);
    //     }

    //     $otp = rand(100000, 999999);

    //     session([
    //         'login_otp_user_id' => $user->id,
    //         'login_otp'         => $otp,
    //         'login_otp_time'    => now()->timestamp,
    //     ]);

    //     $this->sendLoginOtp($user->phone, $otp);

    //     return redirect()->route('login.otp')
    //         ->with('success', 'OTP aapke registered mobile number par bhej diya gaya hai.');
    // }

    public function showOtpForm()
    {
        if (!session('login_otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.login-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        if (!session('login_otp_user_id')) {
            return redirect()->route('login');
        }

        // OTP 5 minute valid
        if (now()->timestamp - session('login_otp_time') > 300) {
            session()->forget(['login_otp_user_id', 'login_otp', 'login_remember', 'login_otp_time']);

            return redirect()->route('login')
                ->withErrors(['otp' => 'OTP expired. Please login again.']);
        }

        if ($request->otp != session('login_otp')) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        Auth::loginUsingId(session('login_otp_user_id'), session('login_remember', false));

        session()->forget(['login_otp_user_id', 'login_otp', 'login_remember', 'login_otp_time']);

        request()->session()->regenerate();

        return redirect()->intended($this->redirectPath());
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


}
