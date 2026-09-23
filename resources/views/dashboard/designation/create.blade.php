@extends('dashboard.layout.root')

@section('title', 'Create Designation')

@push('styles')
<style>
    .designation-form-page {
        color: #0f172a;
        font-family: 'Inter', sans-serif;
    }

    .form-hero {
        position: relative;
        overflow: hidden;
        border-radius: 26px;
        background:
            linear-gradient(
                135deg,
                #0f172a,
                #172554,
                #312e81
            );
        box-shadow:
            0 22px 55px rgba(15,23,42,.25);
    }

    .form-card {
        border: 1px solid #dbe3ee;
        border-radius: 22px;
        background: #fff;
        padding: 24px;
        box-shadow:
            0 10px 30px rgba(15,23,42,.08);
    }

    .form-control-custom {
        width: 100%;
        min-height: 46px;
        border: 1px solid #dbe3ee !important;
        border-radius: 13px !important;
        background: #fff !important;
        color: #0f172a !important;
        padding: 11px 14px !important;
        font-size: 13px;
        font-weight: 600;
        outline: none;
        transition: .2s ease;
    }

    textarea.form-control-custom {
        min-height: 120px;
        resize: vertical;
    }

    .form-control-custom:focus {
        border-color: #818cf8 !important;
        box-shadow:
            0 0 0 4px rgba(99,102,241,.11) !important;
    }
</style>
@endpush


@section('content')

<div class="designation-form-page space-y-6 pb-10">


    <section class="form-hero p-6 sm:p-8">

        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-slate-900/40 px-3 py-2 text-xs font-extrabold text-white">

            <i class="fas fa-id-badge text-indigo-300"></i>

            Organization Management

        </span>


        <h1 class="mt-4 text-3xl font-extrabold text-white">

            Create Designation

        </h1>


        <p class="mt-2 max-w-2xl text-sm font-medium text-blue-100">

            Office और department के लिए नया designation create करें।

        </p>

    </section>


    @include('dashboard.partials.organization-tabs')


    <section class="form-card">

        <div class="mb-6 border-b border-slate-200 pb-5">

            <h2 class="text-xl font-extrabold text-slate-900">

                Designation Information

            </h2>

            <p class="mt-1 text-sm font-medium text-slate-500">

                नीचे designation की basic details भरें।

            </p>

        </div>


        <form method="POST"
              action="{{ route('designations.store') }}">

            @csrf


            @include(
                'dashboard.designation._form',
                [
                    'submitText' => 'Create Designation'
                ]
            )

        </form>

    </section>

</div>

@endsection