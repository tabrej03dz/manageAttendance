<!-- =======================
*** Header/Navbar Section ***
======================== -->
<header id="home"
    class="sticky top-0 left-0 z-[9999999] w-full border-b border-slate-200/70 bg-white/85 shadow-lg shadow-slate-900/5 backdrop-blur-2xl">

    <div class="mx-auto flex h-[92px] w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        {{-- LOGO --}}
        <a href="{{ route('mainpage') }}" class="group flex items-center gap-3">
            <div class="relative">
                <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] opacity-20 blur-xl transition group-hover:opacity-40"></div>

                <div class="relative flex h-20 w-20 items-center justify-center rounded-3xl bg-white shadow-lg ring-1 ring-slate-200">
                    <img src="{{ asset('asset/img/RVG HRMS COLOUR ICON.png') }}"
                         alt="RVG HRMS"
                         class="h-16 w-16 object-contain">
                </div>
            </div>

            <div class="hidden sm:block">
                <h1 class="text-xl font-black leading-none tracking-tight text-[#2b214a]">
                    RVG HRMS
                </h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[.22em] text-[#e00063]">
                    Smart Attendance
                </p>
            </div>
        </a>

        {{-- DESKTOP NAVBAR --}}
        <nav class="hidden lg:flex lg:items-center lg:gap-8">
            <ul class="flex items-center gap-2 text-sm font-black text-[#2b214a]">
                <li>
                    <a href="{{ route('mainpage') }}"
                       class="rounded-full px-4 py-2 text-[#e00063] transition hover:bg-[#e00063]/10">
                        Home
                    </a>
                </li>

                <li>
                    <a href="#attendNow"
                       class="rounded-full px-4 py-2 transition hover:bg-[#e00063]/10 hover:text-[#e00063]">
                        Why RVG
                    </a>
                </li>

                <li>
                    <a href="#benefit"
                       class="rounded-full px-4 py-2 transition hover:bg-[#e00063]/10 hover:text-[#e00063]">
                        Benefits
                    </a>
                </li>

                <li>
                    <a href="#review"
                       class="rounded-full px-4 py-2 transition hover:bg-[#e00063]/10 hover:text-[#e00063]">
                        Reviews
                    </a>
                </li>

                <li>
                    <a href="#price"
                       class="rounded-full px-4 py-2 transition hover:bg-[#e00063]/10 hover:text-[#e00063]">
                        Pricing
                    </a>
                </li>

                <li>
                    <a href="{{ route('reqDemo') }}"
                       class="rounded-full px-4 py-2 transition hover:bg-[#e00063]/10 hover:text-[#e00063]">
                        Request Demo
                    </a>
                </li>

                <li>
                    <a href="{{ route('employee-form') }}"
                       class="rounded-full px-4 py-2 transition hover:bg-[#e00063]/10 hover:text-[#e00063]">
                        Employee Register
                    </a>
                </li>
            </ul>

            <a href="{{ route('login') }}"
               class="group inline-flex items-center justify-center rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] px-7 py-3 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-[#e00063]/20 transition hover:-translate-y-1">
                Login
                <span class="ml-2 transition group-hover:translate-x-1">→</span>
            </a>
        </nav>

        {{-- MOBILE ACTIONS --}}
        <div class="flex items-center gap-3 lg:hidden">
            <a href="{{ route('login') }}"
               class="rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-[#e00063]/20">
                Login
            </a>

            <button id="hamburger"
                type="button"
                class="group relative flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-[#e00063]/40"
                aria-label="Open menu">

                <span class="absolute h-[2px] w-6 -translate-y-2 rounded-full bg-[#2b214a] transition duration-300 group-[.active]:translate-y-0 group-[.active]:rotate-45"></span>
                <span class="absolute h-[2px] w-6 rounded-full bg-[#2b214a] transition duration-300 group-[.active]:opacity-0"></span>
                <span class="absolute h-[2px] w-6 translate-y-2 rounded-full bg-[#2b214a] transition duration-300 group-[.active]:translate-y-0 group-[.active]:-rotate-45"></span>
            </button>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div id="navbarSlide"
         class="fixed inset-x-0 top-[92px] z-[9999998] hidden h-[calc(100vh-92px)] bg-slate-950/40 backdrop-blur-sm lg:hidden">

        <div class="h-full w-[86%] max-w-sm overflow-y-auto rounded-r-[2.5rem] bg-white p-6 shadow-2xl">

            <div class="mb-8 flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow ring-1 ring-slate-200">
                    <img src="{{ asset('asset/img/RVG HRMS COLOUR ICON.png') }}"
                         alt="RVG HRMS"
                         class="h-11 w-11 object-contain">
                </div>

                <div>
                    <h2 class="text-lg font-black text-[#2b214a]">RVG HRMS</h2>
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-[#e00063]">
                        Smart Attendance
                    </p>
                </div>
            </div>

            <ul class="space-y-2 text-base font-black text-[#2b214a]">
                <li>
                    <a class="navLink flex items-center justify-between rounded-2xl bg-[#e00063]/10 px-5 py-4 text-[#e00063]"
                       href="{{ route('mainpage') }}">
                        Home <span>→</span>
                    </a>
                </li>

                <li>
                    <a class="navLink flex items-center justify-between rounded-2xl px-5 py-4 transition hover:bg-[#e00063]/10 hover:text-[#e00063]"
                       href="#attendNow">
                        Why RVG <span>→</span>
                    </a>
                </li>

                <li>
                    <a class="navLink flex items-center justify-between rounded-2xl px-5 py-4 transition hover:bg-[#e00063]/10 hover:text-[#e00063]"
                       href="#benefit">
                        Benefits <span>→</span>
                    </a>
                </li>

                <li>
                    <a class="navLink flex items-center justify-between rounded-2xl px-5 py-4 transition hover:bg-[#e00063]/10 hover:text-[#e00063]"
                       href="#review">
                        Reviews <span>→</span>
                    </a>
                </li>

                <li>
                    <a class="navLink flex items-center justify-between rounded-2xl px-5 py-4 transition hover:bg-[#e00063]/10 hover:text-[#e00063]"
                       href="#price">
                        Pricing <span>→</span>
                    </a>
                </li>

                <li>
                    <a class="navLink flex items-center justify-between rounded-2xl px-5 py-4 transition hover:bg-[#e00063]/10 hover:text-[#e00063]"
                       href="{{ route('reqDemo') }}">
                        Request Demo <span>→</span>
                    </a>
                </li>

                <li>
                    <a class="navLink flex items-center justify-between rounded-2xl px-5 py-4 transition hover:bg-[#e00063]/10 hover:text-[#e00063]"
                       href="{{ route('employee-form') }}">
                        Register as Employee <span>→</span>
                    </a>
                </li>
            </ul>

            <div class="mt-8 rounded-[2rem] bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] p-5 text-white shadow-xl">
                <p class="text-sm font-black uppercase tracking-[.25em] text-white/80">
                    Contact Us
                </p>

                <a class="mt-4 block text-2xl font-black"
                   href="tel:+917753800444">
                    +91 7753800444
                </a>

                <a class="mt-2 block text-sm font-bold text-white/85"
                   href="mailto:info@realvictorygroups.com">
                    info@realvictorygroups.com
                </a>

                <a href="{{ route('reqDemo') }}"
                   class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-black uppercase tracking-wide text-[#e00063]">
                    Request Demo
                </a>
            </div>
        </div>
    </div>
</header>

{{-- MOBILE MENU SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hamburger = document.getElementById('hamburger');
        const navbarSlide = document.getElementById('navbarSlide');
        const navLinks = document.querySelectorAll('.navLink');

        if (hamburger && navbarSlide) {
            hamburger.addEventListener('click', function () {
                navbarSlide.classList.toggle('hidden');
                hamburger.classList.toggle('active');
                document.body.classList.toggle('overflow-hidden');
            });

            navLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    navbarSlide.classList.add('hidden');
                    hamburger.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                });
            });

            navbarSlide.addEventListener('click', function (e) {
                if (e.target === navbarSlide) {
                    navbarSlide.classList.add('hidden');
                    hamburger.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        }
    });
</script>