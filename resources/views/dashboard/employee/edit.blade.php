@extends('dashboard.layout.root')

@section('title', 'Edit Employee')

@push('styles')
<style>
    .employee-edit-page {
        font-family: Arial, Helvetica, sans-serif;
        color: #333;
        font-size: 12px;
    }

    .profile-summary,
    .form-panel {
        background: #fff;
        border: 1px solid #bfc4c9;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .20);
    }

    .profile-summary {
        padding: 8px 12px;
        margin-bottom: 12px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 145px minmax(0, 1fr);
        gap: 18px;
        align-items: center;
    }

    .profile-photo-wrap {
        display: flex;
        justify-content: center;
    }

    .profile-photo {
        width: 125px;
        height: 125px;
        border-radius: 28px;
        object-fit: cover;
        border: 1px solid #cfd4d8;
        box-shadow: 0 5px 14px rgba(0, 0, 0, .20);
        background: #f2f2f2;
    }

    .profile-name {
        margin: 0 0 9px;
        color: #4b4b4b;
        font-size: 23px;
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
        min-height: 15px;
        color: #444;
        font-size: 11px;
        line-height: 1.25;
    }

    .completion-text {
        margin-top: 8px;
        color: #e00000;
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
        min-height: 34px;
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
        text-decoration: none;
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

    .compact-grid.four {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .field-row {
        display: grid;
        grid-template-columns: 90px minmax(0, 1fr);
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

    .field-label .required {
        color: #d00;
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

    .help-text {
        grid-column: 2;
        margin-top: -3px;
        color: #777;
        font-size: 10px;
        line-height: 1.25;
    }

    .document-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 4px;
        color: #0b5ca8;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
    }

    .photo-editor {
        display: grid;
        grid-template-columns: 100px minmax(0, 1fr);
        gap: 14px;
        align-items: center;
    }

    .photo-editor img {
        width: 90px;
        height: 90px;
        border: 1px solid #bbb;
        border-radius: 18px;
        object-fit: cover;
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
        min-width: 105px;
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
        .summary-fields {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .compact-grid.four,
        .compact-grid.three {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .profile-grid,
        .page-form-grid {
            grid-template-columns: 1fr;
        }

        .profile-photo-wrap {
            justify-content: flex-start;
        }

        .summary-fields,
        .compact-grid,
        .compact-grid.three,
        .compact-grid.four {
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

        .field-error,
        .help-text {
            grid-column: 1;
        }

        .profile-photo {
            width: 105px;
            height: 105px;
        }

        .profile-name {
            font-size: 19px;
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
@php
    $currentRole = old('role');

    if (!$currentRole) {
        if ($employee->hasRole('admin')) {
            $currentRole = 'admin';
        } elseif ($employee->hasRole('team_leader')) {
            $currentRole = 'team_leader';
        } else {
            $currentRole = 'employee';
        }
    }

    $profileFields = [
        $employee->name,
        $employee->email,
        $employee->phone,
        $employee->dob,
        $employee->joining_date,
        $employee->address,
        $employee->department_id,
        $employee->designation,
        $employee->office_id,
        $currentRole,
    ];

    $completedFields = collect($profileFields)->filter(
        fn ($value) => !is_null($value) && $value !== ''
    )->count();

    $profilePercentage = (int) round(($completedFields / count($profileFields)) * 100);
    $pendingFields = count($profileFields) - $completedFields;

    $photoUrl = $employee->photo
        ? asset('storage/' . $employee->photo)
        : 'https://ui-avatars.com/api/?name=' . urlencode($employee->name)
            . '&background=d8e0e6&color=333&size=200';
@endphp

<div class="employee-edit-page">


    <section class="profile-summary">
        <div class="profile-grid">
            <div class="profile-photo-wrap">
                <img
                    src="{{ $photoUrl }}"
                    alt="{{ $employee->name }}"
                    class="profile-photo"
                >
            </div>

            <div>
                <h1 class="profile-name">
                    {{ $employee->name }}
                    @if($employee->employee_id)
                        - {{ $employee->employee_id }}
                    @endif
                </h1>

                <div class="summary-fields">
                    <div>
                        <span class="summary-label">Vertical</span>
                        <div class="summary-value">N/A</div>
                    </div>

                    <div>
                        <span class="summary-label">Department</span>
                        <div class="summary-value">
                            {{ $employee->department?->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Designation</span>
                        <div class="summary-value">
                            {{ $employee->designation ?: 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Role</span>
                        <div class="summary-value">
                            {{ strtoupper(str_replace('_', ' ', $currentRole)) }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Reporting Manager</span>
                        <div class="summary-value">
                            {{ $employee->teamLeader?->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Leave Authority</span>
                        <div class="summary-value">
                            {{ $employee->leaveAuthority?->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Is HOD?</span>
                        <div class="summary-value">
                            {{ $currentRole === 'team_leader' ? 'YES' : 'NO' }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Branch</span>
                        <div class="summary-value">
                            {{ $employee->office?->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Region</span>
                        <div class="summary-value">N/A</div>
                    </div>

                    <div>
                        <span class="summary-label">Location</span>
                        <div class="summary-value">
                            {{ $employee->office?->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Status</span>
                        <div class="summary-value">
                            {{ (int) $employee->status === 1 ? 'Active' : 'Inactive' }}
                        </div>
                    </div>

                    <div>
                        <span class="summary-label">Change Status</span>
                        <div class="summary-value">
                            <a href="#status" style="color:#0b5ca8;text-decoration:none;">
                                Change Status
                            </a>
                        </div>
                    </div>
                </div>

                <div class="completion-text">
                    Profile Completion Status
                    {{ $pendingFields > 0 ? 'Pending' : 'Complete' }}
                    {{ $completedFields }} / {{ count($profileFields) }}
                    ({{ $profilePercentage }}%)
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
        action="{{ route('employee.update', ['employee' => $employee->id]) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="page-form-grid">

            {{-- Primary Details --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Primary Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Edit</span>
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
                                value="{{ old('name', $employee->name) }}"
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
                                value="{{ old('email', $employee->email) }}"
                            >
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="phone">Phone</label>
                            <input
                                class="form-control-compact @error('phone') has-error @enderror"
                                id="phone"
                                name="phone"
                                type="text"
                                value="{{ old('phone', $employee->phone) }}"
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
                                value="{{ old('dob', $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('Y-m-d') : '') }}"
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
                                value="{{ old('joining_date', $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('Y-m-d') : '') }}"
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
                                value="{{ old('employee_id', $employee->employee_id) }}"
                            >
                            @error('employee_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Address Details --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Address Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Edit</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row top full">
                            <label class="field-label" for="address">Current Address</label>
                            <textarea
                                class="form-control-compact @error('address') has-error @enderror"
                                id="address"
                                name="address"
                            >{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Employment Details --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Employment Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Edit</span>
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
                                        {{ (string) old('department_id', $employee->department_id) === (string) $department->id ? 'selected' : '' }}
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
                                value="{{ old('designation', $employee->designation) }}"
                            >
                            @error('designation')
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
                                        {{ (string) old('office_id', $employee->office_id) === (string) $office->id ? 'selected' : '' }}
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
                            <label class="field-label" for="role">Role</label>
                            <select
                                class="form-control-compact @error('role') has-error @enderror"
                                name="role"
                                id="role"
                                required
                            >
                                <option value="">Select</option>
                                <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="team_leader" {{ $currentRole === 'team_leader' ? 'selected' : '' }}>
                                    Team Leader
                                </option>
                                <option value="employee" {{ $currentRole === 'employee' ? 'selected' : '' }}>
                                    Employee
                                </option>
                            </select>
                            @error('role')
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
                                        {{ (string) old('team_leader_id', $employee->team_leader_id) === (string) $leader->id ? 'selected' : '' }}
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
                                @foreach($teamLeaders as $authority)
                                    <option
                                        value="{{ $authority->id }}"
                                        data-office-id="{{ $authority->office_id }}"
                                        {{ (string) old('leave_authority_id', $employee->leave_authority_id) === (string) $authority->id ? 'selected' : '' }}
                                    >
                                        {{ $authority->name }}
                                        @if($authority->office)
                                            - {{ $authority->office->name }}
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
                                    {{ (string) old('status', $employee->status) == '1' ? 'selected' : '' }}
                                >
                                    Active
                                </option>

                                <option
                                    value="0"
                                    {{ (string) old('status', $employee->status) == '0' ? 'selected' : '' }}
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
                                value="{{ old('salary', $employee->salary) }}"
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
                            >{{ old('responsibility', $employee->responsibility) }}</textarea>
                            @error('responsibility')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Profile Photo --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Profile Photo</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Edit</span>
                </div>

                <div class="panel-body">
                    <div class="photo-editor">
                        <img src="{{ $photoUrl }}" alt="{{ $employee->name }}">

                        <div>
                            <label class="field-label" for="photo" style="display:block;margin-bottom:5px;">
                                Upload New Photo
                            </label>
                            <input
                                class="form-control-compact @error('photo') has-error @enderror"
                                id="photo"
                                name="photo"
                                type="file"
                                accept="image/*"
                            >
                            <div style="margin-top:5px;color:#777;font-size:10px;">
                                Leave blank to keep the current photo.
                            </div>
                            @error('photo')
                                <div style="margin-top:4px;color:#dc2626;font-size:10px;font-weight:700;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Aadhaar & PAN --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Aadhaar & PAN Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Edit</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row">
                            <label class="field-label" for="adhar_number">Aadhaar Number</label>
                            <input
                                class="form-control-compact @error('adhar_number') has-error @enderror"
                                id="adhar_number"
                                name="adhar_number"
                                type="text"
                                maxlength="12"
                                inputmode="numeric"
                                value="{{ old('adhar_number', $employee->adhar_number) }}"
                            >
                            @error('adhar_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="pan_number">PAN Number</label>
                            <input
                                class="form-control-compact @error('pan_number') has-error @enderror"
                                id="pan_number"
                                name="pan_number"
                                type="text"
                                maxlength="10"
                                value="{{ old('pan_number', $employee->pan_number) }}"
                                oninput="this.value=this.value.toUpperCase()"
                            >
                            @error('pan_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row top">
                            <label class="field-label" for="aadhar_attachment">Aadhaar File</label>
                            <div>
                                <input
                                    class="form-control-compact @error('aadhar_attachment') has-error @enderror"
                                    id="aadhar_attachment"
                                    name="aadhar_attachment"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp,.pdf"
                                >
                                @if($employee->aadhar_attachment)
                                    <a
                                        class="document-link"
                                        href="{{ asset('storage/' . $employee->aadhar_attachment) }}"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <i class="fas fa-eye"></i> View Current Aadhaar
                                    </a>
                                @endif
                            </div>
                            @error('aadhar_attachment')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row top">
                            <label class="field-label" for="pan_attachment">PAN File</label>
                            <div>
                                <input
                                    class="form-control-compact @error('pan_attachment') has-error @enderror"
                                    id="pan_attachment"
                                    name="pan_attachment"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp,.pdf"
                                >
                                @if($employee->pan_attachment)
                                    <a
                                        class="document-link"
                                        href="{{ asset('storage/' . $employee->pan_attachment) }}"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <i class="fas fa-eye"></i> View Current PAN
                                    </a>
                                @endif
                            </div>
                            @error('pan_attachment')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field-row top full">
                            <label class="field-label" for="other_attachment">Other File</label>
                            <div>
                                <input
                                    class="form-control-compact @error('other_attachment') has-error @enderror"
                                    id="other_attachment"
                                    name="other_attachment"
                                    type="file"
                                >
                                @if($employee->other_attachment)
                                    <a
                                        class="document-link"
                                        href="{{ asset('storage/' . $employee->other_attachment) }}"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <i class="fas fa-eye"></i> View Current File
                                    </a>
                                @endif
                            </div>
                            @error('other_attachment')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Attendance Settings --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Attendance Settings</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Edit</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row">
                            <label class="field-label" for="check_in_time">Check In Time</label>
                            <input
                                class="form-control-compact @error('check_in_time') has-error @enderror"
                                id="check_in_time"
                                name="check_in_time"
                                type="time"
                                value="{{ old('check_in_time', $employee->check_in_time ? \Carbon\Carbon::parse($employee->check_in_time)->format('H:i') : '') }}"
                            >
                            @error('check_in_time')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="check_out_time">Check Out Time</label>
                            <input
                                class="form-control-compact @error('check_out_time') has-error @enderror"
                                id="check_out_time"
                                name="check_out_time"
                                type="time"
                                value="{{ old('check_out_time', $employee->check_out_time ? \Carbon\Carbon::parse($employee->check_out_time)->format('H:i') : '') }}"
                            >
                            @error('check_out_time')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="break">Break (Minutes)</label>
                            <input
                                class="form-control-compact @error('break') has-error @enderror"
                                id="break"
                                name="break"
                                type="number"
                                min="0"
                                max="1440"
                                value="{{ old('break', $employee->break) }}"
                            >
                            @error('break')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row full">
                            <span class="field-label">Location Required</span>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input
                                        type="radio"
                                        name="location_required"
                                        value="yes"
                                        {{ old('location_required', $employee->location_required ?? 'no') === 'yes' ? 'checked' : '' }}
                                    >
                                    Yes
                                </label>

                                <label class="radio-label">
                                    <input
                                        type="radio"
                                        name="location_required"
                                        value="no"
                                        {{ old('location_required', $employee->location_required ?? 'no') === 'no' ? 'checked' : '' }}
                                    >
                                    No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Official Identifiers --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Official Identifiers</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Edit</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row">
                            <label class="field-label" for="uan_number">UAN Number</label>
                            <input class="form-control-compact @error('uan_number') has-error @enderror" id="uan_number" name="uan_number" type="text" value="{{ old('uan_number', $employee->uan_number) }}">
                            @error('uan_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="esic_number">ESIC Number</label>
                            <input class="form-control-compact @error('esic_number') has-error @enderror" id="esic_number" name="esic_number" type="text" value="{{ old('esic_number', $employee->esic_number) }}">
                            @error('esic_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Bank Details --}}
            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Bank Details</h2>
                    <span class="panel-edit"><i class="fas fa-university"></i> Edit</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid three">
                        <div class="field-row">
                            <label class="field-label" for="account_holder_name">Account Holder</label>
                            <input class="form-control-compact @error('account_holder_name') has-error @enderror" id="account_holder_name" name="account_holder_name" type="text" maxlength="255" value="{{ old('account_holder_name', $employee->account_holder_name) }}" placeholder="Account holder name">
                            @error('account_holder_name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="bank_name">Bank Name</label>
                            <input class="form-control-compact @error('bank_name') has-error @enderror" id="bank_name" name="bank_name" type="text" maxlength="255" value="{{ old('bank_name', $employee->bank_name) }}" placeholder="Bank name">
                            @error('bank_name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="bank_branch">Branch Name</label>
                            <input class="form-control-compact @error('bank_branch') has-error @enderror" id="bank_branch" name="bank_branch" type="text" maxlength="255" value="{{ old('bank_branch', $employee->bank_branch) }}" placeholder="Branch name">
                            @error('bank_branch')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="account_number">Account Number</label>
                            <input class="form-control-compact @error('account_number') has-error @enderror" id="account_number" name="account_number" type="text" inputmode="numeric" maxlength="30" value="{{ old('account_number', $employee->account_number) }}" placeholder="Bank account number" autocomplete="off">
                            @error('account_number')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="ifsc_code">IFSC Code</label>
                            <input class="form-control-compact @error('ifsc_code') has-error @enderror" id="ifsc_code" name="ifsc_code" type="text" minlength="11" maxlength="11" value="{{ old('ifsc_code', $employee->ifsc_code) }}" placeholder="SBIN0001234" autocomplete="off" oninput="this.value=this.value.toUpperCase()">
                            @error('ifsc_code')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="account_type">Account Type</label>
                            <select class="form-control-compact @error('account_type') has-error @enderror" id="account_type" name="account_type">
                                <option value="">Select</option>
                                @foreach(['savings' => 'Savings', 'current' => 'Current', 'salary' => 'Salary', 'other' => 'Other'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('account_type', $employee->account_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('account_type')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="upi_id">UPI ID</label>
                            <input class="form-control-compact @error('upi_id') has-error @enderror" id="upi_id" name="upi_id" type="text" maxlength="100" value="{{ old('upi_id', $employee->upi_id) }}" placeholder="name@bank" autocomplete="off">
                            @error('upi_id')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Salary Details --}}
            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Salary Details</h2>
                    <span class="panel-edit"><i class="fas fa-pencil-alt"></i> Edit</span>
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
                                value="{{ old('basic_salary', $employee->userSalary?->basic_salary) }}"
                            >
                            @error('basic_salary')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="dearness_allowance">Dearness Allowance</label>
                            <input
                                class="form-control-compact @error('dearness_allowance') has-error @enderror"
                                id="dearness_allowance"
                                name="dearness_allowance"
                                type="number"
                                step="0.01"
                                value="{{ old('dearness_allowance', $employee->userSalary?->dearness_allowance) }}"
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
                                value="{{ old('relieving_charge', $employee->userSalary?->relieving_charge) }}"
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
                                value="{{ old('additional_allowance', $employee->userSalary?->additional_allowance) }}"
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
                                value="{{ old('provident_fund', $employee->userSalary?->provident_fund) }}"
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
                                value="{{ old('employee_state_insurance_corporation', $employee->userSalary?->employee_state_insurance_corporation) }}"
                            >
                            @error('employee_state_insurance_corporation')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel full-width">
                <div class="form-actions">
                    <a href="{{ route('employee.index') }}" class="btn-compact btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>

                    <button type="submit" class="btn-compact btn-save">
                        <i class="fas fa-save"></i> Update Employee
                    </button>
                </div>
            </section>

        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const officeSelect = document.getElementById('office_id');
        const reportingManagerSelect = document.getElementById('team_leader_id');
        const leaveAuthoritySelect = document.getElementById('leave_authority_id');

        function filterByOffice(selectElement, officeId) {
            if (!selectElement) {
                return;
            }

            Array.from(selectElement.options).forEach(function (option, index) {
                if (index === 0) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }

                const optionOfficeId = option.dataset.officeId || '';
                const shouldShow = !officeId || optionOfficeId === String(officeId);

                option.hidden = !shouldShow;
                option.disabled = !shouldShow;

                if (!shouldShow && option.selected) {
                    selectElement.value = '';
                }
            });
        }

        function refreshManagerOptions() {
            const officeId = officeSelect ? officeSelect.value : '';

            filterByOffice(reportingManagerSelect, officeId);
            filterByOffice(leaveAuthoritySelect, officeId);
        }

        if (officeSelect) {
            officeSelect.addEventListener('change', refreshManagerOptions);
        }

        refreshManagerOptions();
    });
</script>
@endpush

@endsection 