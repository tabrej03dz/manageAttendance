<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeAccessController extends Controller
{
    public function switchOffice(Request $request, Office $office)
    {
        $user = $request->user();

        if (!$user->can('switch offices')) {
            abort(403, 'You do not have permission to switch office.');
        }

        $allowed = false;

        if ($user->hasRole('super_admin')) {
            // Super admin permission hai to kisi bhi office me switch kar sakta hai.
            $allowed = true;
        } elseif ($user->hasRole('owner')) {
            // Owner sirf apni offices me switch kar sakta hai.
            $allowed = (int) $office->owner_id === (int) $user->id;
        } else {
            /*
                Normal user:
                users.office_id -> offices.id
                offices.owner_id -> owner id

                User jis office me assigned hai,
                us office ke owner ki offices me hi switch kar sakta hai.
            */

            $currentOffice = $user->office;

            if ($currentOffice && $currentOffice->owner_id) {
                $allowed = (int) $office->owner_id === (int) $currentOffice->owner_id;
            }
        }

        if (!$allowed) {
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

        if (!$user->can('switch offices')) {
            abort(403, 'You do not have permission to clear office switch.');
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