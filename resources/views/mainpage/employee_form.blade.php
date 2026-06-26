{{-- resources/views/employees/create.blade.php --}}
@extends('mainpage.components.main')

@section('content')
@php
    $input = "mt-2 w-full h-12 rounded-2xl !bg-slate-50 !text-slate-900 placeholder:!text-slate-400
              border !border-slate-200 px-4 shadow-sm
              focus:outline-none focus:ring-4 focus:ring-[#e00063]/10 focus:!border-[#e00063] focus:!bg-white transition";

    $textarea = "mt-2 w-full rounded-2xl !bg-slate-50 !text-slate-900 placeholder:!text-slate-400
                 border !border-slate-200 px-4 py-3 shadow-sm
                 focus:outline-none focus:ring-4 focus:ring-[#e00063]/10 focus:!border-[#e00063] focus:!bg-white transition";

    $select = "mt-2 w-full h-12 rounded-2xl !bg-slate-50 !text-slate-900
               border !border-slate-200 px-4 shadow-sm
               focus:outline-none focus:ring-4 focus:ring-[#e00063]/10 focus:!border-[#e00063] focus:!bg-white transition";

    $file = "mt-2 w-full rounded-2xl !bg-slate-50 !text-slate-700
             border !border-slate-200 shadow-sm
             focus:outline-none focus:ring-4 focus:ring-[#e00063]/10 focus:!border-[#e00063] focus:!bg-white transition
             file:mr-4 file:rounded-xl file:border-0 file:bg-[#e00063]/10 file:px-4 file:py-3
             file:text-sm file:font-black file:text-[#e00063] hover:file:bg-[#e00063]/15";

    $label = "text-sm font-black text-[#2b214a]";
@endphp

<div class="min-h-screen overflow-hidden bg-white">

    <section class="relative py-16 lg:py-24">

        {{-- Background Effects --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 h-[34rem] w-[34rem] rounded-full bg-[#e00063]/15 blur-3xl"></div>
            <div class="absolute top-40 -left-40 h-[30rem] w-[30rem] rounded-full bg-[#1f7df2]/15 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-[#7f35b2]/10 blur-3xl"></div>

            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(43,33,74,.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(43,33,74,.05)_1px,transparent_1px)] bg-[size:42px_42px]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- PAGE HEADER --}}
            <div class="mx-auto mb-12 max-w-3xl text-center">
                <div class="inline-flex items-center gap-2 rounded-full bg-[#e00063]/10 px-4 py-2 text-sm font-black text-[#e00063]">
                    <span class="h-2 w-2 rounded-full bg-[#e00063]"></span>
                    Employee Registration
                </div>

                <h1 class="mt-6 text-4xl font-black leading-tight text-[#2b214a] sm:text-5xl lg:text-6xl">
                    Add New
                    <span class="bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] bg-clip-text text-transparent">
                        Employee
                    </span>
                </h1>

                <p class="mt-5 text-lg leading-8 text-slate-600">
                    Create employee profile with documents, timings, salary, office and department details.
                </p>

                <div class="mt-7 flex items-center justify-center gap-2">
                    <span class="h-1 w-14 rounded-full bg-[#1f7df2]"></span>
                    <span class="h-3 w-3 rounded-full bg-[#e00063]"></span>
                    <span class="h-1 w-14 rounded-full bg-[#7f35b2]"></span>
                </div>
            </div>

            {{-- FLASH MESSAGES --}}
            @if(session('success'))
                <div class="mb-6 rounded-[2rem] border border-green-200 bg-green-50 p-5 text-green-700 shadow-sm">
                    <div class="font-black">Success 🎉</div>
                    <p class="mt-1 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-[2rem] border border-red-200 bg-red-50 p-5 text-red-700 shadow-sm">
                    <div class="font-black">Failed ❌</div>
                    <p class="mt-1 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="mb-6 rounded-[2rem] border border-red-200 bg-red-50 p-5 text-red-700 shadow-sm">
                    <div class="mb-2 font-black">Please fix the following:</div>
                    <ul class="ml-5 list-disc space-y-1 text-sm">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- MAIN CARD --}}
            <div class="relative">
                <div class="absolute inset-0 rounded-[3rem] bg-gradient-to-br from-[#1f7df2] via-[#7f35b2] to-[#e00063] opacity-20 blur-3xl"></div>

                <div class="relative overflow-hidden rounded-[3rem] border border-slate-200 bg-white shadow-2xl">

                    {{-- CARD HEADER --}}
                    <div class="relative overflow-hidden bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] px-6 py-8 md:px-10">
                        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,.12)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,.12)_1px,transparent_1px)] bg-[size:32px_32px] opacity-30"></div>

                        <div class="relative flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-black uppercase tracking-[.3em] text-white/75">
                                    RVG HRMS
                                </p>
                                <h2 class="mt-2 text-2xl font-black text-white md:text-3xl">
                                    Employee Registration Form
                                </h2>
                                <p class="mt-2 text-sm text-white/80">
                                    Fill all employee details carefully and save.
                                </p>
                            </div>

                            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/20 text-3xl text-white shadow-xl backdrop-blur">
                                👤
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-8 md:p-10">
                        <form method="POST"
                              action="{{ route('employee-register') }}"
                              enctype="multipart/form-data"
                              class="space-y-10">
                            @csrf

                            {{-- BASIC INFO --}}
                            <div class="rounded-[2rem] border border-slate-200 bg-slate-50/60 p-5 md:p-7">
                                <div class="mb-6 flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#e00063]/10 text-[#e00063]">
                                        👤
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-[#2b214a]">Basic Info</h3>
                                        <p class="text-sm text-slate-500">Employee personal and login details.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label class="{{ $label }}">Name <span class="text-[#e00063]">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="{{ $input }}" placeholder="Employee name" required>
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Email <span class="text-[#e00063]">*</span></label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="{{ $input }}" placeholder="employee@email.com" required>
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Alternate Email</label>
                                        <input type="email" name="email1" value="{{ old('email1') }}" class="{{ $input }}" placeholder="Alternate email optional">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Password <span class="text-[#e00063]">*</span></label>
                                        <input type="password" name="password" class="{{ $input }}" placeholder="Set a password" required>
                                        <p class="mt-1 text-xs text-slate-500">Edit page me password optional rakho.</p>
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="{{ $input }}" placeholder="10 digit number">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Photo</label>
                                        <input type="file" name="photo" accept="image/*" class="{{ $file }}">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="{{ $label }}">Address</label>
                                        <textarea name="address" rows="3" class="{{ $textarea }}" placeholder="Full address">{{ old('address') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- OFFICE / DEPARTMENT --}}
                            <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm md:p-7">
                                <div class="mb-6 flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#1f7df2]/10 text-[#1f7df2]">
                                        🏢
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-[#2b214a]">Office / Department / Team</h3>
                                        <p class="text-sm text-slate-500">Assign employee to office and department.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <div>
                                        <label class="{{ $label }}">Office <span class="text-[#e00063]">*</span></label>
                                        @if(isset($offices) && $offices->count() > 0)
                                            <select name="office_id" class="{{ $select }}" required>
                                                <option value="">-- Select Office --</option>
                                                @foreach($offices as $o)
                                                    <option value="{{ $o->id }}" @selected(old('office_id') == $o->id)>
                                                        {{ $o->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="number" name="office_id" value="{{ old('office_id') }}" class="{{ $input }}" placeholder="Enter Office ID manually">
                                        @endif
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Department <span class="text-[#e00063]">*</span></label>
                                        @if(isset($departments) && $departments->count() > 0)
                                            <select name="department_id" class="{{ $select }}" required>
                                                <option value="">-- Select Department --</option>
                                                @foreach($departments as $d)
                                                    <option value="{{ $d->id }}" @selected(old('department_id') == $d->id)>
                                                        {{ $d->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="number" name="department_id" value="{{ old('department_id') }}" class="{{ $input }}" placeholder="Enter Department ID manually">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- JOB DETAILS --}}
                            <div class="rounded-[2rem] border border-slate-200 bg-slate-50/60 p-5 md:p-7">
                                <div class="mb-6 flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#7f35b2]/10 text-[#7f35b2]">
                                        💼
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-[#2b214a]">Job Details</h3>
                                        <p class="text-sm text-slate-500">Designation and joining related details.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                                    <div>
                                        <label class="{{ $label }}">Joining Date</label>
                                        <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="{{ $input }}">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Designation</label>
                                        <input type="text" name="designation" value="{{ old('designation') }}" class="{{ $input }}" placeholder="e.g. Manager">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Responsibility</label>
                                        <input type="text" name="responsibility" value="{{ old('responsibility') }}" class="{{ $input }}" placeholder="e.g. Sales / HR / Accounts">
                                    </div>
                                </div>
                            </div>

                            {{-- TIMINGS --}}
                            <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm md:p-7">
                                <div class="mb-6 flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#e00063]/10 text-[#e00063]">
                                        ⏱
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-[#2b214a]">Timings</h3>
                                        <p class="text-sm text-slate-500">Shift, office time and break details.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-4">
                                    <div>
                                        <label class="{{ $label }}">Check In Time</label>
                                        <input type="time" name="check_in_time" value="{{ old('check_in_time') }}" class="{{ $input }}">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Check Out Time</label>
                                        <input type="time" name="check_out_time" value="{{ old('check_out_time') }}" class="{{ $input }}">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Office Time</label>
                                        <input type="number" name="office_time" value="{{ old('office_time') }}" class="{{ $input }}" placeholder="e.g. 480">
                                        <p class="mt-1 text-xs text-slate-500">Minutes me enter karein.</p>
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Break</label>
                                        <input type="text" name="break" value="{{ old('break') }}" class="{{ $input }}" placeholder="e.g. 30 min">
                                    </div>
                                </div>
                            </div>

                            {{-- IDS & NUMBERS --}}
                            <div class="rounded-[2rem] border border-slate-200 bg-slate-50/60 p-5 md:p-7">
                                <div class="mb-6 flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#1f7df2]/10 text-[#1f7df2]">
                                        🪪
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-[#2b214a]">Employee IDs & Numbers</h3>
                                        <p class="text-sm text-slate-500">Employee ID, UAN, ESIC and bank account details.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                                    <div>
                                        <label class="{{ $label }}">Employee ID</label>
                                        <input type="text" name="employee_id" value="{{ old('employee_id') }}" class="{{ $input }}" placeholder="EMP001">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">UAN Number</label>
                                        <input type="text" name="uan_number" value="{{ old('uan_number') }}" class="{{ $input }}">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">ESIC Number</label>
                                        <input type="text" name="esic_number" value="{{ old('esic_number') }}" class="{{ $input }}">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="{{ $label }}">Account Number</label>
                                        <input type="text" name="account_number" value="{{ old('account_number') }}" class="{{ $input }}">
                                    </div>
                                </div>
                            </div>

                            {{-- DOCUMENTS --}}
                            <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm md:p-7">
                                <div class="mb-6 flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#7f35b2]/10 text-[#7f35b2]">
                                        📎
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-[#2b214a]">Documents</h3>
                                        <p class="text-sm text-slate-500">Upload Aadhaar, PAN and other documents.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                                    <div>
                                        <label class="{{ $label }}">Aadhaar Attachment</label>
                                        <input type="file" name="aadhar_attachment" class="{{ $file }}">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">PAN Attachment</label>
                                        <input type="file" name="pan_attachment" class="{{ $file }}">
                                    </div>

                                    <div>
                                        <label class="{{ $label }}">Other Attachment</label>
                                        <input type="file" name="other_attachment" class="{{ $file }}">
                                    </div>
                                </div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="flex flex-col gap-3 border-t border-slate-200 pt-8 sm:flex-row sm:justify-end">
                                <a href="{{ route('mainpage') }}"
                                   class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-7 py-4 text-sm font-black uppercase tracking-wide text-[#2b214a] shadow-sm transition hover:-translate-y-1 hover:border-[#e00063]/30 hover:text-[#e00063]">
                                    Back
                                </a>

                                <button type="submit"
                                        class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-[#1f7df2] via-[#7f35b2] to-[#e00063] px-9 py-4 text-sm font-black uppercase tracking-wide text-white shadow-xl shadow-[#e00063]/20 transition hover:-translate-y-1">
                                    Save Employee
                                    <span class="ml-2">→</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection