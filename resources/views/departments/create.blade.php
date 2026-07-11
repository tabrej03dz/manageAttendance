@extends('dashboard.layout.root')

@section('title', 'Create Department')

@push('styles')
<style>
    .department-form-page {
        color: #0f172a;
        font-family: 'Inter', sans-serif;
    }

    .department-form-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #312e81;
        border-radius: 26px;
        background:
            linear-gradient(
                135deg,
                #0f172a 0%,
                #172554 52%,
                #312e81 100%
            );
        box-shadow:
            0 22px 55px rgba(15, 23, 42, 0.28);
        isolation: isolate;
    }

    .department-form-hero::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -90px;
        width: 310px;
        height: 310px;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.34);
        filter: blur(12px);
        z-index: -1;
    }

    .department-form-hero::after {
        content: '';
        position: absolute;
        bottom: -175px;
        left: 35%;
        width: 280px;
        height: 280px;
        border-radius: 999px;
        background: rgba(6, 182, 212, 0.18);
        filter: blur(18px);
        z-index: -1;
    }

    .department-form-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.24);
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.55);
        color: #ffffff;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
    }

    .department-form-card {
        overflow: hidden;
        border: 1px solid #d8e0ea;
        border-radius: 22px;
        background: #ffffff;
        box-shadow:
            0 10px 30px rgba(15, 23, 42, 0.10);
    }

    .department-form-card-header {
        border-bottom: 1px solid #e2e8f0;
        background:
            linear-gradient(
                90deg,
                rgba(238, 242, 255, 0.88),
                rgba(236, 254, 255, 0.65)
            );
    }

    .department-input {
        width: 100%;
        min-height: 50px;
        border: 1px solid #dbe3ee;
        border-radius: 14px;
        background: #ffffff;
        color: #0f172a;
        padding: 12px 15px;
        font-size: 14px;
        font-weight: 500;
        outline: none;
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .department-input::placeholder {
        color: #94a3b8;
    }

    .department-input:focus {
        border-color: #818cf8;
        box-shadow:
            0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    .department-input.input-error {
        border-color: #fb7185;
        background: #fff1f2;
    }

    .department-input.input-error:focus {
        border-color: #e11d48;
        box-shadow:
            0 0 0 4px rgba(225, 29, 72, 0.12);
    }

    .department-submit-button,
    .department-cancel-button {
        display: inline-flex;
        min-height: 46px;
        align-items: center;
        justify-content: center;
        gap: 9px;
        border-radius: 14px;
        padding: 11px 18px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            filter 0.2s ease;
    }

    .department-submit-button {
        border: none;
        background:
            linear-gradient(
                135deg,
                #4f46e5,
                #7c3aed
            );
        color: #ffffff;
        box-shadow:
            0 12px 25px rgba(79, 70, 229, 0.25);
    }

    .department-submit-button:hover {
        color: #ffffff;
        transform: translateY(-2px);
        filter: brightness(1.06);
        box-shadow:
            0 17px 32px rgba(79, 70, 229, 0.32);
    }

    .department-cancel-button {
        border: 1px solid #dbe3ee;
        background: #f8fafc;
        color: #475569;
    }

    .department-cancel-button:hover {
        color: #0f172a;
        background: #f1f5f9;
        transform: translateY(-2px);
        box-shadow:
            0 10px 22px rgba(15, 23, 42, 0.10);
    }

    .department-icon-box {
        display: flex;
        width: 54px;
        height: 54px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 1px solid #c7d2fe;
        border-radius: 17px;
        background:
            linear-gradient(
                135deg,
                #eef2ff,
                #e0e7ff
            );
        color: #4f46e5;
        font-size: 20px;
        box-shadow:
            0 10px 24px rgba(79, 70, 229, 0.12);
    }

    @media (max-width: 640px) {
        .department-form-hero {
            border-radius: 20px;
        }

        .department-form-card {
            border-radius: 18px;
        }

        .department-submit-button,
        .department-cancel-button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="department-form-page space-y-6 pb-10">

    {{-- Page Header --}}
    <section class="department-form-hero p-6 sm:p-8">

        <div class="relative">

            <span class="department-form-badge">
                <span class="h-2 w-2 animate-pulse rounded-full bg-cyan-400"></span>

                Department Management
            </span>

            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Create Department
            </h1>

            <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">
                Organization के लिए नया department add करें।
            </p>

            <div class="mt-5 flex flex-wrap items-center gap-4 text-sm font-semibold text-slate-200">

                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-sitemap text-indigo-300"></i>

                    New Department
                </span>

                <span class="inline-flex items-center gap-2">
                    <i class="far fa-calendar-alt text-cyan-300"></i>

                    {{ now()->format('d F Y') }}
                </span>
            </div>
        </div>
    </section>

    {{-- Form Card --}}
    <section class="department-form-card mx-auto max-w-3xl">

        <div class="department-form-card-header px-5 py-5 sm:px-7">

            <div class="flex items-center gap-4">

                <div class="department-icon-box">
                    <i class="fas fa-plus"></i>
                </div>

                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Department Details
                    </h2>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Department का सही और unique नाम enter करें।
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('departments.store') }}"
              method="POST"
              class="p-5 sm:p-7">

            @csrf

            <div>
                <label for="department-name"
                       class="mb-2 block text-sm font-bold text-slate-700">

                    Department Name

                    <span class="text-rose-500">*</span>
                </label>

                <input type="text"
                       id="department-name"
                       name="name"
                       value="{{ old('name') }}"
                       autocomplete="off"
                       autofocus
                       placeholder="Example: Human Resources"
                       class="department-input @error('name') input-error @enderror">

                @error('name')
                    <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-rose-600">
                        <i class="fas fa-exclamation-circle"></i>

                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-xs font-medium text-slate-500">
                    ऐसा नाम लिखें जिससे department आसानी से identify हो सके।
                </p>
            </div>

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">

                <a href="{{ route('departments.index') }}"
                   class="department-cancel-button">

                    <i class="fas fa-arrow-left"></i>

                    Cancel
                </a>

                <button type="submit"
                        class="department-submit-button">

                    <i class="fas fa-save"></i>

                    Save Department
                </button>
            </div>
        </form>
    </section>
</div>

@endsection