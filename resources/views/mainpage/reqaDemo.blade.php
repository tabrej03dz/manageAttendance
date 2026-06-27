@extends('mainpage.components.main')

@section('content')
    <!-- =======================
    *** Request A Demo Section ***
    ======================== -->
    <section class="relative overflow-hidden bg-white py-16 lg:py-24">

        {{-- Background Effects --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 h-[34rem] w-[34rem] rounded-full bg-[#e00063]/15 blur-3xl"></div>
            <div class="absolute top-40 -left-40 h-[30rem] w-[30rem] rounded-full bg-[#1f7df2]/15 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-[#7f35b2]/10 blur-3xl"></div>

            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(43,33,74,.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(43,33,74,.05)_1px,transparent_1px)] bg-[size:42px_42px]"></div>
        </div>

        <div class="relative mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">

            {{-- LEFT CONTENT --}}
            <div class="hidden lg:block">
                <div class="relative">
                    <div class="absolute inset-0 rounded-[3rem] bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] opacity-20 blur-3xl"></div>

                    <div class="relative overflow-hidden rounded-[3rem] border border-slate-200 bg-white p-8 shadow-2xl">
                        <div class="mb-8 inline-flex items-center gap-2 rounded-full bg-[#e00063]/10 px-4 py-2 text-sm font-black text-[#e00063]">
                            <span class="h-2 w-2 rounded-full bg-[#e00063]"></span>
                            Free Product Demo
                        </div>

                        <h1 class="text-5xl font-black leading-tight text-[#2b214a]">
                            Employee Attendance &
                            <span class="block bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] bg-clip-text text-transparent">
                                Location Tracking
                            </span>
                            Simplified.
                        </h1>

                        <p class="mt-6 text-lg leading-8 text-slate-600">
                            Get a quick demo of RVG HRMS and see how selfie attendance, geofencing,
                            leave management, salary calculation and employee reports can work for your business.
                        </p>

                        <div class="mt-9 grid grid-cols-2 gap-4">
                            <div class="rounded-[2rem] bg-[#2b214a] p-5 text-white">
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-xl">
                                    📍
                                </div>
                                <h3 class="text-lg font-black">GPS Tracking</h3>
                                <p class="mt-2 text-sm leading-6 text-white/70">
                                    Accurate check-in and check-out location records.
                                </p>
                            </div>

                            <div class="rounded-[2rem] bg-[#e00063]/10 p-5">
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#e00063]/10 text-xl">
                                    🤳
                                </div>
                                <h3 class="text-lg font-black text-[#2b214a]">Selfie Attendance</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Secure attendance with employee verification.
                                </p>
                            </div>

                            <div class="rounded-[2rem] bg-[#1f7df2]/10 p-5">
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1f7df2]/10 text-xl">
                                    🧾
                                </div>
                                <h3 class="text-lg font-black text-[#2b214a]">Salary Reports</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Payroll-ready attendance and salary data.
                                </p>
                            </div>

                            <div class="rounded-[2rem] bg-[#7f35b2]/10 p-5">
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#7f35b2]/10 text-xl">
                                    ✅
                                </div>
                                <h3 class="text-lg font-black text-[#2b214a]">Leave Approval</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Manage employee leave requests easily.
                                </p>
                            </div>
                        </div>

                        <div class="mt-8 rounded-[2rem] bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] p-5 text-white">
                            <p class="text-sm font-bold text-white/80">Need help?</p>
                            <a href="tel:+917753800444" class="mt-1 block text-2xl font-black">
                                +91 7753800444
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT FORM --}}
            <div class="relative">
                <div class="absolute inset-0 rounded-[3rem] bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] opacity-20 blur-3xl"></div>

                <div class="relative overflow-hidden rounded-[3rem] border border-slate-200 bg-white shadow-2xl">

                    {{-- Form Header --}}
                    <div class="bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] p-8 text-white">
                        <p class="text-sm font-black uppercase tracking-[.3em] text-white/80">
                            Request Demo
                        </p>

                        <h2 class="mt-3 text-3xl font-black leading-tight md:text-4xl">
                            Book Your Free HRMS Demo
                        </h2>

                        <p class="mt-4 text-base leading-7 text-white/80">
                            Fill your details and our team will contact you shortly.
                        </p>
                    </div>

                    <div class="p-5 sm:p-8">
                        @if ($errors->any())
                            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                                <ul class="list-disc pl-6 text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="simpleUserForm"
                              action="{{ route('request.demo.store') }}"
                              method="post"
                              class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="compan_name" class="mb-2 block text-sm font-black text-[#2b214a]">
                                        Company Name
                                    </label>
                                    <input type="text"
                                           name="compan_name"
                                           id="compan_name"
                                           placeholder="Company Name"
                                           value="{{ old('compan_name') }}"
                                           class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#e00063] focus:bg-white focus:ring-4 focus:ring-[#e00063]/10">
                                    <p class="mt-1 text-sm text-red-500 error-compan_name"></p>
                                </div>

                                <div>
                                    <label for="owner_name" class="mb-2 block text-sm font-black text-[#2b214a]">
                                        Admin <span class="text-[#e00063]">*</span>
                                    </label>
                                    <input type="text"
                                           name="owner_name"
                                           id="owner_name"
                                           placeholder="Account Owner / Admin Name"
                                           required
                                           value="{{ old('owner_name') }}"
                                           class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#e00063] focus:bg-white focus:ring-4 focus:ring-[#e00063]/10">
                                    <p class="mt-1 text-sm text-red-500 error-owner_name"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="number" class="mb-2 block text-sm font-black text-[#2b214a]">
                                        Mobile No <span class="text-[#e00063]">*</span>
                                    </label>
                                    <input type="text"
                                           name="number"
                                           id="number"
                                           inputmode="numeric"
                                           placeholder="Mobile No"
                                           required
                                           value="{{ old('number') }}"
                                           class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#e00063] focus:bg-white focus:ring-4 focus:ring-[#e00063]/10">
                                    <p class="mt-1 text-sm text-red-500 error-number"></p>
                                </div>

                                <div>
                                    <label for="email" class="mb-2 block text-sm font-black text-[#2b214a]">
                                        Email
                                    </label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           placeholder="example@gmail.com"
                                           value="{{ old('email') }}"
                                           class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#e00063] focus:bg-white focus:ring-4 focus:ring-[#e00063]/10">
                                    <p class="mt-1 text-sm text-red-500 error-email"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="pin_code" class="mb-2 block text-sm font-black text-[#2b214a]">
                                        Pin Code
                                    </label>
                                    <input type="number"
                                           name="pin_code"
                                           id="pin_code"
                                           inputmode="numeric"
                                           placeholder="Pin Code"
                                           value="{{ old('pin_code') }}"
                                           class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#e00063] focus:bg-white focus:ring-4 focus:ring-[#e00063]/10">
                                    <p class="mt-1 text-sm text-red-500 error-pin_code"></p>
                                </div>

                                <div>
                                    <label for="emp_size" class="mb-2 block text-sm font-black text-[#2b214a]">
                                        Employee Size
                                    </label>
                                    <select name="emp_size"
                                            id="emp_size"
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-800 outline-none transition focus:border-[#e00063] focus:bg-white focus:ring-4 focus:ring-[#e00063]/10">
                                        <option value="">Select Employee Size</option>
                                        <option value="0-10" {{ old('emp_size') == '0-10' ? 'selected' : '' }}>0-10</option>
                                        <option value="10-25" {{ old('emp_size') == '10-25' ? 'selected' : '' }}>10-25</option>
                                        <option value="25-50" {{ old('emp_size') == '25-50' ? 'selected' : '' }}>25-50</option>
                                        <option value="50-100" {{ old('emp_size') == '50-100' ? 'selected' : '' }}>50-100</option>
                                        <option value="100-500" {{ old('emp_size') == '100-500' ? 'selected' : '' }}>100-500</option>
                                        <option value="500+" {{ old('emp_size') == '500+' ? 'selected' : '' }}>500+</option>
                                    </select>
                                    <p class="mt-1 text-sm text-red-500 error-emp_size"></p>
                                </div>
                            </div>

                            <div>
                                <label for="company_address" class="mb-2 block text-sm font-black text-[#2b214a]">
                                    Company Address
                                </label>
                                <input type="text"
                                       name="company_address"
                                       id="company_address"
                                       placeholder="Company Address"
                                       value="{{ old('company_address') }}"
                                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#e00063] focus:bg-white focus:ring-4 focus:ring-[#e00063]/10">
                                <p class="mt-1 text-sm text-red-500 error-company_address"></p>
                            </div>

                            <div>
                                <label for="designation" class="mb-2 block text-sm font-black text-[#2b214a]">
                                    Designation
                                </label>
                                <select name="designation"
                                        id="designation"
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-semibold text-slate-800 outline-none transition focus:border-[#e00063] focus:bg-white focus:ring-4 focus:ring-[#e00063]/10">
                                    <option value="">Select Designation</option>
                                    <option value="Owner" {{ old('designation') == 'Owner' ? 'selected' : '' }}>Owner</option>
                                    <option value="Manager" {{ old('designation') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Employee" {{ old('designation') == 'Employee' ? 'selected' : '' }}>Employee</option>
                                    <option value="HR" {{ old('designation') == 'HR' ? 'selected' : '' }}>HR</option>
                                    <option value="other" {{ old('designation') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <p class="mt-1 text-sm text-red-500 error-designation"></p>
                            </div>

                            <button type="submit"
                                    class="group mt-3 inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] px-7 py-4 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-[#e00063]/20 transition hover:-translate-y-1">
                                <span id="submitText">Submit Request</span>
                                <span id="loadingSpinner"
                                      class="ml-3 hidden h-5 w-5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                <span class="ml-2 transition group-hover:translate-x-1">→</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUCCESS MODAL --}}
        <div id="successModal"
             class="fixed inset-0 z-[99999999] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md overflow-hidden rounded-[2rem] bg-white shadow-2xl">
                <div class="bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] p-6 text-center text-white">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/20 text-3xl">
                        ✓
                    </div>
                    <h3 class="mt-4 text-3xl font-black">
                        Success!
                    </h3>
                    <p class="mt-2 text-white/80">
                        Your request has been submitted successfully.
                    </p>
                </div>

                <div class="p-6 text-center">
                    <button type="button"
                            id="successModalOk"
                            class="inline-flex w-full items-center justify-center rounded-full bg-[#2b214a] px-6 py-4 text-sm font-black uppercase tracking-wide text-white transition hover:bg-[#e00063]">
                        OK
                    </button>
                </div>
            </div>
        </div>

        {{-- AJAX SCRIPT --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function () {
                const form = $('#simpleUserForm');
                const submitText = $('#submitText');
                const loadingSpinner = $('#loadingSpinner');
                const successModal = $('#successModal');

                form.on('submit', function (e) {
                    e.preventDefault();

                    let formData = form.serialize();

                    submitText.text('Please wait...');
                    loadingSpinner.removeClass('hidden');
                    $('.error-compan_name, .error-owner_name, .error-number, .error-email, .error-pin_code, .error-company_address, .error-emp_size, .error-designation').text('');

                    $.ajax({
                        url: "{{ route('request.demo.store') }}",
                        type: "POST",
                        data: formData,
                        dataType: "json",

                        success: function (response) {
                            if (response.success) {
                                successModal.removeClass('hidden').addClass('flex');
                                form[0].reset();
                            }
                        },

                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;

                                $.each(errors, function (key, value) {
                                    $('.error-' + key).text(value[0]);
                                });
                            } else {
                                alert("Something went wrong. Please try again.");
                            }
                        },

                        complete: function () {
                            submitText.text('Submit Request');
                            loadingSpinner.addClass('hidden');
                        }
                    });
                });

                $('#successModalOk').on('click', function () {
                    successModal.addClass('hidden').removeClass('flex');
                });

                successModal.on('click', function (e) {
                    if (e.target === this) {
                        successModal.addClass('hidden').removeClass('flex');
                    }
                });
            });
        </script>
    </section>
@endsection