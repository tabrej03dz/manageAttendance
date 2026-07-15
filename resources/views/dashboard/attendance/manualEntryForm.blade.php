@extends('dashboard.layout.root')

@section('title', 'Manual Attendance')

@push('styles')
    <style>
        .manual-attendance-page {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        .manual-attendance-card {
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }

        .manual-attendance-header {
            position: relative;
            overflow: hidden;
            padding: 28px 30px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(255, 255, 255, 0.20),
                    transparent 35%
                ),
                linear-gradient(
                    135deg,
                    #4338ca 0%,
                    #4f46e5 50%,
                    #6366f1 100%
                );
        }

        .manual-attendance-header::before {
            position: absolute;
            top: -70px;
            right: -40px;
            width: 190px;
            height: 190px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            content: '';
        }

        .manual-attendance-header::after {
            position: absolute;
            bottom: -90px;
            left: 30%;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            content: '';
        }

        .manual-attendance-icon {
            display: flex;
            width: 58px;
            height: 58px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            font-size: 23px;
            backdrop-filter: blur(10px);
        }

        .manual-form-section {
            padding: 30px;
        }

        .form-field {
            margin-bottom: 0;
        }

        .field-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 9px;
            color: #334155;
            font-size: 13px;
            font-weight: 800;
        }

        .field-label-icon {
            display: inline-flex;
            width: 28px;
            height: 28px;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 12px;
        }

        .required-mark {
            color: #e11d48;
        }

        .manual-input,
        .manual-select {
            display: block;
            width: 100%;
            min-height: 52px;
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

        .manual-input {
            padding: 12px 15px;
        }

        .manual-select {
            padding: 12px 42px 12px 15px;
            cursor: pointer;
        }

        .manual-input:hover,
        .manual-select:hover {
            border-color: #a5b4fc;
            background: #fefeff;
        }

        .manual-input:focus,
        .manual-select:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.13);
        }

        .manual-input.is-invalid,
        .manual-select.is-invalid {
            border-color: #f43f5e;
            background: #fffafb;
        }

        .manual-input.is-invalid:focus,
        .manual-select.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.12);
        }

        .field-error {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 7px;
            color: #be123c;
            font-size: 12px;
            font-weight: 700;
        }

        .form-help {
            margin-top: 7px;
            color: #64748b;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.5;
        }

        .validation-box {
            border: 1px solid #fda4af;
            border-radius: 18px;
            background: #fff1f2;
            color: #9f1239;
            box-shadow: 0 8px 22px rgba(190, 18, 60, 0.08);
        }

        .form-info-box {
            border: 1px solid #c7d2fe;
            border-radius: 18px;
            background: #eef2ff;
            padding: 16px;
        }

        .form-info-icon {
            display: flex;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #ffffff;
            color: #4f46e5;
            box-shadow: 0 5px 14px rgba(79, 70, 229, 0.10);
        }

        .manual-submit-button {
            display: inline-flex;
            min-height: 52px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 0;
            border-radius: 14px;
            padding: 13px 25px;
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

        .manual-submit-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(79, 70, 229, 0.30);
        }

        .manual-submit-button:focus {
            outline: none;
            box-shadow:
                0 0 0 4px rgba(99, 102, 241, 0.18),
                0 12px 25px rgba(79, 70, 229, 0.24);
        }

        .manual-submit-button:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            transform: none;
        }

        .manual-reset-button {
            display: inline-flex;
            min-height: 52px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 13px 22px;
            background: #f8fafc;
            color: #334155;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none !important;
            transition:
                border-color 0.2s ease,
                background-color 0.2s ease,
                color 0.2s ease;
        }

        .manual-reset-button:hover {
            border-color: #a5b4fc;
            background: #eef2ff;
            color: #4338ca;
        }

        .section-divider {
            height: 1px;
            margin: 26px 0;
            background: #e2e8f0;
        }

        @media (max-width: 767px) {
            .manual-attendance-card {
                border-radius: 18px;
            }

            .manual-attendance-header {
                padding: 22px 20px;
            }

            .manual-form-section {
                padding: 22px 18px;
            }

            .manual-attendance-icon {
                width: 50px;
                height: 50px;
                border-radius: 15px;
                font-size: 20px;
            }

            .manual-form-actions {
                flex-direction: column-reverse;
            }

            .manual-submit-button,
            .manual-reset-button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="manual-attendance-page pb-10">

        {{-- Validation Errors --}}
        @if ($errors->any())
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
                            @foreach ($errors->all() as $error)
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
        @if (session('success'))
            <div
                class="mb-5 rounded-4 border border-success-subtle bg-success-subtle p-4 text-success-emphasis"
            >
                <div class="d-flex align-items-center gap-2 fw-bold">
                    <i class="fas fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <section class="manual-attendance-card">

            {{-- Header --}}
            <div class="manual-attendance-header">
                <div class="position-relative z-1 d-flex align-items-center gap-3">
                    <div class="manual-attendance-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-uppercase fw-bold text-white-50"
                            style="font-size: 11px; letter-spacing: 0.12em;"
                        >
                            Attendance Management
                        </p>

                        <h1 class="mb-1 text-white fw-bold fs-3">
                            Manual Attendance
                        </h1>

                        <p class="mb-0 text-white-50 fw-semibold">
                            Select an employee and enter their attendance timings.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Section --}}
            <div class="manual-form-section">
                <form
                    id="manualAttendanceForm"
                    action="{{ route('attendance.store') }}"
                    method="POST"
                >
                    @csrf

                    <div class="row g-4">

                        {{-- Employee --}}
                        <div class="col-12 col-lg-6">
                            <div class="form-field">
                                <label
                                    for="employee_id"
                                    class="field-label"
                                >
                                    <span class="field-label-icon">
                                        <i class="fas fa-user"></i>
                                    </span>

                                    <span>
                                        Employee
                                        <span class="required-mark">*</span>
                                    </span>
                                </label>

                                <select
                                    name="employee_id"
                                    id="employee_id"
                                    class="manual-select @error('employee_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">
                                        Select Employee
                                    </option>

                                    @foreach ($employees as $employee)
                                        <option
                                            value="{{ $employee->id }}"
                                            {{ (string) old('employee_id') === (string) $employee->id ? 'selected' : '' }}
                                        >
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('employee_id')
                                    <p class="field-error">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                                <p class="form-help">
                                    Choose the employee whose attendance you want to add.
                                </p>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-12 col-lg-6">
                            <div class="form-field">
                                <label
                                    for="date"
                                    class="field-label"
                                >
                                    <span class="field-label-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </span>

                                    <span>
                                        Attendance Date
                                        <span class="required-mark">*</span>
                                    </span>
                                </label>

                                <input
                                    type="date"
                                    id="date"
                                    name="date"
                                    class="manual-input @error('date') is-invalid @enderror"
                                    value="{{ old('date', now()->format('Y-m-d')) }}"
                                    max="{{ now()->format('Y-m-d') }}"
                                    required
                                >

                                @error('date')
                                    <p class="field-error">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                                <p class="form-help">
                                    Select the date for this attendance entry.
                                </p>
                            </div>
                        </div>

                        {{-- Check In --}}
                        <div class="col-12 col-lg-6">
                            <div class="form-field">
                                <label
                                    for="check_in"
                                    class="field-label"
                                >
                                    <span class="field-label-icon">
                                        <i class="fas fa-right-to-bracket"></i>
                                    </span>

                                    <span>
                                        Check In Time
                                        <span class="required-mark">*</span>
                                    </span>
                                </label>

                                <input
                                    type="time"
                                    id="check_in"
                                    name="check_in"
                                    class="manual-input @error('check_in') is-invalid @enderror"
                                    value="{{ old('check_in') }}"
                                    required
                                >

                                @error('check_in')
                                    <p class="field-error">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                                <p class="form-help">
                                    Enter the employee's actual arrival time.
                                </p>
                            </div>
                        </div>

                        {{-- Check Out --}}
                        <div class="col-12 col-lg-6">
                            <div class="form-field">
                                <label
                                    for="check_out"
                                    class="field-label"
                                >
                                    <span class="field-label-icon">
                                        <i class="fas fa-right-from-bracket"></i>
                                    </span>

                                    <span>
                                        Check Out Time
                                        <span class="required-mark">*</span>
                                    </span>
                                </label>

                                <input
                                    type="time"
                                    id="check_out"
                                    name="check_out"
                                    class="manual-input @error('check_out') is-invalid @enderror"
                                    value="{{ old('check_out') }}"
                                    required
                                >

                                @error('check_out')
                                    <p class="field-error">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                                <p class="form-help">
                                    Check out time must be later than check in time.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    {{-- Information --}}
                    <div class="form-info-box">
                        <div class="d-flex align-items-start gap-3">
                            <div class="form-info-icon">
                                <i class="fas fa-circle-info"></i>
                            </div>

                            <div>
                                <p class="mb-1 fw-bold text-dark">
                                    Verify attendance details before saving
                                </p>

                                <p class="mb-0 small fw-semibold text-secondary lh-lg">
                                    Manual attendance directly creates an attendance
                                    record for the selected employee. Please ensure the
                                    employee, date and timings are correct.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div
                        class="manual-form-actions mt-4 d-flex align-items-center justify-content-end gap-3"
                    >
                        <button
                            type="reset"
                            class="manual-reset-button"
                        >
                            <i class="fas fa-rotate-left"></i>
                            Reset Form
                        </button>

                        <button
                            type="submit"
                            id="saveAttendanceButton"
                            class="manual-submit-button"
                        >
                            <i class="fas fa-circle-check"></i>

                            <span>
                                Save Attendance
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
            const attendanceForm =
                document.getElementById('manualAttendanceForm');

            const submitButton =
                document.getElementById('saveAttendanceButton');

            const checkInInput =
                document.getElementById('check_in');

            const checkOutInput =
                document.getElementById('check_out');

            let isSubmitting = false;

            /*
            |--------------------------------------------------------------------------
            | Check Out Time Validation
            |--------------------------------------------------------------------------
            */

            function validateAttendanceTime() {
                checkOutInput.setCustomValidity('');

                if (
                    !checkInInput.value ||
                    !checkOutInput.value
                ) {
                    return true;
                }

                if (
                    checkOutInput.value <=
                    checkInInput.value
                ) {
                    checkOutInput.setCustomValidity(
                        'Check out time must be later than check in time.'
                    );

                    return false;
                }

                return true;
            }

            checkInInput.addEventListener(
                'change',
                validateAttendanceTime
            );

            checkOutInput.addEventListener(
                'change',
                validateAttendanceTime
            );

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Submission
            |--------------------------------------------------------------------------
            */

            attendanceForm.addEventListener(
                'submit',
                function (event) {
                    if (!validateAttendanceTime()) {
                        event.preventDefault();

                        checkOutInput.reportValidity();

                        return;
                    }

                    if (isSubmitting) {
                        event.preventDefault();

                        return;
                    }

                    isSubmitting = true;
                    submitButton.disabled = true;

                    submitButton.innerHTML = `
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Saving Attendance...</span>
                    `;
                }
            );
        });
    </script>
@endpush