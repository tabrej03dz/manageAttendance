@extends('mainpage.components.main')
@section('content')
    <div class="app-container">

        <!-- =======================
            *** Main/Hero Section *** 2xl:max-w-full
            ======================== -->
        {{-- <main class="bg-gradient-to-tr from-[#fa0240] to-[#0837f4] z-50 relative">
            <div
                class="absolute inset-0 -z-40 h-full w-full bg-transparent bg-[linear-gradient(to_right,#ffffff12_1px,transparent_1px),linear-gradient(to_bottom,#ffffff12_1px,transparent_1px)] bg-[size:6rem_4rem]">
            </div>

            <div
                class="w-full xl:max-w-7xl mx-auto py-28 px-6 md:px-16 2xl:px-0 block lg:flex md:flex-row-reverse items-center justify-between static z-50">
                <!-- Right Side -->
                <div class="lg:mb-0 mb-14">
                    <center>
                        <img class="shrink-0" src="{{ asset('mainasset/img/mainBg.png') }}" loading="lazy" alt="hero image...">
                    </center>
                </div>

                <!-- Left Side -->
                <div class="place-items-center">
                    <div class="place-items-center lg:place-items-start text-center lg:text-start lg:max-w-2xl mx-auto">
                        <h1
                            class="w-full text-3xl md:text-4xl xl:text-6xl text-pretty text-white font-futuraMd font-bold leading-none md:leading-tight">
                            Attendance simplified, anywhere anytime
                        </h1>
                        <div class="tracking-wide px-8 py-8">
                            <ul
                                class="text-white text-2xl font-futuraLg [&_li]:flex [&_li]:items-center [&_li]:justify-center lg:[&_li]:justify-start lg:[&_li]:text-start [&_li]:py-1 [&_img]:size-6 [&_img]:mr-3 [&_img]:hidden lg:[&_img]:block">
                                <li>
                                    <img src="{{ asset('mainasset/img/mainCheck.png') }}" loading="lazy"
                                        alt="..list_image...">
                                    <p>Mark Attendance with Geotagging</p>
                                </li>
                                <li class="py-0">
                                    <img src="{{ asset('mainasset/img/mainCheck.png') }}" loading="lazy"
                                        alt="..list_image...">
                                    <p>Location Tracking</p>
                                </li>
                                <li>
                                    <img src="{{ asset('mainasset/img/mainCheck.png') }}" loading="lazy"
                                        alt="..list_image...">
                                    <p>Geofencing</p>
                                </li>
                                <li class="py-0">
                                    <img src="{{ asset('mainasset/img/mainCheck.png') }}" loading="lazy"
                                        alt="..list_image...">
                                    <p>Leave Management</p>
                                </li>
                                <li>
                                    <img src="{{ asset('mainasset/img/mainCheck.png') }}" loading="lazy"
                                        alt="..list_image...">
                                    <p>Multi-lingual</p>
                                </li>
                            </ul>
                        </div>
                        <div class="w-full">
                            <a class="px-14 py-3 font-sans text-xl tracking-tight rounded-full duration-150 ease-linear border-2 border-white text-white bg-countryBtn hover:bg-universal hover:text-white"
                                href="{{ route('reqDemo') }}">Request a Demo</a>
                        </div>

                        <div class="text-white py-8 mt-4">
                            <span class="text-2xl">Try it yourself. Download now.</span>
                        </div>


                    </div>
                </div>
            </div>

            <!--Main shap divider -->
            <div class="custom-shape-divider-bottom-1732367268">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                    preserveAspectRatio="none">
                    <path
                        d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
                        opacity=".25" class="shape-fill"></path>
                    <path
                        d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                        opacity=".5" class="shape-fill"></path>
                    <path
                        d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
                        class="shape-fill"></path>
                </svg>
            </div>
        </main> --}}
        <div class="relative h-screen">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0">
              <div class="absolute inset-0 bg-black bg-opacity-50"></div>
              <img 
                src="{{ asset('asset/img/web header.jpg') }}" 
                alt="Background Banner" 
                class="w-full h-full object-cover"
              />
            </div>
          
            <!-- Foreground Content -->
            <div class="relative max-w-7xl mx-auto h-full flex flex-col md:flex-row items-center justify-center p-6 md:p-12">
              
              <!-- Left Section -->
              <div class="bg-white bg-opacity-10 rounded-lg shadow-lg p-6 md:w-1/2 mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">
                  Attendance Management That Empowers Your Workforce
                </h1>
                <p class="text-white text-lg mb-8 leading-relaxed">
                  Bid farewell to tedious excel sheets, and say hello to a more efficient way to manage employee attendance. Generate insightful reports, regularize attendance, and empower your on-site and remote employees to check in from anywhere with our cloud-based attendance management system.
                </p>
                <a 
                  href="{{ route('reqDemo') }}" 
                  class="inline-block bg-green-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-green-700 transition"
                >
                  REQUEST FOR DEMO
                </a>
              </div>
          
              <!-- Optional Right Section (Currently Hidden) -->
              <div class="hidden md:block md:w-1/2 h-full relative mt-8 md:mt-0 md:ml-8">
                <!-- Uncomment the following if image is required -->
                <!-- <img 
                  src="{{ asset('asset/img/app-using.png') }}" 
                  alt="Feature Banner" 
                  class="w-full h-full object-cover rounded-lg shadow-lg"
                /> -->
              </div>
          
            </div>
          </div>
          
          
          
        <!-- =======================
            *** Branding Section ***
            ======================== -->


        <section class="w-full xl:max-w-7xl mx-auto py-16 px-6 md:px-16  2xl:px-0">
            <marquee behavior="scroll-smooth" direction="ltr">
                <div class="slide gap-8 *:cursor-pointer flex justify-center items-center xl:justify-between ">
                    <img src="{{ asset('asset/img/output-onlinepngtools (1).png') }}" loading="lazy" alt="brand" class="h-40 w-40 ">
                    <img src="{{ asset('asset/img/output-onlinepngtools.png') }}" loading="lazy" alt="brand"  class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (2).png') }}" loading="lazy" alt="brand"  class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools.png') }}" loading="lazy" alt="brand"  class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools.png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (2).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (1).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (2).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools.png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (1).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (2).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools.png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (2).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (1).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools.png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (2).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (1).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (2).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools.png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                    <img src="{{ asset('asset/img/output-onlinepngtools (1).png') }}" loading="lazy" alt="brand" class="h-40 w-40">
                </div>
            </marquee>
        </section>


            {{-- ABOUT --}}
        <section id="attendNow" class="aboutBG  relative">
            <div class="absolute top-0 -z-10 h-full w-full bg-white overflow-hidden md:overflow-visible">
                <div
                    class="absolute bottom-auto right-auto left-0 top-0 size-[500px] translate-x-[35%] translate-y-[10%] rounded-full bg-bgSecondary opacity-50 blur-[100px]">
                </div>
                <div
                    class=" absolute top-auto left-auto right-0 bottom-0 size-[200px] -translate-x-[20%] translate-y-[0%] rounded-full bg-bgSecondary opacity-50 blur-[100px]">
                </div>
            </div>

            <div
                class="w-full xl:max-w-7xl mx-auto py-28 px-6 md:px-16 2xl:px-0 block xl:flex justify-between items-center place-items-center *:size-fit">
                <div class="image xl:w-full">
                    <img class="w-[1000px]" src="{{ asset('asset/img/multitask.png') }}" loading="lazy"
                        alt="about...">
                </div>
                <div class="block md:place-items-end xl:place-items-start">
                    <h1
                        class="size-full sm:w-[450px] md:w-[600px] md:h-auto font-futuraBk place-items-center text-center xl:text-left text-3xl md:text-5xl md:leading-tight lg:leading-snug my-8 md:my-0 py-8">
                        Streamlining<strong class="text-bgSecondary font-futuraMd font-bold">Employee Attendance</strong>
                        with
                        Location-Based Insights
                        <hr class="w-[16%] inline-block justify-items-center bg-bgSecondary p-[1px] mb-2 ">
                        <hr class="size-4 inline-block rounded-full bg-bgSecondary mb-1 animate-bounce">
                    </h1>

                    <p
                        class="text-lg font-futuraBk text-textSecondary font-light tracking-wider text-center sm:text-start md:text-justify leading-loose
          w-full h-auto sm:w-[550px] md:w-[600px] md:ml-12 overflow-auto">
          Using GPS technology, employers can track their employees in real-time, identify their location, monitor their movements, and log their arrival and departure times from anywhere. GPS also facilitates automatic attendance recording. RVG provides all of these capabilities, even in areas with limited or no network access, and integrates smoothly with payroll and leave management systems.
                    </p>
                </div>
            </div>

            <!-- about shap devider  -->
            {{-- <div class="custom-shape-divider-bottom-1732348233">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                    preserveAspectRatio="none">
                    <path d="M1200 0L0 0 892.25 114.72 1200 0z" class="shape-fill"></path>
                </svg>
            </div> --}}
        </section>

        <!-- =======================
            *** Benefits Section *** style="background: url('./assets/TestimonialsBg.jpg') no-repeat center center/cover"
            ======================== -->
        {{-- <section id="benefit" class="bg-bgSuccess relative py-20">
            <div class="relative h-full w-full bg-slate-950">
                <div
                    class="absolute bottom-0 left-0 right-0 top-0 bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-[size:14px_24px]">
                </div>
            </div>
            <!-- service shap devider  -->
            <div class="custom-shape-divider-top-1732348142 relative">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                    preserveAspectRatio="none">
                    <path d="M892.25 114.72L0 0 0 120 1200 120 1200 0 892.25 114.72z" class="shape-fill"></path>
                </svg>
            </div>

            <div class="w-full xl:max-w-7xl mx-auto py-[100px] px-6 md:px-16 xl:px-0">
                <div class="mb-28 text-center max-w-md md:max-w-xl mx-auto">
                    <h2 class="text-bgPrimary font-futuraBk text-3xl md:text-5xl md:leading-tight lg:leading-snugt">
                        How <strong class="text-bgSecondary font-futuraMd font-bold">AttendNow</strong> can help your
                        business</h2>
                    <p class="my-6 text-lg text-center leading-tight font-futuraBk text-textSecondary">
                        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Sed non tenetur nihil quibusdam temporas.
                    </p>

                    <hr class="w-[25%] inline-block justify-items-center bg-bgSecondary p-[1px] mb-2 ">

                </div>

                <!-- boxes -->
                <div
                    class="grid place-items-center grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-8 [&_div]:w-[285px] [&_div]:h-[292.5px] [&_div]:bg-bgSecondary [&_div]:rounded-2xl">
                    <div class="servBox box-1">
                        <h1
                            class="text-justify text-2xl font-futuraBk tracking-tight max-w-[189.2px] px-4 py-6 text-white">
                            Attendance
                            with
                            Geotagging</h1>
                        <p class="text-lg px-4 leading-relaxed text-neutral-200">
                            Give flexibility to your sales reps to mark attendance from anywhere so that they can have more
                            meetings and generate more business for you.
                        </p>
                    </div>
                    <div class="servBox box-2">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Location
                            Tracking</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Increase workforce efficiency by having better visibility and insights into what's happening on
                            job.
                            Allocate resources more effectively and reduce travel costs.
                        </p>
                    </div>
                    <div class="servBox box-3">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Face
                            Recognition</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Be rest assured that employees don't claim a colleague's attendance. Eliminate issues related to
                            buddy
                            punching and get additional layer of security.
                        </p>
                    </div>
                    <div class="servBox box-4">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Geofencing</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Get accurate record of an employee's working hours, making it easier to analyze and identify
                            patterns in
                            attendance. Get the exact location of employees entering or leaving the premises
                        </p>
                    </div>
                    <div class="servBox box-5">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Leave Management</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Reduce administration and paperwork enabling HR personnel to manage absences more efficiently.
                            Improve
                            resources planning and reduce disruption
                        </p>
                    </div>
                    <div class="servBox box-6">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Roster</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Get better accuracy level than traditional methods. Employees no longer need to fill in a paper
                            timesheet, reducing the risk of errors. Increases productivity and focus resources on growth.
                        </p>
                    </div>
                    <div class="servBox box-7">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Salary Calculation</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Quickly and accurately calculate salaries and deductions, ensuring efficient use of resources
                            and
                            improved employee satisfaction
                        </p>
                    </div>
                    <div class="servBox box-8">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Payslip
                            Generation</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Eliminate the need for controller and HR staff to manually generate documents and calculations,
                            reducing
                            the amount of administrative overhead.
                        </p>
                    </div>
                    <div class="servBox box-9">
                        <h1 class="font-futuraBk text-2xl tracking-tighter max-w-[189.2px] px-4 py-6 text-white">
                            Timesheet with Approvals</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Encourage employees to stay focused and productive throughout their workdays. This results in
                            increased
                            efficiency and a greater output from workers.
                        </p>
                    </div>
                    <div class="servBox box-10">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Selfie Attendance</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Selfies make attendance tracking is more secure, as only registered individuals can take
                            attendance.
                            Photos look more human and are harder to manipulate.
                        </p>
                    </div>
                    <div class="servBox box-11">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Works
                            Offline</h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Be rest assured that your attendance records are kept safe and secure in remote areas having
                            poor
                            network or no network at all. Get the same accuracy as that of online.
                        </p>
                    </div>
                    <div class="servBox box-12">
                        <h1 class="w-min font-futuraBk text-2xl tracking-tighter max-w-[186.89px] px-4 py-6 text-white">
                            Multilingual
                        </h1>
                        <p class="text-justify text-lg px-4 leading-relaxed text-neutral-200">
                            Employees no longer need to waste time trying to figure out how to use unfamiliar language to
                            finish
                            their tasks quickly and efficiently. AttendNow currently supports English and Hindi. More
                            languages
                            coming soon.
                        </p>
                    </div>
                </div>
            </div>
        </section> --}}

      <section id="benefit" class="bg-bgSuccess relative py-20">
    <div class="relative h-full w-full bg-slate-950">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-[size:14px_24px]"></div>
    </div>

    <div class="w-full xl:max-w-7xl mx-auto py-16 px-6 md:px-12 xl:px-8">
        <div class="mb-16 text-center max-w-2xl mx-auto">
            <h2 class="text-bgPrimary font-futuraBk text-3xl md:text-4xl lg:text-5xl leading-snug">
                How <strong class="text-bgSecondary font-futuraMd font-bold">RealVictoryGroups</strong> Can Transform Your Business
            </h2>
            <p class="my-6 text-lg md:text-xl leading-relaxed font-futuraBk text-textSecondary">
                Streamline workforce management and enhance efficiency with RVG’s smart solutions.
            </p>
            <hr class="w-20 mx-auto bg-gradient-to-t from-black to-red-500 h-[2px] mb-4">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/banner.jpg')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Attendance with Smartphone
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Give flexibility to your employees to mark attendance and take short breaks from their smartphone device.
                </p>
            </div>

            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/service2.jpeg')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Geofencing
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Get accurate records of employee working hours with check-in and check-out, making it easier to analyze patterns. Track exact locations of employees entering or leaving the premises.
                </p>
            </div>

            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/service3.png')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Selfie Attendance
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Secure attendance tracking with selfies. Eliminate buddy punching and add an extra layer of security, ensuring only registered individuals can log attendance.
                </p>
            </div>

            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/service4.jpeg')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Leave Management
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Approval-based leave management system that helps HR personnel efficiently manage absences, improve resource planning, and reduce disruptions.
                </p>
            </div>

            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/service5.jpeg')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Tour Bill Management
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Improve accuracy with digital submissions. Sales teams can upload tickets and bills directly using their mobile phones.
                </p>
            </div>

            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/service6.jpeg')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Salary and Advance Calculation
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Quickly and accurately calculate salaries (hourly and daily) and deductions, ensuring efficient resource use and improved employee satisfaction.
                </p>
            </div>

            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/service7.jpeg')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Payslip Generation
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Automate payslip generation to reduce administrative overhead and free up HR staff for other tasks.
                </p>
            </div>

            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/service8.jpeg')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Employees Data
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Manage employee data by uploading records such as Aadhaar card, PAN card, and previous office details.
                </p>
            </div>

            <div class="servBox p-6 rounded-2xl bg-gradient-to-t from-black to-red-500">
                <div class="flex justify-center mb-4">
                    <img src="{{asset('asset/img/service9.png')}}" alt="" class="rounded-full h-20 w-20">
                </div>
                <h3 class="text-xl font-futuraBk tracking-tight text-white text-center mb-4">
                    Multilingual Support
                </h3>
                <p class="text-base leading-relaxed text-neutral-200 text-center">
                    Empower employees to work in their preferred language. Currently supports English, more languages coming soon.
                </p>
            </div>
        </div>
    </div>
