<aside class="main-sidebar sidebar-dark-primary elevation-4 hidden md:block">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="{{ route('userprofile', ['user' => auth()->user()->id]) }}" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview {{ request()->routeIs('home') ? 'menu-open' : '' }}">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard"></i>
                        <p>Records</p>
                    </a>
                </li>
                @role('admin|super_admin')
                <li class="nav-item">
                    <a href="{{ route('attendance.day-wise') }}" class="nav-link {{ request()->routeIs('attendance.day-wise') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>Attendance</p>
                    </a>
                </li>
                @endrole
                @role('super_admin|admin')
                <li class="nav-item">
                    <a href="{{ route('employee.index') }}" class="nav-link {{ request()->routeIs('employee.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Employee</p>
                    </a>
                </li>
                @endrole
                @role('super_admin|admin|team_leader')
                <li class="nav-item">
                    <a href="{{ route('leave.index') }}" class="nav-link {{ request()->routeIs('leave.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-quote-left"></i>
                        <p>Leave</p>
                    </a>
                </li>
                @endrole
                @role('super_admin')
                <li class="nav-item">
                    <a href="{{ route('office.index') }}" class="nav-link {{ request()->routeIs('office.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-industry"></i>
                        <p>Office</p>
                    </a>
                </li>
                @endrole
                @role('super_admin|admin')
                <li class="nav-item">
                    <a href="{{ route('off.index') }}" class="nav-link {{ request()->routeIs('off.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Manage Offs</p>
                    </a>
                </li>
                @endrole


                @role('super_admin|admin')
                <li class="nav-item">
                    <a href="{{ route('policy.index') }}" class="nav-link {{ request()->routeIs('policy.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-pencil-alt"></i>
                        <p>Policy</p>
                    </a>
                </li>

                @endrole
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-signal"></i>
                        <p>Reports</p>
                    </a>
                </li>

                @role('super_admin|admin')

                <li class="nav-item">
                    <a href="{{ route('employee.attendance') }}" class="nav-link {{ request()->routeIs('employee.attendance') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-signal"></i>
                        <p>Mark Attendance</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('payment.index') }}" class="nav-link {{ request()->routeIs('payment.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-signal"></i>
                        <p>Payment</p>
                    </a>
                </li>
                @endrole

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
