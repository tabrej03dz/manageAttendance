<?php

namespace App\Http\Middleware;

use App\Models\UserActivity;
use App\Models\UserActivityPage;
use App\Services\UserDeviceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | Sirf logged-in users track honge
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | AJAX tracking routes ko dobara track nahi karna
        |--------------------------------------------------------------------------
        */

        if (
            $request->routeIs('user-activity.heartbeat') ||
            $request->routeIs('user-activity.end')
        ) {
            return $response;
        }

        /*
        |--------------------------------------------------------------------------
        | Sirf GET page requests count karna
        |--------------------------------------------------------------------------
        */

        if (
            !$request->isMethod('GET') ||
            $request->expectsJson() ||
            $request->ajax()
        ) {
            return $response;
        }

        $this->recordPageVisit($request);

        return $response;
    }

    private function recordPageVisit(Request $request): void
    {
        try {
            DB::transaction(function () use ($request) {
                $user = auth()->user();

                $activityId = session(
                    'current_user_activity_id'
                );

                $activity = null;

                if ($activityId) {
                    $activity = UserActivity::query()
                        ->where('id', $activityId)
                        ->where('user_id', $user->id)
                        ->whereNull('ended_at')
                        ->first();
                }

                /*
                |--------------------------------------------------------------------------
                | Existing activity purani ho gayi to new session
                |--------------------------------------------------------------------------
                */

                if (
                    $activity &&
                    $activity->last_seen_at &&
                    $activity->last_seen_at->lt(
                        now()->subMinutes(30)
                    )
                ) {
                    $activity->update([
                        'status' => 'ended',
                        'ended_at' => $activity->last_seen_at,
                    ]);

                    $activity = null;
                }

                /*
                |--------------------------------------------------------------------------
                | New activity session
                |--------------------------------------------------------------------------
                */

                if (!$activity) {
                    $device = UserDeviceService::detect(
                        $request->userAgent()
                    );

                    $activity = UserActivity::create([
                        'user_id' => $user->id,
                        'office_id' => $this->resolveOfficeId(
                            $request
                        ),
                        'activity_uuid' => (string) Str::uuid(),
                        'laravel_session_id' => session()->getId(),
                        'started_at' => now(),
                        'last_seen_at' => now(),
                        'active_seconds' => 0,
                        'page_views' => 0,
                        'current_route' => $request
                            ->route()
                            ?->getName(),
                        'current_url' => $request->fullUrl(),
                        'current_page_title' => null,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'device_type' => $device['device_type'],
                        'browser' => $device['browser'],
                        'platform' => $device['platform'],
                        'status' => 'active',
                    ]);

                    session([
                        'current_user_activity_id' => $activity->id,
                    ]);
                }

                $routeName = $request->route()?->getName();

                $activity->update([
                    'office_id' => $this->resolveOfficeId(
                        $request
                    ),
                    'last_seen_at' => now(),
                    'current_route' => $routeName,
                    'current_url' => $request->fullUrl(),
                    'status' => 'active',
                    'page_views' => DB::raw(
                        'page_views + 1'
                    ),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Page-wise usage
                |--------------------------------------------------------------------------
                */

                $page = UserActivityPage::query()
                    ->where(
                        'user_activity_id',
                        $activity->id
                    )
                    ->where(function ($query) use (
                        $routeName,
                        $request
                    ) {
                        if ($routeName) {
                            $query->where(
                                'route_name',
                                $routeName
                            );
                        } else {
                            $query->where(
                                'page_url',
                                $request->url()
                            );
                        }
                    })
                    ->first();

                if ($page) {
                    $page->update([
                        'visit_count' => DB::raw(
                            'visit_count + 1'
                        ),
                        'last_visited_at' => now(),
                        'page_url' => $request->url(),
                    ]);
                } else {
                    UserActivityPage::create([
                        'user_activity_id' => $activity->id,
                        'user_id' => $user->id,
                        'office_id' => $this->resolveOfficeId(
                            $request
                        ),
                        'route_name' => $routeName,
                        'page_title' => null,
                        'page_url' => $request->url(),
                        'visit_count' => 1,
                        'active_seconds' => 0,
                        'first_visited_at' => now(),
                        'last_visited_at' => now(),
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Blade file ko activity ID dena
                |--------------------------------------------------------------------------
                */

                view()->share(
                    'currentUserActivityId',
                    $activity->id
                );
            });
        } catch (\Throwable $exception) {
            /*
            |--------------------------------------------------------------------------
            | Tracking error ki wajah se main software band nahi hona chahiye
            |--------------------------------------------------------------------------
            */

            report($exception);
        }
    }

    private function resolveOfficeId(
        Request $request
    ): ?int {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Switched office ko preference
        |--------------------------------------------------------------------------
        */

        if (session()->has('active_office_id')) {
            return (int) session('active_office_id');
        }

        return $user->office_id
            ? (int) $user->office_id
            : null;
    }
}