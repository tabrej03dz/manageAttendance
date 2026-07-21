@extends('dashboard.layout.root')

@section('title', 'User Activity')

@section('content')
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1">User Activity</h1>

                    <p class="text-muted mb-0">
                        User-wise software usage, sessions and live status
                    </p>
                </div>

                <div class="mt-2 mt-md-0">
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

            {{-- Date Filter --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('user-activity.index') }}">
                        <div class="row">

                            <div class="col-md-4 mb-3 mb-md-0">
                                <label for="from">From Date</label>

                                <input
                                    type="date"
                                    name="from"
                                    id="from"
                                    class="form-control"
                                    value="{{ $from?->format('Y-m-d') }}"
                                >
                            </div>

                            <div class="col-md-4 mb-3 mb-md-0">
                                <label for="to">To Date</label>

                                <input
                                    type="date"
                                    name="to"
                                    id="to"
                                    class="form-control"
                                    value="{{ $to?->format('Y-m-d') }}"
                                >
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button
                                    type="submit"
                                    class="btn btn-primary mr-2"
                                >
                                    <i class="fas fa-filter mr-1"></i>
                                    Apply Filter
                                </button>

                                <a
                                    href="{{ route('user-activity.index') }}"
                                    class="btn btn-light border"
                                >
                                    Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="row">

                <div class="col-xl-3 col-md-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>
                                {{ number_format($summary['total_users'] ?? 0) }}
                            </h3>

                            <p>Total Active Users</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>
                                {{ number_format($summary['online_users'] ?? 0) }}
                            </h3>

                            <p>Online Users</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>
                                {{ number_format($summary['sessions'] ?? 0) }}
                            </h3>

                            <p>Total Sessions</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>
                                {{ number_format($summary['page_views'] ?? 0) }}
                            </h3>

                            <p>Total Page Views</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>

            </div>

            {{-- User Usage Table --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        User-wise Usage
                    </h3>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Usage Time</th>
                                    <th>Page Views</th>
                                    <th>Sessions</th>
                                    <th>Last Active</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($userUsage as $usage)
                                    @php
                                        $totalSeconds = (int) ($usage->total_seconds ?? 0);

                                        $hours = floor($totalSeconds / 3600);
                                        $minutes = floor(($totalSeconds % 3600) / 60);
                                        $seconds = $totalSeconds % 60;

                                        $lastSeenAt = $usage->last_seen_at
                                            ? \Carbon\Carbon::parse($usage->last_seen_at)
                                            : null;

                                        $isOnline = $lastSeenAt
                                            && $lastSeenAt->greaterThanOrEqualTo(
                                                now()->subSeconds(45)
                                            );
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="rounded-circle bg-light border d-flex align-items-center justify-content-center mr-2"
                                                    style="width: 40px; height: 40px;"
                                                >
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>

                                                <div>
                                                    <strong>
                                                        {{ $usage->user?->name ?? 'Deleted User' }}
                                                    </strong>

                                                    <div class="small text-muted">
                                                        {{ $usage->user?->email ?: ($usage->user?->phone ?? '-') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @if($isOnline)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-circle mr-1"></i>
                                                    Online
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    Offline
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <strong>
                                                {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ number_format($usage->total_page_views ?? 0) }}
                                        </td>

                                        <td>
                                            {{ number_format($usage->total_sessions ?? 0) }}
                                        </td>

                                        <td>
                                            @if($lastSeenAt)
                                                <span title="{{ $lastSeenAt->format('d M Y, h:i:s A') }}">
                                                    {{ $lastSeenAt->diffForHumans() }}
                                                </span>
                                            @else
                                                -
                                            @endif
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
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    <i class="fas fa-eye mr-1"></i>
                                                    View
                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    Not available
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="7"
                                            class="text-center py-5"
                                        >
                                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>

                                            <h5>No activity found</h5>

                                            <p class="text-muted mb-0">
                                                Selected dates me koi user activity nahi mili.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>

                @if(method_exists($userUsage, 'links'))
                    <div class="card-footer clearfix">
                        {{ $userUsage->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            {{-- Most Used Pages --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-1"></i>
                        Most Used Pages
                    </h3>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Page</th>
                                    <th>Route</th>
                                    <th>Visits</th>
                                    <th>Usage Time</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($topPages as $page)
                                    @php
                                        $pageSeconds = (int) ($page->total_seconds ?? 0);

                                        $pageHours = floor($pageSeconds / 3600);
                                        $pageMinutes = floor(($pageSeconds % 3600) / 60);
                                        $pageRemainingSeconds = $pageSeconds % 60;
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            <strong>
                                                {{
                                                    $page->page_title
                                                    ?: $page->route_name
                                                    ?: 'Unknown Page'
                                                }}
                                            </strong>

                                            @if($page->page_url)
                                                <div
                                                    class="small text-muted text-truncate"
                                                    style="max-width: 400px;"
                                                    title="{{ $page->page_url }}"
                                                >
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
                                            {{ number_format($page->total_visits ?? 0) }}
                                        </td>

                                        <td>
                                            {{ sprintf(
                                                '%02d:%02d:%02d',
                                                $pageHours,
                                                $pageMinutes,
                                                $pageRemainingSeconds
                                            ) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="5"
                                            class="text-center py-4 text-muted"
                                        >
                                            No page usage found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection