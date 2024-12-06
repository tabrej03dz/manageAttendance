@extends('mainpage.components.main')
@section('content')
    <!-- =======================
        ***  Blog Section ***
        ======================== -->
    <section class="blogsDetails shrink-0"
        style="background: url('{{ asset('mainasset/img/blog/secondry-Blog-Bg.jpg') }}') no-repeat center center/cover">
        <div
            class="w-full h-[40vh] xl:max-w-7xl 2xl:max-w-full mx-auto py-16 px-6 md:px-16 2xl:px-28 grid justify-items-start place-items-end">
            <ul
                class="w-fit flex items-center justify-start font-bold font-futuraBk drop-shadow-2xl md:[&_li]:mx-4 [&_li]:p-2 [&_a]:text-universal [&_a:hover]:text-bgSecondary [&_li]:transition">
                <li><a href="{{ route('mainpage') }}"><i class="fa-solid fa-house"></i> <span>Home</span></a></li>
                <i class="fa-solid fa-angle-right text-bgSecondary"></i>
                <li><a href="{{ route('blogs') }}">Blogs</a></li>
                <i class="fa-solid fa-angle-right text-bgSecondary"></i>
                <li><a href="{{ route('blogDetailsPage') }}" class="cursor-not-allowed">Blog Details</a></li>
            </ul>
        </div>
    </section>

    <!-- =======================
              latest blogs
            ======================= -->
    <section id="attendNow">
        <div class="w-full xl:max-w-7xl mx-auto py-16 px-6 md:px-16 2xl:px-0 grid grid-cols-1 place-items-center">
            <div class="*:text-3xl mb-12 [md:*]:text5xl underline font-bold font-futuraMd">
                <a href="{{ route('blogs') }}">All Blogs</a>
            </div>

            <div class="max-w-5xl grid grid-cols-1 gap-6 place-items-center ">
                <!-- =============
              Main blogs
                =============-->
                <!-- blogPost-1 -->
                <div
                    class="max-w-max rounded-md lg:[&_img]:bg-white lg:[&_img]:p-2 lg:[&_img]:shadow-sm [&_img]:rounded lg:[&_img]:rounded-3xl xl:p-3 [&_img]:aspect-video">
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
                                class="w-16 h-8 border bg-gray-200 rounded-sm shadow-sm grid place-items-center absolute -left-16 bottom-[.1px]"
                                class="hidden" aria-labelledby="dropdownDefaultButton">
                                <a href="#"><i class="fa-solid fa-share text-bgSecondary cursor-pointer"></i></a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="p-1 [&_h4]:text-2xl xl:[&_h4]:text-3xl [&_h4]:mb-4 [&_h4]:font-futuraMd [&_h4]:font-semibold px-4">
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
                            obcaecati <strong>unt reprehenderit!</strong> Deserunt aut autem consequatur tempora ducimus
                            eligendi
                            velit animi
                            dolore
                            eius, minima, soluta amet, <em>illo officiis</em>. Lorem ipsum dolor sit amet, consectetur
                            adipisicing
                            elit.
                            Quas, nostrum aut exercitationem unde sequi voluptas atque. Molestias eligendi sed tenetur vitae
                            laboriosam totam <a class="font-futuraMd uppercase text-sm" href="https://google.com/">ratione
                                nobis</a>
                            fugit,
                            excepturi dolor
                            beatae,
                            accusantium
                            consequatur, quo saepe
                            doloribus explicabo obcaecati veritatis! Dignissimos, temporibus sunt. Totam doloribus officia,
                            labore
                            voluptate amet reprehenderit a, dolore enim eligendi nostrum libero reiciendis explicabo veniam
                            provident sunt at debitis.
                        </p>
                        <p class="py-4 first-line:text-lg first-line:capitalize font-futuraBk">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet necessitatibus vero aspernatur
                            nesciunt
                            obcaecati repellat veniam quis unde hic. Obcaecati, pariatur expedita. Nostrum eum obcaecati
                            velit,
                            fugit minima quo iure rerum perspiciatis cum quia tenetur necessitatibus quod veritatis
                            similique
                            voluptatibus officia accusantium vero assumenda quae!
                        </p>

                        <div class="blogAuthor flex items-center justify-start py-4">
                            <img class="size-12 aspect-square object-cover rounded-full ring-2 ring-bgSecondary drop-shadow-sm"
                                src="{{ asset('mainasset/img/Blog_Profile/people01.png') }}" loading="lazy" alt="profile...">

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

                        <div class="block w-full h-[1px] bg-bgSecondary opacity-30 my-4"></div>
                    </div>
                </div>
                <!-- blogPost-2 -->
                <div
                    class="max-w-max rounded-md lg:[&_img]:bg-white lg:[&_img]:p-2 lg:[&_img]:shadow-sm [&_img]:rounded lg:[&_img]:rounded-3xl xl:p-3 [&_img]:aspect-video">
                    <img src="{{ asset('mainasset/img/blog/blog-7.jpg') }}" width="" alt="blog image">

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
                                        <a class="text-headerBgcolor font-futuraBk text-base md:text-xl tracking-wide"
                                            href="../../index.html">AttendNow</a>
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
                                <a href="#"><i class="fa-solid fa-share text-bgSecondary cursor-pointer"></i></a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="p-1 [&_h4]:text-2xl xl:[&_h4]:text-3xl [&_h4]:mb-4 [&_h4]:font-futuraMd [&_h4]:font-semibold px-4">
                        <h4>Streamline Workforce Management with
                            Digital
                            <mark class="bg-yellow-500 text-white p-1">Rostering:</mark>
                            Increased Accuracy.
                        </h4>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate quia alias dolor nesciunt
                            dicta
                            fugiat ratione illo laborum. Architecto voluptatem doloribus iusto distinctio alias quaerat
                            maxime
                            saepe
                            sit fugiat doloremque, libero placeat assumenda aliquam ad dolores error, esse animi quasi eaque
                            obcaecati <strong>unt reprehenderit!</strong> Deserunt aut autem consequatur tempora ducimus
                            eligendi
                            velit animi
                            dolore
                            eius, minima, soluta amet, <em>illo officiis</em>. Lorem ipsum dolor sit amet, consectetur
                            adipisicing
                            elit.
                            Quas, nostrum aut exercitationem unde sequi voluptas atque. Molestias eligendi sed tenetur vitae
                            laboriosam totam <a class="font-futuraMd uppercase text-sm" href="https://google.com/">ratione
                                nobis</a>
                            fugit,
                            excepturi dolor
                            beatae,
                            accusantium
                            consequatur, quo saepe
                            doloribus explicabo obcaecati veritatis! Dignissimos, temporibus sunt. Totam doloribus officia,
                            labore
                            voluptate amet reprehenderit a, dolore enim eligendi nostrum libero reiciendis explicabo veniam
                            provident sunt at debitis.
                        </p>
                        <p class="py-4 first-line:text-lg first-line:capitalize font-futuraBk">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet necessitatibus vero aspernatur
                            nesciunt
                            obcaecati repellat veniam quis unde hic. Obcaecati, pariatur expedita. Nostrum eum obcaecati
                            velit,
                            fugit minima quo iure rerum perspiciatis cum quia tenetur necessitatibus quod veritatis
                            similique
                            voluptatibus officia accusantium vero assumenda quae!
                        </p>

                        <div class="blogAuthor flex items-center justify-start py-4">
                            <img class="size-12 aspect-square object-cover rounded-full ring-2 ring-bgSecondary drop-shadow-sm"
                                src="{{ asset('mainasset/img/Blog_Profile/people02.png') }}" loading="lazy" alt="profile...">

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

                        <div class="block w-full h-[1px] bg-bgSecondary opacity-30 my-4"></div>
                    </div>
                </div>
                <!-- blogPost-3 -->
                <div
                    class="max-w-max rounded-md lg:[&_img]:bg-white lg:[&_img]:p-2 lg:[&_img]:shadow-sm [&_img]:rounded lg:[&_img]:rounded-3xl xl:p-3 [&_img]:aspect-video">
                    <img src="{{ asset('mainasset/img/blog/blog-2.jpg') }}" width="" alt="blog image">

                    <div class="flex items-center justify-between py-2 px-4">
                        <div class="logo">
                            <a href="{{ route('mainpage') }}" class="">
                                <div class="flex items-center justify-center place-items-start size-fit">
                                    <svg preserveAspectRatio="xMidYMid meet" data-bbox="0 0 142 85" viewBox="0 0 142 85"
                                        width="40" xmlns="http://www.w3.org/2000/svg" data-type="color"
                                        role="img" aria-label="AttendNow">
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
                                        <a class="text-headerBgcolor font-futuraBk text-base md:text-xl tracking-wide"
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
                                class="w-16 h-8 border bg-gray-200 rounded-sm shadow-sm grid place-items-center absolute -left-16 bottom-[.1px]"
                                class="hidden" aria-labelledby="dropdownDefaultButton">
                                <a href="#"><i class="fa-solid fa-share text-bgSecondary cursor-pointer"></i></a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="p-1 [&_h4]:text-2xl xl:[&_h4]:text-3xl [&_h4]:mb-4 [&_h4]:font-futuraMd [&_h4]:font-semibold px-4">
                        <h4>Streamline Workforce Management with
                            <mark class="bg-sky-500 text-white p-1">Digital</mark>
                            Rostering:
                            Increased Accuracy,
                        </h4>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate quia alias dolor nesciunt
                            dicta
                            fugiat ratione illo laborum. Architecto voluptatem doloribus iusto distinctio alias quaerat
                            maxime
                            saepe
                            sit fugiat doloremque, libero placeat assumenda aliquam ad dolores error, esse animi quasi eaque
                            obcaecati <strong>unt reprehenderit!</strong> Deserunt aut autem consequatur tempora ducimus
                            eligendi
                            velit animi
                            dolore
                            eius, minima, soluta amet, <em>illo officiis</em>. Lorem ipsum dolor sit amet, consectetur
                            adipisicing
                            elit.
                            Quas, nostrum aut exercitationem unde sequi voluptas atque. Molestias eligendi sed tenetur vitae
                            laboriosam totam <a class="font-futuraMd uppercase text-sm" href="https://google.com/">ratione
                                nobis</a>
                            fugit,
                            excepturi dolor
                            beatae,
                            accusantium
                            consequatur, quo saepe
                            doloribus explicabo obcaecati veritatis! Dignissimos, temporibus sunt. Totam doloribus officia,
                            labore
                            voluptate amet reprehenderit a, dolore enim eligendi nostrum libero reiciendis explicabo veniam
                            provident sunt at debitis.
                        </p>
                        <p class="py-4 first-line:text-lg first-line:capitalize font-futuraBk">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet necessitatibus vero aspernatur
                            nesciunt
                            obcaecati repellat veniam quis unde hic. Obcaecati, pariatur expedita. Nostrum eum obcaecati
                            velit,
                            fugit minima quo iure rerum perspiciatis cum quia tenetur necessitatibus quod veritatis
                            similique
                            voluptatibus officia accusantium vero assumenda quae!
                        </p>

                        <div class="blogAuthor flex items-center justify-start py-4">
                            <img class="size-12 aspect-square object-cover rounded-full ring-2 ring-bgSecondary drop-shadow-sm"
                                src="{{ asset('mainasset/img/Blog_Profile/people02.png') }}" loading="lazy" alt="profile...">

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

                        <div class="block w-full h-[1px] bg-bgSecondary opacity-30 my-4"></div>
                    </div>
                </div>
                <!-- blogPost-4 -->
                <div
                    class="max-w-max rounded-md lg:[&_img]:bg-white lg:[&_img]:p-2 lg:[&_img]:shadow-sm [&_img]:rounded lg:[&_img]:rounded-3xl xl:p-3 [&_img]:aspect-video">
                    <img src="{{ asset('mainasset/img/blog/blog-6.jpg') }}" width="" alt="blog image">

                    <div class="flex items-center justify-between py-2 px-4">
                        <div class="logo">
                            <a href="{{ route('mainpage') }}" class="">
                                <div class="flex items-center justify-center place-items-start size-fit">
                                    <svg preserveAspectRatio="xMidYMid meet" data-bbox="0 0 142 85" viewBox="0 0 142 85"
                                        width="40" xmlns="http://www.w3.org/2000/svg" data-type="color"
                                        role="img" aria-label="AttendNow">
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
                                        <a class="text-headerBgcolor font-futuraBk text-base md:text-xl tracking-wide"
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
                                <a href="#"><i class="fa-solid fa-share text-bgSecondary cursor-pointer"></i></a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="p-1 [&_h4]:text-2xl xl:[&_h4]:text-3xl [&_h4]:mb-4 [&_h4]:font-futuraMd [&_h4]:font-semibold px-4">
                        <h4>Streamline <span class="underline decoration-pink-400">Workforce</span> Management with
                            Digital
                            Rostering:
                            <mark class="bg-orange-500 text-white p-1">Increased</mark>
                            Accuracy,
                        </h4>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate quia alias dolor nesciunt
                            dicta
                            fugiat ratione illo laborum. Architecto voluptatem doloribus iusto distinctio alias quaerat
                            maxime
                            saepe
                            sit fugiat doloremque, <span class="underline decoration-violet-600">libero</span> placeat
                            assumenda
                            aliquam ad dolores error, esse animi quasi eaque
                            obcaecati <strong>unt reprehenderit!</strong> Deserunt aut autem consequatur tempora ducimus
                            eligendi
                            velit animi
                            dolore
                            eius, minima, soluta amet, <em>illo officiis</em>. Lorem ipsum dolor sit amet, consectetur
                            adipisicing
                            elit.
                            Quas, nostrum aut exercitationem unde sequi voluptas atque. Molestias eligendi sed tenetur vitae
                            laboriosam totam <a class="font-futuraMd uppercase text-sm" href="https://google.com/">ratione
                                nobis</a>
                            fugit,
                            excepturi dolor
                            beatae,
                            accusantium
                            consequatur, quo saepe
                            doloribus explicabo obcaecati veritatis! Dignissimos, temporibus sunt. Totam doloribus officia,
                            labore
                            voluptate amet reprehenderit a, dolore enim eligendi nostrum libero reiciendis explicabo veniam
                            provident sunt at debitis.
                        </p>
                        <p class="py-4 first-line:text-lg first-line:capitalize font-futuraBk">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet necessitatibus vero aspernatur
                            nesciunt
                            obcaecati repellat veniam quis unde hic. Obcaecati, pariatur expedita. Nostrum eum obcaecati
                            velit,
                            fugit minima quo iure rerum perspiciatis cum quia tenetur necessitatibus quod veritatis
                            similique
                            voluptatibus officia accusantium vero assumenda quae!
                        </p>

                        <div class="blogAuthor flex items-center justify-start py-4">
                            <img class="size-12 aspect-square object-cover rounded-full ring-2 ring-bgSecondary drop-shadow-sm"
                                src="{{ asset('mainasset/img/Blog_Profile/people03.png') }}" loading="lazy" alt="profile...">

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

                        <div class="block w-full h-[1px] bg-lightPurple opacity-30 my-4"></div>
                    </div>
                </div>
                <!-- blogPost-5 -->
                <div
                    class="max-w-max rounded-md [&_img]:bg-white [&_img]:p-2 [&_img]:shadow-sm [&_img]:rounded-3xl xl:p-3 [&_img]:aspect-video">
                    <img src="{{ asset('mainasset/img/blog/blog-5.jpg') }}" width="" alt="blog image">

                    <div class="flex items-center justify-between py-2 px-4">
                        <div class="logo">
                            <a href="{{ route('mainpage') }}" class="">
                                <div class="flex items-center justify-center place-items-start size-fit">
                                    <svg preserveAspectRatio="xMidYMid meet" data-bbox="0 0 142 85" viewBox="0 0 142 85"
                                        width="40" xmlns="http://www.w3.org/2000/svg" data-type="color"
                                        role="img" aria-label="AttendNow">
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
                                        <a class="text-headerBgcolor font-futuraBk text-base md:text-xl tracking-wide"
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
                                <a href="#"><i class="fa-solid fa-share text-bgSecondary cursor-pointer"></i></a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="p-1 [&_h4]:text-2xl xl:[&_h4]:text-3xl [&_h4]:mb-4 [&_h4]:font-futuraMd [&_h4]:font-semibold px-4">
                        <h4>Streamline Workforce <mark class="bg-slate-500 text-white p-1">Management</mark> with
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
                            obcaecati <strong>unt reprehenderit!</strong> Deserunt aut autem consequatur tempora ducimus
                            eligendi
                            velit animi
                            dolore
                            eius, minima, soluta amet, <em>illo officiis</em>. Lorem ipsum dolor sit amet, consectetur
                            adipisicing
                            elit.
                            Quas, nostrum aut exercitationem unde sequi voluptas atque. Molestias eligendi sed tenetur vitae
                            laboriosam totam <a class="font-futuraMd uppercase text-sm" href="https://google.com/">ratione
                                nobis</a>
                            fugit,
                            excepturi dolor
                            beatae,
                            accusantium
                            consequatur, quo saepe
                            doloribus explicabo obcaecati veritatis! Dignissimos, temporibus sunt. Totam doloribus officia,
                            labore
                            voluptate amet reprehenderit a, dolore enim eligendi nostrum libero reiciendis explicabo veniam
                            provident sunt at debitis.
                        </p>
                        <p class="py-4 first-line:text-lg first-line:capitalize font-futuraBk">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet necessitatibus vero aspernatur
                            nesciunt
                            obcaecati repellat veniam quis unde hic. Obcaecati, pariatur expedita. Nostrum eum obcaecati
                            velit,
                            fugit minima quo iure rerum perspiciatis cum quia tenetur necessitatibus quod veritatis
                            similique
                            voluptatibus officia accusantium vero assumenda quae!
                        </p>

                        <div class="blogAuthor flex items-center justify-start py-4">
                            <img class="size-12 aspect-square object-cover rounded-full ring-2 ring-bgSecondary drop-shadow-sm"
                                src="{{ asset('mainasset/img/Blog_Profile/people01.png') }}" loading="lazy" alt="profile...">

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

                        <div class="block w-full h-[1px] bg-bgSecondary opacity-30 my-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
