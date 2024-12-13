<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RVG-Attendence</title>

    {{-- tailwindcss --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- style css  -->
    <link rel="stylesheet" href="{{ asset('mainasset/css/style.css') }}">
    <!-- global css  -->
    <link rel="stylesheet" href="{{ asset('mainasset/css/global.css') }}">

    <!-- fontfamily -->
    <link href="https://db.onlinewebfonts.com/c/7fdb09ff5a96f39768f311d5471d68a9?family=FuturaLig" rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/fd6e6c30c7d355528ba9428eea942445?family=Futura+Book" rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/77d394fa0c1103c648f880b77cb4a32f?family=Futura+LT+W01+Medium"
        rel="stylesheet">

    <!-- Font Awesome CDN link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- slider -->
    <link href="https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css" rel="stylesheet" />


</head>

<body class="font-futura scroll-smooth">

    @include('mainpage.components.header')

    @yield('content')

    @include('mainpage.components.footer')

    <!-- =======================
    Back to home button
    ======================= -->
    <div class="hidden md:block">
        <a class="px-2 py-4 rounded-sm shadow-sm bg-bgPrimary font-futuraBk max-w-max fixed left-0 bottom-3 md:bottom-16 text-sm text-white font-light"
            href="{{ route('mainpage') }}">
            <p class="[writing-mode:vertical-lr]">Back to Home</p>
            <i class="fa-solid fa-house text-bgSecondary mt-4"></i>
        </a>
    </div>



    <!-- Back to Top  -->
    <div class="size-full">
        <a class="size-12 rounded-full ring ring-bgPrimary bg-bgPrimary flex items-center justify-center font-futuraBk fixed right-3 bottom-2 z-[999999] md:bottom-14 text-sm text-bgSecondary font-light"
            href="#">
            <i class="fa-solid fa-angles-up text-textPrimary text-2xl"></i>
            <div
                class="bg-bgPrimary size-12 animate-ping transition duration-300 ease-linear rounded-full flex items-center justify-center fixed right-3 bottom-2 md:bottom-14">
            </div>
        </a>
    </div>


    <script type="module">
        import KeenSlider from 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/+esm'

        const keenSlider = new KeenSlider(
            '#keen-slider', {
                loop: true,
                slides: {
                    origin: 'center',
                    perView: 1.25,
                    spacing: 16,
                },
                breakpoints: {
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
        )

        const keenSliderPrevious = document.getElementById('keen-slider-previous')
        const keenSliderNext = document.getElementById('keen-slider-next')

        const keenSliderPreviousDesktop = document.getElementById('keen-slider-previous-desktop')
        const keenSliderNextDesktop = document.getElementById('keen-slider-next-desktop')

        keenSliderPrevious.addEventListener('click', () => keenSlider.prev())
        keenSliderNext.addEventListener('click', () => keenSlider.next())

        keenSliderPreviousDesktop.addEventListener('click', () => keenSlider.prev())
        keenSliderNextDesktop.addEventListener('click', () => keenSlider.next())
    </script>

    <!-- script  -->
    <script src="{{ asset('mainasset/js/script.js') }}"></script>

</body>

</html>
