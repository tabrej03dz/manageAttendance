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
        grid-template-columns: repeat(3, minmax(0, 1fr));
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
    .family-member-list {
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

    @media (max-width: 900px) {
        .family-member-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .family-member-grid {
            grid-template-columns: 1fr;
        }

        .family-member-toolbar {
            flex-direction: column;
            align-items: flex-start;
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
                        <div class="summary-value" id="statusPreview">{{ (string) old('status', '1') == '1' ? 'Active' : 'Inactive' }}</div>
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
                            <label class="field-label" for="alternate_number">Alternate Number</label>
                            <input
                                class="form-control-compact @error('alternate_number') has-error @enderror"
                                id="alternate_number"
                                name="alternate_number"
                                type="text"
                                inputmode="numeric"
                                maxlength="15"
                                value="{{ old('alternate_number') }}"
                                placeholder="Alternate mobile number"
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
                            <label class="field-label" for="last_working_date">Last Working Date</label>
                            <input
                                class="form-control-compact @error('last_working_date') has-error @enderror"
                                id="last_working_date"
                                name="last_working_date"
                                type="date"
                                value="{{ old('last_working_date') }}"
                            >
                            @error('last_working_date')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="employee_id">
                                Employee ID
                            </label>

                            <input
                                class="form-control-compact @error('employee_id') has-error @enderror"
                                id="employee_id"
                                name="employee_id"
                                type="text"
                                value="{{ old('employee_id', $nextEmployeeId) }}"
                                readonly
                            >

                            @error('employee_id')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Address Details</h2>
                    <span class="panel-edit"><i class="fas fa-map-marker-alt"></i> Add</span>
                </div>

                <div class="panel-body">
                    {{-- Backward compatibility: old EmployeeRequest / reports can still use address --}}
                    <input type="hidden" name="address" id="address" value="{{ old('address') }}">

                    <div class="compact-grid three">
                        <div class="field-row">
                            <label class="field-label" for="premise_details">Premise Details</label>
                            <input
                                class="form-control-compact @error('premise_details') has-error @enderror"
                                id="premise_details"
                                name="premise_details"
                                type="text"
                                value="{{ old('premise_details') }}"
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
                                value="{{ old('street_road') }}"
                                placeholder="Street, lane or road"
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
                                value="{{ old('locality_area') }}"
                                placeholder="Colony, sector, village"
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
                                value="{{ old('landmark') }}"
                                placeholder="Nearby prominent place"
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
                                value="{{ old('city') }}"
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
                                value="{{ old('district') }}"
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
                                value="{{ old('state') }}"
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
                                value="{{ old('pin_code') }}"
                                placeholder="6-digit PIN"
                            >
                            @error('pin_code')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Marital & Spouse Details</h2>
                    <span class="panel-edit"><i class="fas fa-heart"></i> Add</span>
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
                                <option value="single" {{ old('marital_status', 'single') === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status') === 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ old('marital_status') === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ old('marital_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ old('marital_status') === 'separated' ? 'selected' : '' }}>Separated</option>
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
                                            value="{{ old('spouse_name') }}"
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
                                            value="{{ old('spouse_phone') }}"
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
                                            value="{{ old('spouse_dob') }}"
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
                                            value="{{ old('spouse_occupation') }}"
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


            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Educational Qualifications</h2>
                    <span class="panel-edit"><i class="fas fa-graduation-cap"></i> Add Multiple</span>
                </div>

                <div class="panel-body">
                    <div class="qualification-toolbar">
                        <div class="qualification-help">
                            Add 10th, 12th, Diploma, Graduation, Post Graduation or any other qualification.
                        </div>

                        <button type="button" class="btn-qualification-add" id="addQualificationBtn">
                            <i class="fas fa-plus"></i> Add Qualification
                        </button>
                    </div>

                    <div id="qualificationList" class="qualification-list">
                        @php
                            $oldQualifications = old('qualifications', [
                                [
                                    'qualification' => '',
                                    'course_name' => '',
                                    'board_university' => '',
                                    'institute_name' => '',
                                    'passing_year' => '',
                                    'result' => '',
                                    'document_type' => '',
                                    'document' => '',
                                ]
                            ]);
                        @endphp

                        @foreach($oldQualifications as $index => $qualification)
                            <div class="qualification-item" data-qualification-row>
                                <div class="qualification-item-grid">

                                    <div class="qualification-field">
                                        <label>
                                            Qualification
                                            @if($index === 0)
                                                <span class="required">*</span>
                                            @endif
                                        </label>
                                        <select
                                            class="form-control-compact @error("qualifications.$index.qualification") has-error @enderror"
                                            name="qualifications[{{ $index }}][qualification]"
                                            {{ $index === 0 ? 'required' : '' }}
                                        >
                                            <option value="">Select Qualification</option>
                                            @foreach([
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
                                            ] as $option)
                                                <option
                                                    value="{{ $option }}"
                                                    {{ ($qualification['qualification'] ?? '') === $option ? 'selected' : '' }}
                                                >
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("qualifications.$index.qualification")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
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
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
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
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
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
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
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
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
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
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
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
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">
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

                                        @error("qualifications.$index.document")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">
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
                                        style="{{ count($oldQualifications) <= 1 ? 'display:none;' : '' }}"
                                    >
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Nominee Details</h2>
                    <span class="panel-edit"><i class="fas fa-user-shield"></i> Add</span>
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
                                <option value="no" {{ old('has_nominee', 'no') === 'no' ? 'selected' : '' }}>No</option>
                                <option value="yes" {{ old('has_nominee') === 'yes' ? 'selected' : '' }}>Yes</option>
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
                                            value="{{ old('nominee_name') }}"
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
                                            value="{{ old('nominee_relationship') }}"
                                            placeholder="Father, Mother, Spouse, etc."
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
                                            value="{{ old('nominee_phone') }}"
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
                                            value="{{ old('nominee_dob') }}"
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
                                            value="{{ old('nominee_aadhaar_number') }}"
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
                                        >{{ old('nominee_address') }}</textarea>
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
                                            value="{{ old('nominee_bank_name') }}"
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
                                            value="{{ old('nominee_account_holder_name') }}"
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
                                            value="{{ old('nominee_account_number') }}"
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
                                            maxlength="11"
                                            value="{{ old('nominee_ifsc_code') }}"
                                            placeholder="e.g. PUNB0037400"
                                            style="text-transform: uppercase;"
                                        >

                                        @error('nominee_ifsc_code')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
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
                            <label class="field-label" for="leave_authority_id">
                                Leave Authority
                            </label>

                            <select
                                class="form-control-compact @error('leave_authority_id') has-error @enderror"
                                name="leave_authority_id"
                                id="leave_authority_id"
                            >
                                <option value="">Select Team Leader</option>

                                @foreach($teamLeaders as $leaveAuthority)
                                    <option
                                        value="{{ $leaveAuthority->id }}"
                                        data-office-id="{{ $leaveAuthority->office_id }}"
                                        {{ (string) old('leave_authority_id') === (string) $leaveAuthority->id ? 'selected' : '' }}
                                    >
                                        {{ $leaveAuthority->name }}

                                        @if($leaveAuthority->office)
                                            - {{ $leaveAuthority->office->name }}
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
                                    {{ (string) old('status', '1') == '1' ? 'selected' : '' }}
                                >
                                    Active
                                </option>

                                <option
                                    value="0"
                                    {{ (string) old('status', '1') == '0' ? 'selected' : '' }}
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
                                Break Minutes 
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
                    <h2 class="panel-title">Family Members</h2>
                    <span class="panel-edit"><i class="fas fa-users"></i> Add Multiple</span>
                </div>

                <div class="panel-body">
                    <div class="family-member-toolbar">
                        <div class="family-member-help">
                            Add employee family member details such as Father, Mother, Spouse, Son, Daughter, Brother or Sister.
                        </div>

                        <button type="button" class="btn-family-add" id="addFamilyMemberBtn">
                            <i class="fas fa-plus"></i> Add Family Member
                        </button>
                    </div>

                    @php
                        $oldFamilyMembers = old('family_members', []);
                        if (!is_array($oldFamilyMembers)) {
                            $oldFamilyMembers = [];
                        }
                    @endphp

                    <div id="familyMemberList" class="family-member-list">
                        @foreach($oldFamilyMembers as $index => $member)
                            <div class="family-member-item" data-family-member-row>
                                <div class="family-member-grid">
                                    <div class="family-member-field">
                                        <label>Name <span class="required">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control-compact @error("family_members.$index.name") has-error @enderror"
                                            name="family_members[{{ $index }}][name]"
                                            value="{{ $member['name'] ?? '' }}"
                                            placeholder="Family member name"
                                            required
                                        >
                                        @error("family_members.$index.name")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="family-member-field">
                                        <label>Relation <span class="required">*</span></label>
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
                                                    {{ ($member['relation'] ?? '') === $relation ? 'selected' : '' }}
                                                >
                                                    {{ $relation }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("family_members.$index.relation")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="family-member-field">
                                        <label>Occupation</label>
                                        <input
                                            type="text"
                                            class="form-control-compact @error("family_members.$index.occupation") has-error @enderror"
                                            name="family_members[{{ $index }}][occupation]"
                                            value="{{ $member['occupation'] ?? '' }}"
                                            placeholder="Occupation"
                                        >
                                        @error("family_members.$index.occupation")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="family-member-field">
                                        <label>Age</label>
                                        <input
                                            type="number"
                                            min="0"
                                            max="120"
                                            class="form-control-compact @error("family_members.$index.age") has-error @enderror"
                                            name="family_members[{{ $index }}][age]"
                                            value="{{ $member['age'] ?? '' }}"
                                            placeholder="Age"
                                        >
                                        @error("family_members.$index.age")
                                            <div class="field-error" style="grid-column:auto;margin-top:3px;">{{ $message }}</div>
                                        @enderror
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
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Identity Details</h2>

                    <span class="panel-edit">
                        <i class="fas fa-id-card"></i> Add
                    </span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid">

                        <div class="field-row">
                            <label class="field-label" for="adhar_number">
                                Aadhaar Number
                            </label>

                            <input
                                class="form-control-compact @error('adhar_number') has-error @enderror"
                                id="adhar_number"
                                name="adhar_number"
                                type="text"
                                inputmode="numeric"
                                maxlength="12"
                                pattern="[0-9]{12}"
                                placeholder="Enter 12 digit Aadhaar number"
                                value="{{ old('adhar_number') }}"
                                autocomplete="off"
                            >

                            @error('adhar_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="pan_number">
                                PAN Number
                            </label>

                            <input
                                class="form-control-compact @error('pan_number') has-error @enderror"
                                id="pan_number"
                                name="pan_number"
                                type="text"
                                minlength="10"
                                maxlength="10"
                                placeholder="ABCDE1234F"
                                value="{{ old('pan_number') }}"
                                autocomplete="off"
                                style="text-transform: uppercase;"
                            >

                            @error('pan_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </section>

            <section class="form-panel full-width">
                <div class="panel-header">
                    <h2 class="panel-title">Bank Details</h2>
                    <span class="panel-edit"><i class="fas fa-university"></i> Add</span>
                </div>

                <div class="panel-body">
                    <div class="compact-grid three">
                        <div class="field-row">
                            <label class="field-label" for="account_holder_name">Account Holder</label>
                            <input
                                class="form-control-compact @error('account_holder_name') has-error @enderror"
                                id="account_holder_name"
                                name="account_holder_name"
                                type="text"
                                maxlength="255"
                                value="{{ old('account_holder_name') }}"
                                placeholder="Account holder name"
                            >
                            @error('account_holder_name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="bank_name">Bank Name</label>
                            <input
                                class="form-control-compact @error('bank_name') has-error @enderror"
                                id="bank_name"
                                name="bank_name"
                                type="text"
                                maxlength="255"
                                value="{{ old('bank_name') }}"
                                placeholder="Bank name"
                            >
                            @error('bank_name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="field-row">
                            <label class="field-label" for="bank_branch">Branch Name</label>
                            <input
                                class="form-control-compact @error('bank_branch') has-error @enderror"
                                id="bank_branch"
                                name="bank_branch"
                                type="text"
                                maxlength="255"
                                value="{{ old('bank_branch') }}"
                                placeholder="Branch name"
                            >
                            @error('bank_branch')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="field-row">
                            <label class="field-label" for="account_number">Account Number</label>
                            <input
                                class="form-control-compact @error('account_number') has-error @enderror"
                                id="account_number"
                                name="account_number"
                                type="text"
                                inputmode="numeric"
                                maxlength="30"
                                value="{{ old('account_number') }}"
                                placeholder="Bank account number"
                                autocomplete="off"
                            >
                            @error('account_number')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="ifsc_code">IFSC Code</label>
                            <input
                                class="form-control-compact @error('ifsc_code') has-error @enderror"
                                id="ifsc_code"
                                name="ifsc_code"
                                type="text"
                                minlength="11"
                                maxlength="11"
                                value="{{ old('ifsc_code') }}"
                                placeholder="SBIN0001234"
                                autocomplete="off"
                                style="text-transform: uppercase;"
                            >
                            @error('ifsc_code')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="account_type">Account Type</label>
                            <select
                                class="form-control-compact @error('account_type') has-error @enderror"
                                id="account_type"
                                name="account_type"
                            >
                                <option value="">Select</option>
                                <option value="savings" {{ old('account_type') === 'savings' ? 'selected' : '' }}>Savings</option>
                                <option value="current" {{ old('account_type') === 'current' ? 'selected' : '' }}>Current</option>
                                <option value="salary" {{ old('account_type') === 'salary' ? 'selected' : '' }}>Salary</option>
                                <option value="other" {{ old('account_type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('account_type')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="upi_id">UPI ID</label>
                            <input
                                class="form-control-compact @error('upi_id') has-error @enderror"
                                id="upi_id"
                                name="upi_id"
                                type="text"
                                maxlength="100"
                                value="{{ old('upi_id') }}"
                                placeholder="name@bank"
                                autocomplete="off"
                            >
                            @error('upi_id')
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
    const status = document.getElementById('status');
    const panInput = document.getElementById('pan_number');
    const ifscInput = document.getElementById('ifsc_code');
    const aadhaarInput = document.getElementById('adhar_number');
    const accountNumberInput = document.getElementById('account_number');

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

        setText(
            'statusPreview',
            status?.value === '1' ? 'Active' : 'Inactive',
            'Active'
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

    [nameInput, department, designation, role, office, leader, leaveAuthority, joiningDate, status]
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

    if (panInput) {
        panInput.addEventListener('input', function () {
            this.value = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .slice(0, 10);
        });
    }

    if (ifscInput) {
        ifscInput.addEventListener('input', function () {
            this.value = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .slice(0, 11);
        });
    }

    if (aadhaarInput) {
        aadhaarInput.addEventListener('input', function () {
            this.value = this.value
                .replace(/\D/g, '')
                .slice(0, 12);
        });
    }

    if (accountNumberInput) {
        accountNumberInput.addEventListener('input', function () {
            this.value = this.value
                .replace(/[^0-9A-Za-z]/g, '')
                .slice(0, 30);
        });
    }

    /*
     * Structured address -> legacy users.address hidden field.
     * This keeps the old EmployeeRequest/reports/API compatible.
     */
    const addressInputIds = [
        'premise_details',
        'street_road',
        'locality_area',
        'landmark',
        'city',
        'district',
        'state'
    ];

    const syncLegacyAddress = () => {
        const parts = addressInputIds
            .map(id => document.getElementById(id)?.value?.trim() || '')
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
    };

    [...addressInputIds, 'pin_code'].forEach(id => {
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

    const maritalStatus = document.getElementById('marital_status');
    const spouseDetails = document.getElementById('spouseDetails');
    const spouseName = document.getElementById('spouse_name');

    const toggleSpouseDetails = () => {
        const isMarried = maritalStatus?.value === 'married';

        if (spouseDetails) {
            spouseDetails.style.display = isMarried ? '' : 'none';
        }

        if (spouseName) {
            spouseName.required = isMarried;
        }
    };

    if (maritalStatus) {
        maritalStatus.addEventListener('change', toggleSpouseDetails);
    }

    const hasNominee = document.getElementById('has_nominee');
    const nomineeDetails = document.getElementById('nomineeDetails');
    const nomineeName = document.getElementById('nominee_name');
    const nomineeRelationship = document.getElementById('nominee_relationship');

    const toggleNomineeDetails = () => {
        const enabled = hasNominee?.value === 'yes';

        if (nomineeDetails) {
            nomineeDetails.style.display = enabled ? '' : 'none';
        }

        if (nomineeName) {
            nomineeName.required = enabled;
        }

        if (nomineeRelationship) {
            nomineeRelationship.required = enabled;
        }
    };

    if (hasNominee) {
        hasNominee.addEventListener('change', toggleNomineeDetails);
    }

    syncLegacyAddress();
    toggleSpouseDetails();
    toggleNomineeDetails();

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

    /* Educational Qualifications */
    const qualificationList = document.getElementById('qualificationList');
    const addQualificationBtn = document.getElementById('addQualificationBtn');

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

    const escapeHtml = (value) => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const buildQualificationOptions = () => {
        return '<option value="">Select Qualification</option>' +
            qualificationOptions.map(option =>
                `<option value="${escapeHtml(option)}">${escapeHtml(option)}</option>`
            ).join('');
    };

    const refreshQualificationRows = () => {
        if (!qualificationList) return;

        const rows = qualificationList.querySelectorAll('[data-qualification-row]');

        rows.forEach((row, index) => {
            row.querySelectorAll('select, input').forEach(field => {
                const currentName = field.getAttribute('name') || '';

                field.setAttribute(
                    'name',
                    currentName.replace(
                        /qualifications\[\d+\]/,
                        `qualifications[${index}]`
                    )
                );
            });

            const qualificationSelect = row.querySelector(
                'select[name*="[qualification]"]'
            );

            if (qualificationSelect) {
                qualificationSelect.required = index === 0;
            }

            const removeButton = row.querySelector('[data-remove-qualification]');
            if (removeButton) {
                removeButton.style.display = rows.length > 1 ? '' : 'none';
            }
        });
    };

    const createQualificationRow = () => {
        const index = qualificationList
            ? qualificationList.querySelectorAll('[data-qualification-row]').length
            : 0;

        const wrapper = document.createElement('div');
        wrapper.className = 'qualification-item';
        wrapper.setAttribute('data-qualification-row', '');

        wrapper.innerHTML = `
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
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;

        return wrapper;
    };

    if (addQualificationBtn && qualificationList) {
        addQualificationBtn.addEventListener('click', function () {
            qualificationList.appendChild(createQualificationRow());
            refreshQualificationRows();
        });

        qualificationList.addEventListener('click', function (event) {
            const removeButton = event.target.closest('[data-remove-qualification]');

            if (!removeButton) return;

            const rows = qualificationList.querySelectorAll('[data-qualification-row]');

            if (rows.length <= 1) return;

            removeButton.closest('[data-qualification-row]')?.remove();
            refreshQualificationRows();
        });

        refreshQualificationRows();
    }

    /* Family Members */
    const familyMemberList = document.getElementById('familyMemberList');
    const addFamilyMemberBtn = document.getElementById('addFamilyMemberBtn');

    const familyRelationOptions = [
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
    ];

    const buildFamilyRelationOptions = () => {
        return '<option value="">Select Relation</option>' +
            familyRelationOptions.map(relation =>
                `<option value="${escapeHtml(relation)}">${escapeHtml(relation)}</option>`
            ).join('');
    };

    const refreshFamilyMemberRows = () => {
        if (!familyMemberList) return;

        const rows = familyMemberList.querySelectorAll('[data-family-member-row]');

        rows.forEach((row, index) => {
            row.querySelectorAll('input, select').forEach(field => {
                const currentName = field.getAttribute('name') || '';

                if (!currentName) return;

                field.setAttribute(
                    'name',
                    currentName.replace(
                        /family_members\[\d+\]/,
                        `family_members[${index}]`
                    )
                );
            });
        });
    };

    const createFamilyMemberRow = () => {
        const index = familyMemberList
            ? familyMemberList.querySelectorAll('[data-family-member-row]').length
            : 0;

        const wrapper = document.createElement('div');
        wrapper.className = 'family-member-item';
        wrapper.setAttribute('data-family-member-row', '');

        wrapper.innerHTML = `
            <div class="family-member-grid">
                <div class="family-member-field">
                    <label>Name <span class="required">*</span></label>
                    <input
                        type="text"
                        class="form-control-compact"
                        name="family_members[${index}][name]"
                        placeholder="Family member name"
                        required
                    >
                </div>

                <div class="family-member-field">
                    <label>Relation <span class="required">*</span></label>
                    <select
                        class="form-control-compact"
                        name="family_members[${index}][relation]"
                        required
                    >
                        ${buildFamilyRelationOptions()}
                    </select>
                </div>

                <div class="family-member-field">
                    <label>Occupation</label>
                    <input
                        type="text"
                        class="form-control-compact"
                        name="family_members[${index}][occupation]"
                        placeholder="Occupation"
                    >
                </div>

                <div class="family-member-field">
                    <label>Age</label>
                    <input
                        type="number"
                        min="0"
                        max="120"
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
    };

    if (addFamilyMemberBtn && familyMemberList) {
        addFamilyMemberBtn.addEventListener('click', function () {
            familyMemberList.appendChild(createFamilyMemberRow());
            refreshFamilyMemberRows();
        });

        familyMemberList.addEventListener('click', function (event) {
            const removeButton = event.target.closest('[data-remove-family-member]');

            if (!removeButton) return;

            removeButton.closest('[data-family-member-row]')?.remove();
            refreshFamilyMemberRows();
        });

        refreshFamilyMemberRows();
    }

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const officeSelect =
        document.getElementById('office_id');

    const employeeIdInput =
        document.getElementById('employee_id');

    if (!officeSelect || !employeeIdInput) {
        return;
    }

    async function loadEmployeeId() {

        const officeId = officeSelect.value;

        if (!officeId) {
            employeeIdInput.value = '';
            return;
        }

        employeeIdInput.value = 'Generating...';

        try {

            const url =
                "{{ route('employee.next-id', ':office') }}"
                    .replace(':office', officeId);

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(
                    'Unable to generate Employee ID'
                );
            }

            const data = await response.json();

            employeeIdInput.value =
                data.employee_id ?? '';

        } catch (error) {

            console.error(error);

            employeeIdInput.value = '';

        }
    }

    officeSelect.addEventListener(
        'change',
        loadEmployeeId
    );
});
</script>
@endpush
