 <!-- =======================
    *** Header/Navbar Section ***
    ======================== -->
 <header id="home" class="w-full h-[97px] sticky top-0 left-0 z-[9999999] drop-shadow-lg backdrop-blur-lg bg-white">
     <div
         class="w-full xl:max-w-7xl 2xl:max-w-full mx-auto py-2 px-4 md:px-8 2xl:px-10 flex justify-between items-center">
         <!-- header logo -->
         <div class="logo flex items-center justify-start cursor-pointer">
             <a href="{{ route('mainpage') }}">
                 <div>
                    

                     <a href="{{ route('mainpage') }}"><img src="{{ asset('asset/img/logo (2).png') }}" alt=""
                             class="h-24 w-24">
                     </a>
                 </div>
                 {{-- <div class="text-paraTextxl ml-3">
                     <h2 class="text-bgHeader font-futuraBk text-xl md:text-2xl tracking-wide"><a
                             href="{{ route('mainpage') }}">AttendNow</a></h2>
                 </div> --}}
             </a>
         </div>

         {{-- button --}}
         <div class="block md:hidden w-full flex justify-center"> <a
                 class="Login bg-red-600 rounded-lg p-2 px-4 font-bold text-white text-lg"
                 href="{{ route('login') }}">Login</a></div>

         <!-- main navbar -->
         <nav class="flex items-center justify-center">
             <div class="hidden lg:block">
                 <div class="flex items-center justify-between">
                     <ul
                         class="navigation mr-16 flex font-futuraBk *:2xl:text-xl tracking-tighter text-textSecondary [&_a:hover]:text-bgHeader lg:mr-4 min-[1285px]:mr-16 *:min-[1024px]:text-base *:min-[1040px]:text-paraTextmd *:min-[1070px]:text-lg gap-4 xl:gap-8">
                         <li><a href="{{ route('mainpage') }}" class="text-bgSecondary">Home</a></li>
                         <li><a href="#attendNow">Why RVG</a></li>
                         <li><a href="#benefit">Benefits</a></li>
                         <li><a href="#review">Reviews</a></li>
                         <li><a href="#price">Pricing</a></li>
                         {{-- <li><a href="{{ route('blogs') }}">Blog</a></li> --}}
                         <li><a href="{{ route('reqDemo') }}">Request a Demo</a>
                         </li>
                         <li><a href="{{ route('employee-form') }}">Register as Employee</a>
                         </li>
                         {{-- <li><a href="{{ route('login') }}">Login</a></li> --}}
                     </ul>

                     <div
                         class="w-[152px] h-11 flex justify-center bg-gradient-to-t from-red-500 to-black text-white hover:text-white hover:bg-red-500 rounded-xl p-2 text-lg ">
                         <a href="{{ route('login') }}">Login</a></div>

                 </div>
             </div>
         </nav>

         <!-- mobile menus -->
         <div id="navbarSlide"
             class="w-full h-screen absolute top-24 -left-0 active:left-0 bg-[#ffffffa9] backdrop-blur-xl overflow-hidden hidden">
             <div class="max-w-max h-full bg-white absolute z-50 border-t border-t-bgSecondary border-opacity-50">
                 <div class="size-full grid px-6 gap-y-8">
                     <ul
                         class="navigation w-full h-auto mt-8 grid gap-y-2 items-center font-futuraMd *:px-4 *:py-0 *:text-bgPrimary [&_li]:w-auto [&_a]:pr-9 [&_a]:pl-2 [&_a]:py-0 [&_a:hover]:text-bgSecondary [&_a:hover]:duration-150 [&_a:hover]:ease-in *:cursor-pointer">
                         <li><a class="navLink" href="{{ route('mainpage') }}">Home</a></li>
                         <li><a class="navLink" href="#attendNow">Why RVG</a></li>
                         <li><a class="navLink" href="#benefit">Benefits</a></li>
                         <li><a class="navLink" href="#review">Reviews</a></li>
                         <li><a class="navLink" href="#price">Pricing</a></li>
                         {{-- <li><a class="navLink" href="{{ route('blogs') }}">Blog</a></li> --}}
                         <li><a class="navLink" href="{{ route('reqDemo') }}">Request a Demo</a></li>
                         {{-- <li><a class="navLink" href="{{ route('login') }}">Login</a></li> --}}
                     </ul>

                     <div>

                         <div class="infoText text-bgSecondary mt-16">
                             <h6 class="pb-4 text-lg text-textSecondary font-futuraBk font-medium">Call or Email Us
                             </h6>
                             <a class="text-2xl font-futuraMd font-semibold" href="tel:+917753800444">+91
                                 7753800444</a>
                             <a class="pt-2 block text-xl font-futuraMd font-semibold"
                                 href="#">info@realvictorygroups</a>
                         </div>
                     </div>


                 </div>

             </div>
         </div>

         <!-- *** hamburger *** -->
         <button id="hamburger"
             class="group lg:hidden bg-transparent grid gap-1.5 place-items-end transition duration-300 ease-in *:transition *:duration-500 *:ease-in-out">
             <span class="line line1"></span>
             <span class="line line2"></span>
             <span class="line line3"></span>
         </button>

     </div>
 </header>
