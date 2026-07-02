<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeAccessController extends Controller
{
    private function hasSwitchOfficeAccess($user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasRole('owner')
            || $user->can('switch offices')
            || $user->can('switch office');
    }

    private function userCanSwitchToOffice($user, Office $office): bool
    {
        if (!$this->hasSwitchOfficeAccess($user)) {
            return false;
        }

        // Super admin kisi bhi office me switch kar sakta hai
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Owner sirf apni offices me switch kar sakta hai
        if ($user->hasRole('owner')) {
            return (int) $office->owner_id === (int) $user->id;
        }

        /*
            Normal user with permission:
            user.office_id -> current office
            current office.owner_id -> owner
            selected office.owner_id same hona chahiye
        */
        $currentOffice = $user->office;

        if (!$currentOffice || !$currentOffice->owner_id) {
            return false;
        }

        return (int) $office->owner_id === (int) $currentOffice->owner_id;
    }

    public function switchOffice(Request $request, Office $office)
    {
        $user = $request->user();

        if (!$this->userCanSwitchToOffice($user, $office)) {
            abort(403, 'You are not allowed to switch to this office.');
        }

        session([
            'active_office_id' => $office->id,
            'active_office_name' => $office->name,
        ]);

        return redirect()
            ->back()
            ->with('success', "Now viewing records for office: {$office->name}");
    }

    public function clearSwitch(Request $request)
    {
        $user = $request->user();

        if (!$this->hasSwitchOfficeAccess($user)) {
            abort(403, 'You are not allowed to clear office switch.');
        }

        session()->forget([
            'active_office_id',
            'active_office_name',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Office view reset successfully.');
    }
}