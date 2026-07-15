```blade
@extends('dashboard.layout.root')

@section('title', 'Edit Owner')

@push('styles')
    <style>
        .owner-edit-page {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        .owner-edit-header {
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

        .owner-edit-header::after {
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

        .owner-input {
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
        .owner-textarea:hover {
            border-color: #a5b4fc;
            background: #fefeff;
        }

        .owner-input:focus,
        .owner-textarea:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.13);
        }

        .owner-input.is-invalid,
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
            gap: 16px;
        }

        .photo-preview {
            display: flex;
            width: 86px;
            height: 86px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 3px solid #ffffff;
            border-radius: 20px;
            background: #eef2ff;
            color: #6366f1;
            font-size: 28px;
            box-shadow:
                0 0 0 1px #dbe3ee,
                0 8px 20px rgba(15, 23, 42, 0.10);
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

        .update-owner-button {
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

        .update-owner-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(79, 70, 229, 0.30);
        }

        .update-owner-button:disabled {
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

        @media (max-width: 767px) {
            .owner-edit-header {
                padding: 21px 18px;
                border-radius: 18px;
            }

            .owner-edit-header-content {
                flex-direction: column;
                align-items: stretch !important;
            }

            .owner-edit-heading {
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

            .photo-preview-wrapper {
                align-items: flex-start;
                flex-direction: column;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .update-owner-button,
            .reset-form-button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="owner-edit-page pb-10">

        {{-- Page Header --}}
        <section class="owner-edit-header mb-5">
            <div
                class="owner-edit-header-content position-relative z-1 d-flex align-items-center justify-content-between gap-4"
            >
                <div class="owner-edit-heading d-flex align-items-center gap-3">
                    <div class="page-title-icon">
                        <i class="fas fa-user-pen"></i>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-uppercase fw-bold"
                            style="font-size: 11px; letter-spacing: 0.12em; color: #4f46e5;"
                        >
                            Owner Management
                        </p>

                        <h1 class="mb-1 fw-bold text-dark">
                            Edit Owner
                        </h1>

                        <p class="mb-0 text-secondary fw-semibold">
                            Update owner profile and contact information.
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

        {{-- Success Message --}}
        @if(session('success'))
            <div
                class="mb-5 rounded-4 border border-success-subtle bg-success-subtle p-4 text-success-emphasis"
            >
                <div class="d-flex align-items-center gap-2 fw-bold">
                    <i class="fas fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Form Card --}}
        <section class="owner-form-card">
            <div class="form-card-header">
                <h2 class="mb-1 fs-5 fw-bold text-dark">
                    Owner Profile
                </h2>

                <p class="mb-0 small fw-semibold text-secondary">
                    Update the information below and save your changes.
                </p>
            </div>

            <div class="form-card-body">
                <form
                    id="ownerEditForm"
                    action="{{ route('owner.update', ['owner' => $owner->id]) }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf

                    {{-- Add this only if your update route uses PUT/PATCH --}}
                    {{-- @method('PUT') --}}

                    <section class="form-section">
                        <div class="section-heading">
                            <div class="section-heading-icon">
                                <i class="fas fa-address-card"></i>
                            </div>

                            <div>
                                <h3>Personal Information</h3>
                                <p>Update the owner's name and contact details.</p>
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
                                        value="{{ old('name', $owner->name) }}"
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
                                        value="{{ old('email', $owner->email) }}"
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
                                        value="{{ old('phone', $owner->phone) }}"
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
                                    >{{ old('address', $owner->address) }}</textarea>

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
                                                @if($owner->photo)
                                                    <img
                                                        src="{{ asset('storage/' . $owner->photo) }}"
                                                        alt="{{ $owner->name }}"
                                                        id="currentOwnerPhoto"
                                                    >
                                                @else
                                                    <i class="fas fa-user"></i>
                                                @endif
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
                                                    Leave this field empty to keep the current photo.
                                                    Upload JPG, PNG or WebP image only.
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

                    {{-- Information --}}
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
                                    Review information before updating
                                </p>

                                <p class="mb-0 small fw-semibold text-secondary lh-lg">
                                    Saving this form will update the owner's profile.
                                    Existing plan details will remain unchanged.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div
                        class="form-actions mt-4 d-flex align-items-center justify-content-end gap-3"
                    >
                        <button
                            type="reset"
                            id="resetOwnerFormButton"
                            class="reset-form-button"
                        >
                            <i class="fas fa-rotate-left"></i>
                            Reset Changes
                        </button>

                        <button
                            type="submit"
                            id="updateOwnerButton"
                            class="update-owner-button"
                        >
                            <i class="fas fa-floppy-disk"></i>

                            <span>
                                Update Owner
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
                document.getElementById('ownerEditForm');

            const submitButton =
                document.getElementById('updateOwnerButton');

            const resetButton =
                document.getElementById('resetOwnerFormButton');

            const photoInput =
                document.getElementById('photo');

            const photoPreview =
                document.getElementById('photoPreview');

            const originalPhotoHtml =
                photoPreview.innerHTML;

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
                        originalPhotoHtml;

                    return;
                }

                if (!file.type.startsWith('image/')) {
                    this.value = '';

                    photoPreview.innerHTML =
                        originalPhotoHtml;

                    alert(
                        'Please select a valid image file.'
                    );

                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!allowedTypes.includes(file.type)) {
                    this.value = '';

                    photoPreview.innerHTML =
                        originalPhotoHtml;

                    alert(
                        'Only JPG, PNG and WebP images are allowed.'
                    );

                    return;
                }

                const maxFileSize =
                    5 * 1024 * 1024;

                if (file.size > maxFileSize) {
                    this.value = '';

                    photoPreview.innerHTML =
                        originalPhotoHtml;

                    alert(
                        'The selected image must not be larger than 5 MB.'
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
            | Reset Form
            |--------------------------------------------------------------------------
            */

            resetButton.addEventListener(
                'click',
                function () {
                    window.setTimeout(
                        function () {
                            photoPreview.innerHTML =
                                originalPhotoHtml;
                        },
                        0
                    );
                }
            );

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
                        <span>Updating Owner...</span>
                    `;
                }
            );
        });
    </script>
@endpush