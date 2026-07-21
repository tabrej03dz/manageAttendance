<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserActivityPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserActivityPageController extends Controller
{
public function index(Request $request)
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

    $from = $request->filled('from')
        ? Carbon::parse($request->from)->startOfDay()
        : now()->startOfDay();

    $to = $request->filled('to')
        ? Carbon::parse($request->to)->endOfDay()
        : now()->endOfDay();

    /*
    |--------------------------------------------------------------------------
    | Allowed users
    |--------------------------------------------------------------------------
    */

    $allowedUserIds = $this->allowedUserIds($request);

    $superAdminIds = User::role('super_admin')
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->all();

    $allowedUserIds = array_values(
        array_diff(
            $allowedUserIds,
            $superAdminIds
        )
    );

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
            ->where(
                'last_seen_at',
                '>=',
                now()->subSeconds(45)
            )
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
            SUM(active_seconds) as total_seconds,
            SUM(page_views) as total_page_views,
            COUNT(*) as total_sessions,
            MAX(last_seen_at) as last_seen_at
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
            SUM(visit_count) as total_visits,
            SUM(active_seconds) as total_seconds
        ')
        ->whereIn('user_id', $allowedUserIds)
        ->whereBetween(
            'first_visited_at',
            [$from, $to]
        )
        ->groupBy(
            'route_name',
            'page_title',
            'page_url'
        )
        ->orderByDesc('total_seconds')
        ->limit(10)
        ->get();

    return view(
        'user_activity.index',
        compact(
            'summary',
            'userUsage',
            'topPages',
            'from',
            'to'
        )
    );
}

    public function show(Request $request, User $user)
    {
        abort_unless(
            in_array(
                $user->id,
                $this->allowedUserIds($request),
                true
            ),
            403
        );

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $activities = UserActivity::query()
            ->with([
                'office:id,name',
                'pages' => fn ($query) =>
                    $query->orderByDesc('active_seconds'),
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
                ->where('office_id', $loggedInUser->activeOfficeId())
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