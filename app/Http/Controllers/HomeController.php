<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Leave;
use App\Models\Office;
use App\Models\User;
use App\Models\UserAdditionalInformation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class HomeController extends Controller
{


    public function mainpage()
    {
        return view('mainpage.index');
    }

    public function blogs()
    {
        return view('mainpage.blog');
    }

    public function blogDetailsPage()
    {
        return view('mainpage.blogDetailsPage');
    }

    public function reqDemo()
    {
        return view('mainpage.reqaDemo');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        //        $halfDayRecords = AttendanceRecord::whereDateBetween('created_at', );
        return view('home');
    }

    public function profile(User $user)
    {

        $leaves = Leave::where('user_id', $user->id)
            ->whereDate('start_date', '>', today())
            ->get();

        $infos = UserAdditionalInformation::where('user_id', $user->id)->get();
        return view('dashboard.user.profile', compact('leaves', 'user', 'infos'));
    }

    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return back()->with('success', 'Password updated successfully');
        } else {
            return back()->with('error', 'Current password does not match');
        }
    }


    public static function getTime($totalMinutes)
    {
        $hours = (int)($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return "$hours h, $minutes m";
    }

    public function updateProfile(Request $request, User $user)
    {
        $status = $user->update($request->all());
        if ($request->file('photo')) {
            $file = $request->file('photo')->store('public/images');
            $user->photo = str_replace('public/', '', $file);
            $user->save();
        }
        return back()->with('success', 'updated successfully');
    }

    static function employeeList()
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin|admin')) {
            if ($user->hasRole('super_admin')) {
                $employees = User::all();
            } else {
                $office = $user->office;
                $employees = $office->users;
            }
        } elseif ($user->hasRole('owner')) {
            $officeIds = Office::where('owner_id', $user->id)->pluck('id');
            $employees = User::whereIn('office_id', $officeIds)->get();
        } else {
            if ($user->hasRole('team_leader')) {
                $employees = $user->members;
                $record = User::where('id', $user->id)->first();
                $employees->push($record);
            } else {
                $employees = User::where('id', $user->id)->get();
            }
        }
        return $employees;
    }

    static function latitudeInDMS($decimal)
    {
        // Determine if the latitude is in the Northern or Southern Hemisphere
        $hemisphere = $decimal >= 0 ? 'N' : 'S';

        // Convert the absolute value to handle negative latitudes
        $decimal = abs($decimal);

        // Get the degrees
        $degrees = intval($decimal);

        // Get the remaining decimal and convert to minutes
        $minutesDecimal = ($decimal - $degrees) * 60;
        $minutes = intval($minutesDecimal);

        // Convert the remaining decimal from minutes to seconds
        $seconds = ($minutesDecimal - $minutes) * 60;

        // Return the formatted DMS string with hemisphere
        return sprintf("%d°%d'%0.1f\"%s", $degrees, $minutes, $seconds, $hemisphere);
    }

    static function longitudeInDMS($decimal)
    {
        // Determine if the longitude is in the Eastern or Western Hemisphere
        $hemisphere = $decimal >= 0 ? 'E' : 'W';

        // Convert the absolute value to handle negative longitudes
        $decimal = abs($decimal);

        // Get the degrees
        $degrees = intval($decimal);

        // Get the remaining decimal and convert to minutes
        $minutesDecimal = ($decimal - $degrees) * 60;
        $minutes = intval($minutesDecimal);

        // Convert the remaining decimal from minutes to seconds
        $seconds = ($minutesDecimal - $minutes) * 60;

        // Return the formatted DMS string with hemisphere
        return sprintf("%d°%d'%0.1f\"%s", $degrees, $minutes, $seconds, $hemisphere);
    }

    //    public function changePass(){
    //        $user = User::where('admin@admin.com')
    //    }


    // public function mainindex()
    // {
    //     return view('mainpage.index');
    // }
}
