@extends('dashboard.layout.root')
@section('content')
    <!-- CSS for responsive cards -->
    <style>
        .responsive-card {
            width: 100%;
            /* This ensures the card takes up full width of the column */
        }

        /* For extra small and small screens: make cards square */
        @media (max-width: 575.98px),
        (min-width: 576px) and (max-width: 767.98px) {
            .responsive-card {
                aspect-ratio: 1 / 1;
                /* Ensures square shape */
                min-height: 140px;
            }
        }

        /* For medium and large screens: allow flexible card height */
        @media (min-width: 768px) {
            .responsive-card {
                height: auto;
                /* Cards will grow based on content */
            }
        }

        /* General Navbar Styling */
        .navbar-dark {
            padding-bottom: 10px;
            /* Adds space at the bottom */
        }

        /* Link Styling */
        .navbar-dark .nav-link {
            position: relative;
            padding: 10px 15px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            z-index: 1;
            /* Ensures no overlapping */
        }

        /* Hover & Focus States */
        .navbar-dark .nav-link:hover,
        .navbar-dark .nav-link:focus {
            background-color: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
            border-radius: 10px;
        }

        /* Icon Styling */
        .navbar-dark .nav-link i {
            font-size: 1.75rem;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        /* Active Icon Effect */
        .navbar-dark .nav-link.active i {
            color: #ffffff;
            transform: translateY(-5px);
        }

        /* Small text styling */
        .navbar-dark .nav-link .small {
            font-size: 0.85rem;
            opacity: 0.8;
            letter-spacing: 0.5px;
        }
    </style>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                            class="btn btn-primary w-100">Check In</a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
                            class="btn btn-danger w-100">Check Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>150</h3>

                            <p>New Orders</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>53<sup style="font-size: 20px">%</sup></h3>

                            <p>Bounce Rate</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>44</h3>

                            <p>User Registrations</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>65</h3>

                            <p>Unique Visitors</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-7 connectedSortable">
                    <!-- Custom tabs (Charts with tabs)-->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Sales
                            </h3>
                            <div class="card-tools">
                                <ul class="nav nav-pills ml-auto">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Area</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content p-0">
                                <!-- Morris chart - Sales -->
                                <div class="chart tab-pane active" id="revenue-chart"
                                    style="position: relative; height: 300px;">
                                    <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
                                </div>
                                <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                                    <canvas id="sales-chart-canvas" height="300" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <!-- DIRECT CHAT -->
                    <div class="card direct-chat direct-chat-primary">
                        <div class="card-header">
                            <h3 class="card-title">Direct Chat</h3>

                            <div class="card-tools">
                                <span data-toggle="tooltip" title="3 New Messages" class="badge badge-primary">3</span>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts"
                                    data-widget="chat-pane-toggle">
                                    <i class="fas fa-comments"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i
                                        class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- Conversations are loaded here -->
                            <div class="direct-chat-messages">
                                <!-- Message. Default to the left -->
                                <div class="direct-chat-msg">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-left">Alexander Pierce</span>
                                        <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                                    </div>
                                    <!-- /.direct-chat-infos -->
                                    <img class="direct-chat-img" src="dist/img/user1-128x128.jpg"
                                        alt="message user image">
                                    <!-- /.direct-chat-img -->
                                    <div class="direct-chat-text">
                                        Is this template really for free? That's unbelievable!
                                    </div>
                                    <!-- /.direct-chat-text -->
                                </div>
                                <!-- /.direct-chat-msg -->

                                <!-- Message to the right -->
                                <div class="direct-chat-msg right">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-right">Sarah Bullock</span>
                                        <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                                    </div>
                                    <!-- /.direct-chat-infos -->
                                    <img class="direct-chat-img" src="dist/img/user3-128x128.jpg"
                                        alt="message user image">
                                    <!-- /.direct-chat-img -->
                                    <div class="direct-chat-text">
                                        You better believe it!
                                    </div>
                                    <!-- /.direct-chat-text -->
                                </div>
                                <!-- /.direct-chat-msg -->

                                <!-- Message. Default to the left -->
                                <div class="direct-chat-msg">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-left">Alexander Pierce</span>
                                        <span class="direct-chat-timestamp float-right">23 Jan 5:37 pm</span>
                                    </div>
                                    <!-- /.direct-chat-infos -->
                                    <img class="direct-chat-img" src="dist/img/user1-128x128.jpg"
                                        alt="message user image">
                                    <!-- /.direct-chat-img -->
                                    <div class="direct-chat-text">
                                        Working with AdminLTE on a great new app! Wanna join?
                                    </div>
                                    <!-- /.direct-chat-text -->
                                </div>
                                <!-- /.direct-chat-msg -->

                                <!-- Message to the right -->
                                <div class="direct-chat-msg right">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-right">Sarah Bullock</span>
                                        <span class="direct-chat-timestamp float-left">23 Jan 6:10 pm</span>
                                    </div>
                                    <!-- /.direct-chat-infos -->
                                    <img class="direct-chat-img" src="dist/img/user3-128x128.jpg"
                                        alt="message user image">
                                    <!-- /.direct-chat-img -->
                                    <div class="direct-chat-text">
                                        I would love to.
                                    </div>
                                    <!-- /.direct-chat-text -->
                                </div>
                                <!-- /.direct-chat-msg -->

                            </div>
                            <!--/.direct-chat-messages-->

                            <!-- Contacts are loaded here -->
                            <div class="direct-chat-contacts">
                                <ul class="contacts-list">
                                    <li>
                                        <a href="#">
                                            <img class="contacts-list-img" src="dist/img/user1-128x128.jpg">

                                            <div class="contacts-list-info">
                                                <span class="contacts-list-name">
                                                    Count Dracula
                                                    <small class="contacts-list-date float-right">2/28/2015</small>
                                                </span>
                                                <span class="contacts-list-msg">How have you been? I was...</span>
                                            </div>
                                            <!-- /.contacts-list-info -->
                                        </a>
                                    </li>
                                    <!-- End Contact Item -->
                                    <li>
                                        <a href="#">
                                            <img class="contacts-list-img" src="dist/img/user7-128x128.jpg">

                                            <div class="contacts-list-info">
                                                <span class="contacts-list-name">
                                                    Sarah Doe
                                                    <small class="contacts-list-date float-right">2/23/2015</small>
                                                </span>
                                                <span class="contacts-list-msg">I will be waiting for...</span>
                                            </div>
                                            <!-- /.contacts-list-info -->
                                        </a>
                                    </li>
                                    <!-- End Contact Item -->
                                    <li>
                                        <a href="#">
                                            <img class="contacts-list-img" src="dist/img/user3-128x128.jpg">

                                            <div class="contacts-list-info">
                                                <span class="contacts-list-name">
                                                    Nadia Jolie
                                                    <small class="contacts-list-date float-right">2/20/2015</small>
                                                </span>
                                                <span class="contacts-list-msg">I'll call you back at...</span>
                                            </div>
                                            <!-- /.contacts-list-info -->
                                        </a>
                                    </li>
                                    <!-- End Contact Item -->
                                    <li>
                                        <a href="#">
                                            <img class="contacts-list-img" src="dist/img/user5-128x128.jpg">

                                            <div class="contacts-list-info">
                                                <span class="contacts-list-name">
                                                    Nora S. Vans
                                                    <small class="contacts-list-date float-right">2/10/2015</small>
                                                </span>
                                                <span class="contacts-list-msg">Where is your new...</span>
                                            </div>
                                            <!-- /.contacts-list-info -->
                                        </a>
                                    </li>
                                    <!-- End Contact Item -->
                                    <li>
                                        <a href="#">
                                            <img class="contacts-list-img" src="dist/img/user6-128x128.jpg">

                                            <div class="contacts-list-info">
                                                <span class="contacts-list-name">
                                                    John K.
                                                    <small class="contacts-list-date float-right">1/27/2015</small>
                                                </span>
                                                <span class="contacts-list-msg">Can I take a look at...</span>
                                            </div>
                                            <!-- /.contacts-list-info -->
                                        </a>
                                    </li>
                                    <!-- End Contact Item -->
                                    <li>
                                        <a href="#">
                                            <img class="contacts-list-img" src="dist/img/user8-128x128.jpg">

                                            <div class="contacts-list-info">
                                                <span class="contacts-list-name">
                                                    Kenneth M.
                                                    <small class="contacts-list-date float-right">1/4/2015</small>
                                                </span>
                                                <span class="contacts-list-msg">Never mind I found...</span>
                                            </div>
                                            <!-- /.contacts-list-info -->
                                        </a>
                                    </li>
                                    <!-- End Contact Item -->
                                </ul>
                                <!-- /.contacts-list -->
                            </div>
                            <!-- /.direct-chat-pane -->
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <form action="#" method="post">
                                <div class="input-group">
                                    <input type="text" name="message" placeholder="Type Message ..."
                                        class="form-control">
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-primary">Send</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-footer-->
                    </div>
                    <!--/.direct-chat -->

                    <!-- TO DO List -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ion ion-clipboard mr-1"></i>
                                To Do List
                            </h3>

                            <div class="card-tools">
                                <ul class="pagination pagination-sm">
                                    <li class="page-item"><a href="#" class="page-link">&laquo;</a></li>
                                    <li class="page-item"><a href="#" class="page-link">1</a></li>
                                    <li class="page-item"><a href="#" class="page-link">2</a></li>
                                    <li class="page-item"><a href="#" class="page-link">3</a></li>
                                    <li class="page-item"><a href="#" class="page-link">&raquo;</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <ul class="todo-list" data-widget="todo-list">
                                <li>
                                    <!-- drag handle -->
                                    <span class="handle">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-v"></i>
                                    </span>
                                    <!-- checkbox -->
                                    <div class="icheck-primary d-inline ml-2">
                                        <input type="checkbox" value="" name="todo1" id="todoCheck1">
                                        <label for="todoCheck1"></label>
                                    </div>
                                    <!-- todo text -->
                                    <span class="text">Design a nice theme</span>
                                    <!-- Emphasis label -->
                                    <small class="badge badge-danger"><i class="far fa-clock"></i> 2 mins</small>
                                    <!-- General tools such as edit or delete-->
                                    <div class="tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-o"></i>
                                    </div>
                                </li>
                                <li>
                                    <span class="handle">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-v"></i>
                                    </span>
                                    <div class="icheck-primary d-inline ml-2">
                                        <input type="checkbox" value="" name="todo2" id="todoCheck2" checked>
                                        <label for="todoCheck2"></label>
                                    </div>
                                    <span class="text">Make the theme responsive</span>
                                    <small class="badge badge-info"><i class="far fa-clock"></i> 4 hours</small>
                                    <div class="tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-o"></i>
                                    </div>
                                </li>
                                <li>
                                    <span class="handle">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-v"></i>
                                    </span>
                                    <div class="icheck-primary d-inline ml-2">
                                        <input type="checkbox" value="" name="todo3" id="todoCheck3">
                                        <label for="todoCheck3"></label>
                                    </div>
                                    <span class="text">Let theme shine like a star</span>
                                    <small class="badge badge-warning"><i class="far fa-clock"></i> 1 day</small>
                                    <div class="tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-o"></i>
                                    </div>
                                </li>
                                <li>
                                    <span class="handle">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-v"></i>
                                    </span>
                                    <div class="icheck-primary d-inline ml-2">
                                        <input type="checkbox" value="" name="todo4" id="todoCheck4">
                                        <label for="todoCheck4"></label>
                                    </div>
                                    <span class="text">Let theme shine like a star</span>
                                    <small class="badge badge-success"><i class="far fa-clock"></i> 3 days</small>
                                    <div class="tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-o"></i>
                                    </div>
                                </li>
                                <li>
                                    <span class="handle">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-v"></i>
                                    </span>
                                    <div class="icheck-primary d-inline ml-2">
                                        <input type="checkbox" value="" name="todo5" id="todoCheck5">
                                        <label for="todoCheck5"></label>
                                    </div>
                                    <span class="text">Check your messages and notifications</span>
                                    <small class="badge badge-primary"><i class="far fa-clock"></i> 1 week</small>
                                    <div class="tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-o"></i>
                                    </div>
                                </li>
                                <li>
                                    <span class="handle">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-v"></i>
                                    </span>
                                    <div class="icheck-primary d-inline ml-2">
                                        <input type="checkbox" value="" name="todo6" id="todoCheck6">
                                        <label for="todoCheck6"></label>
                                    </div>
                                    <span class="text">Let theme shine like a star</span>
                                    <small class="badge badge-secondary"><i class="far fa-clock"></i> 1 month</small>
                                    <div class="tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-o"></i>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer clearfix">
                            <button type="button" class="btn btn-info float-right"><i class="fas fa-plus"></i> Add
                                item</button>
                        </div>
                    </div>
                    <!-- /.card -->
                </section>
                <!-- /.Left col -->
                <!-- right col (We are only adding the ID to make the widgets sortable)-->
                <section class="col-lg-5 connectedSortable">

                    <!-- Map card -->
                    <div class="card bg-gradient-primary">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Visitors
                            </h3>
                            <!-- card tools -->
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm daterange" data-toggle="tooltip"
                                    title="Date range">
                                    <i class="far fa-calendar-alt"></i>
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse"
                                    data-toggle="tooltip" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.card-tools -->
                        </div>
                        <div class="card-body">
                            <div id="world-map" style="height: 250px; width: 100%;"></div>
                        </div>
                        <!-- /.card-body-->
                        <div class="card-footer bg-transparent">
                            <div class="row">
                                <div class="col-4 text-center">
                                    <div id="sparkline-1"></div>
                                    <div class="text-white">Visitors</div>
                                </div>
                                <!-- ./col -->
                                <div class="col-4 text-center">
                                    <div id="sparkline-2"></div>
                                    <div class="text-white">Online</div>
                                </div>
                                <!-- ./col -->
                                <div class="col-4 text-center">
                                    <div id="sparkline-3"></div>
                                    <div class="text-white">Sales</div>
                                </div>
                                <!-- ./col -->
                            </div>
                            <!-- /.row -->
                        </div>
                    </div>
                    <!-- /.card -->

                    <!-- solid sales graph -->
                    <div class="card bg-gradient-info">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-th mr-1"></i>
                                Sales Graph
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas class="chart" id="line-chart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer bg-transparent">
                            <div class="row">
                                <div class="col-4 text-center">
                                    <input type="text" class="knob" data-readonly="true" value="20"
                                        data-width="60" data-height="60" data-fgColor="#39CCCC">

                                    <div class="text-white">Mail-Orders</div>
                                </div>
                                <!-- ./col -->
                                <div class="col-4 text-center">
                                    <input type="text" class="knob" data-readonly="true" value="50"
                                        data-width="60" data-height="60" data-fgColor="#39CCCC">

                                    <div class="text-white">Online</div>
                                </div>
                                <!-- ./col -->
                                <div class="col-4 text-center">
                                    <input type="text" class="knob" data-readonly="true" value="30"
                                        data-width="60" data-height="60" data-fgColor="#39CCCC">

                                    <div class="text-white">In-Store</div>
                                </div>
                                <!-- ./col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.card-footer -->
                    </div>
                    <!-- /.card -->

                    <!-- Calendar -->
                    <div class="card bg-gradient-success">
                        <div class="card-header border-0">

                            <h3 class="card-title">
                                <i class="far fa-calendar-alt"></i>
                                Calendar
                            </h3>
                            <!-- tools card -->
                            <div class="card-tools">
                                <!-- button with a dropdown -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                        data-toggle="dropdown">
                                        <i class="fas fa-bars"></i></button>
                                    <div class="dropdown-menu float-right" role="menu">
                                        <a href="#" class="dropdown-item">Add new event</a>
                                        <a href="#" class="dropdown-item">Clear events</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">View calendar</a>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <!-- /. tools -->
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body pt-0">
                            <!--The calendar -->
                            <div id="calendar" style="width: 100%"></div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </section>
                <!-- right col -->

                <div class="container" style="margin-bottom: 100px;">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold">Dashboard</h2>
                        <a href="#" class="btn btn-outline-danger btn-sm text-decoration-none">LOGOUT</a>
                    </div>

                    <!-- Dashboard Icons Grid -->
                    <div class="row row-cols-2 row-cols-md-3 g-4">
                        <div class="col">
                            <a href="{{ route('leave.create') }}" class="text-decoration-none text-reset d-block">
                                <div class="card shadow border-0 h-100 responsive-card">
                                    <div
                                        class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <i class="fas fa-calendar-alt text-danger mb-3" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">Leave Request</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none text-reset d-block">
                                <div class="card shadow border-0 h-100 responsive-card">
                                    <div
                                        class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <i class="fas fa-file-alt text-danger mb-3" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">Leave Request Details</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none text-reset d-block" style="margin-top: 10px;">
                                <div class="card shadow border-0 h-100 responsive-card">
                                    <div
                                        class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <i class="fas fa-check-square text-danger mb-3" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">Leave Approval</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none text-reset d-block" style="margin-top: 10px;">
                                <div class="card shadow border-0 h-100 responsive-card">
                                    <div
                                        class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <i class="fas fa-id-card text-danger mb-3" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">Attendance Regularization</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none text-reset d-block" style="margin-top: 10px;">
                                <div class="card shadow border-0 h-100 responsive-card">
                                    <div
                                        class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <i class="fas fa-pencil-alt text-danger mb-3" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">Regularization Approval</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none text-reset d-block" style="margin-top: 10px;">
                                <div class="card shadow border-0 h-100 responsive-card">
                                    <div
                                        class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                        <i class="fas fa-list-ul text-danger mb-3" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">Regularization Listing</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile App Style Bottom Navbar (Bootstrap) -->
                <nav class="navbar navbar-dark bg-danger fixed-bottom d-lg-none"
                    style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <div class="container-fluid px-0">
                        <ul class="nav nav-fill w-100">
                            <!-- Home Link -->
                            <li class="nav-item">
                                <a href="#"
                                    class="nav-link text-white d-flex flex-column align-items-center py-2 active">
                                    <i class="fas fa-home mb-1"></i>
                                    <span class="small">Home</span>
                                </a>
                            </li>
                            <!-- Dashboard Link -->
                            <li class="nav-item">
                                <a href="#" class="nav-link text-white d-flex flex-column align-items-center py-2">
                                    <i class="fas fa-th-large mb-1"></i>
                                    <span class="small">Dashboard</span>
                                </a>
                            </li>
                            <!-- Profile Link -->
                            <li class="nav-item">
                                <a href="#" class="nav-link text-white d-flex flex-column align-items-center py-2">
                                    <i class="fas fa-user mb-1"></i>
                                    <span class="small">Profile</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>


            </div>
            <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
