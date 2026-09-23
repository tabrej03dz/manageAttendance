@extends('dashboard.layout.root')

@section('title', 'Departments')

@push('styles')
<style>
    .department-page {
        --primary: #4f46e5;
        --purple: #7c3aed;
        --cyan: #0891b2;
        --red: #e11d48;
        --slate: #0f172a;

        color: #0f172a;
        font-family: 'Inter', sans-serif;
    }

    .department-hero {
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

    .department-hero::before {
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

    .department-hero::after {
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

    .department-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.24);
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.55);
        color: #fff;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
    }

    .department-card {
        overflow: hidden;
        border: 1px solid #d8e0ea;
        border-radius: 22px;
        background: #fff;
        box-shadow:
            0 10px 30px rgba(15, 23, 42, 0.10);
    }

    .department-card-header {
        border-bottom: 1px solid #e2e8f0;
        background:
            linear-gradient(
                90deg,
                rgba(238, 242, 255, .88),
                rgba(236, 254, 255, .65)
            );
    }

    .department-create-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
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
        text-decoration: none !important;
        box-shadow:
            0 12px 25px rgba(79, 70, 229, .25);
        transition: .2s ease;
    }

    .department-create-button:hover {
        transform: translateY(-2px);
        filter: brightness(1.06);
    }

    .department-search-wrapper {
        position: relative;
    }

    .department-search-icon {
        position: absolute;
        top: 50%;
        left: 16px;
        color: #94a3b8;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .department-search-input {
        width: 100%;
        min-height: 46px;
        border: 1px solid #dbe3ee !important;
        border-radius: 14px !important;
        background: #fff !important;
        color: #0f172a !important;
        padding: 11px 16px 11px 45px !important;
        font-size: 13px;
        outline: none;
    }

    .department-search-input:focus {
        border-color: #818cf8 !important;
        box-shadow:
            0 0 0 4px rgba(99, 102, 241, .12) !important;
    }

    .department-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: 1px solid #c7d2fe;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 800;
    }

    .department-table {
        width: 100%;
        min-width: 720px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .department-table thead {
        background: #f8fafc;
    }

    .department-table th {
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .department-table td {
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .department-table tbody tr:hover {
        background: #f8fafc;
    }

    .department-serial {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 1px solid #dbeafe;
        border-radius: 11px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
    }

    .department-name-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        border: 1px solid #c7d2fe;
        border-radius: 14px;
        background:
            linear-gradient(
                135deg,
                #eef2ff,
                #e0e7ff
            );
        color: #4f46e5;
    }

    .department-action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 12px;
        color: #fff !important;
        cursor: pointer;
        text-decoration: none !important;
        transition: .2s ease;
    }

    .department-action-button:hover {
        transform: translateY(-2px);
        filter: brightness(1.06);
    }

    .department-edit-button {
        background:
            linear-gradient(
                135deg,
                #2563eb,
                #4f46e5
            );
    }

    .department-delete-button {
        background:
            linear-gradient(
                135deg,
                #e11d48,
                #f43f5e
            );
    }

    .department-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 68px;
        height: 68px;
        margin: 0 auto;
        border: 1px solid #dbeafe;
        border-radius: 20px;
        background:
            linear-gradient(
                135deg,
                #eff6ff,
                #eef2ff
            );
        color: #6366f1;
        font-size: 24px;
    }

    @media (max-width: 767px) {
        .department-hero {
            border-radius: 20px;
        }

        .department-card {
            border-radius: 18px;
        }

        .department-create-button {
            width: 100%;
        }
    }
</style>
@endpush


@section('content')

