  <!-- =======================
    ***  Footer Section ***
    ======================== -->
  <footer class="bg-bgPrimary py-16">
      <div class="w-full xl:max-w-7xl mx-auto grid grid-row-2 px-6 md:px-16">
          <div class="grid sm:grid-cols-3 sm:py-10 py-8">
              <div class="rightSide">
                  <div class="logo block md:flex">
                      {{-- <svg class="drop-shadow-md" preserveAspectRatio="xMidYMid meet" data-bbox="0 0 142 85"
                          viewBox="0 0 142 85" width="45" xmlns="http://www.w3.org/2000/svg" data-type="color"
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
                      </svg> --}}
                      <a href="{{ route('mainpage') }}"><img src="{{asset('asset/img/logo (2).png')}}" alt="" class="h-24 w-24">
                      </a>
                      <div class="md:ml-1 mt-2 md:mt-4">
                          <h2 class="text-white font-semibold text-lg tracking-tight"><a href="http://realvictorygroups.com/">Real Victory Groups</a></h2>
                          <p class="text-white">73 Basement, Ekta Enclave Society, Lakhanpur, Khyora, Kanpur, Uttar Pradesh 208024</p>
                      </div>
                  </div>
                  <div class="py-6 font-futuraMd text-white md:ml-14">
                      <h4 class="mb-2">
                          <a href="tel: +917753800444" class="hover:text-gray-100 duration-75 ease-linear" title="Call Now"><span
                                  class="font-futuraBk font-semibold">Tel: </span>
                              +917753800444</a>
                      </h4>
                      <h4>
                          <a href="mailto:realvictorygroups@gmail.com" class="hover:text-gray-100 duration-75 ease-linear" title="Email Now">
                              <span class="font-futuraBk font-semibold">Email:
                              </span> realvictorygroups@gmail.com</a>
                      </h4>
                  </div>
                  <div class="text-paraTextxl text-white md:ml-14">
                      <a href="https://www.facebook.com/realvictorygroups/" class="mr-2"><i class="fa-brands fa-square-facebook"></i></a>
                      <a href="https://www.linkedin.com/company/realvictorygroups/?originalSubdomain=in"><i class="fa-brands fa-linkedin"></i></a>
                      <a href="https://www.instagram.com/realvictorygroups/"><i class="fa-brands fa-instagram"></i></a>
                  </div>
              </div>

              <div class="leftSide place-items-start sm:place-items-end mt-4 sm:mt-0">
                  <ul class="font-futuraMd">
                      <li class="text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="{{route('mainpage')}}">Home</a>
                      </li>
                      <li class="py-2 text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="#attendNow">Why RVG</a>
                      </li>
                      <li class="text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="#benefit">Benefits</a>
                      </li>
                      <li class="py-2 text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="#review">Reviews</a>
                      </li>
                      <li class="py-2 text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="#price">Pricing</a>
                      </li>
                  </ul>
              </div>

              <div class="leftSide place-items-start sm:place-items-end mt-4 sm:mt-0">
                  <ul class="font-futuraMd">
                      <li class="text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                      </li>
                      <li class="py-2 text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="https://realvictorygroups.com/terms-conditions/">Terms & Conditions</a>
                      </li>
                      <li class="text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="https://realvictorygroups.com/cancellation-refund-policy/">Refund Policy</a>
                      </li>
                      {{-- <li class="py-2 text-white hover:text-gray-100 duration-100 ease-linear hover:underline">
                          <a href="#">Shipping Policy</a>
                      </li> --}}
                  </ul>
              </div>
          </div>

          <div class="place-items-center pt-4">
              <p class="text-paraTextmd font-futuraMd font-normal text-center text-white">© 2024 by
                <a href="http://realvictorygroups.com/">  Real Victory Groups </a></p>
          </div>
      </div>
  </footer>
