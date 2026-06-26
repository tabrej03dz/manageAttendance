@extends('mainpage.components.main')

@section('content')
    <div class="min-h-screen overflow-hidden bg-white text-slate-900">

        {{-- ================= HERO ================= --}}
        <section class="relative overflow-hidden bg-white">
            {{-- Background Glow --}}
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-40 -right-40 h-[34rem] w-[34rem] rounded-full bg-[#e00063]/20 blur-3xl"></div>
                <div class="absolute top-32 -left-40 h-[30rem] w-[30rem] rounded-full bg-[#1f7df2]/20 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/2 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-[#7f35b2]/20 blur-3xl"></div>
            </div>

            <div class="relative mx-auto grid min-h-[92vh] max-w-7xl grid-cols-1 items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-28">

                {{-- Left Content --}}
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#e00063]/20 bg-[#e00063]/10 px-4 py-2 text-sm font-black text-[#e00063]">
                        <span class="h-2 w-2 rounded-full bg-[#e00063]"></span>
                        Smart HRMS & Attendance Software
                    </div>

                    <h1 class="mt-7 text-4xl font-black leading-tight tracking-tight text-[#2b214a] sm:text-5xl lg:text-7xl">
                        Attendance Management
                        <span class="block bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] bg-clip-text text-transparent">
                            Made Smarter.
                        </span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600 lg:mx-0">
                        Bid farewell to manual excel sheets. RVG HRMS helps you manage employee attendance,
                        selfie check-in, geofencing, leave, salary, payslips and reports from one powerful platform.
                    </p>

                    <div class="mt-9 flex flex-col items-center gap-4 sm:flex-row lg:justify-start">
                        <a href="{{ route('reqDemo') }}"
                           class="group inline-flex items-center justify-center rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] px-8 py-4 text-sm font-black uppercase tracking-wide text-white shadow-2xl shadow-[#e00063]/25 transition hover:-translate-y-1">
                            Request For Demo
                            <span class="ml-2 transition group-hover:translate-x-1">→</span>
                        </a>

                        <a href="#benefit"
                           class="inline-flex items-center justify-center rounded-full border border-[#2b214a]/15 bg-white px-8 py-4 text-sm font-black uppercase tracking-wide text-[#2b214a] shadow-lg transition hover:-translate-y-1 hover:border-[#e00063]/30 hover:text-[#e00063]">
                            Explore Features
                        </a>
                    </div>

                    <div class="mt-10 grid grid-cols-3 gap-4 max-w-xl mx-auto lg:mx-0">
                        <div class="rounded-3xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                            <h3 class="text-2xl font-black text-[#2b214a]">GPS</h3>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Tracking</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                            <h3 class="text-2xl font-black text-[#2b214a]">Face</h3>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Attendance</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white/80 p-4 shadow-sm">
                            <h3 class="text-2xl font-black text-[#2b214a]">HRMS</h3>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Payroll</p>
                        </div>
                    </div>
                </div>

                {{-- Right Visual --}}
                <div class="relative">
                    <div class="absolute inset-0 rounded-[3rem] bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] opacity-20 blur-2xl"></div>

                    <div class="relative overflow-hidden rounded-[3rem] border border-white/60 bg-white p-5 shadow-2xl">
                        <div class="rounded-[2.3rem] bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] p-1">
                            <div class="rounded-[2rem] bg-white p-6 sm:p-8">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-black uppercase tracking-[.25em] text-[#e00063]">RVG HRMS</p>
                                        <h2 class="mt-2 text-2xl font-black text-[#2b214a]">Today Overview</h2>
                                    </div>
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#1f7df2] to-[#e00063] text-2xl text-white shadow-lg">
                                        ✓
                                    </div>
                                </div>

                                <div class="mt-8 grid grid-cols-2 gap-4">
                                    <div class="rounded-3xl bg-[#2b214a] p-5 text-white">
                                        <p class="text-sm text-white/70">Present</p>
                                        <h3 class="mt-2 text-4xl font-black">86</h3>
                                    </div>
                                    <div class="rounded-3xl bg-[#e00063]/10 p-5">
                                        <p class="text-sm text-slate-500">On Leave</p>
                                        <h3 class="mt-2 text-4xl font-black text-[#e00063]">08</h3>
                                    </div>
                                    <div class="rounded-3xl bg-[#1f7df2]/10 p-5">
                                        <p class="text-sm text-slate-500">Check-ins</p>
                                        <h3 class="mt-2 text-4xl font-black text-[#1f7df2]">94</h3>
                                    </div>
                                    <div class="rounded-3xl bg-[#7f35b2]/10 p-5">
                                        <p class="text-sm text-slate-500">Reports</p>
                                        <h3 class="mt-2 text-4xl font-black text-[#7f35b2]">24</h3>
                                    </div>
                                </div>

                                <div class="mt-6 rounded-3xl border border-slate-100 bg-slate-50 p-5">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-bold text-slate-500">Live Location Accuracy</p>
                                            <h4 class="mt-1 text-xl font-black text-[#2b214a]">Geofencing Active</h4>
                                        </div>
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-[#1f7df2] to-[#e00063]"></div>
                                    </div>
                                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-white">
                                        <div class="h-full w-[82%] rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063]"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -bottom-8 -left-8 hidden rounded-3xl bg-white p-5 shadow-2xl md:block">
                        <p class="text-sm font-bold text-slate-500">Selfie Attendance</p>
                        <h3 class="mt-1 text-2xl font-black text-[#2b214a]">Secure Check-in</h3>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= ABOUT ================= --}}
        <section id="attendNow" class="relative overflow-hidden bg-slate-50 py-20 lg:py-28">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute right-0 top-0 h-80 w-80 rounded-full bg-[#e00063]/10 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-[#1f7df2]/10 blur-3xl"></div>
            </div>

            <div class="relative mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="relative order-2 lg:order-1">
                    <div class="absolute inset-0 rounded-[3rem] bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] opacity-20 blur-2xl"></div>

                    <div class="relative rounded-[3rem] bg-white p-6 shadow-xl">
                        <img class="w-full rounded-[2.3rem] object-contain"
                             src="{{ asset('asset/img/multitask.png') }}"
                             loading="lazy"
                             alt="RVG HRMS Attendance Software">
                    </div>
                </div>

                <div class="order-1 lg:order-2">
                    <p class="font-black uppercase tracking-[.3em] text-[#e00063]">About RVG HRMS</p>

                    <h2 class="mt-4 text-4xl font-black leading-tight text-[#2b214a] sm:text-5xl">
                        Streamlining Employee Attendance With
                        <span class="bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] bg-clip-text text-transparent">
                            Location-Based Insights.
                        </span>
                    </h2>

                    <p class="mt-6 text-lg leading-8 text-slate-600">
                        Using GPS technology, employers can track employees in real-time, identify their location,
                        monitor movements, and log arrival and departure times from anywhere. RVG HRMS also supports
                        automatic attendance, payroll, leave and reports even in limited network areas.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1f7df2]/10 text-[#1f7df2]">
                                📍
                            </div>
                            <h3 class="text-lg font-black text-[#2b214a]">Live GPS Tracking</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Track employee check-in and check-out location accurately.</p>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#e00063]/10 text-[#e00063]">
                                🧾
                            </div>
                            <h3 class="text-lg font-black text-[#2b214a]">Payroll Ready</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Generate salary, advance and payslip reports easily.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= BENEFITS ================= --}}
        <section id="benefit" class="relative overflow-hidden bg-[#2b214a] py-20 lg:py-28">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,.06)_1px,transparent_1px)] bg-[size:40px_40px]"></div>
                <div class="absolute -top-40 right-20 h-96 w-96 rounded-full bg-[#e00063]/30 blur-3xl"></div>
                <div class="absolute bottom-0 left-10 h-96 w-96 rounded-full bg-[#1f7df2]/30 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="font-black uppercase tracking-[.3em] text-[#ff4fa0]">Features</p>
                    <h2 class="mt-4 text-4xl font-black leading-tight text-white sm:text-5xl">
                        How RVG HRMS Can Transform Your Business
                    </h2>
                    <p class="mt-6 text-lg leading-8 text-white/70">
                        Streamline workforce management and enhance efficiency with smart attendance, HR and payroll solutions.
                    </p>
                </div>

                @php
                    $features = [
                        [
                            'img' => 'asset/img/banner.jpg',
                            'title' => 'Attendance with Smartphone',
                            'desc' => 'Give flexibility to your employees to mark attendance and take short breaks from their smartphone device.',
                        ],
                        [
                            'img' => 'asset/img/service2.jpeg',
                            'title' => 'Geofencing',
                            'desc' => 'Get accurate records of employee working hours with check-in and check-out location tracking.',
                        ],
                        [
                            'img' => 'asset/img/service3.png',
                            'title' => 'Selfie Attendance',
                            'desc' => 'Secure attendance tracking with selfies. Eliminate buddy punching and add an extra layer of security.',
                        ],
                        [
                            'img' => 'asset/img/service4.jpeg',
                            'title' => 'Leave Management',
                            'desc' => 'Approval-based leave management system that helps HR efficiently manage absences and planning.',
                        ],
                        [
                            'img' => 'asset/img/service5.jpeg',
                            'title' => 'Tour Bill Management',
                            'desc' => 'Sales teams can upload tickets, bills and travel expense proofs directly from mobile phones.',
                        ],
                        [
                            'img' => 'asset/img/service6.jpeg',
                            'title' => 'Salary & Advance Calculation',
                            'desc' => 'Quickly calculate salaries, hourly wages, daily wages, advances and deductions accurately.',
                        ],
                        [
                            'img' => 'asset/img/service7.jpeg',
                            'title' => 'Payslip Generation',
                            'desc' => 'Automate payslip generation and reduce manual HR administrative workload.',
                        ],
                        [
                            'img' => 'asset/img/service8.jpeg',
                            'title' => 'Employees Data',
                            'desc' => 'Manage employee records like Aadhaar, PAN, address, documents and office details.',
                        ],
                        [
                            'img' => 'asset/img/service9.png',
                            'title' => 'Multilingual Support',
                            'desc' => 'Empower employees to use the platform in their preferred language.',
                        ],
                    ];
                @endphp

                <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($features as $feature)
                        <div class="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 p-6 shadow-2xl backdrop-blur transition duration-300 hover:-translate-y-2 hover:bg-white">
                            <div class="absolute -right-16 -top-16 h-36 w-36 rounded-full bg-[#e00063]/30 blur-2xl transition group-hover:bg-[#1f7df2]/20"></div>

                            <div class="relative">
                                <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-3xl bg-white p-2 shadow-xl">
                                    <img src="{{ asset($feature['img']) }}"
                                         alt="{{ $feature['title'] }}"
                                         class="h-full w-full rounded-2xl object-cover">
                                </div>

                                <h3 class="text-xl font-black text-white transition group-hover:text-[#2b214a]">
                                    {{ $feature['title'] }}
                                </h3>

                                <p class="mt-4 text-sm leading-7 text-white/70 transition group-hover:text-slate-600">
                                    {{ $feature['desc'] }}
                                </p>

                                <div class="mt-6 h-1 w-16 rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063]"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================= TESTIMONIALS ================= --}}
        <section id="review" class="relative overflow-hidden bg-white py-20 lg:py-28">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute left-0 top-0 h-96 w-96 rounded-full bg-[#1f7df2]/10 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-[#e00063]/10 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-3 lg:items-center">
                    <div>
                        <p class="font-black uppercase tracking-[.3em] text-[#e00063]">Testimonials</p>
                        <h2 class="mt-4 text-4xl font-black leading-tight text-[#2b214a] sm:text-5xl">
                            Satisfied Customers
                        </h2>
                        <p class="mt-6 text-lg leading-8 text-slate-600">
                            Businesses trust RVG HRMS to improve attendance management, reporting and employee accountability.
                        </p>

                        <div class="mt-8 hidden gap-4 lg:flex">
                            <button aria-label="Previous slide"
                                    id="keen-slider-previous-desktop"
                                    class="rounded-full border border-[#e00063]/30 bg-white p-4 text-[#e00063] shadow-lg transition hover:bg-[#e00063] hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                                </svg>
                            </button>

                            <button aria-label="Next slide"
                                    id="keen-slider-next-desktop"
                                    class="rounded-full border border-[#e00063]/30 bg-white p-4 text-[#e00063] shadow-lg transition hover:bg-[#e00063] hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5L15.75 12l-7.5 7.5"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="-mx-4 lg:col-span-2 lg:mx-0">
                        <div id="keen-slider" class="keen-slider">
                            @php
                                $testimonials = [
                                    [
                                        'name' => 'Bherunath Jewellers',
                                        'text' => "RVG attendance management software has been a game-changer for us. Tracking employee attendance and leave has never been this easy.",
                                    ],
                                    [
                                        'name' => 'Durga Jewellers',
                                        'text' => "We were struggling with manual attendance tracking. Now everything is automated and we can monitor attendance and leave requests seamlessly.",
                                    ],
                                    [
                                        'name' => 'Bathla Hardware',
                                        'text' => "The integration of attendance and leave management in a single platform is reliable, easy to use and saves a lot of administrative time.",
                                    ],
                                ];
                            @endphp

                            @foreach ($testimonials as $item)
                                <div class="keen-slider__slide px-4">
                                    <blockquote class="flex h-full flex-col justify-between rounded-[2rem] border border-slate-100 bg-white p-8 shadow-xl lg:p-10">
                                        <div>
                                            <div class="mb-6 flex gap-1 text-xl text-[#e00063]">
                                                ★ ★ ★ ★ ★
                                            </div>

                                            <p class="text-2xl font-black text-[#2b214a] sm:text-3xl">
                                                {{ $item['name'] }}
                                            </p>

                                            <p class="mt-5 text-base leading-8 text-slate-600">
                                                {{ $item['text'] }}
                                            </p>
                                        </div>

                                        <div class="mt-8 h-1 w-20 rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063]"></div>
                                    </blockquote>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-center gap-4 lg:hidden">
                            <button aria-label="Previous slide"
                                    id="keen-slider-previous"
                                    class="rounded-full border border-[#e00063]/30 bg-white p-4 text-[#e00063] shadow-lg transition hover:bg-[#e00063] hover:text-white">
                                ←
                            </button>

                            <button aria-label="Next slide"
                                    id="keen-slider-next"
                                    class="rounded-full border border-[#e00063]/30 bg-white p-4 text-[#e00063] shadow-lg transition hover:bg-[#e00063] hover:text-white">
                                →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= PRICING ================= --}}
        <section id="price" class="relative overflow-hidden bg-slate-50 py-20 lg:py-28">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-32 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-[#e00063]/10 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="font-black uppercase tracking-[.3em] text-[#e00063]">Pricing</p>
                    <h2 class="mt-4 text-4xl font-black leading-tight text-[#2b214a] sm:text-5xl">
                        Simple Pricing Plans
                    </h2>
                    <p class="mt-6 text-lg leading-8 text-slate-600">
                        Employee Attendance and Salary Management plans for growing businesses.
                    </p>
                </div>

                @php
                    $plans = [
                        [
                            'name' => 'Silver',
                            'price' => '79',
                            'users' => '1 to 10 Users',
                            'office' => 'Including 2 Offices',
                            'year' => '5999 + 18% GST',
                            'popular' => false,
                        ],
                        [
                            'name' => 'Golden',
                            'price' => '69',
                            'users' => '11 to 20 Users',
                            'office' => 'Including 4 Offices',
                            'year' => '7999 + 18% GST',
                            'popular' => true,
                        ],
                        [
                            'name' => 'Diamond',
                            'price' => '59',
                            'users' => '21 to 50 Users',
                            'office' => 'Including 2 Offices',
                            'year' => '9999 + 18% GST',
                            'popular' => false,
                        ],
                    ];
                @endphp

                <div class="mt-14 grid grid-cols-1 gap-8 lg:grid-cols-3">
                    @foreach ($plans as $plan)
                        <div class="relative overflow-hidden rounded-[2.3rem] border {{ $plan['popular'] ? 'border-[#e00063]' : 'border-slate-200' }} bg-white p-7 shadow-xl transition hover:-translate-y-2">
                            @if ($plan['popular'])
                                <div class="absolute right-6 top-6 rounded-full bg-[#e00063] px-4 py-2 text-xs font-black uppercase tracking-wide text-white">
                                    Popular
                                </div>
                            @endif

                            <div class="rounded-[1.8rem] bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] p-6 text-white">
                                <p class="text-sm font-black uppercase tracking-[.35em]">{{ $plan['name'] }}</p>
                                <h3 class="mt-5 text-5xl font-black">
                                    ₹{{ $plan['price'] }}
                                    <span class="text-base font-bold text-white/80">/ user</span>
                                </h3>
                                <p class="mt-2 text-sm font-bold text-white/80">+ 18% GST</p>
                            </div>

                            <div class="mt-8 space-y-5">
                                <div class="flex gap-3">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#e00063]/10 text-sm font-black text-[#e00063]">✓</span>
                                    <div>
                                        <p class="font-black text-[#2b214a]">{{ $plan['users'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $plan['office'] }}</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#e00063]/10 text-sm font-black text-[#e00063]">✓</span>
                                    <div>
                                        <p class="font-black text-[#2b214a]">3 Month Plan</p>
                                        <p class="mt-1 text-sm text-slate-500">Same as per given price</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#e00063]/10 text-sm font-black text-[#e00063]">✓</span>
                                    <div>
                                        <p class="font-black text-[#2b214a]">6 Month Plan</p>
                                        <p class="mt-1 text-sm text-slate-500">1 month subscription free</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#e00063]/10 text-sm font-black text-[#e00063]">✓</span>
                                    <div>
                                        <p class="font-black text-[#2b214a]">1 Year Plan</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $plan['year'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('reqDemo') }}"
                               class="mt-9 inline-flex w-full items-center justify-center rounded-full {{ $plan['popular'] ? 'bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] text-white' : 'border border-[#2b214a]/15 bg-white text-[#2b214a]' }} px-6 py-4 text-sm font-black uppercase tracking-wide shadow-lg transition hover:-translate-y-1">
                                Request A Demo
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    </div>
@endsection