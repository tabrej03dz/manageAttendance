<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Real Victory Groups</title>
    <link rel="shortcut icon" href="{{asset('asset/img/logo.png')}}" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom 3D Hover Effect */
        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
        }
        

        .button-3d {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .button-3d:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-300 flex items-center justify-center min-h-screen p-4">
    <!-- Main Container -->
    <div class="card text-center p-8 bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full transition-transform duration-300">
        <!-- Header -->
        <header class="mb-6">
            <h1 class="text-4xl font-extrabold text-red-600">Real Victory Groups</h1>
            <p class="text-lg text-gray-400 mt-2">Streamline Your Attendance Management</p>
        </header>

        <!-- Main Content -->
        <div class="mb-6">
            <div class="bg-gray-700 p-4 rounded-full shadow-lg mx-auto w-24 h-24 transition-transform duration-300">
                <img src="{{ asset('asset/img/logo.png') }}" alt="Attendance" class="w-full h-full object-contain">
            </div>
            <h2 class="text-2xl font-semibold text-white mt-4">Welcome to the Attendance App</h2>
            <p class="text-gray-400 text-sm mt-2">Easily track and manage employee attendance with our intuitive app. Please log in to mark your attendance.</p>
        </div>

        <!-- Buttons -->
        <div class="space-y-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}"
                        class="button-3d w-full inline-block py-3 bg-red-600 text-white rounded-full hover:bg-red-700 shadow-lg hover:shadow-xl transition-all duration-300">
                        Home
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="button-3d w-full inline-block py-3 bg-red-600 text-white rounded-full hover:bg-red-700 shadow-lg hover:shadow-xl transition-all duration-300">
                        Log in
                    </a>
                @endauth
            @endif
        </div>

        <!-- Footer -->
        <footer class="mt-8 text-gray-500 text-sm">
            <p>&copy; 2024 Real Victory Groups. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>
