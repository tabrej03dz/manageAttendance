<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Attendance Manager') }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    {{-- Ionicons --}}
    <link rel="stylesheet"
        href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    {{-- Tempusdominus Bootstrap 4 --}}
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

    {{-- iCheck --}}
    <link rel="stylesheet"
        href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    {{-- JQVMap --}}
    <link rel="stylesheet"
        href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">

    {{-- AdminLTE --}}
    <link rel="stylesheet"
        href="{{ asset('dist/css/adminlte.min.css') }}">

    {{-- Overlay Scrollbars --}}
    <link rel="stylesheet"
        href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    {{-- Date Range Picker --}}
    <link rel="stylesheet"
        href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

    {{-- Summernote --}}
    <link rel="stylesheet"
        href="{{ asset('plugins/summernote/summernote-bs4.css') }}">

    {{-- Tailwind --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
        rel="stylesheet">

    {{-- Material Icons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet">

    {{-- Flatpickr --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="icon"
        type="image/png"
        href="{{ asset('asset/img/favicon.png') }}">

    <style>
        :root {
            --sidebar-width: 270px;
            --sidebar-color-1: #07111f;
            --sidebar-color-2: #10213b;
            --primary-color: #4f46e5;
            --secondary-color: #7c3aed;
            --cyan-color: #06b6d4;
            --page-background: #f3f6fb;
        }

        html {
            scroll-behavior: smooth;
        }

        body.modern-admin {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(79, 70, 229, 0.09), transparent 30%),
                radial-gradient(circle at bottom left, rgba(6, 182, 212, 0.08), transparent 25%),
                var(--page-background);
            color: #0f172a;
        }

        body.modern-admin .wrapper {
            min-height: 100vh;
            background: transparent;
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        body.modern-admin .main-header {
            min-height: 64px;
            margin-left: var(--sidebar-width);
            border-bottom: 1px solid rgba(148, 163, 184, 0.22);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }

        body.modern-admin .main-header .nav-link {
            color: #475569;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        body.modern-admin .main-header .nav-link:hover {
            color: #4f46e5;
            background: #eef2ff;
        }

        /*
        |--------------------------------------------------------------------------
        | Sidebar
        |--------------------------------------------------------------------------
        */

        body.modern-admin .main-sidebar {
            width: var(--sidebar-width);
            background:
                radial-gradient(circle at top left, rgba(79, 70, 229, 0.34), transparent 30%),
                linear-gradient(
                    180deg,
                    var(--sidebar-color-2) 0%,
                    var(--sidebar-color-1) 65%,
                    #030712 100%
                );
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 18px 0 48px rgba(2, 6, 23, 0.22);
        }

        body.modern-admin .brand-link {
            min-height: 72px;
            display: flex;
            align-items: center;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
        }

        body.modern-admin .brand-link .brand-image {
            max-height: 40px;
            margin-left: 0;
            margin-right: 12px;
            opacity: 1;
            border-radius: 10px;
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.3));
        }

        body.modern-admin .brand-link .brand-text {
            color: #ffffff;
            font-size: 17px;
            font-weight: 800 !important;
            letter-spacing: -0.03em;
        }

        body.modern-admin .sidebar {
            height: calc(100vh - 72px);
            padding: 14px 12px 25px;
            overflow-y: auto;
        }

        body.modern-admin .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        body.modern-admin .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        body.modern-admin .sidebar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.35);
            border-radius: 999px;
        }

        body.modern-admin .user-panel {
            margin: 5px 4px 18px !important;
            padding: 14px 12px !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.055);
        }

        body.modern-admin .user-panel .image img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.22);
        }

        body.modern-admin .user-panel .info a {
            color: #f8fafc;
            font-size: 13px;
            font-weight: 700;
        }

        body.modern-admin .nav-sidebar > .nav-item {
            margin-bottom: 5px;
        }

        body.modern-admin .nav-sidebar .nav-header {
            padding: 18px 12px 8px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        body.modern-admin .nav-sidebar .nav-link {
            min-height: 46px;
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 13px;
            color: #cbd5e1;
            transition: all 0.22s ease;
        }

        body.modern-admin .nav-sidebar .nav-link .nav-icon {
            width: 28px;
            margin-right: 8px;
            color: #94a3b8;
            font-size: 16px;
            transition: all 0.22s ease;
        }

        body.modern-admin .nav-sidebar .nav-link p {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        body.modern-admin .nav-sidebar .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.09);
            transform: translateX(3px);
        }

        body.modern-admin .nav-sidebar .nav-link:hover .nav-icon {
            color: #67e8f9;
        }

        body.modern-admin .nav-sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(
                135deg,
                var(--primary-color),
                var(--secondary-color)
            );
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.34);
        }

        body.modern-admin .nav-sidebar .nav-link.active .nav-icon {
            color: #ffffff;
        }

        body.modern-admin .nav-sidebar .menu-open > .nav-link {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.07);
        }

        body.modern-admin .nav-treeview {
            margin: 5px 0 9px 18px;
            padding-left: 10px;
            border-left: 1px solid rgba(148, 163, 184, 0.18);
        }

        body.modern-admin .nav-treeview .nav-link {
            min-height: 40px;
            padding-top: 8px;
            padding-bottom: 8px;
            border-radius: 10px;
        }

        body.modern-admin .nav-treeview .nav-link.active {
            background: rgba(79, 70, 229, 0.3);
            box-shadow: none;
        }

        /*
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        */

        body.modern-admin .content-wrapper {
            min-height: calc(100vh - 64px) !important;
            margin-left: var(--sidebar-width);
            padding: 24px;
            background: transparent !important;
        }

        body.modern-admin .content-wrapper > .alert {
            border: none;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
        }

        body.modern-admin .main-footer {
            margin-left: var(--sidebar-width);
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile Bottom Navigation
        |--------------------------------------------------------------------------
        */

        .mobile-bottom-nav {
            background: rgba(7, 17, 31, 0.97);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 -15px 35px rgba(2, 6, 23, 0.28);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .mobile-bottom-nav .mobile-nav-link {
            position: relative;
            min-width: 54px;
            padding: 7px 8px;
            border-radius: 14px;
            color: #94a3b8;
            transition: all 0.2s ease;
        }

        .mobile-bottom-nav .mobile-nav-link:hover,
        .mobile-bottom-nav .mobile-nav-link.active {
            color: #ffffff;
            background: rgba(79, 70, 229, 0.38);
        }

        .mobile-bottom-nav .mobile-nav-link.active::before {
            content: '';
            position: absolute;
            top: -9px;
            left: 50%;
            width: 27px;
            height: 3px;
            border-radius: 999px;
            background: #67e8f9;
            transform: translateX(-50%);
        }

        #more-options {
            background: rgba(7, 17, 31, 0.99);
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 991.98px) {
            body.modern-admin .main-header,
            body.modern-admin .content-wrapper,
            body.modern-admin .main-footer {
                margin-left: 0;
            }

            body.modern-admin .main-sidebar {
                width: var(--sidebar-width);
            }

            body.modern-admin .content-wrapper {
                padding: 16px 12px 95px;
            }
        }

        @media (min-width: 992px) {
            body.modern-admin.sidebar-collapse .main-header,
            body.modern-admin.sidebar-collapse .content-wrapper,
            body.modern-admin.sidebar-collapse .main-footer {
                margin-left: 4.6rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed modern-admin">

<div class="wrapper">

    {{-- Header और sidebar दोनों इस file के अंदर होने चाहिए --}}
    @include('dashboard.layout.header')

    @php
        $user = auth()->user();
        $payment = null;

        if (
            $user &&
            $user->hasRole('admin') &&
            $user->office
        ) {
            $payment = App\Models\Payment::where(
                    'office_id',
                    $user->office->id
                )
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->first();

            if (!$payment) {
                $payment = App\Models\Payment::create([
                    'office_id' => $user->office->id,
                    'amount' => (
                        $user->office->number_of_employees *
                        $user->office->price_per_employee
                    ),
                    'date' => now()->startOfMonth(),
                ]);
            }
        }
    @endphp

    <main class="content-wrapper">

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4"
                 role="alert">

                <strong>Error!</strong>
                {{ session('error') }}

                <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="Close">

                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4"
                 role="alert">

                <strong>Success!</strong>
                {{ session('success') }}

                <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="Close">

                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @yield('content')

    </main>

    {{-- Mobile Navigation --}}
    <nav class="mobile-bottom-nav fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl md:hidden">

        <div class="flex justify-around px-2 py-2">

            <a href="{{ route('home') }}"
                class="mobile-nav-link flex flex-col items-center
                {{ request()->routeIs('home') ? 'active' : '' }}">

                <span class="material-icons text-2xl">
                    home
                </span>

                <span class="mt-1 text-xs">
                    Home
                </span>
            </a>

            <a href="{{ route('reports.index') }}"
                class="mobile-nav-link flex flex-col items-center
                {{ request()->routeIs('reports.*') ? 'active' : '' }}">

                <span class="material-icons text-2xl">
                    analytics
                </span>

                <span class="mt-1 text-xs">
                    Reports
                </span>
            </a>

            <a href="{{ route('attendance.index') }}"
                class="mobile-nav-link flex flex-col items-center
                {{ request()->routeIs('attendance.index') ? 'active' : '' }}">

                <span class="material-icons text-2xl">
                    folder_open
                </span>

                <span class="mt-1 text-xs">
                    Records
                </span>
            </a>

            <a href="{{ route('attendance.day-wise') }}"
                class="mobile-nav-link flex flex-col items-center
                {{ request()->routeIs('attendance.day-wise') ? 'active' : '' }}">

                <span class="material-icons text-2xl">
                    access_time
                </span>

                <span class="mt-1 text-xs">
                    Attendance
                </span>
            </a>

            <a href="{{ route('userprofile', ['user' => auth()->id()]) }}"
                class="mobile-nav-link flex flex-col items-center
                {{ request()->routeIs('userprofile') ? 'active' : '' }}">

                <span class="material-icons text-2xl">
                    account_circle
                </span>

                <span class="mt-1 text-xs">
                    Account
                </span>
            </a>

            @role('super_admin|admin')
                <a href="#"
                    id="see-more"
                    class="mobile-nav-link flex cursor-pointer flex-col items-center">

                    <span class="material-icons text-2xl">
                        more_horiz
                    </span>

                    <span class="mt-1 text-xs">
                        More
                    </span>
                </a>
            @endrole
        </div>

        @role('super_admin|admin')
            <div id="more-options"
                data-open="false"
                class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">

                <div class="flex justify-around border-t border-white border-opacity-10 px-2 py-3">

                    <a href="{{ route('leave.index') }}"
                        class="flex flex-col items-center rounded-xl px-3 py-2 text-white transition hover:bg-white hover:bg-opacity-10">

                        <span class="material-icons text-lg">
                            event_busy
                        </span>

                        <span class="mt-1 text-xs">
                            Leave
                        </span>
                    </a>

                    <a href="{{ route('office.index') }}"
                        class="flex flex-col items-center rounded-xl px-3 py-2 text-white transition hover:bg-white hover:bg-opacity-10">

                        <span class="material-icons text-lg">
                            business
                        </span>

                        <span class="mt-1 text-xs">
                            Office
                        </span>
                    </a>

                    <a href="{{ route('off.index') }}"
                        class="flex flex-col items-center rounded-xl px-3 py-2 text-white transition hover:bg-white hover:bg-opacity-10">

                        <span class="material-icons text-lg">
                            settings
                        </span>

                        <span class="mt-1 text-xs">
                            Manage
                        </span>
                    </a>

                    <a href="{{ route('employee.index') }}"
                        class="flex flex-col items-center rounded-xl px-3 py-2 text-white transition hover:bg-white hover:bg-opacity-10">

                        <span class="material-icons text-lg">
                            groups
                        </span>

                        <span class="mt-1 text-xs">
                            Employees
                        </span>
                    </a>
                </div>
            </div>
        @endrole
    </nav>

    <aside class="control-sidebar control-sidebar-dark"></aside>

</div>

{{-- jQuery --}}
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>

{{-- jQuery UI --}}
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>

{{-- Bootstrap --}}
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

{{-- ChartJS --}}
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

{{-- Sparkline --}}
<script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>

{{-- JQVMap --}}
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>

{{-- jQuery Knob --}}
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>

{{-- Moment --}}
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>

{{-- Date Range Picker --}}
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

{{-- Tempusdominus --}}
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

{{-- Summernote --}}
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

{{-- Overlay Scrollbars --}}
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

{{-- Flatpickr --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- AdminLTE --}}
<script src="{{ asset('dist/js/adminlte.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const seeMoreButton = document.getElementById('see-more');
        const moreOptions = document.getElementById('more-options');

        if (seeMoreButton && moreOptions) {
            seeMoreButton.addEventListener('click', function (event) {
                event.preventDefault();

                const isOpen = moreOptions.dataset.open === 'true';

                if (isOpen) {
                    moreOptions.style.maxHeight = '0px';
                    moreOptions.dataset.open = 'false';
                } else {
                    moreOptions.style.maxHeight =
                        moreOptions.scrollHeight + 'px';

                    moreOptions.dataset.open = 'true';
                }
            });
        }

        if (typeof flatpickr === 'function') {
            const datePickerSelectors = [
                '#from-date-mobile',
                '#to-date-mobile',
                '#from-date-web',
                '#to-date-web'
            ];

            datePickerSelectors.forEach(function (selector) {
                const input = document.querySelector(selector);

                if (input) {
                    flatpickr(input, {
                        dateFormat: 'Y-m-d'
                    });
                }
            });
        }
    });
</script>

@stack('scripts')



{{-- @if(auth()->check() && isset($currentUserActivityId))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const activityId = @json($currentUserActivityId);
        const heartbeatUrl = @json(
            route('user-activity.heartbeat')
        );
        const csrfToken = @json(csrf_token());
        const routeName = @json(
            request()->route()?->getName()
        );

        let lastInteractionAt = Date.now();
        let lastHeartbeatAt = Date.now();

        function markUserActive() {
            lastInteractionAt = Date.now();
        }

        [
            'click',
            'mousemove',
            'mousedown',
            'keydown',
            'scroll',
            'touchstart'
        ].forEach(function (eventName) {
            window.addEventListener(
                eventName,
                markUserActive,
                {
                    passive: true
                }
            );
        });

        async function sendHeartbeat() {
            const now = Date.now();

            const inactiveSeconds = Math.floor(
                (now - lastInteractionAt) / 1000
            );

            const heartbeatSeconds = Math.min(
                30,
                Math.max(
                    1,
                    Math.floor(
                        (now - lastHeartbeatAt) / 1000
                    )
                )
            );

            const isActive =
                document.visibilityState === 'visible' &&
                inactiveSeconds <= 60;

            lastHeartbeatAt = now;

            try {
                const response = await fetch(
                    heartbeatUrl,
                    {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            activity_id: activityId,
                            route_name: routeName,
                            page_url: window.location.href,
                            page_title: document.title,
                            active_seconds: heartbeatSeconds,
                            is_active: isActive
                        })
                    }
                );

                if (!response.ok) {
                    console.error(
                        'Activity heartbeat error:',
                        response.status,
                        await response.text()
                    );
                }
            } catch (error) {
                console.error(
                    'Activity heartbeat failed:',
                    error
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Page load ke 2 seconds baad first heartbeat
        |--------------------------------------------------------------------------
        */

        setTimeout(sendHeartbeat, 2000);

        /*
        |--------------------------------------------------------------------------
        | Har 30 second heartbeat
        |--------------------------------------------------------------------------
        */

        setInterval(sendHeartbeat, 30000);

        document.addEventListener(
            'visibilitychange',
            function () {
                sendHeartbeat();
            }
        );
    });
</script>
@endif --}}

</body>
</html>