<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeAccessController extends Controller
{
    // public function switchOffice(Request $request, Office $office)
    // {
    //     $user = $request->user();

    //     if (!$user->hasRole('super_admin')) {
    //         abort(403, 'Only super admin can switch office.');
    //     }

    //     session([
    //         'active_office_id' => $office->id,
    //         'active_office_name' => $office->name,
    //     ]);

    //     return redirect()->back()->with('success', "Now viewing records for office: {$office->name}");
    // }

    // public function clearSwitch(Request $request)
    // {
    //     $user = $request->user();

    //     if (!$user->hasRole('super_admin')) {
    //         abort(403, 'Only super admin can clear office switch.');
    //     }

    //     session()->forget(['active_office_id', 'active_office_name']);

    //     return redirect()->back()->with('success', 'Office view reset successfully.');
    // }

    public function switchOffice(Request $request, Office $office)
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            // super admin kisi bhi office me switch kar sakta hai
            $allowed = true;
        } elseif ($user->hasRole('owner')) {
            // owner sirf apne office me switch kar sake
            $allowed = (int) $office->owner_id === (int) $user->id;
        } else {
            $allowed = false;
        }

        if (!$allowed) {
            abort(403, 'You are not allowed to switch to this office.');
        }

        session([
            'active_office_id' => $office->id,
            'active_office_name' => $office->name,
        ]);

        return redirect()->back()->with('success', "Now viewing records for office: {$office->name}");
    }

    public function clearSwitch(Request $request)
    {
        $user = $request->user();

        if (!($user->hasRole('super_admin') || $user->hasRole('owner'))) {
            abort(403, 'You are not allowed to clear office switch.');
        }

        session()->forget(['active_office_id', 'active_office_name']);

        return redirect()->back()->with('success', 'Office view reset successfully.');
    }
}