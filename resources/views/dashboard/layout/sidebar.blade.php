<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <style>
        #appSidebarScrollArea {
            overflow-y: auto;
            height: calc(100vh - 0px);
        }

        .sidebar-selected-office {
            background: rgba(255, 193, 7, 0.15) !important;
            color: #ffc107 !important;
        }
    </style>

    <div class="sidebar" id="appSidebarScrollArea">

        {{-- Sidebar user panel --}}
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="{{ route('userprofile', ['user' => auth()->user()->id]) }}" class="d-block">
                    {{ auth()->user()->name }}
                </a>
            </div>
        </div>

        @php
            $authUser = auth()->user();

            $sidebarOffices = collect();
            $activeOfficeId = session('active_office_id', $authUser->office_id);
            $activeOfficeName = null;

            $canSeeOfficeSwitch =
                $authUser->hasRole('super_admin') ||
                $authUser->hasRole('owner') ||
                $authUser->can('switch offices');

            if ($canSeeOfficeSwitch) {
                if ($authUser->hasRole('super_admin')) {
                    $sidebarOffices = \App\Models\Office::query()
                        ->where('status', 'active')
                        ->orderBy('name')
                        ->get();
                } elseif ($authUser->hasRole('owner')) {
                    $sidebarOffices = \App\Models\Office::query()
                        ->where('owner_id', $authUser->id)
                        ->where('status', 'active')
                        ->orderBy('name')
                        ->get();
                } else {
                    $currentOffice = $authUser->office;

                    if ($currentOffice && $currentOffice->owner_id) {
                        $sidebarOffices = \App\Models\Office::query()
                            ->where('owner_id', $currentOffice->owner_id)
                            ->where('status', 'active')
                            ->orderBy('name')
                            ->get();
                    }
                }

                $activeOfficeName = optional($sidebarOffices->firstWhere('id', $activeOfficeId))->name;
            }

            /*
                Ek hi menu active rakhne ke liye activeMenu variable use kiya hai.
                Broad routeIs jaise off*, employee*, reports* hata diye hain.
            */
            $activeMenu = null;

            if (request()->routeIs('home')) {
                $activeMenu = 'dashboard';
            } elseif (request()->routeIs('attendance.index')) {
                $activeMenu = 'records';
            } elseif (request()->routeIs('owner.index') || request()->routeIs('owner.create') || request()->routeIs('owner.edit')) {
                $activeMenu = 'owners';
            } elseif (request()->routeIs('attendance.day-wise')) {
                $activeMenu = 'attendance';
            } elseif (request()->routeIs('departments.index') || request()->routeIs('departments.create') || request()->routeIs('departments.edit')) {
                $activeMenu = 'departments';
            } elseif (request()->routeIs('employee.index') || request()->routeIs('employee.create') || request()->routeIs('employee.edit') || request()->routeIs('employee.show')) {
                $activeMenu = 'employee';
            } elseif (request()->routeIs('leave.index') || request()->routeIs('leave.create') || request()->routeIs('leave.edit')) {
                $activeMenu = 'leave';
            } elseif (request()->routeIs('office.index') || request()->routeIs('office.create') || request()->routeIs('office.edit') || request()->routeIs('office.detail')) {
                $activeMenu = 'office';
            } elseif (request()->routeIs('off.index') || request()->routeIs('off.create') || request()->routeIs('off.edit')) {
                $activeMenu = 'manage-offs';
            } elseif (request()->routeIs('rosters.index') || request()->routeIs('rosters.create') || request()->routeIs('rosters.edit')) {
                $activeMenu = 'roaster';
            } elseif (request()->routeIs('policy.index') || request()->routeIs('policy.create') || request()->routeIs('policy.edit')) {
                $activeMenu = 'policy';
            } elseif (request()->routeIs('reports.index')) {
                $activeMenu = 'reports';
            } elseif (request()->routeIs('old-attendance.index')) {
                $activeMenu = 'old-records';
            } elseif (request()->routeIs('employee.attendance')) {
                $activeMenu = 'mark-attendance';
            } elseif (request()->routeIs('salary.index') || request()->routeIs('salary.create') || request()->routeIs('salary.edit')) {
                $activeMenu = 'salary';
            } elseif (request()->routeIs('advance.index') || request()->routeIs('advance.create') || request()->routeIs('advance.edit')) {
                $activeMenu = 'advance';
            } elseif (request()->routeIs('note.index') || request()->routeIs('note.create') || request()->routeIs('note.edit')) {
                $activeMenu = 'note';
            } elseif (request()->routeIs('request.index') || request()->routeIs('request.create') || request()->routeIs('request.edit')) {
                $activeMenu = 'demo-request';
            } elseif (request()->routeIs('role.index') || request()->routeIs('role.create') || request()->routeIs('role.edit')) {
                $activeMenu = 'role';
            } elseif (request()->routeIs('visit.index') || request()->routeIs('visit.create') || request()->routeIs('visit.edit')) {
                $activeMenu = 'visits';
            } elseif (request()->routeIs('recycle.index')) {
                $activeMenu = 'recycle';
            } elseif (request()->routeIs('break.index') || request()->routeIs('break.create') || request()->routeIs('break.edit')) {
                $activeMenu = 'breaks';
            } elseif (request()->routeIs('manual.entry.form')) {
                $activeMenu = 'manual-entry';
            } elseif (request()->routeIs('permission.index')) {
                $activeMenu = 'permissions';
            } elseif (
                request()->routeIs('document-types.index') ||
                request()->routeIs('document-types.create') ||
                request()->routeIs('document-types.edit')
            ) {
                $activeMenu = 'document-types';
            } elseif (
                request()->routeIs('letter-templates.index') ||
                request()->routeIs('letter-templates.create') ||
                request()->routeIs('letter-templates.edit')
            ) {
                $activeMenu = 'letter-templates';
            } elseif (
                request()->routeIs('employee-letters.index') ||
                request()->routeIs('employee-letters.create') ||
                request()->routeIs('employee-letters.edit') ||
                request()->routeIs('employee-letters.show')
            ) {
                $activeMenu = 'employee-letters';
            } elseif (request()->routeIs('half-day.index')) {
                $activeMenu = 'half-days';
            }

            $isOfficeSwitchOpen = $canSeeOfficeSwitch && $sidebarOffices->count() > 0 && session('active_office_id');

            $isHrLettersOpen = in_array($activeMenu, [
                'document-types',
                'letter-templates',
                'employee-letters',
            ]);
        @endphp

        {{-- Sidebar Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                @can('show dashboard')
                    <li class="nav-item">
                        <a href="{{ route('home') }}"
                           class="nav-link {{ $activeMenu === 'dashboard' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endcan

                @can('show records')
                    <li class="nav-item">
                        <a href="{{ route('attendance.index') }}"
                           class="nav-link {{ $activeMenu === 'records' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Records</p>
                        </a>
                    </li>
                @endcan

                @can('show owners')
                    <li class="nav-item">
                        <a href="{{ route('owner.index') }}"
                           class="nav-link {{ $activeMenu === 'owners' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Owners</p>
                        </a>
                    </li>
                @endcan

                @can('show attendance')
                    <li class="nav-item">
                        <a href="{{ route('attendance.day-wise') }}"
                           class="nav-link {{ $activeMenu === 'attendance' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-edit"></i>
                            <p>Attendance</p>
                        </a>
                    </li>
                @endcan

                @canany(['Show Departments', 'show departments'])
                    <li class="nav-item">
                        <a href="{{ route('departments.index') }}"
                           class="nav-link {{ $activeMenu === 'departments' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Departments</p>
                        </a>
                    </li>
                @endcanany

                @can('show employees')
                    <li class="nav-item">
                        <a href="{{ route('employee.index') }}"
                           class="nav-link {{ $activeMenu === 'employee' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Employee</p>
                        </a>
                    </li>
                @endcan

                @can('show leaves')
                    <li class="nav-item">
                        <a href="{{ route('leave.index') }}"
                           class="nav-link {{ $activeMenu === 'leave' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-quote-left"></i>
                            <p>Leave</p>
                        </a>
                    </li>
                @endcan

                @can('show offices')
                    <li class="nav-item">
                        <a href="{{ route('office.index') }}"
                           class="nav-link {{ $activeMenu === 'office' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-industry"></i>
                            <p>Office</p>
                        </a>
                    </li>
                @endcan

                {{-- Office Switch: sirf active offices --}}
                @if($canSeeOfficeSwitch && $sidebarOffices->count() > 0)
                    <li class="nav-item has-treeview {{ $isOfficeSwitchOpen ? 'menu-open' : '' }}">
                        <a href="javascript:void(0)" class="nav-link">
                            <i class="nav-icon fas fa-random"></i>
                            <p>
                                Office Switch

                                @if($activeOfficeName)
                                    <small class="ml-2 text-warning">({{ $activeOfficeName }})</small>
                                @endif

                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview" style="{{ $isOfficeSwitchOpen ? 'display:block;' : '' }}">
                            @foreach($sidebarOffices as $office)
                                <li class="nav-item">
                                    <form action="{{ route('office.switch', $office->id) }}"
                                          method="POST"
                                          style="display:block;">
                                        @csrf

                                        <button type="submit"
                                                class="nav-link w-100 text-left border-0 bg-transparent {{ (int) $activeOfficeId === (int) $office->id ? 'sidebar-selected-office' : '' }}"
                                                style="width:100%; cursor:pointer;">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>
                                                {{ $office->name }}

                                                @if((int) $activeOfficeId === (int) $office->id)
                                                    <span class="badge badge-success ml-2">Selected</span>
                                                @endif
                                            </p>
                                        </button>
                                    </form>
                                </li>
                            @endforeach

                            @if(session('active_office_id'))
                                <li class="nav-item">
                                    <form action="{{ route('office.clearSwitch') }}"
                                          method="POST"
                                          style="display:block;">
                                        @csrf

                                        <button type="submit"
                                                class="nav-link w-100 text-left border-0 bg-transparent text-danger"
                                                style="width:100%; cursor:pointer;">
                                            <i class="fas fa-sign-out-alt nav-icon"></i>
                                            <p>Exit Office View</p>
                                        </button>
                                    </form>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @can('manage offs')
                    <li class="nav-item">
                        <a href="{{ route('off.index') }}"
                           class="nav-link {{ $activeMenu === 'manage-offs' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Manage Offs</p>
                        </a>
                    </li>
                @endcan

                <li class="nav-item">
                    <a href="{{ route('rosters.index') }}"
                       class="nav-link {{ $activeMenu === 'roaster' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Roaster</p>
                    </a>
                </li>

                @can('show policies')
                    <li class="nav-item">
                        <a href="{{ route('policy.index') }}"
                           class="nav-link {{ $activeMenu === 'policy' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-pencil-alt"></i>
                            <p>Policy</p>
                        </a>
                    </li>
                @endcan

                @can('show reports')
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}"
                           class="nav-link {{ $activeMenu === 'reports' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                @endcan

                @can('show old records')
                    <li class="nav-item">
                        <a href="{{ route('old-attendance.index') }}"
                           class="nav-link {{ $activeMenu === 'old-records' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Old Records</p>
                        </a>
                    </li>
                @endcan

                @can('mark attendance of employees')
                    <li class="nav-item">
                        <a href="{{ route('employee.attendance') }}"
                           class="nav-link {{ $activeMenu === 'mark-attendance' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>Mark Attendance</p>
                        </a>
                    </li>
                @endcan

                @can('show salaries')
                    <li class="nav-item">
                        <a href="{{ route('salary.index') }}"
                           class="nav-link {{ $activeMenu === 'salary' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>Salary</p>
                        </a>
                    </li>
                @endcan

                @can('advance salary')
                    <li class="nav-item">
                        <a href="{{ route('advance.index') }}"
                           class="nav-link {{ $activeMenu === 'advance' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Advance Payment</p>
                        </a>
                    </li>
                @endcan

                @role('super_admin|owner')
                    <li class="nav-item">
                        <a href="{{ route('note.index') }}"
                           class="nav-link {{ $activeMenu === 'note' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-sticky-note"></i>
                            <p>Note</p>
                        </a>
                    </li>
                @endrole

                @role('super_admin')
                    <li class="nav-item">
                        <a href="{{ route('request.index') }}"
                           class="nav-link {{ $activeMenu === 'demo-request' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-sticky-note"></i>
                            <p>DemoRequest</p>
                        </a>
                    </li>
                @endrole

                @can('show roles')
                    <li class="nav-item">
                        <a href="{{ route('role.index') }}"
                           class="nav-link {{ $activeMenu === 'role' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Role</p>
                        </a>
                    </li>
                @endcan

                @can('show visits')
                    <li class="nav-item">
                        <a href="{{ route('visit.index') }}"
                           class="nav-link {{ $activeMenu === 'visits' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-map-marker-alt"></i>
                            <p>Visits</p>
                        </a>
                    </li>
                @endcan

                @can('show recycles')
                    <li class="nav-item">
                        <a href="{{ route('recycle.index') }}"
                           class="nav-link {{ $activeMenu === 'recycle' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-recycle"></i>
                            <p>Recycle</p>
                        </a>
                    </li>
                @endcan

                @can('show breaks')
                    <li class="nav-item">
                        <a href="{{ route('break.index') }}"
                           class="nav-link {{ $activeMenu === 'breaks' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-coffee"></i>
                            <p>Breaks</p>
                        </a>
                    </li>
                @endcan

                @can('manual attendance entry')
                    <li class="nav-item">
                        <a href="{{ route('manual.entry.form') }}"
                           class="nav-link {{ $activeMenu === 'manual-entry' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-keyboard"></i>
                            <p>Manual Entry</p>
                        </a>
                    </li>
                @endcan

                @can('show permissions')
                    <li class="nav-item">
                        <a href="{{ route('permission.index') }}"
                           class="nav-link {{ $activeMenu === 'permissions' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-lock"></i>
                            <p>Permissions</p>
                        </a>
                    </li>
                @endcan

                @can('show hr documents')
                    <li class="nav-item has-treeview {{ $isHrLettersOpen ? 'menu-open' : '' }}">
                        <a href="javascript:void(0)" class="nav-link">
                            <i class="nav-icon fas fa-file-signature"></i>
                            <p>
                                HR Letters
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview" style="{{ $isHrLettersOpen ? 'display:block;' : '' }}">
                            <li class="nav-item">
                                <a href="{{ route('document-types.index') }}"
                                   class="nav-link {{ $activeMenu === 'document-types' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Document Types</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('letter-templates.index') }}"
                                   class="nav-link {{ $activeMenu === 'letter-templates' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Letter Templates</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('employee-letters.index') }}"
                                   class="nav-link {{ $activeMenu === 'employee-letters' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Generated Letters</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <li class="nav-item">
                    <a href="{{ route('half-day.index') }}"
                       class="nav-link {{ $activeMenu === 'half-days' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-lock"></i>
                        <p>Half Days</p>
                    </a>
                </li>

                @role('super_admin|owner|admin|team_leader')
                <li class="nav-item">
                    <a
                        href="{{ route('user-activity.index') }}"
                        class="nav-link {{
                            request()->routeIs('user-activity.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>User Activity</p>
                    </a>
                </li>
                @endrole

                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="post" style="display:block;">
                        @csrf
                        <button type="submit"
                                class="nav-link border-0 bg-transparent w-100 text-left"
                                style="width:100%; cursor:pointer;">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<script>
    (function () {
        const storageKey = 'attendance_sidebar_scroll_top_v2';

        function getSidebarBase() {
            return document.getElementById('appSidebarScrollArea');
        }

        function getScrollBox() {
            const sidebar = getSidebarBase();

            if (!sidebar) {
                return null;
            }

            /*
                AdminLTE kabhi sidebar ke andar OverlayScrollbars use karta hai.
                Actual scroll .os-viewport par hota hai.
            */
            return sidebar.querySelector('.os-viewport') || sidebar;
        }

        function saveSidebarScroll() {
            const scrollBox = getScrollBox();

            if (!scrollBox) {
                return;
            }

            localStorage.setItem(storageKey, String(scrollBox.scrollTop || 0));
        }

        function restoreSidebarScroll() {
            const scrollBox = getScrollBox();
            const sidebar = getSidebarBase();

            if (!scrollBox || !sidebar) {
                return;
            }

            const saved = parseInt(localStorage.getItem(storageKey) || '0', 10);
            const activeLink = sidebar.querySelector('.nav-link.active');

            if (saved > 10) {
                scrollBox.scrollTop = saved;
            } else if (activeLink) {
                activeLink.scrollIntoView({
                    block: 'center',
                    inline: 'nearest'
                });

                setTimeout(function () {
                    saveSidebarScroll();
                }, 100);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(restoreSidebarScroll, 100);
            setTimeout(restoreSidebarScroll, 400);
            setTimeout(restoreSidebarScroll, 900);

            const scrollBox = getScrollBox();

            if (scrollBox) {
                scrollBox.addEventListener('scroll', saveSidebarScroll, { passive: true });
            }

            document.addEventListener('click', function (event) {
                const sidebar = getSidebarBase();

                if (sidebar && sidebar.contains(event.target)) {
                    saveSidebarScroll();
                }
            }, true);

            window.addEventListener('beforeunload', saveSidebarScroll);
        });
    })();
</script>