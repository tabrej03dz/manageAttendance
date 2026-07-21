<?php

namespace App\Http\Middleware;

use App\Models\UserActivity;
use App\Models\UserActivityPage;
use App\Services\UserDeviceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Activity ko Blade render hone se pehle create/update karein
        |--------------------------------------------------------------------------
        */

        if ($this->shouldTrack($request)) {
            $this->recordPageVisit($request);
        }

        return $next($request);
    }

    /**
     * Decide whether this request should be tracked.
     */
    private function shouldTrack(Request $request): bool
    {
        if (!auth()->check()) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Heartbeat, end aur activity report requests ko page visit na maanein
        |--------------------------------------------------------------------------
        */

        if (
            $request->routeIs('user-activity.heartbeat') ||
            $request->routeIs('user-activity.end')
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Sirf normal browser GET pages track karein
        |--------------------------------------------------------------------------
        */

        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return false;
        }

        return true;
    }

    /**
     * Create/update user activity and page visit.
     */
    private function recordPageVisit(Request $request): void
    {
        try {
            DB::transaction(function () use ($request) {
                $user = auth()->user();

                if (!$user) {
                    return;
                }

                $activity = $this->getCurrentActivity(
                    (int) $user->id
                );

                /*
                |--------------------------------------------------------------------------
                | 30 minute inactive session ko close karein
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

                    session()->forget(
                        'current_user_activity_id'
                    );

                    $activity = null;
                }

                /*
                |--------------------------------------------------------------------------
                | New activity session create karein
                |--------------------------------------------------------------------------
                */

                if (!$activity) {
                    $activity = $this->createActivity(
                        $request,
                        $user
                    );

                    session([
                        'current_user_activity_id' =>
                            $activity->id,
                    ]);
                }

                $routeName = $request->route()?->getName();
                $officeId = $this->resolveOfficeId();

                /*
                |--------------------------------------------------------------------------
                | Current activity details update karein
                |--------------------------------------------------------------------------
                |
                | Yahan DB::raw use nahi karna hai.
                |
                */

                $activity->update([
                    'office_id' => $officeId,
                    'last_seen_at' => now(),
                    'current_route' => $routeName,
                    'current_url' => $request->fullUrl(),
                    'status' => 'active',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Safe integer increment
                |--------------------------------------------------------------------------
                */

                // $activity->increment('page_views');

                UserActivity::query()
                    ->whereKey($activity->id)
                    ->increment('page_views');

                    $activity->refresh();
                /*
                |--------------------------------------------------------------------------
                | Page-wise activity save karein
                |--------------------------------------------------------------------------
                */

                $this->recordActivityPage(
                    request: $request,
                    activity: $activity,
                    userId: (int) $user->id,
                    officeId: $officeId,
                    routeName: $routeName
                );

                /*
                |--------------------------------------------------------------------------
                | Blade JavaScript ko activity ID dein
                |--------------------------------------------------------------------------
                */

                View::share(
                    'currentUserActivityId',
                    $activity->id
                );
            });
        } catch (\Throwable $exception) {
            Log::error('User activity tracking failed', [
                'user_id' => auth()->id(),
                'url' => $request->fullUrl(),
                'route' => $request->route()?->getName(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            report($exception);
        }
    }

    /**
     * Get current unclosed activity session.
     */
    private function getCurrentActivity(
        int $userId
    ): ?UserActivity {
        $activityId = session(
            'current_user_activity_id'
        );

        if (!$activityId) {
            return null;
        }

        return UserActivity::query()
            ->whereKey($activityId)
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->first();
    }

    /**
     * Create a new activity session.
     */
    private function createActivity(
        Request $request,
        $user
    ): UserActivity {
        $device = UserDeviceService::detect(
            $request->userAgent()
        );

        return UserActivity::create([
            'user_id' => $user->id,

            'office_id' => $this->resolveOfficeId(),

            'activity_uuid' => (string) Str::uuid(),

            'laravel_session_id' =>
                session()->getId(),

            'started_at' => now(),
            'last_seen_at' => now(),
            'ended_at' => null,

            'active_seconds' => 0,
            'page_views' => 0,

            'current_route' =>
                $request->route()?->getName(),

            'current_url' =>
                $request->fullUrl(),

            'current_page_title' => null,

            'ip_address' =>
                $request->ip(),

            'user_agent' =>
                $request->userAgent(),

            'device_type' =>
                $device['device_type'] ?? null,

            'browser' =>
                $device['browser'] ?? null,

            'platform' =>
                $device['platform'] ?? null,

            'status' => 'active',
        ]);
    }

    /**
     * Create or update page-wise activity.
     */
    private function recordActivityPage(
        Request $request,
        UserActivity $activity,
        int $userId,
        ?int $officeId,
        ?string $routeName
    ): void {
        $pageQuery = UserActivityPage::query()
            ->where(
                'user_activity_id',
                $activity->id
            );

        /*
        |--------------------------------------------------------------------------
        | Named route ho to route ke basis par page find karein
        |--------------------------------------------------------------------------
        */

        if ($routeName) {
            $pageQuery->where(
                'route_name',
                $routeName
            );
        } else {
            /*
            |--------------------------------------------------------------------------
            | Route name na ho to URL ke basis par page find karein
            |--------------------------------------------------------------------------
            */

            $pageQuery->where(
                'page_url',
                $request->url()
            );
        }

        $page = $pageQuery->first();

        /*
        |--------------------------------------------------------------------------
        | Existing page visit update karein
        |--------------------------------------------------------------------------
        */

        if ($page) {
            $page->update([
                'last_visited_at' => now(),
                'page_url' => $request->url(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | DB::raw ki jagah increment
            |--------------------------------------------------------------------------
            */

            // $page->increment('visit_count');

            UserActivityPage::query()
                ->whereKey($page->id)
                ->increment('visit_count');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | New page visit create karein
        |--------------------------------------------------------------------------
        */

        UserActivityPage::create([
            'user_activity_id' => $activity->id,
            'user_id' => $userId,
            'office_id' => $officeId,

            'route_name' => $routeName,
            'page_title' => null,
            'page_url' => $request->url(),

            'visit_count' => 1,
            'active_seconds' => 0,

            'first_visited_at' => now(),
            'last_visited_at' => now(),
        ]);
    }

    /**
     * Resolve active office.
     */
    private function resolveOfficeId(): ?int
    {
        $user = auth()->user();

        if (session()->has('active_office_id')) {
            return (int) session(
                'active_office_id'
            );
        }

        if ($user?->office_id) {
            return (int) $user->office_id;
        }

        return null;
    }
}