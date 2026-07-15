```blade
@extends('dashboard.layout.root')

@section('title', 'Create Owner')

@push('styles')
    <style>
        .owner-create-page {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        .owner-create-header {
            position: relative;
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 22px;
            padding: 26px 28px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(99, 102, 241, 0.15),
                    transparent 35%
                ),
                #ffffff;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.09);
        }

        .owner-create-header::after {
            position: absolute;
            top: -70px;
            right: -45px;
            width: 190px;
            height: 190px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.07);
            content: '';
        }

        .page-title-icon {
            display: flex;
            width: 58px;
            height: 58px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: linear-gradient(135deg, #4338ca, #6366f1);
            color: #ffffff;
            font-size: 23px;
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.22);
        }

        .back-owner-button {
            position: relative;
            z-index: 2;
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 13px;
            padding: 11px 18px;
            background: #f8fafc;
            color: #334155 !important;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none !important;
            transition:
                border-color 0.2s ease,
                background-color 0.2s ease,
                color 0.2s ease,
                transform 0.2s ease;
        }

        .back-owner-button:hover {
            border-color: #a5b4fc;
            background: #eef2ff;
            color: #4338ca !important;
            transform: translateY(-1px);
        }

        .owner-form-card {
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.09);
        }

        .form-card-header {
            padding: 22px 26px;
            border-bottom: 1px solid #e2e8f0;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(99, 102, 241, 0.11),
                    transparent 35%
                ),
                #ffffff;
        }

        .form-card-body {
            padding: 28px;
        }

        .form-section {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            padding: 22px;
        }

        .form-section + .form-section {
            margin-top: 22px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .section-heading-icon {
            display: flex;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 16px;
        }

        .section-heading h3 {
            margin: 0;
            color: #0f172a;
            font-size: 17px;
            font-weight: 900;
        }

        .section-heading p {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
        }

        .form-field {
            margin-bottom: 0;
        }

        .field-label {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 800;
        }

        .required-mark {
            color: #e11d48;
        }

        .owner-input,
        .owner-select,
        .owner-textarea {
            display: block;
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            outline: none;
            background: #ffffff;
            color: #0f172a;
            font-size: 14px;
            font-weight: 600;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.2s ease;
        }

        .owner-input,
        .owner-select {
            min-height: 52px;
            padding: 12px 15px;
        }

        .owner-textarea {
            min-height: 112px;
            resize: vertical;
            padding: 13px 15px;
            line-height: 1.6;
        }

        .owner-input:hover,
        .owner-select:hover,
        .owner-textarea:hover {
            border-color: #a5b4fc;
            background: #fefeff;
        }

        .owner-input:focus,
        .owner-select:focus,
        .owner-textarea:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.13);
        }

        .owner-input.is-invalid,
        .owner-select.is-invalid,
        .owner-textarea.is-invalid {
            border-color: #f43f5e;
            background: #fffafb;
        }

        .field-error {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 7px 0 0;
            color: #be123c;
            font-size: 12px;
            font-weight: 700;
        }

        .field-help {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.5;
        }

        .photo-upload-box {
            position: relative;
            overflow: hidden;
            border: 2px dashed #c7d2fe;
            border-radius: 16px;
            background: #f8faff;
            padding: 18px;
            transition:
                border-color 0.2s ease,
                background-color 0.2s ease;
        }

        .photo-upload-box:hover {
            border-color: #818cf8;
            background: #eef2ff;
        }

        .photo-preview-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .photo-preview {
            display: flex;
            width: 72px;
            height: 72px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 18px;
            background: #ffffff;
            color: #6366f1;
            font-size: 26px;
            box-shadow: 0 7px 18px rgba(15, 23, 42, 0.08);
        }

        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-input {
            width: 100%;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
        }

        .photo-input::file-selector-button {
            margin-right: 12px;
            border: 0;
            border-radius: 10px;
            padding: 9px 13px;
            background: #4f46e5;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }

        .permissions-wrapper {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .permission-item {
            position: relative;
        }

        .permission-checkbox {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
        }

        .permission-label {
            display: flex;
            min-height: 54px;
            cursor: pointer;
            align-items: center;
            gap: 10px;
            border: 1px solid #dbe3ee;
            border-radius: 14px;
            padding: 11px 13px;
            background: #ffffff;
            color: #475569;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.4;
            transition:
                border-color 0.2s ease,
                background-color 0.2s ease,
                color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .permission-check-icon {
            display: flex;
            width: 25px;
            height: 25px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            color: transparent;
            font-size: 11px;
            transition:
                border-color 0.2s ease,
                background-color 0.2s ease,
                color 0.2s ease;
        }

        .permission-label:hover {
            border-color: #a5b4fc;
            background: #f8faff;
            color: #4338ca;
        }

        .permission-checkbox:checked + .permission-label {
            border-color: #818cf8;
            background: #eef2ff;
            color: #3730a3;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.10);
        }

        .permission-checkbox:checked
        + .permission-label
        .permission-check-icon {
            border-color: #4f46e5;
            background: #4f46e5;
            color: #ffffff;
        }

        .validation-box {
            border: 1px solid #fda4af;
            border-radius: 18px;
            background: #fff1f2;
            color: #9f1239;
            box-shadow: 0 8px 22px rgba(190, 18, 60, 0.08);
        }

        .form-note-box {
            border: 1px solid #c7d2fe;
            border-radius: 16px;
            background: #eef2ff;
            padding: 15px;
        }

        .register-owner-button {
            display: inline-flex;
            min-height: 52px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 0;
            border-radius: 14px;
            padding: 13px 24px;
            background: linear-gradient(135deg, #4338ca, #6366f1);
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            box-shadow: 0 12px 25px rgba(79, 70, 229, 0.24);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                opacity 0.2s ease;
        }

        .register-owner-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(79, 70, 229, 0.30);
        }

        .register-owner-button:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            transform: none;
        }

        .reset-form-button {
            display: inline-flex;
            min-height: 52px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 13px 21px;
            background: #f8fafc;
            color: #334155;
            font-size: 14px;
            font-weight: 800;
            transition:
                border-color 0.2s ease,
                background-color 0.2s ease,
                color 0.2s ease;
        }

        .reset-form-button:hover {
            border-color: #a5b4fc;
            background: #eef2ff;
            color: #4338ca;
        }

        @media (max-width: 991px) {
            .permissions-wrapper {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .owner-create-header {
                padding: 21px 18px;
                border-radius: 18px;
            }

            .owner-create-header-content {
                flex-direction: column;
                align-items: stretch !important;
            }

            .owner-create-heading {
                align-items: flex-start !important;
            }

            .back-owner-button {
                width: 100%;
            }

            .owner-form-card {
                border-radius: 18px;
            }

            .form-card-header,
            .form-card-body {
                padding: 20px 17px;
            }

            .form-section {
                padding: 18px 15px;
            }

            .permissions-wrapper {
                grid-template-columns: 1fr;
            }

            .photo-preview-wrapper {
                align-items: flex-start;
                flex-direction: column;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .register-owner-button,
            .reset-form-button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="owner-create-page pb-10">

        {{-- Page Header --}}
        <section class="owner-create-header mb-5">
            <div
                class="owner-create-header-content position-relative z-1 d-flex align-items-center justify-content-between gap-4"
            >
                <div class="owner-create-heading d-flex align-items-center gap-3">
                    <div class="page-title-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-uppercase fw-bold"
                            style="font-size: 11px; letter-spacing: 0.12em; color: #4f46e5;"
                        >
                            Owner Management
                        </p>

                        <h1 class="mb-1 fw-bold text-dark">
                            Create Owner
                        </h1>

                        <p class="mb-0 text-secondary fw-semibold">
                            Add owner details, subscription plan and permissions.
                        </p>
                    </div>
                </div>

                @if(Route::has('owner.index'))
                    <a
                        href="{{ route('owner.index') }}"
                        class="back-owner-button"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Owners
                    </a>
                @endif
            </div>
        </section>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="validation-box mb-5 p-4">
                <div class="d-flex align-items-start gap-3">
                    <div
                        class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-3 bg-white text-danger"
                        style="width: 42px; height: 42px;"
                    >
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                    <div>
                        <h3 class="mb-1 fs-6 fw-bold">
                            Please fix the following errors
                        </h3>

                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li class="mt-1 small fw-semibold">
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Card --}}
        <section class="owner-form-card">
            <div class="form-card-header">
                <h2 class="mb-1 fs-5 fw-bold text-dark">
                    Owner Registration Form
                </h2>

                <p class="mb-0 small fw-semibold text-secondary">
                    Fields marked with an asterisk are required.
                </p>
            </div>

            <div class="form-card-body">
                <form
                    id="ownerCreateForm"
                    action="{{ route('owner.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf

                    {{-- Owner Details --}}
                    <section class="form-section">
                        <div class="section-heading">
                            <div class="section-heading-icon">
                                <i class="fas fa-user"></i>
                            </div>

                            <div>
                                <h3>Owner Details</h3>
                                <p>Enter personal and contact information.</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Full Name --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="name"
                                        class="field-label"
                                    >
                                        Full Name
                                        <span class="required-mark">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        class="owner-input @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}"
                                        placeholder="Enter owner's full name"
                                        autocomplete="name"
                                        required
                                    >

                                    @error('name')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="email"
                                        class="field-label"
                                    >
                                        Email Address
                                        <span class="required-mark">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="owner-input @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}"
                                        placeholder="Enter email address"
                                        autocomplete="email"
                                        required
                                    >

                                    @error('email')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="phone"
                                        class="field-label"
                                    >
                                        Phone Number
                                        <span class="required-mark">*</span>
                                    </label>

                                    <input
                                        type="tel"
                                        id="phone"
                                        name="phone"
                                        class="owner-input @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}"
                                        placeholder="Enter phone number"
                                        autocomplete="tel"
                                        inputmode="numeric"
                                        required
                                    >

                                    @error('phone')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="address"
                                        class="field-label"
                                    >
                                        Full Address
                                    </label>

                                    <textarea
                                        id="address"
                                        name="address"
                                        class="owner-textarea @error('address') is-invalid @enderror"
                                        placeholder="Enter complete address"
                                    >{{ old('address') }}</textarea>

                                    @error('address')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Photo --}}
                            <div class="col-12">
                                <div class="form-field">
                                    <label
                                        for="photo"
                                        class="field-label"
                                    >
                                        Owner Photo
                                    </label>

                                    <div class="photo-upload-box">
                                        <div class="photo-preview-wrapper">
                                            <div
                                                id="photoPreview"
                                                class="photo-preview"
                                            >
                                                <i class="fas fa-user"></i>
                                            </div>

                                            <div class="flex-grow-1">
                                                <input
                                                    type="file"
                                                    id="photo"
                                                    name="photo"
                                                    class="photo-input"
                                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                                >

                                                <p class="field-help mb-0">
                                                    Upload JPG, PNG or WebP image.
                                                    Recommended square image size is 500 × 500 pixels.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @error('photo')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Plan Details --}}
                    <section class="form-section">
                        <div class="section-heading">
                            <div class="section-heading-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>

                            <div>
                                <h3>Create Plan</h3>
                                <p>Configure owner account usage limits and duration.</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Number of Offices --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="number_of_offices"
                                        class="field-label"
                                    >
                                        Number of Offices / Shops
                                        <span class="required-mark">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        id="number_of_offices"
                                        name="number_of_offices"
                                        class="owner-input @error('number_of_offices') is-invalid @enderror"
                                        value="{{ old('number_of_offices') }}"
                                        placeholder="Example: 5"
                                        min="1"
                                        step="1"
                                        required
                                    >

                                    @error('number_of_offices')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Number of Employees --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="number_of_employees"
                                        class="field-label"
                                    >
                                        Number of Employees / Workers
                                        <span class="required-mark">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        id="number_of_employees"
                                        name="number_of_employees"
                                        class="owner-input @error('number_of_employees') is-invalid @enderror"
                                        value="{{ old('number_of_employees') }}"
                                        placeholder="Example: 10"
                                        min="1"
                                        step="1"
                                        required
                                    >

                                    @error('number_of_employees')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="price"
                                        class="field-label"
                                    >
                                        Plan Price
                                        <span class="required-mark">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        id="price"
                                        name="price"
                                        class="owner-input @error('price') is-invalid @enderror"
                                        value="{{ old('price') }}"
                                        placeholder="Example: 399"
                                        min="0"
                                        step="0.01"
                                        required
                                    >

                                    @error('price')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Duration --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="duration"
                                        class="field-label"
                                    >
                                        Duration in Days
                                        <span class="required-mark">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        id="duration"
                                        name="duration"
                                        class="owner-input @error('duration') is-invalid @enderror"
                                        value="{{ old('duration') }}"
                                        placeholder="Example: 90"
                                        min="1"
                                        step="1"
                                        required
                                    >

                                    @error('duration')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label
                                        for="start_date"
                                        class="field-label"
                                    >
                                        Plan Start Date
                                    </label>

                                    <input
                                        type="date"
                                        id="start_date"
                                        name="start_date"
                                        class="owner-input @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date', now()->format('Y-m-d')) }}"
                                    >

                                    @error('start_date')
                                        <p class="field-error">
                                            <i class="fas fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror

                                    <p class="field-help">
                                        The plan duration will be calculated from this date.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Permissions --}}
                    <section class="form-section">
                        <div class="section-heading">
                            <div class="section-heading-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>

                            <div>
                                <h3>Permissions</h3>
                                <p>Select the features this owner can access.</p>
                            </div>
                        </div>

                        @if($permissions->count() > 0)
                            <div class="permissions-wrapper">
                                @foreach($permissions as $permission)
                                    @php
                                        $isChecked =
                                            in_array(
                                                $permission->name,
                                                old(
                                                    'permissions',
                                                    $defaultPermissions
                                                )
                                            );
                                    @endphp

                                    <div class="permission-item">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            id="permission_{{ $permission->id }}"
                                            value="{{ $permission->name }}"
                                            class="permission-checkbox"
                                            {{ $isChecked ? 'checked' : '' }}
                                        >

                                        <label
                                            for="permission_{{ $permission->id }}"
                                            class="permission-label"
                                        >
                                            <span class="permission-check-icon">
                                                <i class="fas fa-check"></i>
                                            </span>

                                            <span>
                                                {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="form-note-box">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="fas fa-circle-info mt-1 text-primary"></i>

                                    <p class="mb-0 small fw-semibold text-secondary">
                                        No permissions are currently available.
                                    </p>
                                </div>
                            </div>
                        @endif

                        @error('permissions')
                            <p class="field-error">
                                <i class="fas fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </section>

                    {{-- Important Information --}}
                    <div class="form-note-box mt-4">
                        <div class="d-flex align-items-start gap-3">
                            <div
                                class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-3 bg-white"
                                style="width: 40px; height: 40px; color: #4f46e5;"
                            >
                                <i class="fas fa-circle-info"></i>
                            </div>

                            <div>
                                <p class="mb-1 fw-bold text-dark">
                                    Verify all information before registration
                                </p>

                                <p class="mb-0 small fw-semibold text-secondary lh-lg">
                                    The owner account, plan and selected permissions will
                                    be created together after submitting this form.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div
                        class="form-actions mt-4 d-flex align-items-center justify-content-end gap-3"
                    >
                        <button
                            type="reset"
                            class="reset-form-button"
                        >
                            <i class="fas fa-rotate-left"></i>
                            Reset Form
                        </button>

                        <button
                            type="submit"
                            id="registerOwnerButton"
                            class="register-owner-button"
                        >
                            <i class="fas fa-user-check"></i>

                            <span>
                                Register Owner
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ownerForm =
                document.getElementById('ownerCreateForm');

            const submitButton =
                document.getElementById('registerOwnerButton');

            const photoInput =
                document.getElementById('photo');

            const photoPreview =
                document.getElementById('photoPreview');

            let isSubmitting = false;

            /*
            |--------------------------------------------------------------------------
            | Photo Preview
            |--------------------------------------------------------------------------
            */

            photoInput.addEventListener('change', function () {
                const file =
                    this.files && this.files[0]
                        ? this.files[0]
                        : null;

                if (!file) {
                    photoPreview.innerHTML =
                        '<i class="fas fa-user"></i>';

                    return;
                }

                if (!file.type.startsWith('image/')) {
                    this.value = '';

                    photoPreview.innerHTML =
                        '<i class="fas fa-user"></i>';

                    alert(
                        'Please select a valid image file.'
                    );

                    return;
                }

                const reader =
                    new FileReader();

                reader.onload = function (event) {
                    photoPreview.innerHTML = `
                        <img
                            src="${event.target.result}"
                            alt="Owner photo preview"
                        >
                    `;
                };

                reader.readAsDataURL(file);
            });

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Submission
            |--------------------------------------------------------------------------
            */

            ownerForm.addEventListener(
                'submit',
                function (event) {
                    if (isSubmitting) {
                        event.preventDefault();

                        return;
                    }

                    if (!ownerForm.checkValidity()) {
                        return;
                    }

                    isSubmitting = true;
                    submitButton.disabled = true;

                    submitButton.innerHTML = `
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Registering Owner...</span>
                    `;
                }
            );
        });
    </script>
@endpush
```
