@extends('dashboard.layout.root')

@section('title', 'User Activity')

@push('styles')
<style>
    .activity-page {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --border: #e2e8f0;
        --muted: #64748b;
        --heading: #0f172a;
        --surface: #ffffff;
        --soft: #f8fafc;
        --success: #16a34a;
        --danger: #ef4444;
        color: var(--heading);
    }

    .activity-page .page-heading {
        font-size: 25px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .activity-page .page-description {
        color: var(--muted);
        font-size: 14px;
        margin: 0;
    }

    .activity-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .05);
    }

    .activity-filter {
        padding: 20px;
    }

    .activity-filter label {
        display: block;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .activity-filter .form-control,
    .activity-filter .custom-select {
        height: 46px;
        border: 1px solid #cbd5e1;
        border-radius: 11px;
        color: #0f172a;
        font-size: 14px;
        box-shadow: none;
    }

    .activity-filter .form-control:focus,
    .activity-filter .custom-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
    }

    .activity-filter .btn {
        height: 46px;
        border-radius: 11px;
        font-size: 14px;
        font-weight: 600;
        padding-left: 20px;
        padding-right: 20px;
    }

    .btn-filter {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .btn-filter:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        color: #fff;
    }

    .btn-reset {
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #334155;
    }

    .btn-reset:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .summary-card {
        position: relative;
        min-height: 126px;
        padding: 20px;
        overflow: hidden;
    }

    .summary-label {
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .summary-value {
        color: #020617;
        font-size: 26px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 10px;
    }

    .summary-help {
        color: #94a3b8;
        font-size: 12px;
        margin: 0;
    }

    .summary-icon {
        position: absolute;
        top: 18px;
        right: 18px;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .icon-blue {
        color: #2563eb;
        background: #eff6ff;
    }

    .icon-green {
        color: #16a34a;
        background: #f0fdf4;
    }

    .icon-violet {
        color: #7c3aed;
        background: #f5f3ff;
    }

    .icon-orange {
        color: #ea580c;
        background: #fff7ed;
    }

    .panel-header {
        min-height: 90px;
        padding: 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
    }

    .panel-title {
        color: #020617;
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .panel-subtitle {
        color: var(--muted);
        font-size: 13px;
        margin: 0;
    }

    .panel-badge {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        padding: 6px 11px;
    }

    .user-ranking-table {
        margin: 0;
    }

    .user-ranking-table thead th {
        background: #f8fafc;
        color: #334155;
        border-top: 0;
        border-bottom: 1px solid var(--border);
        font-size: 12px;
        font-weight: 700;
        padding: 14px 16px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .user-ranking-table tbody td {
        border-top: 1px solid #edf2f7;
        padding: 16px;
        vertical-align: middle;
        font-size: 13px;
    }

    .user-ranking-table tbody tr:hover {
        background: #f8fafc;
    }

    .user-ranking-table tbody tr:first-child {
        background: #fff7ed;
    }

    .user-ranking-table tbody tr:first-child td:first-child {
        border-left: 4px solid #f97316;
    }

    .rank-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
    }

    .rank-circle.first-rank {
        background: #fef3c7;
        color: #b45309;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-weight: 700;
        text-transform: uppercase;
    }

    .user-name {
        color: #0f172a;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .user-contact {
        color: #94a3b8;
        font-size: 11px;
        word-break: break-all;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 9px;
    }

    .status-online {
        color: #15803d;
        background: #dcfce7;
    }

    .status-offline {
        color: #64748b;
        background: #f1f5f9;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .usage-value {
        color: #0f172a;
        font-size: 14px;
        font-weight: 700;
        white-space: nowrap;
    }

    .usage-highlight {
        color: #16a34a;
        font-size: 11px;
        font-weight: 600;
        margin-top: 3px;
    }

    .view-button {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        background: #eff6ff;
        border: 0;
        transition: .2s ease;
    }

    .view-button:hover {
        color: #fff;
        background: #2563eb;
        text-decoration: none;
    }

    .top-page-item {
        min-height: 78px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .top-page-item:last-child {
        border-bottom: 0;
    }

    .top-page-item:hover {
        background: #f8fafc;
    }

    .page-rank {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 12px;
        font-weight: 700;
    }

    .page-information {
        min-width: 0;
        flex: 1;
    }

    .page-name {
        color: #020617;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .page-route {
        color: #94a3b8;
        font-size: 11px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .page-stats {
        flex-shrink: 0;
        text-align: right;
    }

    .page-duration {
        color: #020617;
        font-size: 14px;
        font-weight: 700;
        white-space: nowrap;
    }

    .page-visits {
        color: #64748b;
        font-size: 11px;
        margin-top: 3px;
        white-space: nowrap;
    }

    .empty-state {
        padding: 55px 20px;
        text-align: center;
    }

    .empty-state-icon {
        width: 58px;
        height: 58px;
        margin: 0 auto 14px;
        border-radius: 16px;
        color: #64748b;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .pagination-wrapper {
        padding: 14px 18px;
        border-top: 1px solid var(--border);
    }

    @media (max-width: 767.98px) {
        .activity-page .page-heading {
            font-size: 21px;
        }

        .activity-filter {
            padding: 16px;
        }

        .activity-filter .btn {
            width: 100%;
        }

        .filter-buttons {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .filter-buttons .btn {
            margin: 0 !important;
        }

        .summary-card {
            min-height: 116px;
        }

        .panel-header {
            min-height: auto;
            padding: 16px;
        }

        .panel-title {
            font-size: 16px;
        }

        .top-page-item {
            padding: 13px;
        }

        .page-stats {
            max-width: 90px;
        }
    }
</style>
@endpush

@section('content')
@php
    $formatDuration = function ($seconds) {
        $seconds = max(0, (int) $seconds);

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        if ($minutes > 0) {
            return $minutes . 'm ' . $remainingSeconds . 's';
        }

        return $remainingSeconds . 's';
    };

    $usageCollection = collect(
        method_exists($userUsage, 'getCollection')
            ? $userUsage->getCollection()
            : $userUsage
    );

    $totalUsageSeconds = (int) (
        $summary['total_seconds']
        ?? $summary['total_usage_seconds']
        ?? $usageCollection->sum('total_seconds')
    );

    $totalPageViews = (int) (
        $summary['page_views']
        ?? $usageCollection->sum('total_page_views')
    );

    $activeUsers = (int) (
        $summary['total_users']
        ?? $usageCollection->count()
    );

    $onlineUsers = (int) (
        $summary['online_users']
        ?? 0
    );

    $totalSessions = (int) (
        $summary['sessions']
        ?? $usageCollection->sum('total_sessions')
    );

    $averagePageTime = (int) (
        $summary['average_page_time']
        ?? (
            $totalPageViews > 0
                ? round($totalUsageSeconds / $totalPageViews)
                : 0
        )
    );
@endphp

<div class="activity-page">

    <section class="content-header pb-2">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <h1 class="page-heading">User Activity</h1>

                    <p class="page-description">
                        User-wise software usage, page visits and active sessions.
                    </p>
                </div>

                <button
                    type="button"
                    class="btn btn-light border rounded-lg mt-2 mt-md-0"
                    onclick="window.location.reload()"
                >
                    <i class="fas fa-sync-alt mr-1"></i>
                    Refresh
                </button>

                <div class="d-flex align-items-center mt-2 mt-md-0">
                    <button
                        type="button"
                        class="btn btn-light border mr-2"
                        onclick="window.location.reload()"
                    >
                        <i class="fas fa-sync-alt mr-1"></i>
                        Refresh
                    </button>

                    <form
                        method="POST"
                        action="{{ route('user-activity.clear') }}"
                        onsubmit="return confirmClearAllActivity(this)"
                    >
                        @csrf
                        @method('DELETE')

                        <input
                            type="hidden"
                            name="from"
                            value="{{ $from?->format('Y-m-d') }}"
                        >

                        <input
                            type="hidden"
                            name="to"
                            value="{{ $to?->format('Y-m-d') }}"
                        >

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="fas fa-trash-alt mr-1"></i>
                            Clear Activity
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- Filters --}}
            <div class="activity-card activity-filter mb-4">
                <form
                    method="GET"
                    action="{{ route('user-activity.index') }}"
                >
                    <div class="row align-items-end">

                        <div class="col-xl-3 col-md-4 mb-3 mb-xl-0">
                            <label for="from">From Date</label>

                            <input
                                type="date"
                                name="from"
                                id="from"
                                class="form-control"
                                value="{{ $from?->format('Y-m-d') }}"
                            >
                        </div>

                        <div class="col-xl-3 col-md-4 mb-3 mb-xl-0">
                            <label for="to">To Date</label>

                            <input
                                type="date"
                                name="to"
                                id="to"
                                class="form-control"
                                value="{{ $to?->format('Y-m-d') }}"
                            >
                        </div>

                        <div class="col-xl-3 col-md-4 mb-3 mb-xl-0">
                            <label>Activity Status</label>

                            <select name="status" class="custom-select">
                                <option value="">All Users</option>

                                <option
                                    value="online"
                                    @selected(request('status') === 'online')
                                >
                                    Online Users
                                </option>

                                <option
                                    value="offline"
                                    @selected(request('status') === 'offline')
                                >
                                    Offline Users
                                </option>
                            </select>
                        </div>

                        <div class="col-xl-3">
                            <div class="d-flex filter-buttons">
                                <button
                                    type="submit"
                                    class="btn btn-filter mr-2"
                                >
                                    <i class="fas fa-filter mr-1"></i>
                                    Apply
                                </button>

                                <a
                                    href="{{ route('user-activity.index') }}"
                                    class="btn btn-reset"
                                >
                                    Reset
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            {{-- Summary Cards --}}
            <div class="row">

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="activity-card summary-card h-100">
                        <div class="summary-icon icon-blue">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div class="summary-label">
                            Total Active Usage
                        </div>

                        <div class="summary-value">
                            {{ $formatDuration($totalUsageSeconds) }}
                        </div>

                        <p class="summary-help">
                            Actual active software time
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="activity-card summary-card h-100">
                        <div class="summary-icon icon-violet">
                            <i class="fas fa-eye"></i>
                        </div>

                        <div class="summary-label">
                            Total Page Views
                        </div>

                        <div class="summary-value">
                            {{ number_format($totalPageViews) }}
                        </div>

                        <p class="summary-help">
                            Total tracked page visits
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="activity-card summary-card h-100">
                        <div class="summary-icon icon-green">
                            <i class="fas fa-users"></i>
                        </div>

                        <div class="summary-label">
                            Active Users
                        </div>

                        <div class="summary-value">
                            {{ number_format($activeUsers) }}
                        </div>

                        <p class="summary-help">
                            {{ number_format($onlineUsers) }} currently online
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="activity-card summary-card h-100">
                        <div class="summary-icon icon-orange">
                            <i class="fas fa-stopwatch"></i>
                        </div>

                        <div class="summary-label">
                            Average Page Time
                        </div>

                        <div class="summary-value">
                            {{ $formatDuration($averagePageTime) }}
                        </div>

                        <p class="summary-help">
                            {{ number_format($totalSessions) }} total sessions
                        </p>
                    </div>
                </div>

            </div>

            <div class="row align-items-start">

                {{-- User Ranking --}}
                <div class="col-xl-8 mb-4">
                    <div class="activity-card overflow-hidden">

                        <div class="panel-header">
                            <div>
                                <h2 class="panel-title">
                                    User-wise Usage Ranking
                                </h2>

                                <p class="panel-subtitle">
                                    Highest active usage वाले users सबसे ऊपर हैं।
                                </p>
                            </div>

                            <span class="panel-badge">
                                High to Low
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table user-ranking-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>User</th>
                                        <th>Status</th>
                                        <th>Usage</th>
                                        <th>Views</th>
                                        <th>Sessions</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($userUsage as $usage)
                                        @php
                                            $totalSeconds = (int) (
                                                $usage->total_seconds ?? 0
                                            );

                                            $lastSeenAt = $usage->last_seen_at
                                                ? \Carbon\Carbon::parse(
                                                    $usage->last_seen_at
                                                )
                                                : null;

                                            $isOnline = $lastSeenAt
                                                && $lastSeenAt->greaterThanOrEqualTo(
                                                    now()->subSeconds(45)
                                                );

                                            $userName = $usage->user?->name
                                                ?? 'Deleted User';

                                            $userInitial = mb_substr(
                                                trim($userName),
                                                0,
                                                1
                                            );

                                            $rank = method_exists(
                                                $userUsage,
                                                'firstItem'
                                            )
                                                ? (
                                                    ($userUsage->firstItem() ?? 1)
                                                    + $loop->index
                                                )
                                                : $loop->iteration;
                                        @endphp

                                        <tr>
                                            <td>
                                                <span
                                                    class="rank-circle {{
                                                        $rank === 1
                                                            ? 'first-rank'
                                                            : ''
                                                    }}"
                                                >
                                                    {{ $rank }}
                                                </span>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar mr-3">
                                                        {{ $userInitial }}
                                                    </div>

                                                    <div>
                                                        <div class="user-name">
                                                            {{ $userName }}
                                                        </div>

                                                        <div class="user-contact">
                                                            {{
                                                                $usage->user?->email
                                                                ?: (
                                                                    $usage->user?->phone
                                                                    ?? 'No contact available'
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                @if($isOnline)
                                                    <span class="status-badge status-online">
                                                        <span class="status-dot"></span>
                                                        Online
                                                    </span>
                                                @else
                                                    <span class="status-badge status-offline">
                                                        <span class="status-dot"></span>
                                                        Offline
                                                    </span>
                                                @endif

                                                @if($lastSeenAt)
                                                    <div class="text-muted mt-1">
                                                        <small>
                                                            {{ $lastSeenAt->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="usage-value">
                                                    {{ $formatDuration($totalSeconds) }}
                                                </div>

                                                @if($rank === 1)
                                                    <div class="usage-highlight">
                                                        Highest Usage
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <strong>
                                                    {{
                                                        number_format(
                                                            $usage->total_page_views
                                                            ?? 0
                                                        )
                                                    }}
                                                </strong>
                                            </td>

                                            <td>
                                                {{
                                                    number_format(
                                                        $usage->total_sessions
                                                        ?? 0
                                                    )
                                                }}
                                            </td>

                                            <td class="text-center">
                                                @if($usage->user)
                                                    <a
                                                        href="{{ route(
                                                            'user-activity.show',
                                                            [
                                                                'user' => $usage->user->id,
                                                                'from' => request('from'),
                                                                'to' => request('to'),
                                                            ]
                                                        ) }}"
                                                        class="view-button"
                                                        title="View user activity"
                                                    >
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="fas fa-chart-line"></i>
                                                    </div>

                                                    <h5>No activity found</h5>

                                                    <p class="text-muted mb-0">
                                                        Selected dates में कोई user activity
                                                        नहीं मिली।
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($userUsage, 'links'))
                            <div class="pagination-wrapper">
                                {{ $userUsage->withQueryString()->links() }}
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Most Used Pages --}}
                <div class="col-xl-4 mb-4">
                    <div class="activity-card overflow-hidden">

                        <div class="panel-header">
                            <div>
                                <h2 class="panel-title">
                                    Most Used Pages
                                </h2>

                                <p class="panel-subtitle">
                                    सबसे ज्यादा active time वाले pages.
                                </p>
                            </div>

                            <span class="panel-badge">
                                Top {{ min(15, collect($topPages)->count()) }}
                            </span>
                        </div>

                        <div>
                            @forelse($topPages as $page)
                                @php
                                    $pageSeconds = (int) (
                                        $page->total_seconds ?? 0
                                    );

                                    $pageName = $page->page_title
                                        ?: $page->route_name
                                        ?: 'Unknown Page';

                                    $pageRoute = $page->route_name
                                        ?: $page->page_url
                                        ?: '-';
                                @endphp

                                <div class="top-page-item">
                                    <div class="page-rank">
                                        {{ $loop->iteration }}
                                    </div>

                                    <div class="page-information">
                                        <div
                                            class="page-name"
                                            title="{{ $pageName }}"
                                        >
                                            {{ $pageName }}
                                        </div>

                                        <div
                                            class="page-route"
                                            title="{{ $pageRoute }}"
                                        >
                                            {{ $pageRoute }}
                                        </div>
                                    </div>

                                    <div class="page-stats">
                                        <div class="page-duration">
                                            {{ $formatDuration($pageSeconds) }}
                                        </div>

                                        <div class="page-visits">
                                            {{
                                                number_format(
                                                    $page->total_visits ?? 0
                                                )
                                            }}
                                            visits
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>

                                    <h5>No page usage found</h5>

                                    <p class="text-muted mb-0">
                                        अभी तक page tracking data उपलब्ध नहीं है।
                                    </p>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>
</div>


@push('scripts')
<script>
    function confirmClearAllActivity(form) {
        const from = form.querySelector('[name="from"]').value;
        const to = form.querySelector('[name="to"]').value;

        return window.confirm(
            `Are you sure?\n\n` +
            `${from} से ${to} तक की सभी visible users की activity permanently delete हो जाएगी.\n\n` +
            `यह action undo नहीं किया जा सकता।`
        );
    }
</script>
@endpush
@endsection