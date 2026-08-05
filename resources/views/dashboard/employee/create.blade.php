@extends('dashboard.layout.root')

@section('title', 'Employee Registration')

@push('styles')
<style>
    .employee-register-page {
        font-family: Arial, Helvetica, sans-serif;
        color: #333;
        font-size: 12px;
    }

    .registration-summary,
    .form-panel {
        background: #fff;
        border: 1px solid #bfc4c9;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .20);
    }

    .registration-summary {
        padding: 10px 14px;
        margin-bottom: 12px;
    }

    .registration-summary-grid {
        display: grid;
        grid-template-columns: 130px minmax(0, 1fr);
        gap: 18px;
        align-items: center;
    }

    .registration-avatar {
        width: 112px;
        height: 112px;
        border-radius: 26px;
        object-fit: cover;
        border: 1px solid #c7ccd1;
        background: #e7ebee;
        box-shadow: 0 4px 12px rgba(0,0,0,.16);
    }

    .registration-title {
        margin: 0 0 10px;
        color: #4b4b4b;
        font-size: 22px;
        font-weight: 700;
        line-height: 1.1;
        text-transform: uppercase;
    }

    .summary-fields {
        display: grid;
        grid-template-columns: repeat(4, minmax(130px, 1fr));
        gap: 8px 24px;
    }

    .summary-label {
        display: block;
        margin-bottom: 2px;
        color: #555;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .summary-value {
        color: #777;
        font-size: 11px;
        line-height: 1.25;
    }

    .completion-text {
        margin-top: 9px;
        color: #d90000;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .page-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        align-items: start;
    }

    .form-panel {
        overflow: hidden;
        margin-bottom: 10px;
    }

    .form-panel.full-width {
        grid-column: 1 / -1;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 35px;
        padding: 5px 12px;
        border-bottom: 1px solid #d1d1d1;
        background: linear-gradient(#fff, #f4f4f4);
    }

    .panel-title {
        margin: 0;
        color: #111;
        font-size: 16px;
        font-weight: 700;
    }

    .panel-edit {
        color: #111;
        font-size: 11px;
        font-weight: 700;
    }

    .panel-body {
        padding: 10px 12px 12px;
    }

    .compact-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 18px;
        row-gap: 7px;
    }

    .compact-grid.three {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .field-row {
        display: grid;
        grid-template-columns: 100px minmax(0, 1fr);
        gap: 8px;
        align-items: center;
        min-width: 0;
    }

    .field-row.top {
        align-items: start;
    }

    .field-row.full {
        grid-column: 1 / -1;
    }

    .field-label {
        color: #555;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.2;
    }

    .required {
        color: #d00000;
    }

    .form-control-compact {
        width: 100%;
        height: 28px;
        min-width: 0;
        padding: 3px 8px;
        border: 1px solid #cfd3d7 !important;
        border-radius: 0 !important;
        background: #fff !important;
        color: #444 !important;
        font-size: 11px !important;
        outline: none;
        box-shadow: none !important;
    }

    textarea.form-control-compact {
        height: 70px;
        padding-top: 7px;
        resize: vertical;
    }

    input[type="file"].form-control-compact {
        height: auto;
        min-height: 30px;
        padding: 4px;
    }

    .form-control-compact:focus {
        border-color: #7c9ab7 !important;
        box-shadow: 0 0 0 1px #7c9ab7 !important;
    }

    .has-error {
        border-color: #dc2626 !important;
        background: #fff5f5 !important;
    }

    .field-error {
        grid-column: 2;
        margin-top: -3px;
        color: #dc2626;
        font-size: 10px;
        font-weight: 700;
    }

    .radio-group {
        display: flex;
        gap: 18px;
        align-items: center;
        min-height: 28px;
    }

    .radio-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #444;
        font-size: 11px;
        font-weight: 600;
    }

    .error-summary {
        margin-bottom: 10px;
        padding: 10px 13px;
        border: 1px solid #e6a3a3;
        background: #fff0f0;
        color: #9b1515;
        font-size: 12px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 10px 12px;
        border-top: 1px solid #d3d3d3;
        background: #f7f7f7;
    }

    .btn-compact {
        display: inline-flex;
        min-width: 118px;
        height: 32px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 14px;
        border: 1px solid transparent;
        border-radius: 2px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none !important;
        cursor: pointer;
    }

    .btn-save {
        border-color: #315f8c;
        background: #3f78ad;
        color: #fff !important;
    }

    .btn-cancel {
        border-color: #aaa;
        background: #fff;
        color: #333 !important;
    }

    @media (max-width: 1100px) {
        .summary-fields,
        .compact-grid.three {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .registration-summary-grid,
        .page-form-grid,
        .summary-fields,
        .compact-grid,
        .compact-grid.three {
            grid-template-columns: 1fr;
        }

        .form-panel.full-width {
            grid-column: auto;
        }
    }

    @media (max-width: 520px) {
        .field-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }

        .field-error {
            grid-column: 1;
        }

        .registration-avatar {
            width: 96px;
            height: 96px;
        }

        .registration-title {
            font-size: 18px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn-compact {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="employee-register-page">

    <section class="registration-summary">
        <div class="registration-summary-grid">
            <div>
                <img
                    id="employeePhotoPreview"
                    class="registration-avatar"
                    src="https://ui-avatars.com/api/?name=New+Employee&background=d8e0e6&color=333&size=200"
                    alt="New Employee"
                >
            </div>

            <div>
                <h1 class="registration-title" id="employeePreviewName">
                    NEW EMPLOYEE REGISTRATION
                </h1>

                <div class="summary-fields">
                    <div>
                        <span class="summary-label">Department</span>
                        <div class="summary-value" id="departmentPreview">Not Selected</div>
                    </div>

                    <div>
                        <span class="summary-label">Designation</span>
                        <div class="summary-value" id="designationPreview">Not Entered</div>
                    </div>

                    <div>
                        <span class="summary-label">Role</span>
                        <div class="summary-value" id="rolePreview">EMPLOYEE</div>
                    </div>

                    <div>
                        <span class="summary-label">Branch</span>
                        <div class="summary-value" id="officePreview">Not Selected</div>
                    </div>

                    <div>
                        <span class="summary-label">Reporting Manager</span>
                        <div class="summary-value" id="leaderPreview">Not Selected</div>
                    </div>

                    <div>
                        <span class="summary-label">Leave Authority</span>
                        <div class="summary-value" id="leaveAuthorityPreview">Not Selected</div>
                    </div>

                    <div>
                        <span class="summary-label">Joining Date</span>
                        <div class="summary-value" id="joiningPreview">Not Entered</div>
                    </div>

                    <div>
                        <span class="summary-label">Status</span>
                        <div class="summary-value">Active</div>
                    </div>

                    <div>
                        <span class="summary-label">Location Rule</span>
                        <div class="summary-value" id="locationPreview">Not Required</div>
                    </div>
                </div>

                <div class="completion-text">
                    Complete all required fields before registering the employee
                </div>
            </div>
        </div>
    </section>

    @if($errors->any())
        <div class="error-summary">
            <strong>Please correct the following errors:</strong>
            <ul style="margin:6px 0 0 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('employee.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="page-form-grid">

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Primary Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Add</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row">
                            <label class="field-label" for="name">
                                Full Name <span class="required">*</span>
                            </label>
                            <input
                                class="form-control-compact @error('name') has-error @enderror"
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                            >
                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="email">Email</label>
                            <input
                                class="form-control-compact @error('email') has-error @enderror"
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                            >
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="phone">
                                Phone <span class="required">*</span>
                            </label>
                            <input
                                class="form-control-compact @error('phone') has-error @enderror"
                                id="phone"
                                name="phone"
                                type="text"
                                value="{{ old('phone') }}"
                                required
                            >
                            @error('phone')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="dob">Date of Birth</label>
                            <input
                                class="form-control-compact @error('dob') has-error @enderror"
                                id="dob"
                                name="dob"
                                type="date"
                                value="{{ old('dob') }}"
                            >
                            @error('dob')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="joining_date">Joining Date</label>
                            <input
                                class="form-control-compact @error('joining_date') has-error @enderror"
                                id="joining_date"
                                name="joining_date"
                                type="date"
                                value="{{ old('joining_date') }}"
                            >
                            @error('joining_date')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="employee_id">Employee ID</label>
                            <input
                                class="form-control-compact @error('employee_id') has-error @enderror"
                                id="employee_id"
                                name="employee_id"
                                type="text"
                                value="{{ old('employee_id') }}"
                            >
                            @error('employee_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Address Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Add</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row top full">
                            <label class="field-label" for="address">Current Address</label>
                            <textarea
                                class="form-control-compact @error('address') has-error @enderror"
                                id="address"
                                name="address"
                            >{{ old('address') }}</textarea>
                            @error('address')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Employment Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Add</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row">
                            <label class="field-label" for="department_id">Department</label>
                            <select
                                class="form-control-compact @error('department_id') has-error @enderror"
                                name="department_id"
                                id="department_id"
                            >
                                <option value="">Select</option>
                                @foreach($departments as $department)
                                    <option
                                        value="{{ $department->id }}"
                                        {{ (string) old('department_id') === (string) $department->id ? 'selected' : '' }}
                                    >
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="designation">Designation</label>
                            <input
                                class="form-control-compact @error('designation') has-error @enderror"
                                id="designation"
                                name="designation"
                                type="text"
                                value="{{ old('designation') }}"
                            >
                            @error('designation')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="role">Role</label>
                            <select
                                class="form-control-compact @error('role') has-error @enderror"
                                name="role"
                                id="role"
                            >
                                <option value="">Select</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="team_leader" {{ old('role') === 'team_leader' ? 'selected' : '' }}>
                                    Team Leader
                                </option>
                                <option value="employee" {{ old('role', 'employee') === 'employee' ? 'selected' : '' }}>
                                    Employee
                                </option>
                            </select>
                            @error('role')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="office_id">
                                Office <span class="required">*</span>
                            </label>
                            <select
                                class="form-control-compact @error('office_id') has-error @enderror"
                                name="office_id"
                                id="office_id"
                                required
                            >
                                <option value="">Select</option>
                                @foreach($offices as $office)
                                    <option
                                        value="{{ $office->id }}"
                                        {{ (string) old('office_id', $loop->first ? $office->id : null) === (string) $office->id ? 'selected' : '' }}
                                    >
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('office_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="team_leader_id">Reporting Manager</label>
                            <select
                                class="form-control-compact @error('team_leader_id') has-error @enderror"
                                name="team_leader_id"
                                id="team_leader_id"
                            >
                                <option value="">Select</option>
                                @foreach($teamLeaders as $leader)
                                    <option
                                        value="{{ $leader->id }}"
                                        data-office-id="{{ $leader->office_id }}"
                                        {{ (string) old('team_leader_id') === (string) $leader->id ? 'selected' : '' }}
                                    >
                                        {{ $leader->name }}
                                        @if($leader->office)
                                            - {{ $leader->office->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('team_leader_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="leave_authority_id">Leave Authority</label>
                            <select
                                class="form-control-compact @error('leave_authority_id') has-error @enderror"
                                name="leave_authority_id"
                                id="leave_authority_id"
                            >
                                <option value="">Select</option>
                                @foreach($teamLeaders as $teamLeader)
                                    <option
                                        value="{{ $teamLeader->id }}"
                                        data-office-id="{{ $teamLeader->office_id }}"
                                        {{ (string) old('leave_authority_id') === (string) $teamLeader->id ? 'selected' : '' }}
                                    >
                                        {{ $teamLeader->name }}
                                        @if($teamLeader->office)
                                            - {{ $teamLeader->office->name }}
                                        @else
                                            - Global
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_authority_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="status">
                                Status <span class="required">*</span>
                            </label>

                            <select
                                class="form-control-compact @error('status') has-error @enderror"
                                name="status"
                                id="status"
                                required
                            >
                                <option
                                    value="1"
                                    {{ (string) old('status', '1') === '1' ? 'selected' : '' }}
                                >
                                    Active
                                </option>

                                <option
                                    value="0"
                                    {{ (string) old('status', '1') === '0' ? 'selected' : '' }}
                                >
                                    Inactive
                                </option>
                            </select>

                            @error('status')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="salary">Monthly Salary</label>
                            <input
                                class="form-control-compact @error('salary') has-error @enderror"
                                id="salary"
                                name="salary"
                                type="number"
                                step="0.01"
                                value="{{ old('salary') }}"
                            >
                            @error('salary')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row top full">
                            <label class="field-label" for="responsibility">Responsibility</label>
                            <textarea
                                class="form-control-compact @error('responsibility') has-error @enderror"
                                id="responsibility"
                                name="responsibility"
                            >{{ old('responsibility') }}</textarea>
                            @error('responsibility')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Attendance Settings</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Add</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row">
                            <label class="field-label" for="check_in_time">
                                Check In <span class="required">*</span>
                            </label>
                            <input
                                class="form-control-compact @error('check_in_time') has-error @enderror"
                                id="check_in_time"
                                name="check_in_time"
                                type="time"
                                value="{{ old('check_in_time') }}"
                            >
                            @error('check_in_time')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="check_out_time">
                                Check Out <span class="required">*</span>
                            </label>
                            <input
                                class="form-control-compact @error('check_out_time') has-error @enderror"
                                id="check_out_time"
                                name="check_out_time"
                                type="time"
                                value="{{ old('check_out_time') }}"
                            >
                            @error('check_out_time')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="break">
                                Break Minutes <span class="required">*</span>
                            </label>
                            <input
                                class="form-control-compact @error('break') has-error @enderror"
                                id="break"
                                name="break"
                                type="number"
                                value="{{ old('break') }}"
                            >
                            @error('break')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <span class="field-label">Location Required</span>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input
                                        type="radio"
                                        value="yes"
                                        name="location_required"
                                        {{ old('location_required', 'no') === 'yes' ? 'checked' : '' }}
                                    >
                                    Yes
                                </label>

                                <label class="radio-label">
                                    <input
                                        type="radio"
                                        value="no"
                                        name="location_required"
                                        {{ old('location_required', 'no') === 'no' ? 'checked' : '' }}
                                    >
                                    No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Photo & Documents</h2>
                    <span class="panel-edit"><i class="fas fa-paperclip"></i> Upload</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row top">
                            <label class="field-label" for="photo">Employee Photo</label>
                            <input
                                class="form-control-compact @error('photo') has-error @enderror"
                                id="photo"
                                name="photo"
                                type="file"
                                accept="image/*"
                            >
                            @error('photo')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row top">
                            <label class="field-label" for="aadhar_attachment">Aadhaar File</label>
                            <input
                                class="form-control-compact @error('aadhar_attachment') has-error @enderror"
                                id="aadhar_attachment"
                                name="aadhar_attachment"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp,.pdf"
                            >
                            @error('aadhar_attachment')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row top">
                            <label class="field-label" for="pan_attachment">PAN File</label>
                            <input
                                class="form-control-compact @error('pan_attachment') has-error @enderror"
                                id="pan_attachment"
                                name="pan_attachment"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp,.pdf"
                            >
                            @error('pan_attachment')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row top">
                            <label class="field-label" for="other_attachment">Other File</label>
                            <input
                                class="form-control-compact @error('other_attachment') has-error @enderror"
                                id="other_attachment"
                                name="other_attachment"
                                type="file"
                            >
                            @error('other_attachment')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Official Identifiers</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Add</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row">
                            <label class="field-label" for="uan_number">UAN Number</label>
                            <input
                                class="form-control-compact @error('uan_number') has-error @enderror"
                                id="uan_number"
                                name="uan_number"
                                type="text"
                                value="{{ old('uan_number') }}"
                            >
                            @error('uan_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="esic_number">ESIC Number</label>
                            <input
                                class="form-control-compact @error('esic_number') has-error @enderror"
                                id="esic_number"
                                name="esic_number"
                                type="text"
                                value="{{ old('esic_number') }}"
                            >
                            @error('esic_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Salary Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Add</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid three">
                        <div class="field-row">
                            <label class="field-label" for="basic_salary">Basic Pay</label>
                            <input
                                class="form-control-compact @error('basic_salary') has-error @enderror"
                                id="basic_salary"
                                name="basic_salary"
                                type="number"
                                step="0.01"
                                value="{{ old('basic_salary') }}"
                            >
                            @error('basic_salary')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="dearness_allowance">D.A.</label>
                            <input
                                class="form-control-compact @error('dearness_allowance') has-error @enderror"
                                id="dearness_allowance"
                                name="dearness_allowance"
                                type="number"
                                step="0.01"
                                value="{{ old('dearness_allowance') }}"
                            >
                            @error('dearness_allowance')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="relieving_charge">Relieving Charge</label>
                            <input
                                class="form-control-compact @error('relieving_charge') has-error @enderror"
                                id="relieving_charge"
                                name="relieving_charge"
                                type="number"
                                step="0.01"
                                value="{{ old('relieving_charge') }}"
                            >
                            @error('relieving_charge')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="additional_allowance">Additional Allowance</label>
                            <input
                                class="form-control-compact @error('additional_allowance') has-error @enderror"
                                id="additional_allowance"
                                name="additional_allowance"
                                type="number"
                                step="0.01"
                                value="{{ old('additional_allowance') }}"
                            >
                            @error('additional_allowance')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="provident_fund">Provident Fund %</label>
                            <input
                                class="form-control-compact @error('provident_fund') has-error @enderror"
                                id="provident_fund"
                                name="provident_fund"
                                type="number"
                                step="0.01"
                                value="{{ old('provident_fund') }}"
                            >
                            @error('provident_fund')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="employee_state_insurance_corporation">ESIC %</label>
                            <input
                                class="form-control-compact @error('employee_state_insurance_corporation') has-error @enderror"
                                id="employee_state_insurance_corporation"
                                name="employee_state_insurance_corporation"
                                type="number"
                                step="0.01"
                                value="{{ old('employee_state_insurance_corporation') }}"
                            >
                            @error('employee_state_insurance_corporation')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('employee.index') }}" class="btn-compact btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>

                    <button type="submit" class="btn-compact btn-save">
                        <i class="fas fa-user-plus"></i> Register Employee
                    </button>
                </div>
            </section>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');
    const photoInput = document.getElementById('photo');
    const department = document.getElementById('department_id');
    const designation = document.getElementById('designation');
    const role = document.getElementById('role');
    const office = document.getElementById('office_id');
    const leader = document.getElementById('team_leader_id');
    const leaveAuthority = document.getElementById('leave_authority_id');
    const joiningDate = document.getElementById('joining_date');

    const setText = (id, value, fallback) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value && value.trim() !== '' ? value : fallback;
        }
    };

    const selectedText = (select) => {
        if (!select || select.selectedIndex < 0) return '';
        return select.options[select.selectedIndex]?.text || '';
    };

    const updateSummary = () => {
        setText(
            'employeePreviewName',
            nameInput?.value ? nameInput.value.toUpperCase() : '',
            'NEW EMPLOYEE REGISTRATION'
        );

        setText(
            'departmentPreview',
            department?.value ? selectedText(department) : '',
            'Not Selected'
        );

        setText(
            'designationPreview',
            designation?.value,
            'Not Entered'
        );

        setText(
            'rolePreview',
            role?.value ? selectedText(role).toUpperCase() : '',
            'EMPLOYEE'
        );

        setText(
            'officePreview',
            office?.value ? selectedText(office) : '',
            'Not Selected'
        );

        setText(
            'leaderPreview',
            leader?.value ? selectedText(leader) : '',
            'Not Selected'
        );

        setText(
            'leaveAuthorityPreview',
            leaveAuthority?.value ? selectedText(leaveAuthority) : '',
            'Not Selected'
        );

        setText(
            'joiningPreview',
            joiningDate?.value,
            'Not Entered'
        );

        const locationValue = document.querySelector(
            'input[name="location_required"]:checked'
        )?.value;

        setText(
            'locationPreview',
            locationValue === 'yes' ? 'Required' : 'Not Required',
            'Not Required'
        );
    };

    [nameInput, department, designation, role, office, leader, leaveAuthority, joiningDate]
        .filter(Boolean)
        .forEach(element => {
            element.addEventListener('input', updateSummary);
            element.addEventListener('change', updateSummary);
        });

    document.querySelectorAll('input[name="location_required"]').forEach(input => {
        input.addEventListener('change', updateSummary);
    });

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const file = this.files && this.files[0];

            if (!file || !file.type.startsWith('image/')) {
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {
                const preview = document.getElementById('employeePhotoPreview');
                if (preview) preview.src = event.target.result;
            };

            reader.readAsDataURL(file);
        });
    }

    const filterSelectByOffice = (selectElement, officeId, allowGlobal = false) => {
        if (!selectElement) return;

        Array.from(selectElement.options).forEach((option, index) => {
            if (index === 0) {
                option.hidden = false;
                option.disabled = false;
                return;
            }

            const optionOfficeId = option.dataset.officeId || '';
            const isGlobal = allowGlobal && optionOfficeId === '';
            const shouldShow = isGlobal || !officeId || String(optionOfficeId) === String(officeId);

            option.hidden = !shouldShow;
            option.disabled = !shouldShow;
        });

        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (selectedOption && selectedOption.disabled) selectElement.value = '';
    };

    const filterManagersByOffice = () => {
        const selectedOfficeId = office?.value || '';
        filterSelectByOffice(leader, selectedOfficeId, false);
        filterSelectByOffice(leaveAuthority, selectedOfficeId, true);
        updateSummary();
    };

    if (office) office.addEventListener('change', filterManagersByOffice);
    filterManagersByOffice();
});
</script>
@endpush