<?php

namespace App\Helpers;

use App\Models\Office;
use App\Models\User;

class OfficeHelper
{
    public static function getActiveOfficeId(?User $user): ?int
    {
        if (!$user) {
            return null;
        }

        if ($user->hasRole('super_admin')) {
            return session('active_office_id');
        }

        if ($user->hasRole('owner')) {
            return Office::where('owner_id', $user->id)->value('id');
        }

        return $user->office_id;
    }
}