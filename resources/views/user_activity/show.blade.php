@extends('dashboard.layout.root')

@section('title', 'User Activity Details')

@push('styles')
<style>
    .activity-detail-page {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --heading: #0f172a;
        --text: #334155;
        --muted: #64748b;
        --soft-muted: #94a3b8;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft: #f8fafc;
        --success: #16a34a;
        --warning: #d97706;
        --danger: #dc2626;

        color: var(--heading);
    }

    .activity-detail-page .page-title {
        color: var(--heading);
        font-size: 25px;
        font-weight: 700;
        margin: 0 0 5px;
    }

    .activity-detail-page .page-subtitle {
        color: var(--muted);
        font-size: 14px;
        margin: 0;
    }

    .activity-detail-page .page-action {
        min-height: 42px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 15px;
    }

    .activity-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .05);
        overflow: hidden;
    }

    /* User Profile */

    .user-profile-card {
        position: relative;
        padding: 22px;
    }

    .user-profile-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .user-profile-main {
        display: flex;
        align-items: center;
        min-width: 0;
    }

    .user-profile-avatar {
        width: 62px;
        height: 62px;
        border-radius: 18px;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 23px;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 10px 24px rgba(37, 99, 235, .22);
    }

    .user-profile-content {
        min-width: 0;
        margin-left: 16px;
    }

    .user-profile-name {
        color: var(--heading);
        font-size: 19px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .user-profile-contact {
        color: var(--muted);
        font-size: 13px;
        margin-bottom: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .user-profile-period {
        color: var(--soft-muted);
        font-size: 12px;
    }

    .profile-information-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(130px, 1fr));
        gap: 12px;
        min-width: 500px;
    }

    .profile-information-item {
        background: var(--soft);
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 13px 15px;
    }

    .information-label {
        color: var(--soft-muted);
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .information-value {
        color: var(--heading);
        font-size: 13px;
        font-weight: 600;
        word-break: break-word;
    }

    /* Session */

    .session-card {
        margin-bottom: 22px;
    }

    .session-header {
        min-height: 76px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .session-header-left {
        display: flex;
        align-items: center;
        min-width: 0;
    }

    .session-number {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #eff6ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 15px;
    }

    .session-heading-content {
        min-width: 0;
        margin-left: 12px;
    }

    .session-title {
        color: var(--heading);
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .session-date {
        color: var(--muted);
        font-size: 12px;
    }

    .session-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 50px;
        padding: 7px 12px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .session-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .session-status-online {
        color: #15803d;
        background: #dcfce7;
    }

    .session-status-idle {
        color: #b45309;
        background: #fef3c7;
    }

    .session-status-ended {
        color: #475569;
        background: #e2e8f0;
    }

    .session-status-offline {
        color: #64748b;
        background: #f1f5f9;
    }

    .session-body {
        padding: 20px;
    }

    /* Statistics */

    .session-statistics {
        display: grid;
        grid-template-columns: repeat(6, minmax(130px, 1fr));
        gap: 12px;
        margin-bottom: 22px;
    }

    .session-stat-item {
        position: relative;
        min-height: 96px;
        background: var(--soft);
        border: 1px solid #edf2f7;
        border-radius: 13px;
        padding: 15px;
    }

    .stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 11px;
        font-size: 13px;
    }

    .stat-blue {
        color: #2563eb;
        background: #dbeafe;
    }

    .stat-violet {
        color: #7c3aed;
        background: #ede9fe;
    }

    .stat-green {
        color: #16a34a;
        background: #dcfce7;
    }

    .stat-orange {
        color: #ea580c;
        background: #ffedd5;
    }

    .stat-cyan {
        color: #0891b2;
        background: #cffafe;
    }

    .stat-slate {
        color: #475569;
        background: #e2e8f0;
    }

    .stat-label {
        color: var(--soft-muted);
        font-size: 11px;
        margin-bottom: 4px;
    }

    .stat-value {
        color: var(--heading);
        font-size: 14px;
        font-weight: 700;
        word-break: break-word;
    }

    /* Timeline */

    .section-heading {
        color: var(--heading);
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 14px;
    }

    .time-information {
        display: grid;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .time-item {
        position: relative;
        border: 1px solid var(--border);
        border-radius: 13px;
        padding: 15px 15px 15px 47px;
    }

    .time-icon {
        position: absolute;
        left: 14px;
        top: 15px;
        width: 24px;
        height: 24px;
        border-radius: 7px;
        background: #eff6ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }

    .time-label {
        color: var(--soft-muted);
        font-size: 11px;
        margin-bottom: 5px;
    }

    .time-value {
        color: var(--heading);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.5;
        word-break: break-word;
    }

    .time-help {
        color: var(--soft-muted);
        font-size: 10px;
        margin-top: 3px;
    }

    /* Current Page */

    .current-page-box {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: 13px;
        padding: 14px 16px;
        margin-bottom: 20px;
    }

    .current-page-icon {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        background: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 13px;
    }

    .current-page-label {
        color: var(--soft-muted);
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 3px;
    }

    .current-page-name {
        color: var(--heading);
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .current-page-url {
        color: var(--muted);
        font-size: 11px;
        word-break: break-all;
    }

    /* Pages table */

    .pages-section {
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .pages-section-header {
        min-height: 64px;
        padding: 15px 17px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .pages-section-title {
        color: var(--heading);
        font-size: 14px;
        font-weight: 700;
        margin: 0 0 3px;
    }

    .pages-section-description {
        color: var(--muted);
        font-size: 11px;
        margin: 0;
    }

    .pages-count {
        color: #1d4ed8;
        background: #eff6ff;
        border-radius: 50px;
        padding: 6px 11px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .pages-table {
        margin: 0;
    }

    .pages-table thead th {
        background: #f8fafc;
        color: #475569;
        border-top: 0;
        border-bottom: 1px solid var(--border);
        padding: 13px 15px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .pages-table tbody td {
        color: #334155;
        border-top: 1px solid #edf2f7;
        padding: 14px 15px;
        font-size: 12px;
        vertical-align: middle;
    }

    .pages-table tbody tr:hover {
        background: #f8fafc;
    }

    .page-number {
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: #f1f5f9;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
    }

    .page-title-text {
        color: var(--heading);
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .page-url-text {
        color: var(--soft-muted);
        font-size: 10px;
        max-width: 300px;
        word-break: break-all;
    }

    .route-badge {
        display: inline-flex;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #6d28d9;
        background: #f5f3ff;
        border-radius: 7px;
        padding: 5px 8px;
        font-family: inherit;
        font-size: 10px;
        font-weight: 600;
    }

    .visit-count {
        color: var(--heading);
        font-weight: 700;
    }

    .duration-badge {
        display: inline-flex;
        color: #0369a1;
        background: #e0f2fe;
        border-radius: 7px;
        padding: 5px 8px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .date-time-value {
        color: #475569;
        font-size: 11px;
        line-height: 1.5;
        white-space: nowrap;
    }

    /* Empty State */

    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto 15px;
        border-radius: 18px;
        background: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
    }

    .empty-state h5 {
        color: var(--heading);
        font-weight: 700;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 22px;
    }

    @media (max-width: 1199.98px) {
        .user-profile-wrapper {
            align-items: flex-start;
            flex-direction: column;
        }

        .profile-information-grid {
            width: 100%;
            min-width: 0;
        }

        .session-statistics {
            grid-template-columns: repeat(3, 1fr);
        }

        .time-information {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767.98px) {
        .activity-detail-page .page-title {
            font-size: 21px;
        }

        .page-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            gap: 9px;
            margin-top: 13px;
        }

        .page-actions .btn {
            margin: 0 !important;
        }

        .user-profile-card {
            padding: 17px;
        }

        .user-profile-main {
            align-items: flex-start;
        }

        .user-profile-avatar {
            width: 52px;
            height: 52px;
            border-radius: 15px;
            font-size: 19px;
        }

        .user-profile-content {
            margin-left: 12px;
        }

        .user-profile-name {
            font-size: 17px;
        }

        .profile-information-grid {
            grid-template-columns: 1fr;
        }

        .session-header {
            align-items: flex-start;
            padding: 15px;
        }

        .session-number {
            width: 38px;
            height: 38px;
        }

        .session-body {
            padding: 15px;
        }

        .session-statistics {
            grid-template-columns: repeat(2, 1fr);
            gap: 9px;
        }

        .session-stat-item {
            min-height: 91px;
            padding: 12px;
        }

        .time-information {
            grid-template-columns: 1fr;
        }

        .pages-section-header {
            align-items: flex-start;
        }
    }

    @media (max-width: 420px) {
        .session-statistics {
            grid-template-columns: 1fr;
        }
    }


    @media (max-width: 767.98px) {
        .page-actions {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
            gap: 9px;
            margin-top: 13px;
        }

        .page-actions form {
            width: 100%;
            margin: 0 !important;
        }

        .page-actions .btn {
            width: 100%;
            margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $userInitial = mb_substr(
        trim($user->name ?: 'U'),
        0,
        1
    );

    $formatDuration = function ($seconds) {
        $seconds = max(0, (int) $seconds);

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf(
            '%02d:%02d:%02d',
            $hours,
            $minutes,
            $remainingSeconds
        );
    };
@endphp

<div class="activity-detail-page">

    <section class="content-header pb-2">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center">

                <div>
                    <h1 class="page-title">
                        User Activity Details
                    </h1>

                    <p class="page-subtitle">
                        Session-wise software usage and visited pages.
                    </p>
                </div>

                <div class="page-actions">
                    <a
                        href="{{ route(
                            'user-activity.index',
                            [
                                'from' => request('from'),
                                'to' => request('to'),
                            ]
                        ) }}"
                        class="btn btn-light border page-action"
                    >
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </a>

                    <button
                        type="button"
                        class="btn btn-outline-primary page-action ml-2"
                        onclick="window.location.reload()"
                    >
                        <i class="fas fa-sync-alt mr-1"></i>
                        Refresh
                    </button>
                    <form
                        method="POST"
                        action="{{ route(
                            'user-activity.user.clear',
                            $user->id
                        ) }}"
                        class="d-inline-block ml-2"
                        onsubmit="return confirmUserActivityClear()"
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
                            class="btn btn-danger page-action"
                        >
                            <i class="fas fa-trash-alt mr-1"></i>
                            Clear User Activity
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <section class="content">
        @if(session('success'))
            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
                <i class="fas fa-check-circle mr-1"></i>

                {{ session('success') }}

                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="container-fluid">

            {{-- User Profile --}}
            <div class="activity-card user-profile-card mb-4">
                <div class="user-profile-wrapper">

                    <div class="user-profile-main">
                        <div class="user-profile-avatar">
                            {{ $userInitial }}
                        </div>

                        <div class="user-profile-content">
                            <div class="user-profile-name">
                                {{ $user->name }}
                            </div>

                            <div class="user-profile-contact">
                                {{ $user->email ?: 'Email not available' }}
                            </div>

                            <div class="user-profile-period">
                                Activity from
                                <strong>
                                    {{ $from?->format('d M Y') ?: '-' }}
                                </strong>
                                to
                                <strong>
                                    {{ $to?->format('d M Y') ?: '-' }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="profile-information-grid">

                        <div class="profile-information-item">
                            <div class="information-label">
                                Email Address
                            </div>

                            <div class="information-value">
                                {{ $user->email ?: '-' }}
                            </div>
                        </div>

                        <div class="profile-information-item">
                            <div class="information-label">
                                Phone Number
                            </div>

                            <div class="information-value">
                                {{ $user->phone ?: '-' }}
                            </div>
                        </div>

                        <div class="profile-information-item">
                            <div class="information-label">
                                Total Sessions
                            </div>

                            <div class="information-value">
                                {{
                                    number_format(
                                        method_exists($activities, 'total')
                                            ? $activities->total()
                                            : collect($activities)->count()
                                    )
                                }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Sessions --}}
            @forelse($activities as $activity)
                @php
                    $activeSeconds = (int) (
                        $activity->active_seconds ?? 0
                    );

                    $lastSeenAt = $activity->last_seen_at
                        ? \Carbon\Carbon::parse(
                            $activity->last_seen_at
                        )
                        : null;

                    $startedAt = $activity->started_at
                        ? \Carbon\Carbon::parse(
                            $activity->started_at
                        )
                        : null;

                    $endedAt = $activity->ended_at
                        ? \Carbon\Carbon::parse(
                            $activity->ended_at
                        )
                        : null;

                    $isOnline = !$endedAt
                        && $lastSeenAt
                        && $lastSeenAt->greaterThanOrEqualTo(
                            now()->subSeconds(45)
                        );

                    $isIdle = !$endedAt
                        && !$isOnline
                        && $lastSeenAt
                        && $lastSeenAt->greaterThanOrEqualTo(
                            now()->subMinutes(3)
                        );

                    $status = $endedAt
                        ? 'Ended'
                        : (
                            $isOnline
                                ? 'Online'
                                : (
                                    $isIdle
                                        ? 'Idle'
                                        : 'Offline'
                                )
                        );

                    $statusClass = match ($status) {
                        'Online' => 'session-status-online',
                        'Idle' => 'session-status-idle',
                        'Ended' => 'session-status-ended',
                        default => 'session-status-offline',
                    };

                    $pagesCount = collect(
                        $activity->pages ?? []
                    )->count();
                @endphp

                <div class="activity-card session-card">

                    {{-- Session Header --}}
                    <div class="session-header">

                        <div class="session-header-left">
                            <div class="session-number">
                                <i class="fas fa-desktop"></i>
                            </div>

                            <div class="session-heading-content">
                                <div class="session-title">
                                    Session #{{ $activity->id }}
                                </div>

                                <div class="session-date">
                                    <i class="far fa-calendar-alt mr-1"></i>

                                    {{
                                        $startedAt
                                            ? $startedAt->format(
                                                'd M Y, h:i A'
                                            )
                                            : 'Start time not available'
                                    }}
                                </div>
                            </div>
                        </div>

                        <span class="session-status {{ $statusClass }}">
                            <span class="session-status-dot"></span>
                            {{ $status }}
                        </span>

                    </div>

                    <div class="session-body">

                        {{-- Session Statistics --}}
                        <div class="session-statistics">

                            <div class="session-stat-item">
                                <div class="stat-icon stat-blue">
                                    <i class="fas fa-stopwatch"></i>
                                </div>

                                <div class="stat-label">
                                    Active Duration
                                </div>

                                <div class="stat-value">
                                    {{ $formatDuration($activeSeconds) }}
                                </div>
                            </div>

                            <div class="session-stat-item">
                                <div class="stat-icon stat-violet">
                                    <i class="fas fa-eye"></i>
                                </div>

                                <div class="stat-label">
                                    Page Views
                                </div>

                                <div class="stat-value">
                                    {{
                                        number_format(
                                            $activity->page_views ?? 0
                                        )
                                    }}
                                </div>
                            </div>

                            <div class="session-stat-item">
                                <div class="stat-icon stat-green">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>

                                <div class="stat-label">
                                    Device
                                </div>

                                <div class="stat-value">
                                    {{
                                        ucfirst(
                                            $activity->device_type
                                            ?: 'Unknown'
                                        )
                                    }}
                                </div>
                            </div>

                            <div class="session-stat-item">
                                <div class="stat-icon stat-orange">
                                    <i class="fas fa-globe"></i>
                                </div>

                                <div class="stat-label">
                                    Browser
                                </div>

                                <div class="stat-value">
                                    {{ $activity->browser ?: 'Unknown' }}
                                </div>
                            </div>

                            <div class="session-stat-item">
                                <div class="stat-icon stat-cyan">
                                    <i class="fas fa-laptop-code"></i>
                                </div>

                                <div class="stat-label">
                                    Platform
                                </div>

                                <div class="stat-value">
                                    {{ $activity->platform ?: 'Unknown' }}
                                </div>
                            </div>

                            <div class="session-stat-item">
                                <div class="stat-icon stat-slate">
                                    <i class="fas fa-network-wired"></i>
                                </div>

                                <div class="stat-label">
                                    IP Address
                                </div>

                                <div class="stat-value">
                                    {{ $activity->ip_address ?: '-' }}
                                </div>
                            </div>

                        </div>

                        {{-- Time Information --}}
                        <h3 class="section-heading">
                            <i class="far fa-clock text-primary mr-1"></i>
                            Session Timeline
                        </h3>

                        <div class="time-information">

                            <div class="time-item">
                                <div class="time-icon">
                                    <i class="fas fa-play"></i>
                                </div>

                                <div class="time-label">
                                    Started At
                                </div>

                                <div class="time-value">
                                    {{
                                        $startedAt
                                            ? $startedAt->format(
                                                'd M Y, h:i:s A'
                                            )
                                            : '-'
                                    }}
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="time-icon">
                                    <i class="fas fa-history"></i>
                                </div>

                                <div class="time-label">
                                    Last Active
                                </div>

                                <div class="time-value">
                                    {{
                                        $lastSeenAt
                                            ? $lastSeenAt->format(
                                                'd M Y, h:i:s A'
                                            )
                                            : '-'
                                    }}
                                </div>

                                @if($lastSeenAt)
                                    <div class="time-help">
                                        {{ $lastSeenAt->diffForHumans() }}
                                    </div>
                                @endif
                            </div>

                            <div class="time-item">
                                <div class="time-icon">
                                    <i class="fas fa-stop"></i>
                                </div>

                                <div class="time-label">
                                    Ended At
                                </div>

                                <div class="time-value">
                                    {{
                                        $endedAt
                                            ? $endedAt->format(
                                                'd M Y, h:i:s A'
                                            )
                                            : 'Session is running'
                                    }}
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="time-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>

                                <div class="time-label">
                                    Current Page
                                </div>

                                <div class="time-value">
                                    {{
                                        $activity->current_page_title
                                        ?: $activity->current_route
                                        ?: '-'
                                    }}
                                </div>
                            </div>

                        </div>

                        {{-- Current Page --}}
                        @if(
                            $activity->current_url
                            || $activity->current_page_title
                            || $activity->current_route
                        )
                            <div class="current-page-box">
                                <div class="current-page-icon">
                                    <i class="fas fa-location-arrow"></i>
                                </div>

                                <div>
                                    <div class="current-page-label">
                                        Current user location
                                    </div>

                                    <div class="current-page-name">
                                        {{
                                            $activity->current_page_title
                                            ?: $activity->current_route
                                            ?: 'Unknown Page'
                                        }}
                                    </div>

                                    @if($activity->current_url)
                                        <div class="current-page-url">
                                            {{ $activity->current_url }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Pages Table --}}
                        <div class="pages-section">

                            <div class="pages-section-header">
                                <div>
                                    <h3 class="pages-section-title">
                                        Visited Pages
                                    </h3>

                                    <p class="pages-section-description">
                                        Pages visited during this session.
                                    </p>
                                </div>

                                <span class="pages-count">
                                    {{ number_format($pagesCount) }} Pages
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="table pages-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Page</th>
                                            <th>Route</th>
                                            <th>Visits</th>
                                            <th>Active Time</th>
                                            <th>First Visit</th>
                                            <th>Last Visit</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($activity->pages as $page)
                                            @php
                                                $pageSeconds = (int) (
                                                    $page->active_seconds ?? 0
                                                );

                                                $firstVisitedAt =
                                                    $page->first_visited_at
                                                        ? \Carbon\Carbon::parse(
                                                            $page->first_visited_at
                                                        )
                                                        : null;

                                                $lastVisitedAt =
                                                    $page->last_visited_at
                                                        ? \Carbon\Carbon::parse(
                                                            $page->last_visited_at
                                                        )
                                                        : null;
                                            @endphp

                                            <tr>
                                                <td>
                                                    <span class="page-number">
                                                        {{ $loop->iteration }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <div class="page-title-text">
                                                        {{
                                                            $page->page_title
                                                            ?: $page->route_name
                                                            ?: 'Unknown Page'
                                                        }}
                                                    </div>

                                                    @if($page->page_url)
                                                        <div class="page-url-text">
                                                            {{ $page->page_url }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span
                                                        class="route-badge"
                                                        title="{{ $page->route_name }}"
                                                    >
                                                        {{
                                                            $page->route_name
                                                            ?: '-'
                                                        }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="visit-count">
                                                        {{
                                                            number_format(
                                                                $page->visit_count
                                                                ?? 0
                                                            )
                                                        }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="duration-badge">
                                                        {{
                                                            $formatDuration(
                                                                $pageSeconds
                                                            )
                                                        }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <div class="date-time-value">
                                                        {{
                                                            $firstVisitedAt
                                                                ? $firstVisitedAt->format(
                                                                    'd M Y'
                                                                )
                                                                : '-'
                                                        }}

                                                        @if($firstVisitedAt)
                                                            <br>

                                                            {{
                                                                $firstVisitedAt->format(
                                                                    'h:i:s A'
                                                                )
                                                            }}
                                                        @endif
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="date-time-value">
                                                        {{
                                                            $lastVisitedAt
                                                                ? $lastVisitedAt->format(
                                                                    'd M Y'
                                                                )
                                                                : '-'
                                                        }}

                                                        @if($lastVisitedAt)
                                                            <br>

                                                            {{
                                                                $lastVisitedAt->format(
                                                                    'h:i:s A'
                                                                )
                                                            }}
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7">
                                                    <div class="empty-state">
                                                        <div class="empty-state-icon">
                                                            <i class="fas fa-file-alt"></i>
                                                        </div>

                                                        <h5>
                                                            No page activity found
                                                        </h5>

                                                        <p class="text-muted mb-0">
                                                            इस session में कोई page activity
                                                            नहीं मिली।
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>
                </div>
            @empty
                <div class="activity-card">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-history"></i>
                        </div>

                        <h5>No activity sessions found</h5>

                        <p class="text-muted mb-0">
                            Selected dates में इस user की कोई activity
                            available नहीं है।
                        </p>
                    </div>
                </div>
            @endforelse

            @if(method_exists($activities, 'links'))
                <div class="pagination-wrapper">
                    {{ $activities->withQueryString()->links() }}
                </div>
            @endif

        </div>
    </section>
</div>

@push('scripts')
<script>
    function confirmUserActivityClear() {
        return window.confirm(
            'क्या आप {{ addslashes($user->name) }} की selected date range वाली पूरी activity delete करना चाहते हैं?\n\n' +
            'यह action undo नहीं किया जा सकता।'
        );
    }
</script>
@endpush
@endsection