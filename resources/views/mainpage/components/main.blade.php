<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RVG HRMS - Smart Attendance Management</title>
    <link rel="icon" type="image/png" href="{{ asset('asset/img/favicon.png') }}">

    <meta name="description"
          content="RVG HRMS is a smart attendance, HRMS, leave, salary and employee management software by Real Victory Groups.">

    <meta name="theme-color" content="#2b214a">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Tailwind Custom Theme --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        rvgBlue: '#1f7df2',
                        rvgPurple: '#2b214a',
                        rvgViolet: '#7f35b2',
                        rvgPink: '#e00063',
                        rvgPinkLight: '#ff4fa0',
                    },
                    fontFamily: {
                        futura: ['Futura Book', 'sans-serif'],
                        futuraBk: ['Futura Book', 'sans-serif'],
                        futuraMd: ['Futura LT W01 Medium', 'sans-serif'],
                    },
                    boxShadow: {
                        rvg: '0 24px 80px rgba(224, 0, 99, 0.18)',
                    }
                }
            }
        }
    </script>

    {{-- Style CSS --}}
    <link rel="stylesheet" href="{{ asset('mainasset/css/style.css') }}">

    {{-- Global CSS --}}
    <link rel="stylesheet" href="{{ asset('mainasset/css/global.css') }}">

    {{-- Font Family --}}
    <link href="https://db.onlinewebfonts.com/c/7fdb09ff5a96f39768f311d5471d68a9?family=FuturaLig" rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/fd6e6c30c7d355528ba9428eea942445?family=Futura+Book" rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/77d394fa0c1103c648f880b77cb4a32f?family=Futura+LT+W01+Medium" rel="stylesheet">

    {{-- Font Awesome CDN --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />

    {{-- Keen Slider --}}
    <link href="https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css" rel="stylesheet" />

    <style>
        body {
            background: #ffffff;
        }

        ::selection {
            background: #e00063;
            color: #ffffff;
        }

        /* Smooth scrollbar look */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #1f7df2, #7f35b2, #e00063);
            border-radius: 999px;
        }

        .rvg-gradient-text {
            background: linear-gradient(90deg, #1f7df2, #7f35b2, #e00063);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .rvg-gradient-bg {
            background: linear-gradient(90deg, #1f7df2, #7f35b2, #e00063);
        }
    </style>
</head>

<body class="font-futura scroll-smooth bg-white text-slate-900 antialiased">

    {{-- Header --}}
    @include('mainpage.components.header')

    {{-- Page Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('mainpage.components.footer')

    {{-- =======================
    Back to Home Button
    ======================= --}}
    <div class="hidden md:block">
        <a href="{{ route('mainpage') }}"
           class="group fixed left-0 bottom-16 z-[999998] overflow-hidden rounded-r-2xl border border-white/20 bg-[#2b214a] px-3 py-5 text-sm font-bold text-white shadow-2xl transition hover:bg-[#e00063]">

            <span class="absolute inset-0 bg-gradient-to-b from-[#1f7df2]/30 via-[#7f35b2]/30 to-[#e00063]/30 opacity-0 transition group-hover:opacity-100"></span>

            <span class="relative flex flex-col items-center gap-3">
                <span class="[writing-mode:vertical-lr] tracking-wide">
                    Back to Home
                </span>

                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-white">
                    <i class="fa-solid fa-house"></i>
                </span>
            </span>
        </a>
    </div>

    {{-- =======================
    Back to Top Button
    ======================= --}}
    <a href="#"
       aria-label="Back to top"
       class="group fixed right-4 bottom-4 z-[999999] flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] text-white shadow-2xl shadow-[#e00063]/30 transition hover:-translate-y-1 md:bottom-14">

        <span class="absolute inset-0 rounded-full bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] opacity-60 blur-md transition group-hover:blur-lg"></span>

        <span class="absolute inset-0 rounded-full animate-ping bg-[#e00063]/25"></span>

        <i class="fa-solid fa-angles-up relative text-xl"></i>
    </a>

    {{-- Keen Slider Script --}}
    <script type="module">
        import KeenSlider from 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/+esm'

        const sliderElement = document.getElementById('keen-slider');

        if (sliderElement) {
            const keenSlider = new KeenSlider(
                '#keen-slider',
                {
                    loop: true,
                    slides: {
                        origin: 'center',
                        perView: 1.05,
                        spacing: 16,
                    },
                    breakpoints: {
                        '(min-width: 640px)': {
                            slides: {
                                origin: 'center',
                                perView: 1.15,
                                spacing: 20,
                            },
                        },
                        '(min-width: 1024px)': {
                            slides: {
                                origin: 'auto',
                                perView: 1.5,
                                spacing: 32,
                            },
                        },
                    },
                },
                []
            );

            const keenSliderPrevious = document.getElementById('keen-slider-previous');
            const keenSliderNext = document.getElementById('keen-slider-next');

            const keenSliderPreviousDesktop = document.getElementById('keen-slider-previous-desktop');
            const keenSliderNextDesktop = document.getElementById('keen-slider-next-desktop');

            if (keenSliderPrevious) {
                keenSliderPrevious.addEventListener('click', () => keenSlider.prev());
            }

            if (keenSliderNext) {
                keenSliderNext.addEventListener('click', () => keenSlider.next());
            }

            if (keenSliderPreviousDesktop) {
                keenSliderPreviousDesktop.addEventListener('click', () => keenSlider.prev());
            }

            if (keenSliderNextDesktop) {
                keenSliderNextDesktop.addEventListener('click', () => keenSlider.next());
            }
        }
    </script>

    {{-- Main Script --}}
    <script src="{{ asset('mainasset/js/script.js') }}"></script>

</body>

</html>