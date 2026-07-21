@extends('dashboard.layout.root')

@section('title', 'User Activity Details')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center">

            <div>
                <h1 class="mb-1">
                    User Activity Details
                </h1>

                <p class="text-muted mb-0">
                    {{ $user->name }}

                    @if($user->email)
                        — {{ $user->email }}
                    @endif
                </p>
            </div>

            <div class="mt-2 mt-md-0">
                <a
                    href="{{ route(
                        'user-activity.index',
                        [
                            'from' => request('from'),
                            'to' => request('to'),
                        ]
                    ) }}"
                    class="btn btn-light border"
                >
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back
                </a>

                <button
                    type="button"
                    class="btn btn-outline-primary"
                    onclick="window.location.reload()"
                >
                    <i class="fas fa-sync-alt mr-1"></i>
                    Refresh
                </button>
            </div>

        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- User Information --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3 mb-md-0">
                        <small class="text-muted d-block">
                            User Name
                        </small>

                        <strong>
                            {{ $user->name }}
                        </strong>
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <small class="text-muted d-block">
                            Email
                        </small>

                        <strong>
                            {{ $user->email ?: '-' }}
                        </strong>
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <small class="text-muted d-block">
                            Phone
                        </small>

                        <strong>
                            {{ $user->phone ?: '-' }}
                        </strong>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block">
                            Date Range
                        </small>

                        <strong>
                            {{ $from?->format('d M Y') }}
                            -
                            {{ $to?->format('d M Y') }}
                        </strong>
                    </div>

                </div>
            </div>
        </div>

        @forelse($activities as $activity)
            @php
                $activeSeconds = (int) (
                    $activity->active_seconds ?? 0
                );

                $hours = floor($activeSeconds / 3600);

                $minutes = floor(
                    ($activeSeconds % 3600) / 60
                );

                $seconds = $activeSeconds % 60;

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
                    'Online' => 'success',
                    'Idle' => 'warning',
                    'Ended' => 'dark',
                    default => 'secondary',
                };
            @endphp

            <div class="card shadow-sm">

                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">

                        <div>
                            <strong>
                                Session #{{ $activity->id }}
                            </strong>

                            <span class="text-muted ml-2">
                                {{
                                    $startedAt
                                        ? $startedAt->format(
                                            'd M Y, h:i A'
                                        )
                                        : '-'
                                }}
                            </span>
                        </div>

                        <span class="badge badge-{{ $statusClass }} p-2">
                            @if($status === 'Online')
                                <i class="fas fa-circle mr-1"></i>
                            @endif

                            {{ $status }}
                        </span>

                    </div>
                </div>

                <div class="card-body">

                    {{-- Session Summary --}}
                    <div class="row">

                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <small class="text-muted d-block">
                                Active Duration
                            </small>

                            <strong>
                                {{
                                    sprintf(
                                        '%02d:%02d:%02d',
                                        $hours,
                                        $minutes,
                                        $seconds
                                    )
                                }}
                            </strong>
                        </div>

                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <small class="text-muted d-block">
                                Page Views
                            </small>

                            <strong>
                                {{
                                    number_format(
                                        $activity->page_views ?? 0
                                    )
                                }}
                            </strong>
                        </div>

                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <small class="text-muted d-block">
                                Device
                            </small>

                            <strong>
                                {{
                                    ucfirst(
                                        $activity->device_type
                                        ?: 'Unknown'
                                    )
                                }}
                            </strong>
                        </div>

                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <small class="text-muted d-block">
                                Browser
                            </small>

                            <strong>
                                {{ $activity->browser ?: 'Unknown' }}
                            </strong>
                        </div>

                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <small class="text-muted d-block">
                                Platform
                            </small>

                            <strong>
                                {{ $activity->platform ?: 'Unknown' }}
                            </strong>
                        </div>

                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <small class="text-muted d-block">
                                IP Address
                            </small>

                            <strong>
                                {{ $activity->ip_address ?: '-' }}
                            </strong>
                        </div>

                    </div>

                    <hr>

                    {{-- Time Information --}}
                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block">
                                Started At
                            </small>

                            <strong>
                                {{
                                    $startedAt
                                        ? $startedAt->format(
                                            'd M Y, h:i:s A'
                                        )
                                        : '-'
                                }}
                            </strong>
                        </div>

                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block">
                                Last Active
                            </small>

                            <strong>
                                {{
                                    $lastSeenAt
                                        ? $lastSeenAt->format(
                                            'd M Y, h:i:s A'
                                        )
                                        : '-'
                                }}
                            </strong>

                            @if($lastSeenAt)
                                <div class="small text-muted">
                                    {{ $lastSeenAt->diffForHumans() }}
                                </div>
                            @endif
                        </div>

                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block">
                                Ended At
                            </small>

                            <strong>
                                {{
                                    $endedAt
                                        ? $endedAt->format(
                                            'd M Y, h:i:s A'
                                        )
                                        : 'Session running'
                                }}
                            </strong>
                        </div>

                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block">
                                Current Page
                            </small>

                            <strong>
                                {{
                                    $activity->current_page_title
                                    ?: $activity->current_route
                                    ?: '-'
                                }}
                            </strong>
                        </div>

                    </div>

                    @if($activity->current_url)
                        <div class="alert alert-light border">
                            <strong>
                                Current URL:
                            </strong>

                            <span class="text-break">
                                {{ $activity->current_url }}
                            </span>
                        </div>
                    @endif

                    {{-- Pages Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
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

                                        $pageHours = floor(
                                            $pageSeconds / 3600
                                        );

                                        $pageMinutes = floor(
                                            ($pageSeconds % 3600) / 60
                                        );

                                        $pageRemainingSeconds =
                                            $pageSeconds % 60;
                                    @endphp

                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            <strong>
                                                {{
                                                    $page->page_title
                                                    ?: $page->route_name
                                                    ?: 'Unknown Page'
                                                }}
                                            </strong>

                                            @if($page->page_url)
                                                <div class="small text-muted text-break">
                                                    {{ $page->page_url }}
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            <code>
                                                {{ $page->route_name ?: '-' }}
                                            </code>
                                        </td>

                                        <td>
                                            {{
                                                number_format(
                                                    $page->visit_count ?? 0
                                                )
                                            }}
                                        </td>

                                        <td>
                                            {{
                                                sprintf(
                                                    '%02d:%02d:%02d',
                                                    $pageHours,
                                                    $pageMinutes,
                                                    $pageRemainingSeconds
                                                )
                                            }}
                                        </td>

                                        <td>
                                            {{
                                                $page->first_visited_at
                                                    ? \Carbon\Carbon::parse(
                                                        $page->first_visited_at
                                                    )->format(
                                                        'd M Y, h:i:s A'
                                                    )
                                                    : '-'
                                            }}
                                        </td>

                                        <td>
                                            {{
                                                $page->last_visited_at
                                                    ? \Carbon\Carbon::parse(
                                                        $page->last_visited_at
                                                    )->format(
                                                        'd M Y, h:i:s A'
                                                    )
                                                    : '-'
                                            }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="7"
                                            class="text-center py-4 text-muted"
                                        >
                                            Is session me koi page activity nahi mili.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        @empty
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>

                    <h5>No activity sessions found</h5>

                    <p class="text-muted mb-0">
                        Selected dates me is user ki activity available nahi hai.
                    </p>
                </div>
            </div>
        @endforelse

        @if(method_exists($activities, 'links'))
            <div class="d-flex justify-content-center">
                {{ $activities->withQueryString()->links() }}
            </div>
        @endif

    </div>
</section>
@endsection