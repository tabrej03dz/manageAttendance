<!-- =======================
*** Footer Section ***
======================== -->
<footer class="relative overflow-hidden bg-[#2b214a] text-white">

    {{-- Background Effects --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-[#1f7df2]/30 blur-3xl"></div>
        <div class="absolute right-0 top-20 h-96 w-96 rounded-full bg-[#e00063]/25 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-[#7f35b2]/30 blur-3xl"></div>

        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,.06)_1px,transparent_1px)] bg-[size:42px_42px] opacity-40"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">

        {{-- Top Footer --}}
        <div class="grid gap-10 lg:grid-cols-12">

            {{-- Brand Info --}}
            <div class="lg:col-span-4">
                <a href="{{ route('mainpage') }}" class="inline-flex items-center gap-4">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] opacity-40 blur-xl"></div>

                        <div class="relative flex h-20 w-20 items-center justify-center rounded-3xl bg-white shadow-2xl">
                            <img src="{{ asset('asset/img/RVG HRMS COLOUR ICON.png') }}"
                                 alt="Real Victory Groups"
                                 class="h-16 w-16 object-contain">
                        </div>
                    </div>

                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-white">
                            Real Victory Groups
                        </h2>
                        <p class="mt-1 text-xs font-black uppercase tracking-[.25em] text-[#ff4fa0]">
                            RVG HRMS
                        </p>
                    </div>
                </a>

                <p class="mt-6 max-w-md text-base leading-8 text-white/70">
                    Smart attendance, HRMS, salary management, leave tracking and employee productivity solution for modern businesses.
                </p>

                <div class="mt-6 rounded-[2rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-sm font-black uppercase tracking-[.25em] text-[#ff4fa0]">
                        Office Address
                    </p>

                    <p class="mt-3 text-sm leading-7 text-white/75">
                        73 Basement, Ekta Enclave Society, Lakhanpur, Khyora,
                        Kanpur, Uttar Pradesh 208024
                    </p>
                </div>

                {{-- Social Icons --}}
                <div class="mt-7 flex items-center gap-3">
                    <a href="https://www.facebook.com/realvictorygroups/"
                       target="_blank"
                       class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-xl text-white transition hover:-translate-y-1 hover:bg-[#e00063]">
                        <i class="fa-brands fa-square-facebook"></i>
                    </a>

                    <a href="https://www.linkedin.com/company/realvictorygroups/?originalSubdomain=in"
                       target="_blank"
                       class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-xl text-white transition hover:-translate-y-1 hover:bg-[#1f7df2]">
                        <i class="fa-brands fa-linkedin"></i>
                    </a>

                    <a href="https://www.instagram.com/realvictorygroups/"
                       target="_blank"
                       class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-xl text-white transition hover:-translate-y-1 hover:bg-gradient-to-br hover:from-[#1f7df2] hover:via-[#7f35b2] hover:to-[#e00063]">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="lg:col-span-2">
                <h3 class="text-lg font-black text-white">
                    Quick Links
                </h3>

                <div class="mt-4 h-1 w-14 rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063]"></div>

                <ul class="mt-7 space-y-4 text-sm font-bold text-white/70">
                    <li>
                        <a href="{{ route('mainpage') }}"
                           class="group inline-flex items-center gap-2 transition hover:text-[#ff4fa0]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#ff4fa0]"></span>
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="#attendNow"
                           class="group inline-flex items-center gap-2 transition hover:text-[#ff4fa0]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#ff4fa0]"></span>
                            Why RVG
                        </a>
                    </li>

                    <li>
                        <a href="#benefit"
                           class="group inline-flex items-center gap-2 transition hover:text-[#ff4fa0]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#ff4fa0]"></span>
                            Benefits
                        </a>
                    </li>

                    <li>
                        <a href="#review"
                           class="group inline-flex items-center gap-2 transition hover:text-[#ff4fa0]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#ff4fa0]"></span>
                            Reviews
                        </a>
                    </li>

                    <li>
                        <a href="#price"
                           class="group inline-flex items-center gap-2 transition hover:text-[#ff4fa0]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#ff4fa0]"></span>
                            Pricing
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Legal Links --}}
            <div class="lg:col-span-3">
                <h3 class="text-lg font-black text-white">
                    Policies
                </h3>

                <div class="mt-4 h-1 w-14 rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063]"></div>

                <ul class="mt-7 space-y-4 text-sm font-bold text-white/70">
                    <li>
                        <a href="{{ route('privacy-policy') }}"
                           class="inline-flex items-center gap-2 transition hover:text-[#ff4fa0]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#ff4fa0]"></span>
                            Privacy Policy
                        </a>
                    </li>

                    <li>
                        <a href="https://realvictorygroups.com/terms-conditions/"
                           class="inline-flex items-center gap-2 transition hover:text-[#ff4fa0]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#ff4fa0]"></span>
                            Terms & Conditions
                        </a>
                    </li>

                    <li>
                        <a href="https://realvictorygroups.com/cancellation-refund-policy/"
                           class="inline-flex items-center gap-2 transition hover:text-[#ff4fa0]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#ff4fa0]"></span>
                            Refund Policy
                        </a>
                    </li>
                </ul>


                {{-- App Download Links --}}
                <div class="mt-6">
                    <p class="text-sm font-black uppercase tracking-[.25em] text-[#ff4fa0]">
                        Download App
                    </p>

                    <div class="mt-4 grid gap-3">
                        <a href="https://apps.apple.com/in/app/victory-hrms-attendance-salary/id6759782174"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-4 rounded-2xl bg-black px-5 py-4 text-white shadow-xl transition hover:-translate-y-1 hover:bg-[#111827]">

                            <span class="flex h-11 w-11 items-center justify-center text-3xl">
                                <i class="fa-brands fa-apple"></i>
                            </span>

                            <span class="leading-tight">
                                <span class="block text-xs font-bold text-white/70">
                                    Download on the
                                </span>
                                <span class="block text-xl font-black">
                                    App Store
                                </span>
                            </span>
                        </a>

                        <a href="https://play.google.com/store/apps/details?id=com.realvictorygroup.attendancepro2026"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-4 rounded-2xl bg-black px-5 py-4 text-white shadow-xl transition hover:-translate-y-1 hover:bg-[#111827]">

                            <span class="flex h-11 w-11 items-center justify-center text-3xl">
                                <i class="fa-brands fa-google-play"></i>
                            </span>

                            <span class="leading-tight">
                                <span class="block text-xs font-bold text-white/70">
                                    Get it on
                                </span>
                                <span class="block text-xl font-black">
                                    Google Play
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Contact Box --}}
            <div class="lg:col-span-3">
                <div class="rounded-[2rem] border border-white/10 bg-white/10 p-6 shadow-2xl backdrop-blur">
                    <p class="text-sm font-black uppercase tracking-[.25em] text-[#ff4fa0]">
                        Contact Us
                    </p>

                    <h3 class="mt-4 text-2xl font-black leading-tight text-white">
                        Need HRMS Demo?
                    </h3>

                    <p class="mt-3 text-sm leading-7 text-white/70">
                        Contact our team and get a quick demo for RVG HRMS attendance software.
                    </p>

                    <div class="mt-6 space-y-4">
                        <a href="tel:+917753800444"
                           class="flex items-center gap-3 rounded-2xl bg-white/10 p-4 transition hover:bg-white/15">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#1f7df2]/30 text-lg">
                                ☎
                            </span>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-white/50">Call Now</p>
                                <p class="font-black text-white">+91 7753800444</p>
                            </div>
                        </a>

                        <a href="mailto:realvictorygroups@gmail.com"
                           class="flex items-center gap-3 rounded-2xl bg-white/10 p-4 transition hover:bg-white/15">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#e00063]/30 text-lg">
                                ✉
                            </span>

                            <div class="min-w-0">
                                <p class="text-xs font-bold uppercase tracking-wide text-white/50">Email</p>
                                <p class="truncate font-black text-white">realvictorygroups@gmail.com</p>
                            </div>
                        </a>
                    </div>

                    <a href="{{ route('reqDemo') }}"
                       class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] px-6 py-4 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-[#e00063]/20 transition hover:-translate-y-1">
                        Request Demo
                    </a>
                </div>
            </div>
        </div>

        {{-- Bottom Footer --}}
        <div class="mt-12 border-t border-white/10 pt-6">
            <div class="flex flex-col items-center justify-between gap-4 text-center sm:flex-row">
                <p class="text-sm font-semibold text-white/60">
                    © {{ date('Y') }} by
                    <a href="http://realvictorygroups.com/"
                       class="font-black text-white transition hover:text-[#ff4fa0]">
                        Real Victory Groups
                    </a>
                    . All rights reserved.
                </p>

                <p class="text-sm font-semibold text-white/60">
                    Designed By
                    <a href="https://realvictorygroups.com/">
                    <span class="font-black bg-gradient-to-r from-[#1f7df2] via-[#ff4fa0] to-[#e00063] bg-clip-text text-transparent">
                        Real Victory Grous &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                    </span>
                    </a>
                </p>
            </div>
        </div>
    </div>
</footer>