</section>

        
        


        <!-- =======================
            ***  Reviews Section ***
            ======================== -->
        <section id="review" class="relavtive py-12 ">
            <div class="w-full grid grid-cols-1 xl:max-w-7xl mx-auto py-16 px-6 md:px-16">
                <div class="w-full md:max-w-xl">
                    <h2 class="text-4xl md:text-5xl leading-snug font-futuraBk">Satisfied Customers</h2>
                    <p class="mt-8 mb-4 text-sm md:text-2xl leading-tight font-futuraBk text-textSecondary">
                        The flexible plans are affordable for businesses of all sizes, and the overall value is excellent.
                        Highly
                        recommend this app for any business looking to improve attendance management and increase
                        accountability!
                    </p>
                    <hr class="w-[16%] inline-flex justify-items-center bg-bgHeader p-[1px] mb-2 ">
                    <hr class="size-4 inline-flex rounded-full bg-bgHeader mb-1 animate-bounce">
                </div>

                <!-- slider -->


                {{-- <section class="bg-gray-50">
                    <div class="mx-auto max-w-[1340px] px-4 py-12 sm:px-6 lg:me-0 lg:py-16 lg:pe-0 lg:ps-8 xl:py-24">
                        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:items-center lg:gap-16">
                            <div class="max-w-xl text-center ltr:sm:text-left rtl:sm:text-right">
                                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Testimonial
                                </h2>

                                <p class="mt-4 text-gray-700">
                                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptas veritatis illo
                                    placeat
                                    harum porro optio fugit a culpa sunt id!
                                </p>

                                <div class="hidden lg:mt-8 lg:flex lg:gap-4">
                                    <button aria-label="Previous slide" id="keen-slider-previous-desktop"
                                        class="rounded-full border border-rose-600 p-3 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5 rtl:rotate-180">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 19.5L8.25 12l7.5-7.5" />
                                        </svg>
                                    </button>

                                    <button aria-label="Next slide" id="keen-slider-next-desktop"
                                        class="rounded-full border border-rose-600 p-3 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                                        <svg class="size-5 rtl:rotate-180" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="-mx-6 lg:col-span-2 lg:mx-0">
                                <div id="keen-slider" class="keen-slider">
                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">Bherunath Jewellers
                                                    </p>

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                        Real Victory Groups' attendance management software has been a game-changer for us. Tracking employee attendance and leave has never been this easy. The user-friendly interface and detailed reporting tools have significantly improved our efficiency
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>

                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">DURGA JEWELLERS
                                                    </p>

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                        We were struggling with manual attendance tracking until we discovered this software. Now, everything is automated, and we can monitor attendance and leave requests seamlessly. Highly recommend it to all businesses!
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>

                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">BATHLA HARDWARE

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                        The integration of attendance and leave management in a single platform is what sets this software apart. It's reliable, easy to use, and saves a lot of administrative time. Kudos to Real Victory Groups!
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                            <div class="-mx-6 lg:col-span-2 lg:mx-0">
                                <div id="keen-slider" class="keen-slider">
                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">Bherunath Jewellers
                                                    </p>

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                        Real Victory Groups' attendance management software has been a game-changer for us. Tracking employee attendance and leave has never been this easy. The user-friendly interface and detailed reporting tools have significantly improved our efficiency
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>

                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">DURGA JEWELLERS
                                                    </p>

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                        We were struggling with manual attendance tracking until we discovered this software. Now, everything is automated, and we can monitor attendance and leave requests seamlessly. Highly recommend it to all businesses!
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>

                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">AASHIRVAD JEWELLERS

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                       This attendance management software has reduced errors and streamlined our payroll process. The support team is always helpful and quick to respond. It’s a must-have tool for any organization.
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                            <div class="-mx-6 lg:col-span-2 lg:mx-0">
                                <div id="keen-slider" class="keen-slider">
                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">Bherunath Jewellers
                                                    </p>

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                        Real Victory Groups' attendance management software has been a game-changer for us. Tracking employee attendance and leave has never been this easy. The user-friendly interface and detailed reporting tools have significantly improved our efficiency
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>

                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">DURGA JEWELLERS
                                                    </p>

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                        We were struggling with manual attendance tracking until we discovered this software. Now, everything is automated, and we can monitor attendance and leave requests seamlessly. Highly recommend it to all businesses!
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>

                                    <div class="keen-slider__slide">
                                        <blockquote
                                            class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                            <div>
                                                <div class="flex gap-0.5 text-green-500">
                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>

                                                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>

                                                <div class="mt-4">
                                                    <p class="text-2xl font-bold text-rose-600 sm:text-3xl">SR Construction

                                                    <p class="mt-4 leading-relaxed text-gray-700">
                                                        With this software, we have full control over attendance and leave tracking. The insights we get from the reports have helped us make better decisions about workforce planning.
                                                    </p>
                                                </div>
                                            </div>

                                            <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">
                                                &mdash; Michael Scott
                                            </footer>
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-center gap-4 lg:hidden">
                            <button aria-label="Previous slide" id="keen-slider-previous"
                                class="rounded-full border border-rose-600 p-4 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                                <svg class="size-5 -rotate-180 transform" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" />
                                </svg>
                            </button>

                            <button aria-label="Next slide" id="keen-slider-next"
                                class="rounded-full border border-rose-600 p-4 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </section> --}}



                <section class="bg-gray-50">
                    <div class="mx-auto max-w-[1340px] px-4 py-12 sm:px-6 lg:py-16 xl:py-24">
                      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:items-center lg:gap-16">
                        <!-- Section Header -->
                        <div class="max-w-xl text-center sm:text-left">
                          <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Testimonial</h2>
                          <p class="mt-4 text-gray-700">
                            See what our clients say about Real Victory Groups' attendance management software.
                          </p>
                          <div class="hidden lg:mt-8 lg:flex lg:gap-4">
                            <button aria-label="Previous slide" id="keen-slider-previous-desktop" class="rounded-full border border-rose-600 p-3 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:rotate-180">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                              </svg>
                            </button>
                            <button aria-label="Next slide" id="keen-slider-next-desktop" class="rounded-full border border-rose-600 p-3 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:rotate-180">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5L15.75 12l-7.5 7.5" />
                              </svg>
                            </button>
                          </div>
                        </div>
                  
                        <!-- Testimonials Slider -->
                        <div class="-mx-6 lg:col-span-2 lg:mx-0">
                          <div id="keen-slider" class="keen-slider">
                            <!-- Testimonial 1 -->
                            <div class="keen-slider__slide">
                              <blockquote class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                <div class="mt-4">
                                  <p class="text-2xl font-bold text-rose-600 sm:text-3xl">Bherunath Jewellers</p>
                                  <div><span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                </div>
                                  <p class="mt-4 leading-relaxed text-gray-700">
                                    Real Victory Groups' attendance management software has been a game-changer for us. Tracking employee
                                    attendance and leave has never been this easy. The user-friendly interface and detailed reporting tools
                                    have significantly improved our efficiency.
                                  </p>
                                </div>
                                {{-- <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">&mdash; Michael Scott</footer> --}}
                              </blockquote>
                            </div>
                  
                            <!-- Testimonial 2 -->
                            <div class="keen-slider__slide">
                              <blockquote class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                
                                <div class="mt-4">
                                  <p class="text-2xl font-bold text-rose-600 sm:text-3xl">Durga Jewellers</p>
                                  <div><span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                </div>
                                  <p class="mt-4 leading-relaxed text-gray-700">
                                    We were struggling with manual attendance tracking until we discovered this software. Now, everything is
                                    automated, and we can monitor attendance and leave requests seamlessly. Highly recommend it to all
                                    businesses!
                                  </p>
                                </div>
                                {{-- <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">&mdash; Michael Scott</footer> --}}
                              </blockquote>
                            </div>
                  
                            <!-- Testimonial 3 -->
                            <div class="keen-slider__slide">
                              <blockquote class="flex h-full flex-col justify-between bg-white p-6 shadow-sm sm:p-8 lg:p-12">
                                <div class="mt-4">
                                  <p class="text-2xl font-bold text-rose-600 sm:text-3xl">Bathla Hardware</p>
                                  <div><span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                    <span style="color: #7DE88D;">★</span>
                                </div>
                                  <p class="mt-4 leading-relaxed text-gray-700">
                                    The integration of attendance and leave management in a single platform is what sets this software
                                    apart. It's reliable, easy to use, and saves a lot of administrative time. Kudos to Real Victory Groups!
                                  </p>
                                </div>
                                {{-- <footer class="mt-4 text-sm font-medium text-gray-700 sm:mt-6">&mdash; Michael Scott</footer> --}}
                              </blockquote>
                            </div>
                          </div>
                        </div>
                      </div>
                  
                      <!-- Mobile Navigation Buttons -->
                      <div class="mt-8 flex justify-center gap-4 lg:hidden">
                        <button aria-label="Previous slide" id="keen-slider-previous" class="rounded-full border border-rose-600 p-4 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                          <svg class="w-5 h-5 -rotate-180 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                          </svg>
                        </button>
                        <button aria-label="Next slide" id="keen-slider-next" class="rounded-full border border-rose-600 p-4 text-rose-600 transition hover:bg-rose-600 hover:text-white">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </section>
                  


            </div>
        </section>

        <!-- shape 1 -->
        {{-- <div class="custom-shape-divider-top-1732633376 bg-red-500">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                preserveAspectRatio="none">
                <path
                    d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
                    opacity=".25" class="shape-fill"></path>
                <path
                    d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                    opacity=".5" class="shape-fill"></path>
                <path
                    d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
                    class="shape-fill"></path>
            </svg>
        </div> --}}


        <!-- =======================
            ***  Price Section ***
            ======================== -->
        <section id="price" class="bg-gradient-to-t from-black to-red-500 -py-24 ">
            <div class="justify-items-center w-full xl:max-w-7xl mx-auto py-16 px-6 md:px-16 ">
                <div class="mb-24 w-full md:max-w-xl mx-auto text-center">
                    <h2 class="text-4xl md:text-5xl leading-snug font-futuraBk">Pricing Plans</h2>
                    <p class="mt-8 mb-4 text-sm md:text-2xl leading-tight font-futuraBk text-textSecondary">
                        Employee Attendance and Salary Management </p>
                    <hr class="w-[16%] inline-flex justify-items-center bg-bgSecondary p-[1px] mb-2 ">
                    <hr class="size-4 inline-flex rounded-full bg-bgSecondary mb-1 animate-bounce">
                </div>
                <div
                    class=" w-full max-w-full mx-auto block mt-8 md:mt-0 md:grid grid-cols-1 md:grid-cols-2 lg:flex gap-x-6 place-items-center last:row-span-2 *:shadow-lg">

                    <!-- car1 -->
                    {{-- <div
                        class="box-1 bg-white w-full max-w-[375px] h-max mx-auto px-4 py-10 border-[1px] border-red-800 shadow-md shadow-red-500 font-futuraMd text-gray-500 rounded-2xl">
                        <div class="text-center">
                            <h4 class="text-xl text-gray-900 font-futuraBk font-semibold tracking-[.4rem]">SILVER</h4>
                            <p class="text-paraTextmd font-futuraMd py-3 tracking-wide">PACK</p>
                            <div class="bg-red-500">
                                <h3 class="text-2xl text-white mt-1 font-futuraBk font-semibold tracking-wide">IND.79/user
                                </h3>
                                <h5 class="text-lg text-paraTextmd text-white">+18% GST</h5>
                            </div>
                        </div>

                        <div class="py-8">
                            <!-- List of features -->
                            <ul class="list-none flex flex-col items-start">
                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">1 to 10 Users</span>
                                </li>
                                <h3 class="px-8">Including 2 Offices</h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">3 Month plan</span>
                                </li>
                                <h3 class="px-8">Same as per given price</h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">6 Month plan</span>
                                </li>
                                <h3 class="px-8">1 month</h3>
                                <h3 class="px-8">Subscription <span class="font-bold">FREE</span></h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">1 Year plan</span>
                                </li>
                                <h3 class="px-8">5999+18% GST</h3>
                            </ul>
                        </div>

                        <div class="text-center mt-0 lg:mt-16">
                            <a href="{{ route('reqDemo') }}"
                                class="font-futuraMd font-normal tracking-wide border-2 border-bgPrimary bg-white hover:bg-bgPrimary hover:text-white duration-150 ease-linear px-8 py-2 rounded-full">
                                Request A Demo
                            </a>
                        </div>

                    </div> --}}

                    <div class="box-1 bg-white w-full max-w-[375px] h-max mx-auto px-4 py-10 border-[1px] border-red-800 shadow-lg shadow-red-500 font-futuraMd text-gray-500 rounded-2xl">
                        <div class="text-center">
                            <h4 class="text-xl text-gray-900 font-futuraBk font-semibold tracking-[.4rem]">SILVER</h4>
                            <p class="text-paraTextmd font-futuraMd py-3 tracking-wide">PACK</p>
                            <div class="bg-red-500">
                                <h3 class="text-2xl text-white mt-1 font-futuraBk font-semibold tracking-wide">IND.79/user
                                </h3>
                                <h5 class="text-lg text-paraTextmd text-white">+18% GST</h5>
                            </div>
                        </div>
                    
                        <div class="py-8">
                            <!-- List of features -->
                            <ul class="list-none flex flex-col items-start">
                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">1 to 10 Users</span>
                                </li>
                                <h3 class="px-8">Including 2 Offices</h3>
                    
                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">3 Month plan</span>
                                </li>
                                <h3 class="px-8">Same as per given price</h3>
                    
                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">6 Month plan</span>
                                </li>
                                <h3 class="px-8">1 month</h3>
                                <h3 class="px-8">Subscription <span class="font-bold">FREE</span></h3>
                    
                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">1 Year plan</span>
                                </li>
                                <h3 class="px-8">5999+18% GST</h3>
                            </ul>
                        </div>
                    
                        <div class="text-center mt-0 lg:mt-16">
                            <a href="{{ route('reqDemo') }}"
                                class="font-futuraMd font-normal tracking-wide border-2 border-bgPrimary bg-white hover:bg-bgPrimary hover:text-white duration-150 ease-linear px-8 py-2 rounded-full">
                                Request A Demo
                            </a>
                        </div>
                    
                    </div>
                    


                    <!-- card2 -->
                    <div
                        class="box-1 bg-white w-full max-w-[375px] h-max mx-auto px-4 py-10 border-[1px] border-red-800 shadow-lg shadow-red-500  font-futuraMd text-gray-500 rounded-2xl">
                        <div class="text-center">
                            <h4 class="text-xl text-gray-900 font-futuraBk font-semibold tracking-[.4rem]">GOLDEN</h4>
                            <p class="text-paraTextmd font-futuraMd py-3 tracking-wide">PACK</p>
                            <div class="bg-red-500">
                                <h3 class="text-2xl text-white mt-1 font-futuraBk font-semibold tracking-wide">IND.69/user
                                </h3>
                                <h5 class="text-lg text-paraTextmd text-white">+18% GST</h5>
                            </div>
                        </div>

                        <div class="py-8">
                            <!-- List of features -->
                            <ul class="list-none flex flex-col items-start">
                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">11 to 20 Users</span>
                                </li>
                                <h3 class="px-8">Including 4 Offices</h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">3 Month plan</span>
                                </li>
                                <h3 class="px-8">Same as per given price</h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">6 Month plan</span>
                                </li>
                                <h3 class="px-8">1 month</h3>
                                <h3 class="px-8">Subscription <span class="font-bold">FREE</span></h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">1 Year plan</span>
                                </li>
                                <h3 class="px-8">7999+18% GST</h3>
                            </ul>
                        </div>
                        <div class="text-center mt-0 lg:mt-16">
                            <a href="{{ route('reqDemo') }}"
                                class="font-futuraMd font-normal tracking-wide border-2 border-bgPrimary bg-white hover:bg-bgPrimary hover:text-white duration-150 ease-linear px-8 py-2 rounded-full">
                                Request A Demo
                            </a>
                        </div>

                    </div>

                    <!-- card3 -->
                    <div
                        class="box-1 bg-white w-full max-w-[375px] h-max mx-auto px-4 py-10 border-[1px] border-red-800 shadow-lg shadow-red-500  font-futuraMd text-gray-500 rounded-2xl">
                        <div class="text-center">
                            <h4 class="text-xl text-gray-900 font-futuraBk font-semibold tracking-[.4rem]">DIAMOND</h4>
                            <p class="text-paraTextmd font-futuraMd py-3 tracking-wide">PACK</p>
                            <div class="bg-red-500">
                                <h3 class="text-2xl text-white mt-1 font-futuraBk font-semibold tracking-wide">IND.59/user
                                </h3>
                                <h5 class="text-lg text-paraTextmd text-white">+18% GST</h5>
                            </div>
                        </div>

                        <div class="py-8">
                            <!-- List of features -->
                            <ul class="list-none flex flex-col items-start">
                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">21 to 50 Users</span>
                                </li>
                                <h3 class="px-8">Including 2 Offices</h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">3 Month plan</span>
                                </li>
                                <h3 class="px-8">Same as per given price</h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">6 Month plan</span>
                                </li>
                                <h3 class="px-8">1 month</h3>
                                <h3 class="px-8">Subscription <span class="font-bold">FREE</span></h3>

                                <li class="flex items-center py-2">
                                    <img class="object-contain aspect-square mr-2 size-5" src="{{ asset('mainasset/img/mainCheck2.png') }}"
                                        alt="checkmark">
                                    <span class="text-base tracking-tight text-neutral-700">1 Year plan</span>
                                </li>
                                <h3 class="px-8">9999+18% GST</h3>
                            </ul>
                        </div>

                        <div class="text-center mt-0 lg:mt-16">
                            <a href="{{ route('reqDemo') }}"
                                class="font-futuraMd font-normal tracking-wide border-2 border-bgPrimary bg-white hover:bg-bgPrimary hover:text-white duration-150 ease-linear px-8 py-2 rounded-full">
                                Request A Demo
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </section>


        <!-- shape 2  -->
    
        {{-- <div class="custom-shape-divider-bottom-1732633459 bg-gradient-to-r from-[#B33333] to-[#591919]">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                preserveAspectRatio="none">
                <path
                    d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"
                    class="shape-fill"></path>
            </svg>
        </div> --}}
        


    </div>
@endsection
