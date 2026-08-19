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

    /* Educational Qualifications */
    .qualification-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .qualification-item {
        padding: 10px;
        border: 1px solid #d6d9dc;
        background: #fafafa;
    }

    .qualification-item-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px 14px;
    }

    .qualification-field label {
        display: block;
        margin-bottom: 3px;
        color: #555;
        font-size: 10px;
        font-weight: 700;
    }

    .qualification-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .qualification-help {
        color: #777;
        font-size: 11px;
    }

    .btn-qualification-add,
    .btn-qualification-remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 28px;
        padding: 0 10px;
        border-radius: 2px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-qualification-add {
        border: 1px solid #315f8c;
        background: #3f78ad;
        color: #fff;
    }

    .btn-qualification-remove {
        border: 1px solid #c84b4b;
        background: #fff;
        color: #b42318;
    }

    .qualification-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .qualification-current-document {
        margin-top: 5px;
    }

    .qualification-current-document a {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #0b5ca8;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
    }

    @media (max-width: 1100px) {
        .qualification-item-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .qualification-item-grid {
            grid-template-columns: 1fr;
        }

        .qualification-toolbar {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    /* Family Members */
    .family-members-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .family-member-item {
        padding: 10px;
        border: 1px solid #d6d9dc;
        background: #fafafa;
    }

    .family-member-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px 14px;
    }

    .family-member-field label {
        display: block;
        margin-bottom: 3px;
        color: #555;
        font-size: 10px;
        font-weight: 700;
    }

    .family-member-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .family-member-help {
        color: #777;
        font-size: 11px;
    }

    .btn-family-add,
    .btn-family-remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 28px;
        padding: 0 10px;
        border-radius: 2px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-family-add {
        border: 1px solid #315f8c;
        background: #3f78ad;
        color: #fff;
    }

    .btn-family-remove {
        border: 1px solid #c84b4b;
        background: #fff;
        color: #b42318;
    }

    .family-member-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    @media (max-width: 1100px) {
        .family-member-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .family-member-grid {
            grid-template-columns: 1fr;
        }

        .family-member-toolbar {
            align-items: flex-start;
            flex-direction: column;
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
                            <label class="field-label" for="alternate_number">Alternate Number</label>
                            <input
                                class="form-control-compact @error('alternate_number') has-error @enderror"
                                id="alternate_number"
                                name="alternate_number"
                                type="text"
                                maxlength="20"
                                value="{{ old('alternate_number', $employee->alternate_number) }}"
                                placeholder="Alternate contact number"
                            >
                            @error('alternate_number')
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

            {{-- Structured Address Details --}}
            @php
                $addressPremise = old(
                    'premise_details',
                    $employeeAddress?->premise_details
                        ?? (!$employeeAddress ? $employee->address : '')
                );

                $currentMaritalStatus = old(
                    'marital_status',
                    $employeeFamily?->marital_status ?? 'single'
                );

                $currentHasNominee = old(
                    'has_nominee',
                    $employeeNominee ? 'yes' : 'no'
                );
            @endphp

            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Structured Address Details</h2>
                    <span class="panel-edit"><i class="fas fa-map-marker-alt"></i> Edit</span>
                </div>

                <div class="panel-body">
                    {{-- Keep old users.address working for reports/APIs --}}
                    <input
                        type="hidden"
                        name="address"
                        id="address"
                        value="{{ old('address', $employee->address) }}"
                    >

                    @if(!$employeeAddress && !empty($employee->address))
                        <div style="margin-bottom:8px;padding:7px 9px;background:#fff8dd;border:1px solid #ead89b;color:#6b5a1c;font-size:10px;">
                            This is an old employee address. The previous plain address has been placed in
                            Premise Details. After Update, it will also be saved in the new structured address table.
                        </div>
                    @endif

                    <div class="compact-grid three">
                        <div class="field-row">
                            <label class="field-label" for="premise_details">Premise Details</label>
                            <input
                                class="form-control-compact @error('premise_details') has-error @enderror"
                                id="premise_details"
                                name="premise_details"
                                type="text"
                                value="{{ $addressPremise }}"
                                placeholder="House / Flat / Building / Floor"
                            >
                            @error('premise_details')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="street_road">Street / Road</label>
                            <input
                                class="form-control-compact @error('street_road') has-error @enderror"
                                id="street_road"
                                name="street_road"
                                type="text"
                                value="{{ old('street_road', $employeeAddress?->street_road) }}"
                            >
                            @error('street_road')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="locality_area">Locality / Area</label>
                            <input
                                class="form-control-compact @error('locality_area') has-error @enderror"
                                id="locality_area"
                                name="locality_area"
                                type="text"
                                value="{{ old('locality_area', $employeeAddress?->locality_area) }}"
                            >
                            @error('locality_area')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="landmark">Landmark</label>
                            <input
                                class="form-control-compact @error('landmark') has-error @enderror"
                                id="landmark"
                                name="landmark"
                                type="text"
                                value="{{ old('landmark', $employeeAddress?->landmark) }}"
                            >
                            @error('landmark')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="city">City / Town</label>
                            <input
                                class="form-control-compact @error('city') has-error @enderror"
                                id="city"
                                name="city"
                                type="text"
                                value="{{ old('city', $employeeAddress?->city) }}"
                            >
                            @error('city')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="district">District</label>
                            <input
                                class="form-control-compact @error('district') has-error @enderror"
                                id="district"
                                name="district"
                                type="text"
                                value="{{ old('district', $employeeAddress?->district) }}"
                            >
                            @error('district')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="state">State / UT</label>
                            <input
                                class="form-control-compact @error('state') has-error @enderror"
                                id="state"
                                name="state"
                                type="text"
                                value="{{ old('state', $employeeAddress?->state) }}"
                            >
                            @error('state')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="pin_code">PIN Code</label>
                            <input
                                class="form-control-compact @error('pin_code') has-error @enderror"
                                id="pin_code"
                                name="pin_code"
                                type="text"
                                inputmode="numeric"
                                maxlength="6"
                                value="{{ old('pin_code', $employeeAddress?->pin_code) }}"
                            >
                            @error('pin_code')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- Marital & Spouse Details --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Marital & Spouse Details</h2>
                    <span class="panel-edit"><i class="fas fa-heart"></i> Edit</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row full">
                            <label class="field-label" for="marital_status">
                                Marital Status <span class="required">*</span>
                            </label>
                            <select
                                class="form-control-compact @error('marital_status') has-error @enderror"
                                id="marital_status"
                                name="marital_status"
                                required
                            >
                                <option value="single" {{ $currentMaritalStatus === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ $currentMaritalStatus === 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ $currentMaritalStatus === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ $currentMaritalStatus === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ $currentMaritalStatus === 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                            @error('marital_status')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="spouseDetails" class="field-row full" style="display:none;">
                            <div style="grid-column:1 / -1;">
                                <div class="compact-grid">
                                    <div class="field-row">
                                        <label class="field-label" for="spouse_name">
                                            Spouse Name <span class="required">*</span>
                                        </label>
                                        <input
                                            class="form-control-compact @error('spouse_name') has-error @enderror"
                                            id="spouse_name"
                                            name="spouse_name"
                                            type="text"
                                            value="{{ old('spouse_name', $employeeFamily?->spouse_name) }}"
                                        >
                                        @error('spouse_name')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="spouse_phone">Spouse Phone</label>
                                        <input
                                            class="form-control-compact @error('spouse_phone') has-error @enderror"
                                            id="spouse_phone"
                                            name="spouse_phone"
                                            type="text"
                                            value="{{ old('spouse_phone', $employeeFamily?->spouse_phone) }}"
                                        >
                                        @error('spouse_phone')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="spouse_dob">Spouse DOB</label>
                                        <input
                                            class="form-control-compact @error('spouse_dob') has-error @enderror"
                                            id="spouse_dob"
                                            name="spouse_dob"
                                            type="date"
                                            value="{{ old('spouse_dob', $employeeFamily?->spouse_dob ? \Carbon\Carbon::parse($employeeFamily->spouse_dob)->format('Y-m-d') : '') }}"
                                        >
                                        @error('spouse_dob')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="spouse_occupation">Occupation</label>
                                        <input
                                            class="form-control-compact @error('spouse_occupation') has-error @enderror"
                                            id="spouse_occupation"
                                            name="spouse_occupation"
                                            type="text"
                                            value="{{ old('spouse_occupation', $employeeFamily?->spouse_occupation) }}"
                                        >
                                        @error('spouse_occupation')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Nominee Details --}}
            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Nominee Details</h2>
                    <span class="panel-edit"><i class="fas fa-user-shield"></i> Edit</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">
                        <div class="field-row full">
                            <label class="field-label" for="has_nominee">
                                Add Nominee? <span class="required">*</span>
                            </label>
                            <select
                                class="form-control-compact @error('has_nominee') has-error @enderror"
                                id="has_nominee"
                                name="has_nominee"
                                required
                            >
                                <option value="no" {{ $currentHasNominee === 'no' ? 'selected' : '' }}>No</option>
                                <option value="yes" {{ $currentHasNominee === 'yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('has_nominee')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="nomineeDetails" class="field-row full" style="display:none;">
                            <div style="grid-column:1 / -1;">
                                <div class="compact-grid">
                                    <div class="field-row">
                                        <label class="field-label" for="nominee_name">
                                            Nominee Name <span class="required">*</span>
                                        </label>
                                        <input
                                            class="form-control-compact @error('nominee_name') has-error @enderror"
                                            id="nominee_name"
                                            name="nominee_name"
                                            type="text"
                                            value="{{ old('nominee_name', $employeeNominee?->name) }}"
                                        >
                                        @error('nominee_name')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="nominee_relationship">
                                            Relationship <span class="required">*</span>
                                        </label>
                                        <input
                                            class="form-control-compact @error('nominee_relationship') has-error @enderror"
                                            id="nominee_relationship"
                                            name="nominee_relationship"
                                            type="text"
                                            value="{{ old('nominee_relationship', $employeeNominee?->relationship) }}"
                                        >
                                        @error('nominee_relationship')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="nominee_phone">Phone</label>
                                        <input
                                            class="form-control-compact @error('nominee_phone') has-error @enderror"
                                            id="nominee_phone"
                                            name="nominee_phone"
                                            type="text"
                                            value="{{ old('nominee_phone', $employeeNominee?->phone) }}"
                                        >
                                        @error('nominee_phone')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="nominee_dob">Date of Birth</label>
                                        <input
                                            class="form-control-compact @error('nominee_dob') has-error @enderror"
                                            id="nominee_dob"
                                            name="nominee_dob"
                                            type="date"
                                            value="{{ old('nominee_dob', $employeeNominee?->dob ? \Carbon\Carbon::parse($employeeNominee->dob)->format('Y-m-d') : '') }}"
                                        >
                                        @error('nominee_dob')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="nominee_aadhaar_number">Aadhaar No.</label>
                                        <input
                                            class="form-control-compact @error('nominee_aadhaar_number') has-error @enderror"
                                            id="nominee_aadhaar_number"
                                            name="nominee_aadhaar_number"
                                            type="text"
                                            inputmode="numeric"
                                            maxlength="12"
                                            value="{{ old('nominee_aadhaar_number', $employeeNominee?->aadhaar_number) }}"
                                        >
                                        @error('nominee_aadhaar_number')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row top full">
                                        <label class="field-label" for="nominee_address">Nominee Address</label>
                                        <textarea
                                            class="form-control-compact @error('nominee_address') has-error @enderror"
                                            id="nominee_address"
                                            name="nominee_address"
                                        >{{ old('nominee_address', $employeeNominee?->address) }}</textarea>
                                        @error('nominee_address')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Nominee Bank Details --}}
                                    <div class="field-row">
                                        <label class="field-label" for="nominee_bank_name">
                                            Bank Name
                                        </label>
                                        <input
                                            class="form-control-compact @error('nominee_bank_name') has-error @enderror"
                                            id="nominee_bank_name"
                                            name="nominee_bank_name"
                                            type="text"
                                            maxlength="255"
                                            value="{{ old('nominee_bank_name', $employeeNominee?->bank_name) }}"
                                            placeholder="Enter bank name"
                                        >
                                        @error('nominee_bank_name')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="nominee_account_holder_name">
                                            Account Holder
                                        </label>
                                        <input
                                            class="form-control-compact @error('nominee_account_holder_name') has-error @enderror"
                                            id="nominee_account_holder_name"
                                            name="nominee_account_holder_name"
                                            type="text"
                                            maxlength="255"
                                            value="{{ old('nominee_account_holder_name', $employeeNominee?->account_holder_name) }}"
                                            placeholder="Account holder name"
                                        >
                                        @error('nominee_account_holder_name')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="nominee_account_number">
                                            Account Number
                                        </label>
                                        <input
                                            class="form-control-compact @error('nominee_account_number') has-error @enderror"
                                            id="nominee_account_number"
                                            name="nominee_account_number"
                                            type="text"
                                            inputmode="numeric"
                                            maxlength="30"
                                            autocomplete="off"
                                            value="{{ old('nominee_account_number', $employeeNominee?->account_number) }}"
                                            placeholder="Enter account number"
                                        >
                                        @error('nominee_account_number')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="field-row">
                                        <label class="field-label" for="nominee_ifsc_code">
                                            IFSC Code
                                        </label>
                                        <input
                                            class="form-control-compact @error('nominee_ifsc_code') has-error @enderror"
                                            id="nominee_ifsc_code"
                                            name="nominee_ifsc_code"
                                            type="text"
                                            maxlength="20"
                                            autocomplete="off"
                                            value="{{ old('nominee_ifsc_code', $employeeNominee?->ifsc_code) }}"
                                            placeholder="SBIN0001234"
                                            oninput="this.value=this.value.toUpperCase()"
                                        >
                                        @error('nominee_ifsc_code')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- <div class="field-row">
                                        <label class="field-label" for="nominee_branch_name">
                                            Branch Name
                                        </label>
                                        <input
                                            class="form-control-compact @error('nominee_branch_name') has-error @enderror"
                                            id="nominee_branch_name"
                                            name="nominee_branch_name"
                                            type="text"
                                            maxlength="255"
                                            value="{{ old('nominee_branch_name', $employeeNominee?->branch_name) }}"
                                            placeholder="Enter branch name"
                                        >
                                        @error('nominee_branch_name')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            {{-- Educational Qualifications --}}
            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Educational Qualifications</h2>
                    <span class="panel-edit">
                        <i class="fas fa-graduation-cap"></i> Edit
                    </span>
                </div>

                <div class="panel-body">
                    <div class="qualification-toolbar">
                        <div class="qualification-help">
                            Existing qualification edit karein, document replace karein, ya new qualification add karein.
                        </div>

                        <button
                            type="button"
                            class="btn-qualification-add"
                            id="addQualificationBtn"
                        >
                            <i class="fas fa-plus"></i> Add Qualification
                        </button>
                    </div>

                    @php
                        $qualificationRows = old('qualifications');

                        if ($qualificationRows === null) {
                            $qualificationRows = $employeeQualifications
                                ->map(function ($item) {
                                    return [
                                        'id' => $item->id,
                                        'qualification' => $item->qualification,
                                        'course_name' => $item->course_name,
                                        'board_university' => $item->board_university,
                                        'institute_name' => $item->institute_name,
                                        'passing_year' => $item->passing_year,
                                        'result' => $item->result,
                                        'document_type' => $item->document_type,
                                    ];
                                })
                                ->values()
                                ->all();
                        }

                        if (empty($qualificationRows)) {
                            $qualificationRows = [[
                                'id' => '',
                                'qualification' => '',
                                'course_name' => '',
                                'board_university' => '',
                                'institute_name' => '',
                                'passing_year' => '',
                                'result' => '',
                                'document_type' => '',
                            ]];
                        }

                        $qualificationOptions = [
                            '10th',
                            '12th',
                            'ITI',
                            'Diploma',
                            'Graduation',
                            'Post Graduation',
                            'B.Tech',
                            'B.E.',
                            'B.Com',
                            'B.Sc',
                            'B.A.',
                            'BCA',
                            'M.Tech',
                            'M.Com',
                            'M.Sc',
                            'M.A.',
                            'MCA',
                            'MBA',
                            'PhD',
                            'Other',
                        ];
                    @endphp

                    <div id="deletedQualificationIds"></div>

                    <div id="qualificationList" class="qualification-list">
                        @foreach($qualificationRows as $index => $qualification)
                            @php
                                $qualificationId = $qualification['id'] ?? null;

                                $currentQualification = $qualificationId
                                    ? $employeeQualifications->firstWhere('id', (int) $qualificationId)
                                    : null;
                            @endphp

                            <div class="qualification-item" data-qualification-row>
                                <input
                                    type="hidden"
                                    name="qualifications[{{ $index }}][id]"
                                    value="{{ $qualificationId }}"
                                    data-qualification-id
                                >

                                <div class="qualification-item-grid">

                                    <div class="qualification-field">
                                        <label>Qualification</label>

                                        <select
                                            class="form-control-compact @error("qualifications.$index.qualification") has-error @enderror"
                                            name="qualifications[{{ $index }}][qualification]"
                                        >
                                            <option value="">Select Qualification</option>

                                            @foreach($qualificationOptions as $option)
                                                <option
                                                    value="{{ $option }}"
                                                    {{ ($qualification['qualification'] ?? '') === $option ? 'selected' : '' }}
                                                >
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error("qualifications.$index.qualification")
                                            <div
                                                class="field-error"
                                                style="grid-column:auto;margin-top:3px;"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="qualification-field">
                                        <label>Course / Stream</label>

                                        <input
                                            type="text"
                                            class="form-control-compact @error("qualifications.$index.course_name") has-error @enderror"
                                            name="qualifications[{{ $index }}][course_name]"
                                            value="{{ $qualification['course_name'] ?? '' }}"
                                            placeholder="Science, Commerce, B.Tech CSE..."
                                        >

                                        @error("qualifications.$index.course_name")
                                            <div
                                                class="field-error"
                                                style="grid-column:auto;margin-top:3px;"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="qualification-field">
                                        <label>Board / University</label>

                                        <input
                                            type="text"
                                            class="form-control-compact @error("qualifications.$index.board_university") has-error @enderror"
                                            name="qualifications[{{ $index }}][board_university]"
                                            value="{{ $qualification['board_university'] ?? '' }}"
                                            placeholder="CBSE, UP Board, AKTU..."
                                        >

                                        @error("qualifications.$index.board_university")
                                            <div
                                                class="field-error"
                                                style="grid-column:auto;margin-top:3px;"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="qualification-field">
                                        <label>School / College / Institute</label>

                                        <input
                                            type="text"
                                            class="form-control-compact @error("qualifications.$index.institute_name") has-error @enderror"
                                            name="qualifications[{{ $index }}][institute_name]"
                                            value="{{ $qualification['institute_name'] ?? '' }}"
                                            placeholder="Institute name"
                                        >

                                        @error("qualifications.$index.institute_name")
                                            <div
                                                class="field-error"
                                                style="grid-column:auto;margin-top:3px;"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="qualification-field">
                                        <label>Passing Year</label>

                                        <input
                                            type="number"
                                            min="1950"
                                            max="{{ now()->year + 10 }}"
                                            class="form-control-compact @error("qualifications.$index.passing_year") has-error @enderror"
                                            name="qualifications[{{ $index }}][passing_year]"
                                            value="{{ $qualification['passing_year'] ?? '' }}"
                                            placeholder="2024"
                                        >

                                        @error("qualifications.$index.passing_year")
                                            <div
                                                class="field-error"
                                                style="grid-column:auto;margin-top:3px;"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="qualification-field">
                                        <label>Result</label>

                                        <input
                                            type="text"
                                            maxlength="100"
                                            class="form-control-compact @error("qualifications.$index.result") has-error @enderror"
                                            name="qualifications[{{ $index }}][result]"
                                            value="{{ $qualification['result'] ?? '' }}"
                                            placeholder="75%, 8.5 CGPA, First Division..."
                                        >

                                        @error("qualifications.$index.result")
                                            <div
                                                class="field-error"
                                                style="grid-column:auto;margin-top:3px;"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="qualification-field">
                                        <label>Document Type</label>

                                        <select
                                            class="form-control-compact @error("qualifications.$index.document_type") has-error @enderror"
                                            name="qualifications[{{ $index }}][document_type]"
                                        >
                                            <option value="">Select Document Type</option>

                                            <option
                                                value="marksheet"
                                                {{ ($qualification['document_type'] ?? '') === 'marksheet' ? 'selected' : '' }}
                                            >
                                                Marksheet
                                            </option>

                                            <option
                                                value="degree"
                                                {{ ($qualification['document_type'] ?? '') === 'degree' ? 'selected' : '' }}
                                            >
                                                Degree
                                            </option>

                                            <option
                                                value="certificate"
                                                {{ ($qualification['document_type'] ?? '') === 'certificate' ? 'selected' : '' }}
                                            >
                                                Certificate
                                            </option>
                                        </select>

                                        @error("qualifications.$index.document_type")
                                            <div
                                                class="field-error"
                                                style="grid-column:auto;margin-top:3px;"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="qualification-field">
                                        <label>Marksheet / Degree</label>

                                        <input
                                            type="file"
                                            class="form-control-compact @error("qualifications.$index.document") has-error @enderror"
                                            name="qualifications[{{ $index }}][document]"
                                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        >

                                        @if($currentQualification?->document_path)
                                            <div class="qualification-current-document">
                                                <a
                                                    href="{{ asset('storage/' . $currentQualification->document_path) }}"
                                                    target="_blank"
                                                    rel="noopener"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                    View Current
                                                    {{ ucfirst($currentQualification->document_type ?: 'Document') }}
                                                </a>
                                            </div>
                                        @endif

                                        @error("qualifications.$index.document")
                                            <div
                                                class="field-error"
                                                style="grid-column:auto;margin-top:3px;"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                </div>

                                <div class="qualification-actions">
                                    <button
                                        type="button"
                                        class="btn-qualification-remove"
                                        data-remove-qualification
                                        style="{{ count($qualificationRows) <= 1 ? 'display:none;' : '' }}"
                                    >
                                        <i class="fas fa-trash"></i>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
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


            {{-- Family Members --}}
            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Family Members</h2>
                    <span class="panel-edit">
                        <i class="fas fa-users"></i> Edit Multiple
                    </span>
                </div>

                <div class="panel-body">
                    <div class="family-member-toolbar">
                        <div class="family-member-help">
                            Employee ke family members add, edit ya remove karein.
                        </div>

                        <button
                            type="button"
                            class="btn-family-add"
                            id="addFamilyMemberBtn"
                        >
                            <i class="fas fa-plus"></i> Add Family Member
                        </button>
                    </div>

                    @php
                        $familyMemberRows = old('family_members');

                        if ($familyMemberRows === null) {
                            $familyMemberRows = $employeeFamilyMembers
                                ->map(function ($member) {
                                    return [
                                        'name' => $member->name,
                                        'relation' => $member->relation,
                                        'occupation' => $member->occupation,
                                        'age' => $member->age,
                                    ];
                                })
                                ->values()
                                ->all();
                        }

                        if (empty($familyMemberRows)) {
                            $familyMemberRows = [[
                                'name' => '',
                                'relation' => '',
                                'occupation' => '',
                                'age' => '',
                            ]];
                        }
                    @endphp

                    <div id="familyMembersList" class="family-members-list">
                        @foreach($familyMemberRows as $index => $familyMember)
                            <div class="family-member-item" data-family-member-row>
                                <div class="family-member-grid">

                                    <div class="family-member-field">
                                        <label>
                                            Name <span class="required">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control-compact @error("family_members.$index.name") has-error @enderror"
                                            name="family_members[{{ $index }}][name]"
                                            value="{{ $familyMember['name'] ?? '' }}"
                                            maxlength="255"
                                            placeholder="Family member name"
                                        >
                                        @error("family_members.$index.name")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="family-member-field">
                                        <label>
                                            Relation <span class="required">*</span>
                                        </label>
                                        <select
                                            class="form-control-compact @error("family_members.$index.relation") has-error @enderror"
                                            name="family_members[{{ $index }}][relation]"
                                            required
                                        >
                                            <option value="">Select Relation</option>
                                            @foreach([
                                                'Father',
                                                'Mother',
                                                'Spouse',
                                                'Son',
                                                'Daughter',
                                                'Brother',
                                                'Sister',
                                                'Grandfather',
                                                'Grandmother',
                                                'Other'
                                            ] as $relation)
                                                <option
                                                    value="{{ $relation }}"
                                                    {{ ($familyMember['relation'] ?? '') === $relation ? 'selected' : '' }}
                                                >
                                                    {{ $relation }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("family_members.$index.relation")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="family-member-field">
                                        <label>Occupation</label>
                                        <input
                                            type="text"
                                            class="form-control-compact @error("family_members.$index.occupation") has-error @enderror"
                                            name="family_members[{{ $index }}][occupation]"
                                            value="{{ $familyMember['occupation'] ?? '' }}"
                                            maxlength="255"
                                            placeholder="Occupation"
                                        >
                                        @error("family_members.$index.occupation")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="family-member-field">
                                        <label>Age</label>
                                        <input
                                            type="number"
                                            min="0"
                                            max="150"
                                            class="form-control-compact @error("family_members.$index.age") has-error @enderror"
                                            name="family_members[{{ $index }}][age]"
                                            value="{{ $familyMember['age'] ?? '' }}"
                                            placeholder="Age"
                                        >
                                        @error("family_members.$index.age")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                </div>

                                <div class="family-member-actions">
                                    <button
                                        type="button"
                                        class="btn-family-remove"
                                        data-remove-family-member
                                        style="{{ count($familyMemberRows) <= 1 ? 'display:none;' : '' }}"
                                    >
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
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

                        {{-- <div class="field-row">
                            <label class="field-label" for="bank_branch">Branch Name</label>
                            <input class="form-control-compact @error('bank_branch') has-error @enderror" id="bank_branch" name="bank_branch" type="text" maxlength="255" value="{{ old('bank_branch', $employee->bank_branch) }}" placeholder="Branch name">
                            @error('bank_branch')<div class="field-error">{{ $message }}</div>@enderror
                        </div> --}}

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

        const addressInputIds = [
            'premise_details',
            'street_road',
            'locality_area',
            'landmark',
            'city',
            'district',
            'state'
        ];

        function syncLegacyAddress() {
            const parts = addressInputIds
                .map(function (id) {
                    return document.getElementById(id)?.value?.trim() || '';
                })
                .filter(Boolean);

            const pinCode = document.getElementById('pin_code')?.value?.trim() || '';

            let fullAddress = parts.join(', ');

            if (pinCode) {
                fullAddress += (fullAddress ? ' - ' : '') + pinCode;
            }

            const legacyAddress = document.getElementById('address');

            if (legacyAddress) {
                legacyAddress.value = fullAddress;
            }
        }

        [...addressInputIds, 'pin_code'].forEach(function (id) {
            const input = document.getElementById(id);

            if (input) {
                input.addEventListener('input', syncLegacyAddress);
                input.addEventListener('change', syncLegacyAddress);
            }
        });

        const pinCodeInput = document.getElementById('pin_code');

        if (pinCodeInput) {
            pinCodeInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });
        }

        const nomineeAadhaarInput = document.getElementById('nominee_aadhaar_number');

        if (nomineeAadhaarInput) {
            nomineeAadhaarInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 12);
            });
        }

        const nomineeAccountNumberInput = document.getElementById('nominee_account_number');

        if (nomineeAccountNumberInput) {
            nomineeAccountNumberInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 30);
            });
        }

        const maritalStatus = document.getElementById('marital_status');
        const spouseDetails = document.getElementById('spouseDetails');
        const spouseName = document.getElementById('spouse_name');

        function toggleSpouseDetails() {
            const isMarried = maritalStatus && maritalStatus.value === 'married';

            if (spouseDetails) {
                spouseDetails.style.display = isMarried ? '' : 'none';
            }

            if (spouseName) {
                spouseName.required = !!isMarried;
            }
        }

        if (maritalStatus) {
            maritalStatus.addEventListener('change', toggleSpouseDetails);
        }

        const hasNominee = document.getElementById('has_nominee');
        const nomineeDetails = document.getElementById('nomineeDetails');
        const nomineeName = document.getElementById('nominee_name');
        const nomineeRelationship = document.getElementById('nominee_relationship');

        function toggleNomineeDetails() {
            const enabled = hasNominee && hasNominee.value === 'yes';

            if (nomineeDetails) {
                nomineeDetails.style.display = enabled ? '' : 'none';
            }

            if (nomineeName) {
                nomineeName.required = !!enabled;
            }

            if (nomineeRelationship) {
                nomineeRelationship.required = !!enabled;
            }

        }

        if (hasNominee) {
            hasNominee.addEventListener('change', toggleNomineeDetails);
        }

        syncLegacyAddress();
        toggleSpouseDetails();
        toggleNomineeDetails();

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



        /*
        |--------------------------------------------------------------------------
        | Family Members
        |--------------------------------------------------------------------------
        */

        const familyMembersList =
            document.getElementById('familyMembersList');

        const addFamilyMemberBtn =
            document.getElementById('addFamilyMemberBtn');

        function refreshFamilyMemberRows() {
            if (!familyMembersList) {
                return;
            }

            const rows = familyMembersList.querySelectorAll(
                '[data-family-member-row]'
            );

            rows.forEach(function (row, index) {
                row.querySelectorAll('[name]').forEach(function (input) {
                    const currentName = input.getAttribute('name');

                    if (!currentName) {
                        return;
                    }

                    input.setAttribute(
                        'name',
                        currentName.replace(
                            /family_members\[\d+\]/,
                            `family_members[${index}]`
                        )
                    );
                });

                const removeButton = row.querySelector(
                    '[data-remove-family-member]'
                );

                if (removeButton) {
                    removeButton.style.display =
                        rows.length <= 1
                            ? 'none'
                            : '';
                }
            });
        }

        function createFamilyMemberRow() {
            const index = familyMembersList
                ? familyMembersList.querySelectorAll(
                    '[data-family-member-row]'
                ).length
                : 0;

            const wrapper = document.createElement('div');

            wrapper.className = 'family-member-item';
            wrapper.setAttribute(
                'data-family-member-row',
                ''
            );

            wrapper.innerHTML = `
                <div class="family-member-grid">

                    <div class="family-member-field">
                        <label>
                            Name <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control-compact"
                            name="family_members[${index}][name]"
                            maxlength="255"
                            placeholder="Family member name"
                        >
                    </div>

                    <div class="family-member-field">
                        <label>
                            Relation <span class="required">*</span>
                        </label>

                        <select
                            class="form-control-compact"
                            name="family_members[${index}][relation]"
                            required
                        >
                            <option value="">Select Relation</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Son">Son</option>
                            <option value="Daughter">Daughter</option>
                            <option value="Brother">Brother</option>
                            <option value="Sister">Sister</option>
                            <option value="Grandfather">Grandfather</option>
                            <option value="Grandmother">Grandmother</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="family-member-field">
                        <label>Occupation</label>

                        <input
                            type="text"
                            class="form-control-compact"
                            name="family_members[${index}][occupation]"
                            maxlength="255"
                            placeholder="Occupation"
                        >
                    </div>

                    <div class="family-member-field">
                        <label>Age</label>

                        <input
                            type="number"
                            min="0"
                            max="150"
                            class="form-control-compact"
                            name="family_members[${index}][age]"
                            placeholder="Age"
                        >
                    </div>

                </div>

                <div class="family-member-actions">
                    <button
                        type="button"
                        class="btn-family-remove"
                        data-remove-family-member
                    >
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            `;

            return wrapper;
        }

        if (
            familyMembersList &&
            addFamilyMemberBtn
        ) {
            addFamilyMemberBtn.addEventListener(
                'click',
                function () {
                    familyMembersList.appendChild(
                        createFamilyMemberRow()
                    );

                    refreshFamilyMemberRows();
                }
            );

            familyMembersList.addEventListener(
                'click',
                function (event) {
                    const removeButton =
                        event.target.closest(
                            '[data-remove-family-member]'
                        );

                    if (!removeButton) {
                        return;
                    }

                    const rows =
                        familyMembersList.querySelectorAll(
                            '[data-family-member-row]'
                        );

                    if (rows.length <= 1) {
                        return;
                    }

                    const row =
                        removeButton.closest(
                            '[data-family-member-row]'
                        );

                    if (row) {
                        row.remove();
                        refreshFamilyMemberRows();
                    }
                }
            );

            refreshFamilyMemberRows();
        }

        /*
        |--------------------------------------------------------------------------
        | Educational Qualifications
        |--------------------------------------------------------------------------
        */

        const qualificationList =
            document.getElementById('qualificationList');

        const addQualificationBtn =
            document.getElementById('addQualificationBtn');

        const deletedQualificationIds =
            document.getElementById('deletedQualificationIds');

        const qualificationOptions = [
            '10th',
            '12th',
            'ITI',
            'Diploma',
            'Graduation',
            'Post Graduation',
            'B.Tech',
            'B.E.',
            'B.Com',
            'B.Sc',
            'B.A.',
            'BCA',
            'M.Tech',
            'M.Com',
            'M.Sc',
            'M.A.',
            'MCA',
            'MBA',
            'PhD',
            'Other'
        ];

        function escapeQualificationHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildQualificationOptions() {
            return '<option value="">Select Qualification</option>' +
                qualificationOptions
                    .map(function (option) {
                        const safe = escapeQualificationHtml(option);

                        return `<option value="${safe}">${safe}</option>`;
                    })
                    .join('');
        }

        function refreshQualificationRows() {
            if (!qualificationList) {
                return;
            }

            const rows = qualificationList.querySelectorAll(
                '[data-qualification-row]'
            );

            rows.forEach(function (row, index) {
                row.querySelectorAll('input, select').forEach(function (field) {
                    const currentName =
                        field.getAttribute('name') || '';

                    if (!currentName.startsWith('qualifications[')) {
                        return;
                    }

                    field.setAttribute(
                        'name',
                        currentName.replace(
                            /qualifications\[\d+\]/,
                            `qualifications[${index}]`
                        )
                    );
                });

                const removeButton = row.querySelector(
                    '[data-remove-qualification]'
                );

                if (removeButton) {
                    removeButton.style.display =
                        rows.length > 1 ? '' : 'none';
                }
            });
        }

        function createQualificationRow() {
            const index = qualificationList
                ? qualificationList.querySelectorAll(
                    '[data-qualification-row]'
                ).length
                : 0;

            const wrapper = document.createElement('div');

            wrapper.className = 'qualification-item';
            wrapper.setAttribute(
                'data-qualification-row',
                ''
            );

            wrapper.innerHTML = `
                <input
                    type="hidden"
                    name="qualifications[${index}][id]"
                    value=""
                    data-qualification-id
                >

                <div class="qualification-item-grid">

                    <div class="qualification-field">
                        <label>Qualification</label>

                        <select
                            class="form-control-compact"
                            name="qualifications[${index}][qualification]"
                        >
                            ${buildQualificationOptions()}
                        </select>
                    </div>

                    <div class="qualification-field">
                        <label>Course / Stream</label>

                        <input
                            type="text"
                            class="form-control-compact"
                            name="qualifications[${index}][course_name]"
                            placeholder="Science, Commerce, B.Tech CSE..."
                        >
                    </div>

                    <div class="qualification-field">
                        <label>Board / University</label>

                        <input
                            type="text"
                            class="form-control-compact"
                            name="qualifications[${index}][board_university]"
                            placeholder="CBSE, UP Board, AKTU..."
                        >
                    </div>

                    <div class="qualification-field">
                        <label>School / College / Institute</label>

                        <input
                            type="text"
                            class="form-control-compact"
                            name="qualifications[${index}][institute_name]"
                            placeholder="Institute name"
                        >
                    </div>

                    <div class="qualification-field">
                        <label>Passing Year</label>

                        <input
                            type="number"
                            min="1950"
                            max="${new Date().getFullYear() + 10}"
                            class="form-control-compact"
                            name="qualifications[${index}][passing_year]"
                            placeholder="2024"
                        >
                    </div>

                    <div class="qualification-field">
                        <label>Result</label>

                        <input
                            type="text"
                            maxlength="100"
                            class="form-control-compact"
                            name="qualifications[${index}][result]"
                            placeholder="75%, 8.5 CGPA, First Division..."
                        >
                    </div>

                    <div class="qualification-field">
                        <label>Document Type</label>

                        <select
                            class="form-control-compact"
                            name="qualifications[${index}][document_type]"
                        >
                            <option value="">Select Document Type</option>
                            <option value="marksheet">Marksheet</option>
                            <option value="degree">Degree</option>
                            <option value="certificate">Certificate</option>
                        </select>
                    </div>

                    <div class="qualification-field">
                        <label>Marksheet / Degree</label>

                        <input
                            type="file"
                            class="form-control-compact"
                            name="qualifications[${index}][document]"
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                        >
                    </div>

                </div>

                <div class="qualification-actions">
                    <button
                        type="button"
                        class="btn-qualification-remove"
                        data-remove-qualification
                    >
                        <i class="fas fa-trash"></i>
                        Remove
                    </button>
                </div>
            `;

            return wrapper;
        }

        if (
            addQualificationBtn &&
            qualificationList
        ) {
            addQualificationBtn.addEventListener(
                'click',
                function () {
                    qualificationList.appendChild(
                        createQualificationRow()
                    );

                    refreshQualificationRows();
                }
            );

            qualificationList.addEventListener(
                'click',
                function (event) {
                    const removeButton =
                        event.target.closest(
                            '[data-remove-qualification]'
                        );

                    if (!removeButton) {
                        return;
                    }

                    const rows =
                        qualificationList.querySelectorAll(
                            '[data-qualification-row]'
                        );

                    if (rows.length <= 1) {
                        return;
                    }

                    const row = removeButton.closest(
                        '[data-qualification-row]'
                    );

                    if (!row) {
                        return;
                    }

                    const idInput = row.querySelector(
                        '[data-qualification-id]'
                    );

                    const qualificationId =
                        idInput ? idInput.value : '';

                    if (
                        qualificationId &&
                        deletedQualificationIds
                    ) {
                        const deletedInput =
                            document.createElement('input');

                        deletedInput.type = 'hidden';
                        deletedInput.name =
                            'deleted_qualification_ids[]';

                        deletedInput.value =
                            qualificationId;

                        deletedQualificationIds.appendChild(
                            deletedInput
                        );
                    }

                    row.remove();

                    refreshQualificationRows();
                }
            );

            refreshQualificationRows();
        }

    });
</script>
@endpush

@endsection 