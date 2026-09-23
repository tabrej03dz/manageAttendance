@extends('dashboard.layout.root')

@section('title', 'Designation Details')


@section('content')

<div class="space-y-6 pb-10">


    {{-- Header --}}
    <section class="overflow-hidden rounded-[26px] bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-900 p-6 shadow-2xl sm:p-8">

        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-black/20 px-3 py-2 text-xs font-extrabold text-white">

                    <i class="fas fa-id-badge text-indigo-300"></i>

                    Designation Details

                </span>


                <h1 class="mt-4 text-3xl font-extrabold text-white">

                    {{ $designation->name }}

                </h1>


                <p class="mt-2 text-sm font-semibold text-blue-100">

                    {{ $designation->code ?: 'No designation code' }}

                </p>

            </div>


            <div class="flex flex-col gap-2 sm:flex-row">

                <a href="{{ route(
                    'designations.edit',
                    $designation
                ) }}"
                   class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl bg-white px-5 text-sm font-extrabold text-indigo-700">

                    <i class="fas fa-pen"></i>

                    Edit

                </a>


                <a href="{{ route('designations.index') }}"
                   class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/10 px-5 text-sm font-extrabold text-white">

                    <i class="fas fa-arrow-left"></i>

                    Back

                </a>

            </div>

        </div>

    </section>


    @include('dashboard.partials.organization-tabs')


    {{-- Details --}}
    <section class="rounded-[22px] border border-slate-200 bg-white p-6 shadow-sm">


        <div class="mb-6 border-b border-slate-200 pb-5">

            <h2 class="text-xl font-extrabold text-slate-900">

                Basic Information

            </h2>

            <p class="mt-1 text-sm font-medium text-slate-500">

                Complete designation information.

            </p>

        </div>


        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">


            {{-- Name --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Designation Name

                </div>

                <div class="mt-2 text-base font-extrabold text-slate-900">

                    {{ $designation->name }}

                </div>

            </div>


            {{-- Code --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Code

                </div>

                <div class="mt-2 text-base font-extrabold text-slate-900">

                    {{ $designation->code ?? '—' }}

                </div>

            </div>


            {{-- Office --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Office

                </div>

                <div class="mt-2 text-base font-extrabold text-slate-900">

                    {{ $designation->office?->name ?? '—' }}

                </div>

            </div>


            {{-- Department --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Department

                </div>

                <div class="mt-2 text-base font-extrabold text-slate-900">

                    {{ $designation->department?->name ?? '—' }}

                </div>

            </div>


            {{-- Level --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Hierarchy Level

                </div>

                <div class="mt-2 text-base font-extrabold text-slate-900">

                    {{ $designation->level ?? '—' }}

                </div>

            </div>


            {{-- Sort --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Sort Order

                </div>

                <div class="mt-2 text-base font-extrabold text-slate-900">

                    {{ $designation->sort_order }}

                </div>

            </div>


            {{-- Status --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Status

                </div>

                <div class="mt-2">

                    @if($designation->is_active)

                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-extrabold text-emerald-700">

                            <i class="fas fa-check-circle"></i>

                            Active

                        </span>

                    @else

                        <span class="inline-flex items-center gap-2 rounded-full bg-red-100 px-3 py-1.5 text-xs font-extrabold text-red-700">

                            <i class="fas fa-times-circle"></i>

                            Inactive

                        </span>

                    @endif

                </div>

            </div>


            {{-- Created by --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Created By

                </div>

                <div class="mt-2 text-base font-extrabold text-slate-900">

                    {{ $designation->creator?->name ?? '—' }}

                </div>

            </div>


            {{-- Created Date --}}
            <div class="rounded-2xl bg-slate-50 p-4">

                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                    Created Date

                </div>

                <div class="mt-2 text-base font-extrabold text-slate-900">

                    {{
                        $designation->created_at
                            ?->format('d M Y, h:i A')
                        ?? '—'
                    }}

                </div>

            </div>

        </div>


        {{-- Description --}}
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">

            <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">

                Description

            </div>

            <div class="mt-3 whitespace-pre-line text-sm font-medium leading-7 text-slate-700">

                {{ $designation->description ?: 'No description available.' }}

            </div>

        </div>

    </section>

</div>

@endsection