<!--Mobile View Header Section -->
<style>
    /* Scroll animation for the note */
    @keyframes scroll {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    .animate-scroll {
        display: inline-block;
        animation: scroll 30s linear infinite;
    }
</style>


<header class="flex justify-between items-center py-3 px-4 shadow-lg rounded bg-white relative md:hidden">
    <!-- Logo Section -->
    <div class="flex items-center">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <img src="{{ asset('asset/img/logorvg.jpg') }}" alt="Logo" class="w-12 h-12 ml-4 mr-4">
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
            class="hidden absolute right-0 mt-56 w-48 bg-white shadow-lg rounded-lg z-50 transition-all duration-300 ease-out">
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
                    <form action="{{ route('logout') }}" method="post">
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
<nav class="main-header z-10 navbar navbar-expand navbar-white navbar-light md:flex">



    <!-- SEARCH FORM -->
    {{-- <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form> --}}
    @php
        $note = \App\Models\NoteUser::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->first()?->note;
    @endphp
    @if ($note)
        <div class="relative bg-red-100 text-black rounded-lg shadow-md p-4 w-full overflow-hidden"
            style="min-height: 40px;">
            <div class="absolute w-full top-4 left-0 animate-scroll whitespace-nowrap">
                <span class="inline-block">{{ $note?->description }}</span>
            </div>
        </div>
    @endif


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
