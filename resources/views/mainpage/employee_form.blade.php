{{-- resources/views/employees/create.blade.php --}}
@extends('mainpage.components.main')

@section('content')
@php
  $input = "mt-2 w-full h-11 rounded-xl !bg-white !text-slate-900 placeholder:!text-slate-400
            border !border-slate-300 shadow-sm
            focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:!border-red-500";

  $textarea = "mt-2 w-full rounded-xl !bg-white !text-slate-900 placeholder:!text-slate-400
               border !border-slate-300 shadow-sm
               focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:!border-red-500";

  $select = "mt-2 w-full h-11 rounded-xl !bg-white !text-slate-900
             border !border-slate-300 shadow-sm
             focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:!border-red-500";

  $file = "mt-2 w-full h-11 rounded-xl !bg-white !text-slate-700
           border !border-slate-300 shadow-sm
           focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:!border-red-500
           file:mr-4 file:rounded-lg file:border-0 file:bg-red-50 file:px-4 file:py-2
           file:text-sm file:font-semibold file:text-red-600 hover:file:bg-red-100";
@endphp

<div class="app-container">
  <section class="relative py-14 md:py-20">
    {{-- Background blur --}}
    <div class="absolute inset-0 -z-10 h-full w-full bg-white overflow-hidden">
      <div class="absolute left-0 top-0 size-[520px] translate-x-[20%] translate-y-[10%] rounded-full bg-bgSecondary opacity-40 blur-[110px]"></div>
      <div class="absolute right-0 bottom-0 size-[260px] -translate-x-[10%] -translate-y-[10%] rounded-full bg-bgSecondary opacity-40 blur-[110px]"></div>
    </div>

    <div class="w-full xl:max-w-7xl mx-auto px-4 sm:px-6 md:px-12 2xl:px-0">

      {{-- Header --}}
      <div class="mb-10 text-center">
        <h1 class="text-3xl md:text-5xl font-futuraBk font-semibold text-slate-900">Add Employee</h1>
        <p class="mt-3 text-slate-600 text-base md:text-lg max-w-2xl mx-auto">
          Create employee profile with documents, timings, salary & office details.
        </p>

        <div class="mt-6 flex items-center justify-center gap-2">
          <span class="h-[2px] w-10 bg-red-500"></span>
          <span class="h-2 w-2 rounded-full bg-red-500"></span>
          <span class="h-[2px] w-10 bg-black/80"></span>
        </div>
      </div>

      {{-- Errors --}}
      @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm">
          <div class="font-semibold mb-2">Please fix the following:</div>
          <ul class="list-disc ml-5 space-y-1 text-sm">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Card --}}
      <div class="bg-white/90 backdrop-blur rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="px-6 md:px-10 py-5 bg-gradient-to-r from-black to-red-600">
          <h2 class="text-white font-semibold text-lg tracking-wide">Employee Registration Form</h2>
          <p class="text-white/80 text-sm mt-1">Fill all details carefully and save.</p>
        </div>

        <div class="p-6 md:p-10">
          <form method="POST" action="{{ route('employee-register') }}" enctype="multipart/form-data" class="space-y-10">
            @csrf

            {{-- BASIC INFO --}}
            <div>
              <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-semibold text-slate-900">Basic Info</h3>
                <span class="h-[1px] flex-1 bg-slate-200"></span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                  <label class="text-sm font-medium text-slate-700">Name <span class="text-red-500">*</span></label>
                  <input type="text" name="name" value="{{ old('name') }}" class="{{ $input }}" placeholder="Employee name" required>
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Email <span class="text-red-500">*</span></label>
                  <input type="email" name="email" value="{{ old('email') }}" class="{{ $input }}" placeholder="employee@email.com" required>
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Alternate Email (email1)</label>
                  <input type="email" name="email1" value="{{ old('email1') }}" class="{{ $input }}" placeholder="alternate email (optional)">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Password <span class="text-red-500">*</span></label>
                  <input type="password" name="password" class="{{ $input }}" placeholder="Set a password" required>
                  <p class="text-xs text-slate-500 mt-1">Edit page me password optional rakho.</p>
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Phone</label>
                  <input type="text" name="phone" value="{{ old('phone') }}" class="{{ $input }}" placeholder="10 digit number">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Photo</label>
                  <input type="file" name="photo" accept="image/*" class="{{ $file }}">
                </div>

                <div class="md:col-span-2">
                  <label class="text-sm font-medium text-slate-700">Address</label>
                  <textarea name="address" rows="3" class="{{ $textarea }}" placeholder="Full address">{{ old('address') }}</textarea>
                </div>
              </div>
            </div>

            {{-- JOB DETAILS --}}
            <div>
              <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-semibold text-slate-900">Job Details</h3>
                <span class="h-[1px] flex-1 bg-slate-200"></span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                  <label class="text-sm font-medium text-slate-700">Joining Date</label>
                  <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="{{ $input }}">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Designation</label>
                  <input type="text" name="designation" value="{{ old('designation') }}" class="{{ $input }}" placeholder="e.g. Manager">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Responsibility</label>
                  <input type="text" name="responsibility" value="{{ old('responsibility') }}" class="{{ $input }}" placeholder="e.g. Sales / HR / Accounts">
                </div>

                {{-- <div>
                  <label class="text-sm font-medium text-slate-700">Salary</label>
                  <input type="number" step="0.01" name="salary" value="{{ old('salary') }}" class="{{ $input }}" placeholder="0.00">
                </div> --}}
              </div>
            </div>

            {{-- TIMINGS --}}
            <div>
              <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-semibold text-slate-900">Timings</h3>
                <span class="h-[1px] flex-1 bg-slate-200"></span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div>
                  <label class="text-sm font-medium text-slate-700">Check In Time</label>
                  <input type="time" name="check_in_time" value="{{ old('check_in_time') }}" class="{{ $input }}">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Check Out Time</label>
                  <input type="time" name="check_out_time" value="{{ old('check_out_time') }}" class="{{ $input }}">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Office Time (int minutes)</label>
                  <input type="number" name="office_time" value="{{ old('office_time') }}" class="{{ $input }}" placeholder="e.g. 480">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Break</label>
                  <input type="text" name="break" value="{{ old('break') }}" class="{{ $input }}" placeholder="e.g. 30 min">
                </div>
              </div>
            </div>

            {{-- RELATIONS --}}
            {{-- <div>
              <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-semibold text-slate-900">Office / Department / Team</h3>
                <span class="h-[1px] flex-1 bg-slate-200"></span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                  <label class="text-sm font-medium text-slate-700">Office</label>
                  @if(!empty($offices))
                    <select name="office_id" class="{{ $select }}">
                      <option value="">-- Select Office --</option>
                      @foreach($offices as $o)
                        <option value="{{ $o->id }}" @selected(old('office_id')==$o->id)>{{ $o->name ?? ('Office #'.$o->id) }}</option>
                      @endforeach
                    </select>
                  @else
                    <input type="number" name="office_id" value="{{ old('office_id') }}" class="{{ $input }}" placeholder="Office ID (optional)">
                  @endif
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Department</label>
                  @if(!empty($departments))
                    <select name="department_id" class="{{ $select }}">
                      <option value="">-- Select Department --</option>
                      @foreach($departments as $d)
                        <option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name ?? ('Department #'.$d->id) }}</option>
                      @endforeach
                    </select>
                  @else
                    <input type="number" name="department_id" value="{{ old('department_id') }}" class="{{ $input }}" placeholder="Department ID (optional)">
                  @endif
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Team Leader</label>
                  @if(!empty($teamLeaders))
                    <select name="team_leader_id" class="{{ $select }}">
                      <option value="">-- Select Team Leader --</option>
                      @foreach($teamLeaders as $tl)
                        <option value="{{ $tl->id }}" @selected(old('team_leader_id')==$tl->id)>{{ $tl->name ?? ('Employee #'.$tl->id) }}</option>
                      @endforeach
                    </select>
                  @else
                    <input type="number" name="team_leader_id" value="{{ old('team_leader_id') }}" class="{{ $input }}" placeholder="Team Leader ID (optional)">
                  @endif
                </div>
              </div>
            </div> --}}

            {{-- IDs & NUMBERS --}}
            <div>
              <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-semibold text-slate-900">Employee IDs & Numbers</h3>
                <span class="h-[1px] flex-1 bg-slate-200"></span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                  <label class="text-sm font-medium text-slate-700">Employee ID</label>
                  <input type="text" name="employee_id" value="{{ old('employee_id') }}" class="{{ $input }}" placeholder="EMP001">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">UAN Number</label>
                  <input type="text" name="uan_number" value="{{ old('uan_number') }}" class="{{ $input }}">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">ESIC Number</label>
                  <input type="text" name="esic_number" value="{{ old('esic_number') }}" class="{{ $input }}">
                </div>

                <div class="md:col-span-3">
                  <label class="text-sm font-medium text-slate-700">Account Number</label>
                  <input type="text" name="account_number" value="{{ old('account_number') }}" class="{{ $input }}">
                </div>
              </div>
            </div>

            {{-- DOCUMENTS --}}
            <div>
              <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-semibold text-slate-900">Documents</h3>
                <span class="h-[1px] flex-1 bg-slate-200"></span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                  <label class="text-sm font-medium text-slate-700">Aadhaar Attachment</label>
                  <input type="file" name="aadhar_attachment" class="{{ $file }}">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">PAN Attachment</label>
                  <input type="file" name="pan_attachment" class="{{ $file }}">
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Other Attachment</label>
                  <input type="file" name="other_attachment" class="{{ $file }}">
                </div>
              </div>
            </div>

            {{-- STATUS / SETTINGS --}}
            {{-- <div>
              <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-semibold text-slate-900">Status & Settings</h3>
                <span class="h-[1px] flex-1 bg-slate-200"></span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                  <label class="text-sm font-medium text-slate-700">Status</label>
                  <select name="status" class="{{ $select }}">
                    <option value="1" @selected(old('status','1')=='1')>Active (1)</option>
                    <option value="0" @selected(old('status')=='0')>Inactive (0)</option>
                  </select>
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Is Accepted</label>
                  <select name="is_accepted" class="{{ $select }}">
                    <option value="0" @selected(old('is_accepted','0')=='0')>No (0)</option>
                    <option value="1" @selected(old('is_accepted')=='1')>Yes (1)</option>
                  </select>
                </div>

                <div>
                  <label class="text-sm font-medium text-slate-700">Location Required</label>
                  <select name="location_required" class="{{ $select }}">
                    <option value="no"  @selected(old('location_required','no')=='no')>No</option>
                    <option value="yes" @selected(old('location_required')=='yes')>Yes</option>
                  </select>
                </div>
              </div>
            </div> --}}

            {{-- ACTIONS --}}
            <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-end">
              <a href="{{ route('mainpage') }}"
                 class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Back
              </a>

              <button type="submit"
                      class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-black to-red-600 px-8 py-3 text-sm font-semibold text-white shadow-lg hover:opacity-95">
                Save Employee
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </section>
</div>
@endsection
