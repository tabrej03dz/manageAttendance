<?php

namespace App\Http\Middleware;

use App\Models\Office;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveOffice
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Owner ke liye active office
        |--------------------------------------------------------------------------
        */
        if ($user->hasRole('owner')) {
            $activeOfficeId = session('active_office_id');

            /*
             * Check karo session wala office:
             * 1. Isi owner ka hai
             * 2. Active hai
             */
            $validOfficeExists = false;

            if ($activeOfficeId) {
                $validOfficeExists = Office::query()
                    ->where('id', $activeOfficeId)
                    ->where('owner_id', $user->id)
                    ->where('status', 'active')
                    ->exists();
            }

            /*
             * Agar session me valid office nahi mila,
             * to owner ka first active office select karo.
             */
            if (!$validOfficeExists) {
                $office = Office::query()
                    ->where('owner_id', $user->id)
                    ->where('status', 'active')
                    ->orderBy('id')
                    ->first();

                if ($office) {
                    session([
                        'active_office_id' => $office->id,
                    ]);
                } else {
                    session()->forget('active_office_id');
                }
            }

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Normal employee/admin ke liye
        |--------------------------------------------------------------------------
        */
        if (!session()->has('active_office_id') && $user->office_id) {
            $officeExists = Office::query()
                ->where('id', $user->office_id)
                ->where('status', 'active')
                ->exists();

            if ($officeExists) {
                session([
                    'active_office_id' => $user->office_id,
                ]);
            }
        }

        return $next($request);
    }
}