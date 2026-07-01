@extends('dashboard.layout.root')

@section('content')
<div class="office-page">
    <div class="container-fluid">

        <div class="page-header">
            <div>
                <h2>Create Office</h2>
                <p>Manage office location, owner and login security settings.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger professional-alert">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('office.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="office-card">
                <div class="section-title">
                    <h4>Basic Office Information</h4>
                    <span>Required details</span>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label">Office Name <span>*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" placeholder="Enter office name" required>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label">Office Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small class="text-muted">Allowed: JPG, PNG, JPEG, WEBP. Max: 2MB</small>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label">Number of Employees</label>
                        <input type="number" name="number_of_employees" class="form-control"
                               value="{{ old('number_of_employees') }}" placeholder="Example: 25">
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label">Latitude <span>*</span></label>
                        <input type="text" name="latitude" class="form-control"
                               value="{{ old('latitude') }}" placeholder="Example: 26.4499" required>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label">Longitude <span>*</span></label>
                        <input type="text" name="longitude" class="form-control"
                               value="{{ old('longitude') }}" placeholder="Example: 80.3319" required>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label">Radius</label>
                        <input type="text" name="radius" class="form-control"
                               value="{{ old('radius') }}" placeholder="Example: 100">
                    </div>

                    @if($owners)
                        <div class="col-lg-6">
                            <label class="form-label">Owner</label>
                            <select name="owner_id" class="form-control">
                                <option value="">Select Owner</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                                        {{ $owner->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="owner_id" value="{{ auth()->user()->id }}">
                    @endif

                    <div class="col-12">
                        <label class="form-label">Full Address</label>
                        <textarea name="address" class="form-control" rows="3"
                                  placeholder="Enter full office address">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-lg-6">
                    <div class="settings-card">
                        <div class="settings-icon location-icon">📍</div>
                        <div class="settings-content">
                            <h5>Location Verification</h5>
                            <p>Require employees to be inside office radius for attendance.</p>

                            <div class="radio-group">
                                <label class="radio-box">
                                    <input type="radio" name="under_radius_required" value="1"
                                        {{ old('under_radius_required') == '1' ? 'checked' : '' }}>
                                    <span>
                                        <strong>Enable</strong>
                                        <small>Radius check required</small>
                                    </span>
                                </label>

                                <label class="radio-box">
                                    <input type="radio" name="under_radius_required" value="0"
                                        {{ old('under_radius_required', '0') == '0' ? 'checked' : '' }}>
                                    <span>
                                        <strong>Disable</strong>
                                        <small>Radius check not required</small>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="settings-card">
                        <div class="settings-icon security-icon">🔐</div>
                        <div class="settings-content">
                            <h5>Login OTP Security</h5>
                            <p>Enable phone OTP login for users of this office.</p>

                            <div class="radio-group">
                                <label class="radio-box">
                                    <input type="radio" name="otp_enable" value="1"
                                        {{ old('otp_enable') == '1' ? 'checked' : '' }}>
                                    <span>
                                        <strong>Enable</strong>
                                        <small>Phone + OTP login</small>
                                    </span>
                                </label>

                                <label class="radio-box">
                                    <input type="radio" name="otp_enable" value="0"
                                        {{ old('otp_enable', '0') == '0' ? 'checked' : '' }}>
                                    <span>
                                        <strong>Disable</strong>
                                        <small>Email/phone + password login</small>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-area">
                <button type="submit" class="submit-btn">
                    Create Office
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .office-page {
        padding: 28px 0;
        background: #f5f7fb;
        min-height: 100vh;
    }

    .page-header {
        background: linear-gradient(135deg, #ffffff, #fff5f5);
        border: 1px solid #f1d4d4;
        border-radius: 18px;
        padding: 22px 26px;
        margin-bottom: 22px;
        box-shadow: 0 10px 30px rgba(0,0,0,.04);
    }

    .page-header h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
    }

    .page-header p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 14px;
    }

    .office-card,
    .settings-card {
        background: #ffffff;
        border: 1px solid #e9edf5;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 14px 35px rgba(17, 24, 39, .06);
    }

    .section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eef2f7;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .section-title h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .section-title span {
        background: #fee2e2;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 7px;
    }

    .form-label span {
        color: #dc2626;
    }

    .form-control {
        height: 46px;
        border-radius: 12px;
        border: 1px solid #d8dee9;
        font-size: 14px;
        color: #111827;
        box-shadow: none;
    }

    input[type="file"].form-control {
        padding: 10px 12px;
    }

    textarea.form-control {
        height: auto;
        min-height: 95px;
    }

    .form-control:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, .08);
    }

    .settings-card {
        display: flex;
        gap: 18px;
        height: 100%;
    }

    .settings-icon {
        min-width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .location-icon {
        background: #eff6ff;
    }

    .security-icon {
        background: #ecfdf5;
    }

    .settings-content {
        width: 100%;
    }

    .settings-content h5 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #111827;
    }

    .settings-content p {
        margin: 5px 0 16px;
        color: #6b7280;
        font-size: 13px;
    }

    .radio-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .radio-box {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px;
        display: flex;
        gap: 10px;
        cursor: pointer;
        transition: .2s ease;
        background: #fbfdff;
    }

    .radio-box:hover {
        border-color: #dc2626;
        background: #fff7f7;
    }

    .radio-box input {
        margin-top: 4px;
        accent-color: #dc2626;
    }

    .radio-box strong {
        display: block;
        color: #111827;
        font-size: 14px;
    }

    .radio-box small {
        display: block;
        color: #6b7280;
        font-size: 12px;
        margin-top: 2px;
    }

    .action-area {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .submit-btn {
        border: none;
        background: linear-gradient(135deg, #dc2626, #991b1b);
        color: #fff;
        padding: 13px 34px;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 700;
        box-shadow: 0 12px 22px rgba(220, 38, 38, .25);
        transition: .2s ease;
    }

    .submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(220, 38, 38, .32);
    }

    .professional-alert {
        border-radius: 14px;
        border: none;
        box-shadow: 0 10px 25px rgba(220, 38, 38, .08);
    }

    @media (max-width: 768px) {
        .office-page {
            padding: 18px 0;
        }

        .page-header,
        .office-card,
        .settings-card {
            border-radius: 14px;
            padding: 18px;
        }

        .settings-card {
            flex-direction: column;
        }

        .radio-group {
            grid-template-columns: 1fr;
        }

        .action-area {
            justify-content: stretch;
        }

        .submit-btn {
            width: 100%;
        }
    }
</style>
@endsection