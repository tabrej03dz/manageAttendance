@extends('mainpage.components.main')
@section('content')
    <!-- =======================
            ***  Blog Section ***
            ======================== -->
    <section class="relative shrink-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('mainasset/img/blog/blogBg2.jpg') }}');">
        <div
            class="blogMain grid items-center justify-center xl:max-w-7xl 2xl:max-w-full mx-auto py-16 px-6 md:px-16 2xl:px-28">
            <h2 class="strokText text-white drop-shadow-md text-9xl font-bold font-futuraBk">
                Blogs
            </h2>
        </div>
    </section>


    <!-- =======================
            latest blogs
            ======================= -->
    <section id="attendNow" class="bg-aboutPrice">
        <div class="w-full xl:max-w-7xl mx-auto py-16 px-6 md:px-16 2xl:px-0 grid grid-cols-1">
            <div class="*:text-3xl mb-12 [md:*]:text5xl underline font-bold font-futuraMd">
                <h3>Latest Blogs</h3>
            </div>
            <div class="size-full grid grid-cols-1 xl:grid-cols-2 gap-6 place-items-center">
                <!-- Main blogs  -->
                <div
                    class="max-w-max rounded-md [&_img]:bg-white [&_img]:p-2 [&_img]:shadow-sm [&_img]:rounded-3xl xl:p-3 [&_img]:aspect-video">
                    <img src="{{ asset('mainasset/img/blog/blog-4.jpg') }}" width="" alt="blog image">
                    <div class="flex items-center justify-between py-2 px-4">
                        <div class="logo">
                            <a href="{{ route('mainpage') }}" class="">
                                <div class="flex items-center justify-center place-items-start size-fit">
                                    <svg preserveAspectRatio="xMidYMid meet" data-bbox="0 0 142 85" viewBox="0 0 142 85"
                                        width="40" xmlns="http://www.w3.org/2000/svg" data-type="color" role="img"
                                        aria-label="AttendNow">
                                        <g>
                                            <path fill="#BB2323"
                                                d="M86 42.5C86 65.972 66.748 85 43 85S0 65.972 0 42.5 19.252 0 43 0s43 19.028 43 42.5z"
                                                data-color="1"></path>
                                            <path fill="#DC2626"
                                                d="M142 42.298c0 23.36-18.937 42.298-42.298 42.298-23.36 0-42.298-18.937-42.298-42.298C57.404 18.938 76.341 0 99.702 0 123.062 0 142 18.937 142 42.298z"
                                                data-color="2"></path>
                                            <path fill="#ffffff"
                                                d="M81.404 39.968 95.656 54.22a3.848 3.848 0 1 1-5.442 5.442L75.963 45.41a3.848 3.848 0 1 1 5.442-5.442z"
                                                data-color="3"></path>
                                            <path fill="#ffffff"
                                                d="M124.44 30.855 95.678 59.62a3.848 3.848 0 1 1-5.442-5.442L119 25.413a3.848 3.848 0 1 1 5.442 5.442z"
                                                data-color="3"></path>
                                        </g>
                                    </svg>
                                    <div class="text-paraTextxl ml-2 xl:ml-3">
                                        <a class="text-bgPrimary font-futuraBk text-base md:text-xl tracking-wide"
                                            href="{{ route('mainpage') }}">AttendNow</a>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="relative">
                            <!-- Button to trigger the dropdown -->
                            <button class="relative size-10 hover:bg-gray-200 place-items-center rounded-full"
                                type="button" id="dropdownDefaultButton" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 512" width="5"
                                    aria-hidden="true">
                                    <path
                                        d="M64 360a56 56 0 1 0 0 112 56 56 0 1 0 0-112zm0-160a56 56 0 1 0 0 112 56 56 0 1 0 0-112zM120 96A56 56 0 1 0 8 96a56 56 0 1 0 112 0z" />
                                </svg>
                            </button>

                            <!-- Dropdown content -->
                            <div id="dropdown"
                                class="w-16 h-8 border bg-gray-200 rounded-sm shadow-sm grid place-items-center absolute -left-16 bottom-[.1px] "
                                class="hidden" aria-labelledby="dropdownDefaultButton">
                                <a href="#"><i class="fa-solid fa-share text-universal cursor-pointer"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="p-1 [&_h4]:text-xl [&_h4]:mb-4 [&_h4]:font-semibold px-4">
                        <h4>Streamline Workforce <mark class="bg-bgSecondary text-white p-1">Management</mark> with
                            Digital
                            Rostering:
                            Increased Accuracy,</h4>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate quia alias dolor nesciunt
                            dicta
                            fugiat ratione illo laborum. Architecto voluptatem doloribus iusto distinctio alias quaerat
                            maxime
                            saepe
                            sit fugiat doloremque, libero placeat assumenda aliquam ad dolores error, esse animi quasi eaque
                            obcaecati sunt reprehenderit! Deserunt aut autem consequatur tempora ducimus eligendi velit
                            animi
                            dolore
                            eius, minima, soluta amet, illo officiis.
                            <a href="{{ route('blogDetailsPage') }}" class="text-bgSecondary font-futuraBk">Read More</a>
                        </p>

                        <div class="blogAuthor flex items-center justify-start py-4">
                            <img class="size-12 aspect-square object-cover rounded-full ring-2 ring-bgSecondary drop-shadow-sm"
                                src="{{ asset('mainasset/img/Blog_Profile/people01.png') }}" loading="lazy"
                                alt="profile...">

                            <div class="profileText ml-4 mb-0">
                                <h5 class="font-semibold tracking-tighter m-0">Sunil Shinde</h5>
                                <p>23 Nov, 2024 &nbsp;&nbsp; <em>12:32 PM</em></p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between items-center px-4">
                            <div class="flex font-futuraMd items-center justify-center w-auto">
                                <p class="mr-6">Views 1.5k</p>
                                <p>Comments 200+</p>
                            </div>
                            <button id="like" class="fa-regular fa-heart text-bgSecondary text-2xl"></button>
                        </div>

                        <div class="block w-full h-[1px] bg-universal opacity-30 my-4"></div>

                        <!-- input field  -->
                        <form
                            class="p-[6px] mt-4 mb-6 py-6 [&>div]:grid grid-cols-1 text-start [&>div]:py-2 [&_label]:text-lg [&_label]:py-2  [&_label]:text-headerBgcolor [&_label]:cursor-pointer [&_label]:font-bold"
                            action="backend.php">
                            <div class="[&_label]:w-fit">
                                <label for="textarea">Comment:</label>
                                <textarea
                                    class="border bg-transparent border-gray-200 py-3 px-4 [&_textarea]:text-base rounded-md outline-none caret-bgSecondary focus:outline-bgSecondary"
                                    name="textarea" id="textarea" placeholder="Leave a message" cols="30" rows="6" required></textarea>
                            </div>
                            <div>
                                <button
                                    class="rounded-md px-6 py-3 font-medium bg-bgPrimary text-white hover:bg-bgSecondary duration-150 ease-in">
                                    Send Now
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- aside -->
                <div class="w-full xl:max-w-sm">
                    <h3
                        class="text-xl font-futuraBk text-headerBgcolor mb-3 rounded-full hover:bg-gray-200 w-fit px-6 py-1">
                        <a href="{{ route('blogDetailsPage') }}">All-Blogs</a>
                    </h3>
                    <div class="gap-4 rounded-md [&_img]:rounded-lg xl:p-3 [&_img]:aspect-video">
                        <div
                            class="border max-w-max rounded-md [&_img]:object-cover [&_img]:rounded-lg xl:p-3  [&_img]:aspect-video">
                            <img src="{{ asset('mainasset/img/blog/blog-5.jpg') }}" width="812" alt="blog image">

                            <div class="p-1 [&_h4]:text-lg [&_h4]:font-semibold [&_h4]:py-2">
                                <h4>Latest Technology</h4>
                                <p>
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate quia alias dolor
                                    nesciunt dicta
                                    fugiat ratione illo laborum.
                                    <a href="{{ route('blogDetailsPage') }}" class="text-bgSecondary font-futuraMd">Read
                                        More</a>
                                </p>
                            </div>
                        </div>
                        <div
                            class="border my-4 max-w-max rounded-md [&_img]:object-cover [&_img]:rounded-lg xl:p-3  [&_img]:aspect-video">
                            <img src="{{ asset('mainasset/img/blog/blog-7.jpg') }}" width="812" alt="blog image">

                            <div class="p-1 [&_h4]:text-lg [&_h4]:font-semibold [&_h4]:py-2">
                                <h4>Latest Technology</h4>
                                <p>
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate quia alias dolor
                                    nesciunt dicta
                                    fugiat ratione illo laborum.
                                    <a href="{{ route('blogDetailsPage') }}" class="text-bgSecondary font-futuraMd">Read
                                        More</a>
                                </p>
                            </div>

                        </div>

                        <div
                            class="border max-w-max rounded-md [&_img]:object-cover [&_img]:rounded-lg xl:p-3  [&_img]:aspect-video">
                            <img src="{{ asset('mainasset/img/blog/blog-6.jpg') }}" width="812" alt="blog image">

                            <div class="p-1 [&_h4]:text-lg [&_h4]:font-semibold [&_h4]:py-2">
                                <h4>Latest Technology</h4>
                                <p>
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate quia alias dolor
                                    nesciunt dicta
                                    fugiat ratione illo laborum.
                                    <a href="{{ route('blogDetailsPage') }}" class="text-bgSecondary font-futuraMd">Read
                                        More</a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
    </section>
    
@endsection
