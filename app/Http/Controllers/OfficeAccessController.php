<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeAccessController extends Controller
{
    public function switchOffice(Request $request, Office $office)
    {
        $user = $request->user();

        if (!$user->hasRole('super_admin')) {
            abort(403, 'Only super admin can switch office.');
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

        if (!$user->hasRole('super_admin')) {
            abort(403, 'Only super admin can clear office switch.');
        }

        session()->forget(['active_office_id', 'active_office_name']);

        return redirect()->back()->with('success', 'Office view reset successfully.');
    }
}