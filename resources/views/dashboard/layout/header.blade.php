<!--Mobile View Header Section -->


<header class="flex justify-between items-center py-3 px-4 shadow-lg rounded bg-white relative md:hidden">
    <!-- Logo Section -->
    <div class="flex items-center">
        <img src="{{ asset('asset/img/logo.png') }}" alt="Logo" class="w-12 h-12 mr-4">
    </div>

    <!-- User Info Section -->
    <div class="flex items-center relative">
        <!-- Avatar with Dropdown Trigger -->
        <div class="relative">
            @php
                $user = auth()->user();
                // Get user's name and split it into parts
                $userName = $user->name;
                $nameParts = explode(' ', $userName);
                $firstLetter = strtoupper($nameParts[0][0] ?? '');
                $lastLetter = strtoupper($nameParts[1][0] ?? '');

            @endphp

            @if ($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="User Avatar"
                    class="rounded-full cursor-pointer w-10 h-10 mr-2 hover:opacity-90 transition-opacity"
                    aria-haspopup="true" aria-expanded="false"
                    onclick="document.getElementById('profileDropdown').classList.toggle('hidden')">
            @else
                <div class="flex items-center justify-center bg-gray-300 rounded-full w-10 h-10 mr-2 cursor-pointer hover:opacity-90 transition-opacity"
                    aria-haspopup="true" aria-expanded="false"
                    onclick="document.getElementById('profileDropdown').classList.toggle('hidden')">
                    <span class="font-bold text-white">{{ $firstLetter }}{{ $lastLetter }}</span>
                </div>
            @endif
        </div>


        <!-- Dropdown Menu -->
        <div id="profileDropdown"
            class="hidden absolute right-0 mt-56 w-48 bg-white shadow-lg rounded-lg z-10 transition-all duration-300 ease-out">
            <ul class="py-2 text-gray-700">
                <li class="px-4 py-2 font-bold text-center border-b">{{ $userName }}</li> <!-- User's name -->
                <li>
                    <a href=""
                        class="flex items-center px-4 py-2 text-gray-700 hover:bg-red-500 hover:text-white focus:bg-red-500 focus:text-white transition-colors duration-200">
                        <i class="fas fa-user-circle mr-2"></i>
                        Profile
                    </a>
                </li>
                <li>
                    <a href=""
                        class="flex items-center px-4 py-2 text-gray-700 hover:bg-red-500 hover:text-white focus:bg-red-500 focus:text-white transition-colors duration-200">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
                    </a>
                </li>
                <li>
                    <form action="{{route('logout')}}" method="post">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white focus:bg-red-500 focus:text-white transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>


</header>


<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light md:flex">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>




    <!-- Right navbar links -->
    {{--        <ul class="navbar-nav ml-auto"> --}}
    {{--            <!-- Messages Dropdown Menu --> --}}
    {{--            <li class="nav-item dropdown"> --}}
    {{--                <a class="nav-link" data-toggle="dropdown" href="#"> --}}
    {{--                    <i class="far fa-comments"></i> --}}
    {{--                    <span class="badge badge-danger navbar-badge">3</span> --}}
    {{--                </a> --}}
    {{--                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"> --}}
    {{--                    <a href="#" class="dropdown-item"> --}}
    {{--                        <!-- Message Start --> --}}
    {{--                        <div class="media"> --}}
    {{--                            <img src="{{asset('dist/img/user1-128x128.jpg')}}" alt="User Avatar" class="img-size-50 mr-3 img-circle"> --}}
    {{--                            <div class="media-body"> --}}
    {{--                                <h3 class="dropdown-item-title"> --}}
    {{--                                    Brad Diesel --}}
    {{--                                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span> --}}
    {{--                                </h3> --}}
    {{--                                <p class="text-sm">Call me whenever you can...</p> --}}
    {{--                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p> --}}
    {{--                            </div> --}}
    {{--                        </div> --}}
    {{--                        <!-- Message End --> --}}
    {{--                    </a> --}}
    {{--                    <div class="dropdown-divider"></div> --}}
    {{--                    <a href="#" class="dropdown-item"> --}}
    {{--                        <!-- Message Start --> --}}
    {{--                        <div class="media"> --}}
    {{--                            <img src="{{asset('dist/img/user8-128x128.jpg')}}" alt="User Avatar" class="img-size-50 img-circle mr-3"> --}}
    {{--                            <div class="media-body"> --}}
    {{--                                <h3 class="dropdown-item-title"> --}}
    {{--                                    John Pierce --}}
    {{--                                    <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span> --}}
    {{--                                </h3> --}}
    {{--                                <p class="text-sm">I got your message bro</p> --}}
    {{--                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p> --}}
    {{--                            </div> --}}
    {{--                        </div> --}}
    {{--                        <!-- Message End --> --}}
    {{--                    </a> --}}
    {{--                    <div class="dropdown-divider"></div> --}}
    {{--                    <a href="#" class="dropdown-item"> --}}
    {{--                        <!-- Message Start --> --}}
    {{--                        <div class="media"> --}}
    {{--                            <img src="{{asset('dist/img/user3-128x128.jpg')}}" alt="User Avatar" class="img-size-50 img-circle mr-3"> --}}
    {{--                            <div class="media-body"> --}}
    {{--                                <h3 class="dropdown-item-title"> --}}
    {{--                                    Nora Silvester --}}
    {{--                                    <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span> --}}
    {{--                                </h3> --}}
    {{--                                <p class="text-sm">The subject goes here</p> --}}
    {{--                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p> --}}
    {{--                            </div> --}}
    {{--                        </div> --}}
    {{--                        <!-- Message End --> --}}
    {{--                    </a> --}}
    {{--                    <div class="dropdown-divider"></div> --}}
    {{--                    <a href="#" class="dropdown-item dropdown-footer">See All Messages</a> --}}
    {{--                </div> --}}
    {{--            </li> --}}
    {{--            <!-- Notifications Dropdown Menu --> --}}
    {{--            <li class="nav-item dropdown"> --}}
    {{--                <a class="nav-link" data-toggle="dropdown" href="#"> --}}
    {{--                    <i class="far fa-bell"></i> --}}
    {{--                    <span class="badge badge-warning navbar-badge">15</span> --}}
    {{--                </a> --}}
    {{--                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"> --}}
    {{--                    <span class="dropdown-item dropdown-header">15 Notifications</span> --}}
    {{--                    <div class="dropdown-divider"></div> --}}
    {{--                    <a href="#" class="dropdown-item"> --}}
    {{--                        <i class="fas fa-envelope mr-2"></i> 4 new messages --}}
    {{--                        <span class="float-right text-muted text-sm">3 mins</span> --}}
    {{--                    </a> --}}
    {{--                    <div class="dropdown-divider"></div> --}}
    {{--                    <a href="#" class="dropdown-item"> --}}
    {{--                        <i class="fas fa-users mr-2"></i> 8 friend requests --}}
    {{--                        <span class="float-right text-muted text-sm">12 hours</span> --}}
    {{--                    </a> --}}
    {{--                    <div class="dropdown-divider"></div> --}}
    {{--                    <a href="#" class="dropdown-item"> --}}
    {{--                        <i class="fas fa-file mr-2"></i> 3 new reports --}}
    {{--                        <span class="float-right text-muted text-sm">2 days</span> --}}
    {{--                    </a> --}}
    {{--                    <div class="dropdown-divider"></div> --}}
    {{--                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a> --}}
    {{--                </div> --}}
    {{--            </li> --}}
    {{--            <li class="nav-item"> --}}
    {{--                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button"> --}}
    {{--                    <i class="fas fa-th-large"></i> --}}
    {{--                </a> --}}
    {{--            </li> --}}
    {{--        </ul> --}}
</nav>
<!-- /.navbar -->



<script>
    // Close dropdown if clicked outside
    window.onclick = function(event) {
        const dropdown = document.getElementById('profileDropdown');
        if (!event.target.closest('.relative') && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    }

    // Accessibility: Allow closing the dropdown with the Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('profileDropdown').classList.add('hidden');
        }
    });
</script>

@include('dashboard.layout.sidebar')
