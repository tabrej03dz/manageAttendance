<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserActivityPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserActivityPageController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeActivityManager($request);

        [$from, $to] = $this->resolveDateRange($request);

        $allowedUserIds = $this->visibleUserIds($request);

        $activitiesQuery = UserActivity::query()
            ->with([
                'user:id,name,email,phone,office_id',
                'office:id,name',
            ])
            ->whereIn('user_id', $allowedUserIds)
            ->whereBetween('started_at', [$from, $to]);

        $summary = [
            'total_users' => (clone $activitiesQuery)
                ->distinct('user_id')
                ->count('user_id'),

            'online_users' => UserActivity::query()
                ->whereIn('user_id', $allowedUserIds)
                ->where('status', 'active')
                ->where('last_seen_at', '>=', now()->subSeconds(45))
                ->distinct('user_id')
                ->count('user_id'),

            'total_seconds' => (clone $activitiesQuery)
                ->sum('active_seconds'),

            'page_views' => (clone $activitiesQuery)
                ->sum('page_views'),

            'sessions' => (clone $activitiesQuery)
                ->count(),
        ];

        $userUsage = UserActivity::query()
            ->selectRaw('
                user_id,
                SUM(active_seconds) AS total_seconds,
                SUM(page_views) AS total_page_views,
                COUNT(*) AS total_sessions,
                MAX(last_seen_at) AS last_seen_at
            ')
            ->with([
                'user:id,name,email,phone,office_id',
            ])
            ->whereIn('user_id', $allowedUserIds)
            ->whereBetween('started_at', [$from, $to])
            ->groupBy('user_id')
            ->orderByDesc('total_seconds')
            ->paginate(20)
            ->withQueryString();

        $topPages = UserActivityPage::query()
            ->selectRaw('
                route_name,
                page_title,
                page_url,
                SUM(visit_count) AS total_visits,
                SUM(active_seconds) AS total_seconds
            ')
            ->whereIn('user_id', $allowedUserIds)
            ->whereBetween('first_visited_at', [$from, $to])
            ->groupBy(
                'route_name',
                'page_title',
                'page_url'
            )
            ->orderByDesc('total_seconds')
            ->limit(10)
            ->get();

        return view('user_activity.index', compact(
            'summary',
            'userUsage',
            'topPages',
            'from',
            'to'
        ));
    }

    public function show(Request $request, User $user)
    {
        $this->authorizeActivityManager($request);
        $this->authorizeUserAccess($request, $user);

        [$from, $to] = $this->resolveDateRange($request);

        $activities = UserActivity::query()
            ->with([
                'office:id,name',
                'pages' => fn ($query) => $query
                    ->orderByDesc('active_seconds'),
            ])
            ->where('user_id', $user->id)
            ->whereBetween('started_at', [$from, $to])
            ->latest('started_at')
            ->paginate(20)
            ->withQueryString();

        return view('user_activity.show', compact(
            'user',
            'activities',
            'from',
            'to'
        ));
    }

    /**
     * Selected date range की सभी allowed users की activity delete करें।
     */
    public function clearRange(Request $request)
    {
        $this->authorizeActivityManager($request);

        $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        [$from, $to] = $this->resolveDateRange($request);

        $allowedUserIds = $this->visibleUserIds($request);

        $deletedSessions = $this->deleteActivities(
            UserActivity::query()
                ->whereIn('user_id', $allowedUserIds)
                ->whereBetween('started_at', [$from, $to])
        );

        return redirect()
            ->route('user-activity.index', [
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ])
            ->with(
                'success',
                "{$deletedSessions} activity session(s) successfully cleared."
            );
    }

    /**
     * Selected user की selected date range activity delete करें।
     */
    public function clearUserRange(Request $request, User $user)
    {
        $this->authorizeActivityManager($request);
        $this->authorizeUserAccess($request, $user);

        $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        [$from, $to] = $this->resolveDateRange($request);

        $deletedSessions = $this->deleteActivities(
            UserActivity::query()
                ->where('user_id', $user->id)
                ->whereBetween('started_at', [$from, $to])
        );

        return redirect()
            ->route('user-activity.show', [
                'user' => $user->id,
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ])
            ->with(
                'success',
                "{$deletedSessions} session(s) cleared for {$user->name}."
            );
    }

    /**
     * केवल एक session delete करें।
     */
    public function destroySession(
        Request $request,
        UserActivity $activity
    ) {
        $this->authorizeActivityManager($request);

        abort_unless(
            in_array(
                (int) $activity->user_id,
                $this->visibleUserIds($request),
                true
            ),
            403
        );

        DB::transaction(function () use ($activity) {
            $activity->pages()->delete();
            $activity->delete();
        });

        return back()->with(
            'success',
            "Session #{$activity->id} successfully cleared."
        );
    }

    /**
     * Activity sessions और उनकी pages सुरक्षित तरीके से delete करें।
     */
    private function deleteActivities(Builder $query): int
    {
        $deletedSessions = 0;

        DB::transaction(function () use (
            $query,
            &$deletedSessions
        ) {
            $query
                ->select('id')
                ->orderBy('id')
                ->chunkById(
                    200,
                    function ($activities) use (&$deletedSessions) {
                        foreach ($activities as $activity) {
                            $activity->pages()->delete();
                            $activity->delete();

                            $deletedSessions++;
                        }
                    }
                );
        });

        return $deletedSessions;
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        abort_if(
            $from->greaterThan($to),
            422,
            'From date cannot be greater than to date.'
        );

        return [$from, $to];
    }

    private function authorizeActivityManager(Request $request): void
    {
        abort_unless(
            $request->user()->hasAnyRole([
                'super_admin',
                'owner',
                'admin',
                'team_leader',
            ]),
            403
        );
    }

    private function authorizeUserAccess(
        Request $request,
        User $user
    ): void {
        abort_unless(
            in_array(
                (int) $user->id,
                $this->visibleUserIds($request),
                true
            ),
            403
        );
    }

    /**
     * Activity page पर दिखाई देने वाले users।
     * Super admins को activity list/delete से exclude रखा गया है।
     */
    private function visibleUserIds(Request $request): array
    {
        $allowedUserIds = $this->allowedUserIds($request);

        $superAdminIds = User::role('super_admin')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return array_values(
            array_diff($allowedUserIds, $superAdminIds)
        );
    }

    private function allowedUserIds(Request $request): array
    {
        $loggedInUser = $request->user();

        if ($loggedInUser->hasRole('super_admin')) {
            return User::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($loggedInUser->hasRole('owner')) {
            $officeIds = $loggedInUser
                ->offices()
                ->pluck('id');

            return User::query()
                ->whereIn('office_id', $officeIds)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($loggedInUser->hasRole('admin')) {
            return User::query()
                ->where(
                    'office_id',
                    $loggedInUser->activeOfficeId()
                )
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($loggedInUser->hasRole('team_leader')) {
            return $loggedInUser
                ->getAllTeamMembers()
                ->pluck('id')
                ->push($loggedInUser->id)
                ->unique()
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        return [(int) $loggedInUser->id];
    }
}