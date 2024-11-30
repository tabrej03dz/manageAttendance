<aside class="main-sidebar sidebar-dark-primary elevation-4 ">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="{{ route('userprofile', ['user' => auth()->user()->id]) }}"
                    class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @can('show dashboard')
                    <li class="nav-item has-treeview {{ request()->routeIs('home') ? 'menu-open' : '' }}">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                    </li>
                @endcan
                @can('show records')
                <li class="nav-item">
                    <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Records</p>
                    </a>
                </li>
                    @endcan


                @can('show owners')
                <li class="nav-item">
                    <a href="{{ route('owner.index') }}" class="nav-link {{ request()->routeIs('owner.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Owners</p>
                    </a>
                </li>
                    @endcan

                @can('show attendance')
                    <li class="nav-item">
                        <a href="{{ route('attendance.day-wise') }}"
                            class="nav-link {{ request()->routeIs('attendance.day-wise') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-edit"></i>
                            <p>Attendance</p>
                        </a>
                    </li>
                @endcan
                @can('show employees')
                    <li class="nav-item">
                        <a href="{{ route('employee.index') }}"
                            class="nav-link {{ request()->routeIs('employee.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Employee</p>
                        </a>
                    </li>
                @endcan
                @can('show leaves')
                    <li class="nav-item">
                        <a href="{{ route('leave.index') }}"
                            class="nav-link {{ request()->routeIs('leave.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-quote-left"></i>
                            <p>Leave</p>
                        </a>
                    </li>
                    @endcan
                @can('show offices')
                    <li class="nav-item">
                        <a href="{{ route('office.index') }}"
                            class="nav-link {{ request()->routeIs('office.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-industry"></i>
                            <p>Office</p>
                        </a>
                    </li>
                @endcan
                @role('manage offs')
                    <li class="nav-item">
                        <a href="{{ route('off.index') }}"
                            class="nav-link {{ request()->routeIs('off.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Manage Offs</p>
                        </a>
                    </li>
                    @endcan


                @can('show policies')
                    <li class="nav-item">
                        <a href="{{ route('policy.index') }}"
                            class="nav-link {{ request()->routeIs('policy.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-pencil-alt"></i>
                            <p>Policy</p>
                        </a>
                    </li>
                @endcan

                    @can('show reports')
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}"
                        class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports</p>
                    </a>
                </li>
                    @endcan


                @can('mark attendance of employees')
                    <li class="nav-item">
                        <a href="{{ route('employee.attendance') }}"
                            class="nav-link {{ request()->routeIs('employee.attendance') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>Mark Attendance</p>
                        </a>
                    </li>
                    @endcan

                    @can('show payments')
                    <li class="nav-item">
                        <a href="{{ route('payment.index') }}"
                            class="nav-link {{ request()->routeIs('payment.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Payment</p>
                        </a>
                    </li>
                    @endcan


                    @can('show salaries')
                    <li class="nav-item">
                        <a href="{{ route('salary.index') }}"
                            class="nav-link {{ request()->routeIs('salary.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>Salary</p>
                        </a>
                    </li>
                    @endcan

                    @can('advance salary')
                    <li class="nav-item">
                        <a href="{{ route('advance.index') }}" class="nav-link {{ request()->routeIs('advance.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-signal"></i>
                            <p>Advance Payment</p>
                        </a>
                    </li>

                    @endcan


                    @role('super_admin|owner')
                    <li class="nav-item">
                        <a href="{{ route('note.index') }}" class="nav-link {{ request()->routeIs('note.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-signal"></i>
                            <p>Note</p>
                        </a>
                    </li>
                    @endrole
                    @can('show roles')
                    <li class="nav-item">
                        <a href="{{ route('role.index') }}" class="nav-link {{ request()->routeIs('role.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-signal"></i>
                            <p>Role</p>
                        </a>
                    </li>
                    @endcan
                    @can('show visits')
                <li class="nav-item">
                    <a href="{{ route('visit.index') }}"
                        class="nav-link {{ request()->routeIs('visit.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>Visits</p>
                    </a>
                </li>
                    @endcan


                    @can('show recycles')
                <li class="nav-item">
{{--                    <a href="{{ route('recycle.index') }}" class="nav-link {{ request()->routeIs('recycle.index') ? 'active' : '' }}">--}}
{{--                        <i class="nav-icon fas fa-signal"></i></a>--}}

                    <a href="{{ route('recycle.index') }}"
                        class="nav-link {{ request()->routeIs('recycle.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-recycle"></i>

                        <p>Recycle</p>
                    </a>
                </li>
                    @endcan

                    @can('show breaks')
                <li class="nav-item">
                    <a href="{{ route('break.index') }}" class="nav-link {{ request()->routeIs('break.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-signal"></i>
                        <p>Breaks</p>
                    </a>
                </li>
                    @endcan

                    @can('manual attendance entry')
                <li class="nav-item">
                    <a href="{{ route('manual.entry.form') }}" class="nav-link {{ request()->routeIs('manual.entry.form') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-signal"></i>
                        <p>Manual Entry</p>
                    </a>
                </li>
                    @endcan


                    @can('show permissions')
                <li class="nav-item">
                    <a href="{{ route('permission.index') }}" class="nav-link {{ request()->routeIs('permission.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-signal"></i>
                        <p>Permissions</p>
                    </a>
                </li>
                    @endcan

                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
