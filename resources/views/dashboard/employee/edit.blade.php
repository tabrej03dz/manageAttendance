@extends('dashboard.layout.root')

@section('title', 'Edit Employee')


@push('styles')
<style>
    .employee-page{font-family:'Inter',sans-serif;color:#0f172a}
    .employee-hero{position:relative;overflow:hidden;border:1px solid #312e81;border-radius:26px;background:linear-gradient(135deg,#0f172a 0%,#172554 52%,#312e81 100%);box-shadow:0 22px 55px rgba(15,23,42,.28);isolation:isolate}
    .employee-hero:before{content:'';position:absolute;width:320px;height:320px;right:-100px;top:-125px;border-radius:999px;background:rgba(99,102,241,.34);filter:blur(12px);z-index:-1}
    .employee-hero:after{content:'';position:absolute;width:280px;height:280px;left:36%;bottom:-185px;border-radius:999px;background:rgba(6,182,212,.18);filter:blur(18px);z-index:-1}
    .employee-hero-badge{display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,.24);border-radius:999px;background:rgba(15,23,42,.55);color:#fff;padding:7px 12px;font-size:12px;font-weight:700}
    .employee-card{overflow:hidden;border:1px solid #d8e0ea;border-radius:22px;background:#fff;box-shadow:0 10px 30px rgba(15,23,42,.10)}
    .employee-card-header{border-bottom:1px solid #e2e8f0;background:linear-gradient(90deg,rgba(238,242,255,.88),rgba(236,254,255,.65))}
    .section-card{border:1px solid #e2e8f0;border-radius:18px;background:#fff;overflow:hidden}
    .section-title{display:flex;align-items:center;gap:12px;border-bottom:1px solid #e2e8f0;background:#f8fafc;padding:16px 18px}
    .section-icon{display:flex;width:42px;height:42px;align-items:center;justify-content:center;border-radius:13px;background:#e0e7ff;color:#4338ca;flex-shrink:0}
    .employee-label{display:block;margin-bottom:7px;color:#334155;font-size:13px;font-weight:800}
    .employee-input,.employee-select,.employee-textarea{width:100%;border:1px solid #dbe3ee!important;border-radius:13px!important;background:#fff!important;color:#0f172a!important;padding:11px 13px!important;font-size:13px!important;font-weight:500;outline:none;transition:border-color .2s,box-shadow .2s}
    .employee-input,.employee-select{min-height:46px}
    .employee-textarea{min-height:92px;resize:vertical}
    .employee-input:focus,.employee-select:focus,.employee-textarea:focus{border-color:#818cf8!important;box-shadow:0 0 0 4px rgba(99,102,241,.12)!important}
    .employee-input.input-error,.employee-select.input-error,.employee-textarea.input-error{border-color:#fb7185!important;background:#fff1f2!important}
    .employee-help{margin-top:6px;color:#64748b;font-size:11px;font-weight:500}
    .employee-error{display:flex;align-items:flex-start;gap:7px;margin-top:7px;color:#e11d48;font-size:12px;font-weight:700}
    .error-summary{border:1px solid #fecdd3;border-radius:16px;background:#fff1f2;padding:16px;color:#9f1239}
    .action-primary,.action-secondary,.action-dark,.action-warning{display:inline-flex;min-height:45px;align-items:center;justify-content:center;gap:8px;border-radius:13px;padding:10px 17px;font-size:13px;font-weight:800;text-decoration:none!important;transition:transform .2s,box-shadow .2s,filter .2s}
    .action-primary{border:0;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff!important;box-shadow:0 12px 25px rgba(79,70,229,.24)}
    .action-primary:hover{color:#fff!important;transform:translateY(-2px);filter:brightness(1.06);box-shadow:0 17px 32px rgba(79,70,229,.31)}
    .action-secondary{border:1px solid #dbe3ee;background:#f8fafc;color:#475569!important}
    .action-secondary:hover{color:#0f172a!important;background:#f1f5f9;transform:translateY(-2px)}
    .action-dark{border:1px solid #334155;background:#0f172a;color:#fff!important}
    .action-warning{border:1px solid #fde68a;background:#fffbeb;color:#92400e!important}
    .radio-card{display:flex;align-items:center;gap:10px;border:1px solid #e2e8f0;border-radius:13px;background:#f8fafc;padding:12px 14px;cursor:pointer}
    .radio-card:has(input:checked){border-color:#818cf8;background:#eef2ff}
    .table-scroll::-webkit-scrollbar{width:7px;height:7px}.table-scroll::-webkit-scrollbar-thumb{background:linear-gradient(90deg,#818cf8,#06b6d4);border-radius:999px}.table-scroll::-webkit-scrollbar-track{background:#f1f5f9;border-radius:999px}
    .employee-table{width:100%;min-width:1100px;border-collapse:separate;border-spacing:0}.employee-table th{border-bottom:1px solid #e2e8f0;background:#f8fafc;padding:15px 16px;text-align:left;color:#64748b;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}.employee-table td{border-bottom:1px solid #f1f5f9;padding:15px 16px;color:#334155;font-size:13px;vertical-align:middle}.employee-table tbody tr:hover{background:#f8fafc}.employee-table tbody tr:last-child td{border-bottom:0}
    .icon-action{display:inline-flex;width:39px;height:39px;align-items:center;justify-content:center;border:0;border-radius:11px;color:#fff!important;text-decoration:none!important;box-shadow:0 8px 18px rgba(15,23,42,.14);transition:transform .2s,filter .2s}
    .icon-action:hover{color:#fff!important;transform:translateY(-2px);filter:brightness(1.06)}
    .filter-control{width:100%;min-height:44px;border:1px solid #dbe3ee!important;border-radius:12px!important;background:#fff!important;color:#0f172a!important;padding:10px 12px!important;font-size:13px!important;outline:none}.filter-control:focus{border-color:#818cf8!important;box-shadow:0 0 0 4px rgba(99,102,241,.12)!important}
    @media(max-width:767px){.employee-hero{border-radius:20px}.employee-card{border-radius:18px}.action-primary,.action-secondary{width:100%}}
</style>
@endpush


@section('content')
<div class="employee-page space-y-6 pb-10">

    <section class="employee-hero p-6 sm:p-8">
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <span class="employee-hero-badge"><span class="h-2 w-2 animate-pulse rounded-full bg-cyan-400"></span>Employee Management</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Edit Employee</h1>
                <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">Update {{ $employee->name }}'s profile, attendance settings, role और salary details.</p>
                <div class="mt-4 flex flex-wrap gap-3 text-xs font-bold text-slate-200">
                    <span class="rounded-full bg-white/10 px-3 py-2"><i class="fas fa-id-badge mr-2 text-indigo-300"></i>{{ $employee->employee_id ?: 'No Employee ID' }}</span>
                    <span class="rounded-full bg-white/10 px-3 py-2"><i class="fas fa-building mr-2 text-cyan-300"></i>{{ $employee->office?->name ?? 'No Office' }}</span>
                </div>
            </div>
            <a href="{{ route('employee.index') }}" class="action-secondary bg-white"><i class="fas fa-arrow-left"></i>Back to Employees</a>
        </div>
    </section>

    @if($errors->any())
        <div class="error-summary">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle mt-1"></i>
                <div><p class="font-extrabold">Please correct the following errors:</p><ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-semibold">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            </div>
        </div>
    @endif

    <form action="{{ route('employee.update', ['employee' => $employee->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <section class="employee-card">
            <div class="employee-card-header px-5 py-5 sm:px-6">
                <h2 class="text-lg font-extrabold text-slate-900">Update Employee Profile</h2>
                <p class="mt-1 text-sm font-medium text-slate-500">Existing values are already filled. Change only the required information.</p>
            </div>

            <div class="space-y-6 p-5 sm:p-6">
                <div class="section-card">
                    <div class="section-title"><div class="section-icon"><i class="fas fa-user"></i></div><div><h3 class="font-extrabold text-slate-900">Personal Information</h3><p class="text-xs font-medium text-slate-500">Identity and contact details</p></div></div>
                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                        <div><label class="employee-label" for="name">Full Name <span class="text-rose-500">*</span></label><input class="employee-input" id="name" name="name" type="text" value="{{ old('name', $employee->name) }}" required></div>
                        <div><label class="employee-label" for="email">Email</label><input class="employee-input" id="email" name="email" type="email" value="{{ old('email', $employee->email) }}"></div>
                        <div><label class="employee-label" for="phone">Phone</label><input class="employee-input" id="phone" name="phone" type="text" value="{{ old('phone', $employee->phone) }}"></div>
                        <div><label class="employee-label" for="joining_date">Joining Date</label><input class="employee-input" id="joining_date" name="joining_date" type="date" value="{{ old('joining_date', $employee->joining_date) }}"></div>
                        <div class="md:col-span-2"><label class="employee-label" for="address">Full Address</label><textarea class="employee-textarea" id="address" name="address">{{ old('address', $employee->address) }}</textarea></div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title"><div class="section-icon"><i class="fas fa-camera"></i></div><div><h3 class="font-extrabold text-slate-900">Profile Photo</h3><p class="text-xs font-medium text-slate-500">Upload only when the existing photo needs replacement</p></div></div>
                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-[120px_1fr] md:items-center">
                        <div>
                            <img src="{{ $employee->photo ? asset('storage/'.$employee->photo) : 'https://ui-avatars.com/api/?name='.urlencode($employee->name).'&background=4f46e5&color=fff&size=160' }}" alt="{{ $employee->name }}" class="h-24 w-24 rounded-2xl border-2 border-indigo-100 object-cover shadow">
                        </div>
                        <div><label class="employee-label" for="photo">New Photo</label><input class="employee-input" id="photo" name="photo" type="file" accept="image/*"><p class="employee-help">Leave empty to keep the current photo.</p></div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title"><div class="section-icon"><i class="fas fa-briefcase"></i></div><div><h3 class="font-extrabold text-slate-900">Employment Details</h3><p class="text-xs font-medium text-slate-500">Department, office, role and reporting information</p></div></div>
                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                        <div><label class="employee-label" for="department_id">Department</label><select class="employee-select" name="department_id" id="department_id"><option value="">Choose Department</option>@foreach($departments as $department)<option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach</select></div>
                        <div><label class="employee-label" for="designation">Designation</label><input class="employee-input" id="designation" name="designation" type="text" value="{{ old('designation', $employee->designation) }}"></div>
                        <div><label class="employee-label" for="office_id">Office <span class="text-rose-500">*</span></label><select class="employee-select" name="office_id" id="office_id" required><option value="">Select Office</option>@foreach($offices as $office)<option value="{{ $office->id }}" {{ old('office_id', $employee->office_id) == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>@endforeach</select></div>
                        <div><label class="employee-label" for="role">Role</label><select class="employee-select" name="role" id="role" required><option value="">Select Role</option><option value="admin" {{ $employee->hasRole('admin') ? 'selected' : '' }}>Admin</option><option value="employee" {{ $employee->hasRole('employee') ? 'selected' : '' }}>Employee</option><option value="team_leader" {{ $employee->hasRole('team_leader') ? 'selected' : '' }}>Team Leader</option></select></div>
                        <div><label class="employee-label" for="team_leader_id">Team Leader</label><select class="employee-select" name="team_leader_id" id="team_leader_id"><option value="">Select Team Leader</option>@foreach($teamLeaders as $leader)<option value="{{ $leader->id }}" {{ old('team_leader_id', $employee->team_leader_id) == $leader->id ? 'selected' : '' }}>{{ $leader->name }}</option>@endforeach</select></div>
                        <div><label class="employee-label" for="status">Status</label><select class="employee-select" name="status" id="status"><option value="1" {{ old('status', $employee->status) == 1 ? 'selected' : '' }}>Active</option><option value="0" {{ old('status', $employee->status) == 0 ? 'selected' : '' }}>Inactive</option></select></div>
                        <div><label class="employee-label" for="salary">Monthly Salary</label><input class="employee-input" id="salary" name="salary" type="number" step="0.01" value="{{ old('salary', $employee->salary) }}"></div>
                        <div class="md:col-span-2"><label class="employee-label" for="responsibility">Responsibility</label><textarea class="employee-textarea" id="responsibility" name="responsibility">{{ old('responsibility', $employee->responsibility) }}</textarea></div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title"><div class="section-icon"><i class="fas fa-clock"></i></div><div><h3 class="font-extrabold text-slate-900">Attendance Settings</h3><p class="text-xs font-medium text-slate-500">Working schedule and location rule</p></div></div>
                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                        <div><label class="employee-label" for="check_in_time">Check In Time</label><input class="employee-input" id="check_in_time" name="check_in_time" type="time" value="{{ old('check_in_time', $employee->check_in_time ? \Carbon\Carbon::parse($employee->check_in_time)->format('H:i') : '') }}"></div>
                        <div><label class="employee-label" for="check_out_time">Check Out Time</label><input class="employee-input" id="check_out_time" name="check_out_time" type="time" value="{{ old('check_out_time', $employee->check_out_time ? \Carbon\Carbon::parse($employee->check_out_time)->format('H:i') : '') }}"></div>
                        <div class="md:col-span-2"><span class="employee-label">Location Required</span><div class="grid grid-cols-1 gap-3 sm:grid-cols-2"><label class="radio-card"><input type="radio" value="yes" name="location_required" {{ old('location_required', $employee->location_required ?? 'no') === 'yes' ? 'checked' : '' }}><span class="font-bold text-slate-700">Yes, location is required</span></label><label class="radio-card"><input type="radio" value="no" name="location_required" {{ old('location_required', $employee->location_required ?? 'no') === 'no' ? 'checked' : '' }}><span class="font-bold text-slate-700">No location restriction</span></label></div></div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title"><div class="section-icon"><i class="fas fa-lock"></i></div><div><h3 class="font-extrabold text-slate-900">Password</h3><p class="text-xs font-medium text-slate-500">Leave both fields empty to keep the current password</p></div></div>
                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                        <div><label class="employee-label" for="password">New Password</label><input class="employee-input" id="password" name="password" type="password" autocomplete="new-password"></div>
                        <div><label class="employee-label" for="confirm_password">Confirm Password</label><input class="employee-input" id="confirm_password" name="confirm_password" type="password" autocomplete="new-password"></div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title"><div class="section-icon"><i class="fas fa-id-card"></i></div><div><h3 class="font-extrabold text-slate-900">Official & Bank Details</h3><p class="text-xs font-medium text-slate-500">Employee, statutory and account identifiers</p></div></div>
                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 lg:grid-cols-4">
                        <div><label class="employee-label" for="employee_id">Employee ID</label><input class="employee-input" id="employee_id" name="employee_id" type="text" value="{{ old('employee_id', $employee->employee_id) }}"></div>
                        <div><label class="employee-label" for="uan_number">UAN Number</label><input class="employee-input" id="uan_number" name="uan_number" type="text" value="{{ old('uan_number', $employee->uan_number) }}"></div>
                        <div><label class="employee-label" for="esic_number">ESIC Number</label><input class="employee-input" id="esic_number" name="esic_number" type="text" value="{{ old('esic_number', $employee->esic_number) }}"></div>
                        <div><label class="employee-label" for="account_number">Account Number</label><input class="employee-input" id="account_number" name="account_number" type="text" value="{{ old('account_number', $employee->account_number) }}"></div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title"><div class="section-icon"><i class="fas fa-wallet"></i></div><div><h3 class="font-extrabold text-slate-900">Salary Details</h3><p class="text-xs font-medium text-slate-500">Pay components, allowances and deductions</p></div></div>
                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 lg:grid-cols-3">
                        <div><label class="employee-label" for="basic_salary">Basic Pay</label><input class="employee-input" id="basic_salary" name="basic_salary" type="number" step="0.01" value="{{ old('basic_salary', $employee->userSalary?->basic_salary) }}"></div>
                        <div><label class="employee-label" for="dearness_allowance">Dearness Allowance</label><input class="employee-input" id="dearness_allowance" name="dearness_allowance" type="number" step="0.01" value="{{ old('dearness_allowance', $employee->userSalary?->dearness_allowance) }}"></div>
                        <div><label class="employee-label" for="relieving_charge">Relieving Charge</label><input class="employee-input" id="relieving_charge" name="relieving_charge" type="number" step="0.01" value="{{ old('relieving_charge', $employee->userSalary?->relieving_charge) }}"></div>
                        <div><label class="employee-label" for="additional_allowance">Additional Allowance</label><input class="employee-input" id="additional_allowance" name="additional_allowance" type="number" step="0.01" value="{{ old('additional_allowance', $employee->userSalary?->additional_allowance) }}"></div>
                        <div><label class="employee-label" for="provident_fund">Provident Fund %</label><input class="employee-input" id="provident_fund" name="provident_fund" type="number" step="0.01" value="{{ old('provident_fund', $employee->userSalary?->provident_fund) }}"></div>
                        <div><label class="employee-label" for="employee_state_insurance_corporation">ESIC %</label><input class="employee-input" id="employee_state_insurance_corporation" name="employee_state_insurance_corporation" type="number" step="0.01" value="{{ old('employee_state_insurance_corporation', $employee->userSalary?->employee_state_insurance_corporation) }}"></div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('employee.index') }}" class="action-secondary"><i class="fas fa-times"></i>Cancel</a>
                    <button type="submit" class="action-primary"><i class="fas fa-save"></i>Update Employee</button>
                </div>
            </div>
        </section>
    </form>
</div>
@endsection
