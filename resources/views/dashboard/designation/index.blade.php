@extends('dashboard.layout.root')

@section('title', 'Designations')

@push('styles')
<style>
    .designation-page {
        color: #0f172a;
        font-family: 'Inter', sans-serif;
    }

    .designation-hero {
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
            0 22px 55px rgba(15, 23, 42, .28);
        isolation: isolate;
    }

    .designation-hero::before {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        right: -100px;
        top: -130px;
        border-radius: 999px;
        background: rgba(124, 58, 237, .35);
        filter: blur(14px);
        z-index: -1;
    }

    .designation-hero::after {
        content: '';
        position: absolute;
        width: 260px;
        height: 260px;
        left: 30%;
        bottom: -180px;
        border-radius: 999px;
        background: rgba(6, 182, 212, .20);
        filter: blur(18px);
        z-index: -1;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 999px;
        background: rgba(15,23,42,.50);
        padding: 7px 12px;
        color: #fff;
        font-size: 12px;
        font-weight: 800;
    }

    .designation-card {
        overflow: hidden;
        border: 1px solid #d8e0ea;
        border-radius: 22px;
        background: #fff;
        box-shadow:
            0 10px 30px rgba(15,23,42,.10);
    }

    .designation-card-header {
        border-bottom: 1px solid #e2e8f0;
        background:
            linear-gradient(
                90deg,
                rgba(238,242,255,.88),
                rgba(236,254,255,.65)
            );
    }

    .primary-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 46px;
        border-radius: 14px;
        padding: 11px 18px;
        background:
            linear-gradient(
                135deg,
                #4f46e5,
                #7c3aed
            );
        color: #fff !important;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        box-shadow:
            0 12px 25px rgba(79,70,229,.25);
    }

    .primary-button:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
    }

    .filter-input {
        width: 100%;
        min-height: 44px;
        border: 1px solid #dbe3ee !important;
        border-radius: 12px !important;
        background: #fff !important;
        padding: 10px 12px !important;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a !important;
        outline: none;
    }

    .filter-input:focus {
        border-color: #818cf8 !important;
        box-shadow:
            0 0 0 4px rgba(99,102,241,.10) !important;
    }

    .designation-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .designation-table thead {
        background: #f8fafc;
    }

    .designation-table th {
        border-bottom: 1px solid #e2e8f0;
        padding: 15px 18px;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .07em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .designation-table td {
        border-bottom: 1px solid #f1f5f9;
        padding: 15px 18px;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .designation-table tbody tr:hover {
        background: #f8fafc;
    }

    .designation-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        border: 1px solid #ddd6fe;
        border-radius: 14px;
        background:
            linear-gradient(
                135deg,
                #f5f3ff,
                #ede9fe
            );
        color: #7c3aed;
    }

    .serial-box {
        display: inline-flex;
        width: 36px;
        height: 36px;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #eff6ff;
        color: #2563eb;
        font-weight: 800;
    }

    .status-active {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 800;
    }

    .status-inactive {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        background: #fff1f2;
        color: #be123c;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 800;
    }

    .action-button {
        display: inline-flex;
        width: 38px;
        height: 38px;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        color: #fff !important;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .action-view {
        background: #0891b2;
    }

    .action-edit {
        background: #4f46e5;
    }

    .action-delete {
        background: #e11d48;
    }

    .action-status {
        background: #475569;
    }

    @media (max-width: 767px) {
        .designation-hero {
            border-radius: 20px;
        }

        .primary-button {
            width: 100%;
        }
    }
</style>
@endpush


@section('content')

<div class="designation-page space-y-6 pb-10">


    {{-- Hero --}}
    <section class="designation-hero p-6 sm:p-8">

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <span class="hero-badge">

                    <span class="h-2 w-2 animate-pulse rounded-full bg-cyan-400"></span>

                    Organization Management

                </span>

                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">

                    Designations

                </h1>

                <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">

                    Office और department के अनुसार employee
                    designations manage करें।

                </p>

                <div class="mt-5 flex flex-wrap items-center gap-4 text-sm font-semibold text-slate-200">

                    <span class="inline-flex items-center gap-2">

                        <i class="fas fa-id-badge text-indigo-300"></i>

                        {{ $designations->total() }}

                        Designations

                    </span>


                    <span class="inline-flex items-center gap-2">

                        <i class="far fa-calendar-alt text-cyan-300"></i>

                        {{ now()->format('d F Y') }}

                    </span>

                </div>

            </div>


            <div class="w-full lg:w-auto">

                <a href="{{ route('designations.create') }}"
                   class="primary-button">

                    <i class="fas fa-plus-circle"></i>

                    Create Designation

                </a>

            </div>

        </div>

    </section>


    {{-- Tabs --}}
    @include('dashboard.partials.organization-tabs')


    {{-- Filters --}}
    <section class="designation-card">

        <div class="designation-card-header px-5 py-5 sm:px-6">

            <div class="mb-4">

                <h2 class="text-lg font-extrabold text-slate-900">
                    Designation List
                </h2>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Search और filter करके designation manage करें।
                </p>

            </div>


            <form method="GET"
                  action="{{ route('designations.index') }}"
                  class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">


                {{-- Search --}}
                <div class="lg:col-span-2">

                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="filter-input"
                           placeholder="Search name, code...">

                </div>


                {{-- Office --}}
                <div>

                    <select name="office_id"
                            class="filter-input">

                        <option value="">
                            All Offices
                        </option>

                        @foreach($offices as $office)

                            <option value="{{ $office->id }}"
                                @selected(
                                    (string) request('office_id')
                                    === (string) $office->id
                                )>

                                {{ $office->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Department --}}
                <div>

                    <select name="department_id"
                            class="filter-input">

                        <option value="">
                            All Departments
                        </option>

                        @foreach($departments as $department)

                            <option value="{{ $department->id }}"
                                @selected(
                                    (string) request('department_id')
                                    === (string) $department->id
                                )>

                                {{ $department->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Status --}}
                <div>

                    <select name="is_active"
                            class="filter-input">

                        <option value="">
                            All Status
                        </option>

                        <option value="1"
                            @selected(request('is_active') === '1')>

                            Active

                        </option>

                        <option value="0"
                            @selected(request('is_active') === '0')>

                            Inactive

                        </option>

                    </select>

                </div>


                {{-- Button --}}
                <div class="flex gap-2">

                    <button type="submit"
                            class="flex min-h-[44px] flex-1 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-extrabold text-white">

                        <i class="fas fa-filter"></i>

                        Filter

                    </button>


                    <a href="{{ route('designations.index') }}"
                       title="Reset"
                       class="flex min-h-[44px] w-11 items-center justify-center rounded-xl bg-slate-200 text-slate-700">

                        <i class="fas fa-redo"></i>

                    </a>

                </div>

            </form>

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="designation-table">

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Designation
                        </th>

                        <th>
                            Office
                        </th>

                        <th>
                            Department
                        </th>

                        <th>
                            Level
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created By
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($designations as $designation)

                    <tr>

                        <td>

                            <span class="serial-box">

                                {{
                                    $designations->firstItem()
                                    + $loop->index
                                }}

                            </span>

                        </td>


                        <td>

                            <div class="flex items-center gap-3">

                                <div class="designation-icon">

                                    <i class="fas fa-id-badge"></i>

                                </div>


                                <div>

                                    <div class="font-extrabold text-slate-900">

                                        {{ $designation->name }}

                                    </div>

                                    <div class="mt-1 text-xs font-semibold text-slate-500">

                                        @if($designation->code)

                                            Code:
                                            {{ $designation->code }}

                                        @else

                                            No Code

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </td>


                        <td>

                            <span class="font-bold text-slate-700">

                                {{ $designation->office?->name ?? '—' }}

                            </span>

                        </td>


                        <td>

                            {{ $designation->department?->name ?? '—' }}

                        </td>


                        <td>

                            @if($designation->level)

                                <span class="rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-extrabold text-indigo-700">

                                    Level {{ $designation->level }}

                                </span>

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            @if($designation->is_active)

                                <span class="status-active">

                                    <i class="fas fa-circle text-[7px]"></i>

                                    Active

                                </span>

                            @else

                                <span class="status-inactive">

                                    <i class="fas fa-circle text-[7px]"></i>

                                    Inactive

                                </span>

                            @endif

                        </td>


                        <td>

                            {{ $designation->creator?->name ?? '—' }}

                        </td>


                        <td>

                            <div class="flex items-center gap-2">


                                {{-- View --}}
                                <a href="{{ route(
                                    'designations.show',
                                    $designation
                                ) }}"
                                   title="View"
                                   class="action-button action-view">

                                    <i class="fas fa-eye"></i>

                                </a>


                                {{-- Edit --}}
                                <a href="{{ route(
                                    'designations.edit',
                                    $designation
                                ) }}"
                                   title="Edit"
                                   class="action-button action-edit">

                                    <i class="fas fa-pen"></i>

                                </a>


                                {{-- Status --}}
                                <form action="{{ route(
                                        'designations.toggle-status',
                                        $designation
                                    ) }}"
                                      method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                            title="Change Status"
                                            class="action-button action-status">

                                        @if($designation->is_active)

                                            <i class="fas fa-toggle-on"></i>

                                        @else

                                            <i class="fas fa-toggle-off"></i>

                                        @endif

                                    </button>

                                </form>


                                {{-- Delete --}}
                                <form action="{{ route(
                                        'designations.destroy',
                                        $designation
                                    ) }}"
                                      method="POST"
                                      onsubmit="return confirm(
                                          'Are you sure you want to delete this designation?'
                                      )">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Delete"
                                            class="action-button action-delete">

                                        <i class="fas fa-trash-alt"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8"
                            class="px-6 py-16 text-center">

                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-600">

                                <i class="fas fa-id-badge"></i>

                            </div>

                            <h3 class="mt-4 font-extrabold text-slate-800">

                                No Designation Found

                            </h3>

                            <p class="mt-2 text-sm font-medium text-slate-500">

                                अभी तक कोई designation उपलब्ध नहीं है।

                            </p>


                            <a href="{{ route('designations.create') }}"
                               class="primary-button mt-5">

                                <i class="fas fa-plus-circle"></i>

                                Create Designation

                            </a>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if($designations->hasPages())

            <div class="border-t border-slate-200 px-5 py-4">

                {{ $designations->links() }}

            </div>

        @endif

    </section>

</div>

@endsection