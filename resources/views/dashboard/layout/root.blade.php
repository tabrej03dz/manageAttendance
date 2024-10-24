<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">



    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        @keyframes scroll {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        .animate-scroll {
            animation: scroll 10s linear infinite;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">




        @include('dashboard.layout.header')


        @php
            if ($user->hasRole('admin')){
                 // Importing Carbon to keep it consistent

                // Fetch the current month's payment for the user's office
                $payment = App\Models\Payment::where('office_id', $user->office->id)
                    ->whereMonth('date', Carbon\Carbon::now()->month)
                    ->whereYear('date', Carbon\Carbon::now()->year)
                    ->first();

                // If no payment exists, create a new payment record for this office
                if ($payment == null) {
                    $payment = App\Models\Payment::create([
                        'office_id' => $user->office->id,
                        'amount' => ($user->office->number_of_employees * $user->office->price_per_employee),
                        'date' => Carbon\Carbon::now()->firstOfMonth(),  // Create payment for the first day of the month
                    ]);
                }
            }
        @endphp

            <!-- Check if payment amount is greater than the paid amount -->


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">


            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
                @if($user->hasRole('admin') && $payment->amount > $payment->paid_amount)
                    <div class="overflow-hidden whitespace-nowrap border-3">
                        <div class="inline-block animate-scroll text-danger">
                            Payment of {{ $payment->amount - $payment->paid_amount }} rs is due for the month of {{ $payment->date->format('F') }}.

                        </div>
                    </div>
                @endif
            @yield('content')
        </div>





    </div>







    <!-- Mobile Navigation Bar with Hover Effects & Elevated Design -->
    <nav
        class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl z-50">
        <div class="flex justify-around py-2">
            <a href="{{ route('home') }}"
                class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">home</span>
                <span class="text-xs text-white mt-1">Home</span>
            </a>
            <a href="{{route('reports.index')}}" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">list</span>
                <span class="text-xs text-white mt-1">Reports</span>
            </a>
            <a href="{{ route('attendance.index') }}"
                class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">folder</span>
                <span class="text-xs text-white mt-1">Records</span>
            </a>
            <a href="{{ route('attendance.day-wise') }}"
                class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">access_time</span>
                <span class="text-xs text-white mt-1">Attendance</span>
            </a>
            <a href="{{ route('userprofile', ['user' => auth()->user()->id]) }}"
                class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">account_circle</span>
                <span class="text-xs text-white mt-1">Account</span>
            </a>
            @role('super_admin|admin')
                <a href="#" id="see-more"
                    class="group flex flex-col items-center transform hover:scale-105 transition duration-300 cursor-pointer">
                    <span class="material-icons text-white text-2xl">more_horiz</span>
                    <span class="text-xs text-white mt-1">See More</span>
                </a>
            @endrole
        </div>
        <!-- Additional Options with Icons and Border -->
        <div id="more-options" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div
                class="flex border-2 border-white justify-around bg-gradient-to-r from-red-600 to-red-500 shadow-md rounded-t-2xl py-2 mt-2">
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">logout</span>
                    <span class="text-xs">Leave</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">work</span>
                    <span class="text-xs">Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">manage_accounts</span>
                    <span class="text-xs">Manage Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">people</span>
                    <span class="text-xs">Employees</span>
                </a>
            </div>
        </div>
    </nav>

    <script>
        document.getElementById('see-more').addEventListener('click', function() {
            const moreOptions = document.getElementById('more-options');
            const isOpen = moreOptions.style.maxHeight; // Check current state

            if (!isOpen || isOpen === '0px') {
                moreOptions.style.maxHeight = moreOptions.scrollHeight + 'px'; // Set to full height
            } else {
                moreOptions.style.maxHeight = '0'; // Collapse
            }
        });
        // Initialize datepickers
        flatpickr("#from-date-mobile", {
            dateFormat: "Y-m-d",
        });
        flatpickr("#to-date-mobile", {
            dateFormat: "Y-m-d",
        });
        flatpickr("#from-date-web", {
            dateFormat: "Y-m-d",
        });
        flatpickr("#to-date-web", {
            dateFormat: "Y-m-d",
        });
    </script>








    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>

</body>

</html>