<div class="department-page space-y-6 pb-10">

    {{-- Header --}}
    <section class="department-hero p-6 sm:p-8">

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <span class="department-hero-badge">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-cyan-400"></span>
                    Organization Management
                </span>

                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    Departments
                </h1>

                <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">
                    Company departments को create, update और manage करें।
                </p>

                <div class="mt-5 flex flex-wrap items-center gap-4 text-sm font-semibold text-slate-200">

                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-layer-group text-indigo-300"></i>

                        {{ $departments->count() }}

                        {{ \Illuminate\Support\Str::plural(
                            'Department',
                            $departments->count()
                        ) }}
                    </span>

                    <span class="inline-flex items-center gap-2">
                        <i class="far fa-calendar-alt text-cyan-300"></i>

                        {{ now()->format('d F Y') }}
                    </span>

                </div>

            </div>


            <div class="w-full lg:w-auto">

                <a href="{{ route('departments.create') }}"
                   class="department-create-button">

                    <i class="fas fa-plus-circle"></i>

                    Create Department

                </a>

            </div>

        </div>

    </section>


    {{-- Department / Designation Tabs --}}
    @include('dashboard.partials.organization-tabs')


    {{-- Main Card --}}
    <section class="department-card">

        <div class="department-card-header flex flex-col gap-4 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <h2 class="text-lg font-extrabold text-slate-900">
                    Department List
                </h2>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    सभी available departments की जानकारी।
                </p>

            </div>


            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">

                <span class="department-count-badge">

                    <i class="fas fa-building"></i>

                    <span id="visible-department-count">
                        {{ $departments->count() }}
                    </span>

                    Total

                </span>


                <div class="department-search-wrapper w-full sm:w-80">

                    <i class="department-search-icon fas fa-search"></i>

                    <input type="text"
                           id="department-search"
                           class="department-search-input"
                           placeholder="Search department...">

                </div>

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="department-table">

                <thead>

                <tr>

                    <th class="w-24">
                        #
                    </th>

                    <th>
                        Department Name
                    </th>

                    <th class="w-40">
                        Actions
                    </th>

                </tr>

                </thead>


                <tbody id="department-table-body">

                @forelse($departments as $department)

                    <tr class="department-row"
                        data-search="{{ strtolower($department->name) }}">

                        <td>

                            <span class="department-serial">
                                {{ $loop->iteration }}
                            </span>

                        </td>


                        <td>

                            <div class="flex items-center gap-3">

                                <div class="department-name-icon">

                                    <i class="fas fa-sitemap"></i>

                                </div>


                                <div class="min-w-0">

                                    <p class="truncate text-sm font-extrabold text-slate-900">
                                        {{ $department->name }}
                                    </p>

                                    <p class="mt-1 text-xs font-medium text-slate-500">

                                        Department ID:

                                        {{ $department->id }}

                                    </p>

                                </div>

                            </div>

                        </td>


                        <td>

                            <div class="flex items-center gap-2">

                                <a href="{{ route(
                                    'departments.edit',
                                    $department->id
                                ) }}"
                                   title="Edit Department"
                                   class="department-action-button department-edit-button">

                                    <i class="fas fa-pen text-sm"></i>

                                </a>


                                <form action="{{ route(
                                        'departments.destroy',
                                        $department->id
                                    ) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirmDepartmentDelete(
                                          '{{ addslashes($department->name) }}'
                                      )">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Delete Department"
                                            class="department-action-button department-delete-button">

                                        <i class="fas fa-trash-alt text-sm"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3"
                            class="px-5 py-16 text-center">

                            <div class="department-empty-icon">
                                <i class="fas fa-sitemap"></i>
                            </div>

                            <h3 class="mt-4 text-base font-extrabold text-slate-800">
                                No Department Found
                            </h3>

                            <p class="mt-2 text-sm font-medium text-slate-500">
                                अभी तक कोई department create नहीं किया गया है।
                            </p>

                            <a href="{{ route('departments.create') }}"
                               class="department-create-button mt-5">

                                <i class="fas fa-plus-circle"></i>

                                Create First Department

                            </a>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        <div id="department-no-result"
             class="hidden border-t border-slate-200 px-5 py-16 text-center">

            <div class="department-empty-icon">
                <i class="fas fa-search"></i>
            </div>

            <h3 class="mt-4 text-base font-extrabold text-slate-800">
                No Matching Department
            </h3>

            <p class="mt-2 text-sm font-medium text-slate-500">
                किसी दूसरे department name से search करें।
            </p>

        </div>

    </section>

</div>

@endsection


@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const searchInput =
            document.getElementById('department-search');

        const departmentRows =
            document.querySelectorAll('.department-row');

        const noResultBox =
            document.getElementById('department-no-result');

        const visibleCount =
            document.getElementById(
                'visible-department-count'
            );

        if (!searchInput) {
            return;
        }

        searchInput.addEventListener(
            'input',
            function () {

                const keyword =
                    this.value
                        .toLowerCase()
                        .trim();

                let totalVisibleRows = 0;

                departmentRows.forEach(
                    function (row) {

                        const searchableText =
                            row.dataset.search || '';

                        const isVisible =
                            searchableText.includes(
                                keyword
                            );

                        row.classList.toggle(
                            'hidden',
                            !isVisible
                        );

                        if (isVisible) {
                            totalVisibleRows++;
                        }

                    }
                );

                if (visibleCount) {
                    visibleCount.textContent =
                        totalVisibleRows;
                }

                if (noResultBox) {

                    noResultBox.classList.toggle(
                        'hidden',
                        totalVisibleRows > 0
                            || keyword === ''
                    );

                }

            }
        );

    });


    function confirmDepartmentDelete(
        departmentName
    ) {

        return confirm(
            'Are you sure you want to delete "' +
            departmentName +
            '" department?'
        );

    }

</script>

@endpush