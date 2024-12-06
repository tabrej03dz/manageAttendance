@extends('mainpage.components.main')
@section('content')

<!-- =======================
    ***  LogIn Section ***
    ======================== -->
<section class="login shrink-0">
    <div class="backgroundLogin">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="relative h-screen ">
        <div
            class="w-full h-[90vh] place-items-center xl:max-w-7xl 2xl:max-w-full mx-auto py-16 px-6 md:px-16 2xl:px-28">
            <div class="absolute inset-0">
                <div
                    class="relative h-full w-full [&>div]:absolute [&>div]:inset-0 [&>div]:bg-heroGradient [&>div]:opacity-40 [&>div]:mix-blend-multiply">
                    <div></div>
                </div>
            </div>

            <!-- login Content -->
            <div class="relative z-10 flex h-full flex-col items-center justify-center px-4">
                <div class="max-w-3xl w-full mx-auto text-center">
                    <h2 class="mb-12 text-3xl lg:text-6xl font-bold tracking-tight text-white font-futuraMd">
                        Login
                    </h2>

                    <form
                        class="border-y rounded-md border-bgSecondary mt-2 mb-6 py-6 [&>div]:grid [&>div]:w-[300px] grid-cols-1 text-start [&>div]:py-2 [&_label]:text-lg [&_label]:text-white [&_label]:py-2  [&_label]:text-headerBgcolor [&_label]:w-fit [&_label]:cursor-pointer [&_label]:font-bold [&_input]:border [&_input]:py-3 [&_input]:px-4 [&_input]:text-base [&_input]:rounded-md [&_input:]:outline-none [&_input:focus]:outline [&_input:focus]:outline-bgSecondary"
                        action="backend.php">
                        <div>
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" placeholder="example@gmail.com">
                        </div>

                        <div>
                            <button
                                class="rounded-md px-6 py-3 font-medium bg-bgHeader text-white hover:bg-universal duration-150 ease-in">
                                LOGIN
                            </button>
                        </div>

                        <div class="font-light text-center mt-6 mb-2 text-white">
                            <span>Or Sign Up Using</span>
                        </div>

                        <div class="grid grid-cols-3 place-items-center [&_i]:text-white [&_i]:p-2 [&_i]:rounded-sm">
                            <a href="#"><i class="fa-brands fa-google bg-red-500"></i></a>
                            <a href="#"><i class="fa-brands fa-twitter bg-blue-500"></i></a>
                            <a href="#"><i class="fa-brands fa-github bg-gray-950"></i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
