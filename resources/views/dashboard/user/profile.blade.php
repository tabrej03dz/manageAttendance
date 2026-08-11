@extends('dashboard.layout.root')

@section('title', 'Employee Profile')

@push('styles')
<style>
    .employee-register-page {
        font-family: Arial, Helvetica, sans-serif;
        color: #333;
        font-size: 12px;
        padding-bottom: 90px;
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
        word-break: break-word;
    }

    .profile-status-text {
        margin-top: 9px;
        color: #315f8c;
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
        color: #777;
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

    .form-control-compact,
    .read-only-value {
        width: 100%;
        min-height: 28px;
        min-width: 0;
        padding: 5px 8px;
        border: 1px solid #cfd3d7 !important;
        border-radius: 0 !important;
        background: #f8f9fa !important;
        color: #444 !important;
        font-size: 11px !important;
        line-height: 17px;
        box-shadow: none !important;
        word-break: break-word;
    }

    .read-only-value.multiline {
        min-height: 54px;
        white-space: pre-wrap;
    }

    .empty-value {
        color: #999 !important;
        font-style: italic;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        padding: 2px 8px;
        border: 1px solid #cfd3d7;
        background: #f8f9fa;
        color: #444;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .document-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .document-card {
        min-height: 76px;
        padding: 9px;
        border: 1px solid #d6dade;
        background: #fafafa;
    }

    .document-title {
        margin-bottom: 6px;
        color: #555;
        font-size: 11px;
        font-weight: 700;
    }

    .document-actions {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
    }

    .document-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 27px;
        padding: 3px 9px;
        border: 1px solid #315f8c;
        background: #3f78ad;
        color: #fff !important;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none !important;
    }

    .no-document {
        color: #999;
        font-size: 10px;
        font-style: italic;
    }

    .password-panel {
        margin-top: 2px;
    }

    .password-panel .form-control-compact {
        background: #fff !important;
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

    .btn-logout {
        border-color: #9f1d1d;
        background: #c62828;
        color: #fff !important;
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
        .compact-grid.three,
        .document-grid {
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

        .registration-avatar {
            width: 96px;
            height: 96px;
        }

        .registration-title {
            font-size: 18px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-compact {
            width: 100%;
        }
    }

    /* Educational Qualifications */
    .qualification-profile-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .qualification-profile-card {
        padding: 10px;
        border: 1px solid #d6dade;
        background: #fafafa;
    }

    .qualification-profile-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px 16px;
    }

    .qualification-profile-field {
        min-width: 0;
    }

    .qualification-profile-label {
        display: block;
        margin-bottom: 3px;
        color: #555;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .qualification-profile-value {
        min-height: 28px;
        padding: 5px 8px;
        border: 1px solid #cfd3d7;
        background: #f8f9fa;
        color: #444;
        font-size: 11px;
        line-height: 17px;
        word-break: break-word;
    }

    .qualification-profile-document {
        margin-top: 9px;
        padding-top: 8px;
        border-top: 1px dashed #d6dade;
    }

    .qualification-empty {
        padding: 14px;
        border: 1px dashed #cfd3d7;
        background: #fafafa;
        color: #999;
        font-size: 11px;
        font-style: italic;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .qualification-profile-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .qualification-profile-grid {
            grid-template-columns: 1fr;
        }
    }

</style>
@endpush

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Read-only display helpers
    |--------------------------------------------------------------------------
    */
    $show = function ($value, $fallback = 'Not Available') {
        if (is_null($value) || trim((string) $value) === '') {
            return $fallback;
        }

        return $value;
    };

    $dateValue = function ($value) {
        if (!$value) {
            return 'Not Available';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y');
        } catch (\Throwable $e) {
            return $value;
        }
    };

    $timeValue = function ($value) {
        if (!$value) {
            return 'Not Available';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('h:i A');
        } catch (\Throwable $e) {
            return $value;
        }
    };

    $money = function ($value) {
        if (is_null($value) || $value === '') {
            return 'Not Available';
        }

        return '₹ ' . number_format((float) $value, 2);
    };

    /*
     * These relations are expected on User:
     * department, office, teamLeader/reportingManager, leaveAuthority.
     *
     * If your relation name is different, only change the corresponding
     * variable below; the HTML does not need to change.
     */
    $departmentName = data_get($user, 'department.name')
        ?: data_get($user, 'department_name')
        ?: $show(data_get($user, 'department_id'));

    $officeName = data_get($user, 'office.name')
        ?: data_get($user, 'office_name')
        ?: $show(data_get($user, 'office_id'));

    $reportingManagerName = data_get($user, 'teamLeader.name')
        ?: data_get($user, 'reportingManager.name')
        ?: data_get($user, 'manager.name')
        ?: $show(data_get($user, 'team_leader_id'));

    $leaveAuthorityName = data_get($user, 'leaveAuthority.name')
        ?: data_get($user, 'leave_authority.name')
        ?: $show(data_get($user, 'leave_authority_id'));

    $roleName = method_exists($user, 'getRoleNames')
        ? ($user->getRoleNames()->first() ?: $show(data_get($user, 'role')))
        : $show(data_get($user, 'role'));

    $statusText = (string) data_get($user, 'status', '1') === '1' ? 'Active' : 'Inactive';

    $locationRequired = strtolower((string) data_get($user, 'location_required', 'no')) === 'yes'
        ? 'Required'
        : 'Not Required';

    $photoUrl = $user->photo
        ? asset('storage/' . $user->photo)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?: 'Employee') . '&background=d8e0e6&color=333&size=200';

    $fullAddressParts = array_filter([
        data_get($user, 'premise_details'),
        data_get($user, 'street_road'),
        data_get($user, 'locality_area'),
        data_get($user, 'landmark'),
        data_get($user, 'city'),
        data_get($user, 'district'),
        data_get($user, 'state'),
    ], fn ($item) => !is_null($item) && trim((string) $item) !== '');

    $structuredAddress = implode(', ', $fullAddressParts);

    if (data_get($user, 'pin_code')) {
        $structuredAddress .= ($structuredAddress ? ' - ' : '') . data_get($user, 'pin_code');
    }

    $fullAddress = $structuredAddress ?: data_get($user, 'address');
@endphp

<div class="employee-register-page">

    {{-- ======================== PROFILE SUMMARY ======================== --}}
    <section class="registration-summary">
        <div class="registration-summary-grid">
            <div>
                <img
                    class="registration-avatar"
                    src="{{ $photoUrl }}"
                    alt="{{ $user->name ?: 'Employee' }}"
                >
            </div>

            <div>
                <h1 class="registration-title">
                    {{ $user->name ?: 'EMPLOYEE PROFILE' }}
                </h1>

                <div class="summary-fields">
                    <div>
                        <span class="summary-label">Department</span>
                        <div class="summary-value">{{ $departmentName }}</div>
                    </div>

                    <div>
                        <span class="summary-label">Designation</span>
                        <div class="summary-value">{{ $show($user->designation) }}</div>
                    </div>

                    <div>
                        <span class="summary-label">Role</span>
                        <div class="summary-value">{{ strtoupper($roleName) }}</div>
                    </div>

                    <div>
                        <span class="summary-label">Branch</span>
                        <div class="summary-value">{{ $officeName }}</div>
                    </div>

                    <div>
                        <span class="summary-label">Reporting Manager</span>
                        <div class="summary-value">{{ $reportingManagerName }}</div>
                    </div>

                    <div>
                        <span class="summary-label">Leave Authority</span>
                        <div class="summary-value">{{ $leaveAuthorityName }}</div>
                    </div>

                    <div>
                        <span class="summary-label">Joining Date</span>
                        <div class="summary-value">{{ $dateValue($user->joining_date) }}</div>
                    </div>

                    <div>
                        <span class="summary-label">Status</span>
                        <div class="summary-value">{{ $statusText }}</div>
                    </div>

                    <div>
                        <span class="summary-label">Location Rule</span>
                        <div class="summary-value">{{ $locationRequired }}</div>
                    </div>
                </div>

                <div class="profile-status-text">
                    Employee profile details — view only
                </div>
            </div>
        </div>
    </section>

    <div class="page-form-grid">

        {{-- ======================== PRIMARY DETAILS ======================== --}}
        <section class="form-panel">
            <div class="panel-header">
                <h2 class="panel-title">Primary Details</h2>
                <span class="panel-edit"><i class="fas fa-eye"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid">
                    @foreach([
                        ['Full Name', $user->name],
                        ['Email', $user->email],
                        ['Secondary Email', data_get($user, 'email1')],
                        ['Phone', $user->phone],
                        ['Date of Birth', $dateValue(data_get($user, 'dob'))],
                        ['Joining Date', $dateValue(data_get($user, 'joining_date'))],
                        ['Employee ID', data_get($user, 'employee_id')],
                    ] as [$label, $value])
                        <div class="field-row">
                            <span class="field-label">{{ $label }}</span>
                            <div class="read-only-value {{ $value === 'Not Available' || !$value ? 'empty-value' : '' }}">
                                {{ $show($value) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ======================== EMPLOYMENT DETAILS ======================== --}}
        <section class="form-panel">
            <div class="panel-header">
                <h2 class="panel-title">Employment Details</h2>
                <span class="panel-edit"><i class="fas fa-eye"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid">
                    <div class="field-row">
                        <span class="field-label">Department</span>
                        <div class="read-only-value">{{ $departmentName }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Designation</span>
                        <div class="read-only-value">{{ $show($user->designation) }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Role</span>
                        <div class="read-only-value">{{ $roleName }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Office</span>
                        <div class="read-only-value">{{ $officeName }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Reporting Manager</span>
                        <div class="read-only-value">{{ $reportingManagerName }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Leave Authority</span>
                        <div class="read-only-value">{{ $leaveAuthorityName }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Status</span>
                        <div class="read-only-value">{{ $statusText }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Monthly Salary</span>
                        <div class="read-only-value">{{ $money(data_get($user, 'salary')) }}</div>
                    </div>

                    <div class="field-row top full">
                        <span class="field-label">Responsibility</span>
                        <div class="read-only-value multiline">{{ $show(data_get($user, 'responsibility')) }}</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================== ADDRESS DETAILS ======================== --}}
        <section class="form-panel full-width">
            <div class="panel-header">
                <h2 class="panel-title">Address Details</h2>
                <span class="panel-edit"><i class="fas fa-map-marker-alt"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid three">
                    @foreach([
                        ['Premise Details', data_get($user, 'premise_details')],
                        ['Street / Road', data_get($user, 'street_road')],
                        ['Locality / Area', data_get($user, 'locality_area')],
                        ['Landmark', data_get($user, 'landmark')],
                        ['City / Town', data_get($user, 'city')],
                        ['District', data_get($user, 'district')],
                        ['State / UT', data_get($user, 'state')],
                        ['PIN Code', data_get($user, 'pin_code')],
                    ] as [$label, $value])
                        <div class="field-row">
                            <span class="field-label">{{ $label }}</span>
                            <div class="read-only-value {{ !$value ? 'empty-value' : '' }}">
                                {{ $show($value) }}
                            </div>
                        </div>
                    @endforeach

                    <div class="field-row top full">
                        <span class="field-label">Full Address</span>
                        <div class="read-only-value multiline">{{ $show($fullAddress) }}</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================== MARITAL / SPOUSE ======================== --}}
        <section class="form-panel">
            <div class="panel-header">
                <h2 class="panel-title">Marital & Spouse Details</h2>
                <span class="panel-edit"><i class="fas fa-heart"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid">
                    <div class="field-row full">
                        <span class="field-label">Marital Status</span>
                        <div class="read-only-value">
                            {{ ucfirst($show(data_get($user, 'marital_status'))) }}
                        </div>
                    </div>

                    @if(strtolower((string) data_get($user, 'marital_status')) === 'married')
                        @foreach([
                            ['Spouse Name', data_get($user, 'spouse_name')],
                            ['Spouse Phone', data_get($user, 'spouse_phone')],
                            ['Spouse DOB', $dateValue(data_get($user, 'spouse_dob'))],
                            ['Occupation', data_get($user, 'spouse_occupation')],
                        ] as [$label, $value])
                            <div class="field-row">
                                <span class="field-label">{{ $label }}</span>
                                <div class="read-only-value">{{ $show($value) }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        {{-- ======================== NOMINEE DETAILS ======================== --}}
        <section class="form-panel">
            <div class="panel-header">
                <h2 class="panel-title">Nominee Details</h2>
                <span class="panel-edit"><i class="fas fa-user-shield"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid">
                    <div class="field-row full">
                        <span class="field-label">Nominee Added?</span>
                        <div class="read-only-value">
                            {{ strtolower((string) data_get($user, 'has_nominee', 'no')) === 'yes' ? 'Yes' : 'No' }}
                        </div>
                    </div>

                    @if(strtolower((string) data_get($user, 'has_nominee', 'no')) === 'yes')
                        @foreach([
                            ['Nominee Name', data_get($user, 'nominee_name')],
                            ['Relationship', data_get($user, 'nominee_relationship')],
                            ['Phone', data_get($user, 'nominee_phone')],
                            ['Date of Birth', $dateValue(data_get($user, 'nominee_dob'))],
                            ['Aadhaar No.', data_get($user, 'nominee_aadhaar_number')],
                        ] as [$label, $value])
                            <div class="field-row">
                                <span class="field-label">{{ $label }}</span>
                                <div class="read-only-value">{{ $show($value) }}</div>
                            </div>
                        @endforeach

                        <div class="field-row top full">
                            <span class="field-label">Nominee Address</span>
                            <div class="read-only-value multiline">
                                {{ $show(data_get($user, 'nominee_address')) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>


        {{-- ======================== EDUCATIONAL QUALIFICATIONS ======================== --}}
        <section class="form-panel full-width">
            <div class="panel-header">
                <h2 class="panel-title">Educational Qualifications</h2>
                <span class="panel-edit">
                    <i class="fas fa-graduation-cap"></i> View Only
                </span>
            </div>

            <div class="panel-body">
                @if($qualifications->isNotEmpty())
                    <div class="qualification-profile-list">
                        @foreach($qualifications as $qualification)
                            <div class="qualification-profile-card">
                                <div class="qualification-profile-grid">

                                    <div class="qualification-profile-field">
                                        <span class="qualification-profile-label">
                                            Qualification
                                        </span>

                                        <div class="qualification-profile-value">
                                            {{ $show($qualification->qualification) }}
                                        </div>
                                    </div>

                                    <div class="qualification-profile-field">
                                        <span class="qualification-profile-label">
                                            Course / Stream
                                        </span>

                                        <div class="qualification-profile-value">
                                            {{ $show($qualification->course_name) }}
                                        </div>
                                    </div>

                                    <div class="qualification-profile-field">
                                        <span class="qualification-profile-label">
                                            Board / University
                                        </span>

                                        <div class="qualification-profile-value">
                                            {{ $show($qualification->board_university) }}
                                        </div>
                                    </div>

                                    <div class="qualification-profile-field">
                                        <span class="qualification-profile-label">
                                            School / College / Institute
                                        </span>

                                        <div class="qualification-profile-value">
                                            {{ $show($qualification->institute_name) }}
                                        </div>
                                    </div>

                                    <div class="qualification-profile-field">
                                        <span class="qualification-profile-label">
                                            Passing Year
                                        </span>

                                        <div class="qualification-profile-value">
                                            {{ $show($qualification->passing_year) }}
                                        </div>
                                    </div>

                                    <div class="qualification-profile-field">
                                        <span class="qualification-profile-label">
                                            Result
                                        </span>

                                        <div class="qualification-profile-value">
                                            {{ $show($qualification->result) }}
                                        </div>
                                    </div>

                                </div>

                                <div class="qualification-profile-document">
                                    <span class="qualification-profile-label">
                                        {{ ucfirst($qualification->document_type ?: 'Document') }}
                                    </span>

                                    @if($qualification->document_path)
                                        <div class="document-actions">
                                            <a
                                                href="{{ asset('storage/' . $qualification->document_path) }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="document-link"
                                            >
                                                <i class="fas fa-eye"></i>
                                                View
                                                {{ ucfirst($qualification->document_type ?: 'Document') }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="no-document">
                                            No document uploaded
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="qualification-empty">
                        No educational qualification added.
                    </div>
                @endif
            </div>
        </section>

        {{-- ======================== ATTENDANCE SETTINGS ======================== --}}
        <section class="form-panel">
            <div class="panel-header">
                <h2 class="panel-title">Attendance Settings</h2>
                <span class="panel-edit"><i class="fas fa-clock"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid">
                    <div class="field-row">
                        <span class="field-label">Check In</span>
                        <div class="read-only-value">{{ $timeValue(data_get($user, 'check_in_time')) }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Check Out</span>
                        <div class="read-only-value">{{ $timeValue(data_get($user, 'check_out_time')) }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Break Minutes</span>
                        <div class="read-only-value">{{ $show(data_get($user, 'break')) }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Location Required</span>
                        <div class="read-only-value">{{ $locationRequired }}</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================== OFFICIAL IDENTIFIERS ======================== --}}
        <section class="form-panel">
            <div class="panel-header">
                <h2 class="panel-title">Official Identifiers</h2>
                <span class="panel-edit"><i class="fas fa-id-badge"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid">
                    <div class="field-row">
                        <span class="field-label">UAN Number</span>
                        <div class="read-only-value">{{ $show(data_get($user, 'uan_number')) }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">ESIC Number</span>
                        <div class="read-only-value">{{ $show(data_get($user, 'esic_number')) }}</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================== IDENTITY DETAILS ======================== --}}
        <section class="form-panel">
            <div class="panel-header">
                <h2 class="panel-title">Identity Details</h2>
                <span class="panel-edit"><i class="fas fa-id-card"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid">
                    <div class="field-row">
                        <span class="field-label">Aadhaar Number</span>
                        <div class="read-only-value">{{ $show(data_get($user, 'adhar_number')) }}</div>
                    </div>

                    <div class="field-row">
                        <span class="field-label">PAN Number</span>
                        <div class="read-only-value">
                            {{ strtoupper($show(data_get($user, 'pan_number'))) }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================== PHOTO & DOCUMENTS ======================== --}}
        <section class="form-panel">
            <div class="panel-header">
                <h2 class="panel-title">Photo & Documents</h2>
                <span class="panel-edit"><i class="fas fa-paperclip"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="document-grid">
                    @php
                        $documents = [
                            'Employee Photo' => data_get($user, 'photo'),
                            'Aadhaar File' => data_get($user, 'aadhar_attachment'),
                            'PAN File' => data_get($user, 'pan_attachment'),
                            'Other File' => data_get($user, 'other_attachment'),
                        ];
                    @endphp

                    @foreach($documents as $label => $path)
                        <div class="document-card">
                            <div class="document-title">{{ $label }}</div>

                            <div class="document-actions">
                                @if($path)
                                    <a
                                        href="{{ asset('storage/' . $path) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="document-link"
                                    >
                                        <i class="fas fa-eye"></i>
                                        View File
                                    </a>
                                @else
                                    <span class="no-document">No file uploaded</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ======================== BANK DETAILS ======================== --}}
        <section class="form-panel full-width">
            <div class="panel-header">
                <h2 class="panel-title">Bank Details</h2>
                <span class="panel-edit"><i class="fas fa-university"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid three">
                    @foreach([
                        ['Account Holder', data_get($user, 'account_holder_name')],
                        ['Bank Name', data_get($user, 'bank_name')],
                        ['Account Number', data_get($user, 'account_number')],
                        ['IFSC Code', strtoupper((string) data_get($user, 'ifsc_code'))],
                        ['Account Type', ucfirst((string) data_get($user, 'account_type'))],
                        ['UPI ID', data_get($user, 'upi_id')],
                    ] as [$label, $value])
                        <div class="field-row">
                            <span class="field-label">{{ $label }}</span>
                            <div class="read-only-value">{{ $show($value) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ======================== SALARY DETAILS ======================== --}}
        <section class="form-panel full-width">
            <div class="panel-header">
                <h2 class="panel-title">Salary Details</h2>
                <span class="panel-edit"><i class="fas fa-money-bill-wave"></i> View Only</span>
            </div>

            <div class="panel-body">
                <div class="compact-grid three">
                    @foreach([
                        ['Basic Pay', $money(data_get($user, 'basic_salary'))],
                        ['D.A.', $money(data_get($user, 'dearness_allowance'))],
                        ['Relieving Charge', $money(data_get($user, 'relieving_charge'))],
                        ['Additional Allowance', $money(data_get($user, 'additional_allowance'))],
                        ['Provident Fund %', $show(data_get($user, 'provident_fund'))],
                        ['ESIC %', $show(data_get($user, 'employee_state_insurance_corporation'))],
                    ] as [$label, $value])
                        <div class="field-row">
                            <span class="field-label">{{ $label }}</span>
                            <div class="read-only-value">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ======================== CHANGE PASSWORD ======================== --}}
        <section class="form-panel full-width password-panel">
            <div class="panel-header">
                <h2 class="panel-title">Change Password</h2>
                <span class="panel-edit"><i class="fas fa-key"></i> Security</span>
            </div>

            <form action="{{ route('userPassword', ['user' => $user->id]) }}" method="POST">
                @csrf

                <div class="panel-body">
                    <div class="compact-grid three">
                        <div class="field-row">
                            <label class="field-label" for="current-password">Current Password</label>
                            <input
                                type="password"
                                id="current-password"
                                name="current_password"
                                class="form-control-compact"
                                placeholder="Current password"
                                autocomplete="current-password"
                            >
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="newPassword">New Password</label>
                            <input
                                type="password"
                                id="newPassword"
                                name="new_password"
                                class="form-control-compact"
                                placeholder="New password"
                                autocomplete="new-password"
                            >
                        </div>

                        <div class="field-row">
                            <label class="field-label" for="confirm-password">Confirm Password</label>
                            <input
                                type="password"
                                id="confirm-password"
                                name="confirm_password"
                                class="form-control-compact"
                                placeholder="Confirm new password"
                                autocomplete="new-password"
                            >
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-compact btn-save">
                        <i class="fas fa-key"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </section>

        {{-- ======================== LOGOUT ======================== --}}
        <section class="form-panel full-width">
            <div class="panel-header">
                <h2 class="panel-title">Account</h2>
                <span class="panel-edit"><i class="fas fa-user"></i> Session</span>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <div class="form-actions">
                    <button type="submit" class="btn-compact btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Log Out
                    </button>
                </div>
            </form>
        </section>

    </div>
</div>
@endsection