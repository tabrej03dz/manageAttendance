```blade
@extends('dashboard.layout.root')

@section('title', 'Owners')

@push('styles')
    <style>
        .owners-page {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        .owners-header {
            position: relative;
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 22px;
            padding: 26px 28px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(99, 102, 241, 0.14),
                    transparent 35%
                ),
                #ffffff;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.09);
        }

        .owners-header::after {
            position: absolute;
            top: -65px;
            right: -45px;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.07);
            content: '';
        }

        .page-title-icon {
            display: flex;
            width: 56px;
            height: 56px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 17px;
            background: linear-gradient(135deg, #4338ca, #6366f1);
            color: #ffffff;
            font-size: 22px;
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.22);
        }

        .create-owner-button {
            position: relative;
            z-index: 2;
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 0;
            border-radius: 14px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #4338ca, #6366f1);
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none !important;
            box-shadow: 0 11px 24px rgba(79, 70, 229, 0.24);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .create-owner-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 15px 28px rgba(79, 70, 229, 0.30);
        }

        .owners-table-card {
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.09);
        }

        .table-card-header {
            padding: 21px 24px;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
        }

        .owners-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 999px;
            padding: 7px 12px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 800;
        }

        .owners-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .owners-table {
            width: 100%;
            min-width: 960px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .owners-table thead th {
            border: 0;
            border-bottom: 1px solid #dbe3ee;
            padding: 15px 18px;
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.07em;
            text-align: left;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .owners-table tbody td {
            border: 0;
            border-bottom: 1px solid #edf2f7;
            padding: 16px 18px;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
            vertical-align: middle;
        }

        .owners-table tbody tr {
            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .owners-table tbody tr:hover {
            background: #f8fafc;
        }

        .owners-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .serial-number {
            display: inline-flex;
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 900;
        }

        .owner-profile {
            display: flex;
            min-width: 180px;
            align-items: center;
            gap: 11px;
        }

        .owner-avatar {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            border: 3px solid #ffffff;
            border-radius: 14px;
            background: #e2e8f0;
            object-fit: cover;
            box-shadow: 0 0 0 1px #dbe3ee;
        }

        .owner-name {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 900;
        }

        .owner-email {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            word-break: break-all;
        }

        .owner-email:hover {
            color: #4f46e5;
        }

        .owner-phone {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .owner-phone:hover {
            color: #4f46e5;
        }

        .status-badge {
            display: inline-flex;
            min-width: 82px;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 900;
            text-decoration: none !important;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .status-badge:hover {
            transform: translateY(-1px);
        }

        .status-active {
            border: 1px solid #a7f3d0;
            background: #ecfdf5;
            color: #047857 !important;
        }

        .status-inactive {
            border: 1px solid #fecaca;
            background: #fff1f2;
            color: #be123c !important;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: currentColor;
        }

        .owner-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        .owner-action-button {
            display: inline-flex;
            width: 39px;
            height: 39px;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 11px;
            font-size: 14px;
            text-decoration: none !important;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.2s ease;
        }

        .owner-action-button:hover {
            transform: translateY(-1px);
        }

        .action-edit {
            border-color: #bfdbfe;
            background: #eff6ff;
            color: #2563eb !important;
        }

        .action-edit:hover {
            background: #dbeafe;
            box-shadow: 0 7px 16px rgba(37, 99, 235, 0.15);
        }

        .action-delete {
            border-color: #fecaca;
            background: #fff1f2;
            color: #e11d48 !important;
        }

        .action-delete:hover {
            background: #ffe4e6;
            box-shadow: 0 7px 16px rgba(225, 29, 72, 0.15);
        }

        .action-plan {
            border-color: #a7f3d0;
            background: #ecfdf5;
            color: #059669 !important;
        }

        .action-plan:hover {
            background: #d1fae5;
            box-shadow: 0 7px 16px rgba(5, 150, 105, 0.15);
        }

        .delete-owner-form {
            display: inline-flex;
            margin: 0;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state-icon {
            display: flex;
            width: 72px;
            height: 72px;
            margin: 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 22px;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 28px;
        }

        .owners-pagination {
            padding: 18px 24px;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
        }

        @media (max-width: 767px) {
            .owners-header {
                padding: 21px 18px;
                border-radius: 18px;
            }

            .owners-header-content {
                flex-direction: column;
                align-items: stretch !important;
            }

            .owners-heading-wrapper {
                align-items: flex-start !important;
            }

            .create-owner-button {
                width: 100%;
            }

            .owners-table-card {
                border-radius: 18px;
            }

            .table-card-header {
                padding: 18px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="owners-page pb-10">

        {{-- Page Header --}}
        <section class="owners-header mb-5">
            <div
                class="owners-header-content position-relative z-1 d-flex align-items-center justify-content-between gap-4"
            >
                <div class="owners-heading-wrapper d-flex align-items-center gap-3">
                    <div class="page-title-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-uppercase fw-bold text-indigo-600"
                            style="font-size: 11px; letter-spacing: 0.12em; color: #4f46e5;"
                        >
                            User Management
                        </p>

                        <h1 class="mb-1 fw-bold text-dark">
                            Owners
                        </h1>

                        <p class="mb-0 text-secondary fw-semibold">
                            Manage owner accounts, plans and account status.
                        </p>
                    </div>
                </div>

                @can('create owners')
                    <a
                        href="{{ route('owner.create') }}"
                        class="create-owner-button"
                    >
                        <i class="fas fa-user-plus"></i>
                        Create Owner
                    </a>
                @endcan
            </div>
        </section>

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

        {{-- Error Message --}}
        @if (session('error'))
            <div
                class="mb-5 rounded-4 border border-danger-subtle bg-danger-subtle p-4 text-danger-emphasis"
            >
                <div class="d-flex align-items-center gap-2 fw-bold">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Owners Table --}}
        <section class="owners-table-card">
            <div
                class="table-card-header d-flex align-items-center justify-content-between gap-3"
            >
                <div>
                    <h2 class="mb-1 fs-5 fw-bold text-dark">
                        Owner Accounts
                    </h2>

                    <p class="mb-0 small fw-semibold text-secondary">
                        View and manage all registered owners.
                    </p>
                </div>

                <span class="owners-count-badge">
                    <i class="fas fa-users"></i>

                    {{ method_exists($owners, 'total') ? $owners->total() : $owners->count() }}
                    Owners
                </span>
            </div>

            @if ($owners->count() > 0)
                <div class="owners-table-wrapper">
                    <table class="owners-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Owner</th>
                                <th>Email Address</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($owners as $owner)
                                <tr>
                                    {{-- Serial Number --}}
                                    <td>
                                        <span class="serial-number">
                                            @if (method_exists($owners, 'firstItem'))
                                                {{ $owners->firstItem() + $loop->index }}
                                            @else
                                                {{ $loop->iteration }}
                                            @endif
                                        </span>
                                    </td>

                                    {{-- Owner Profile --}}
                                    <td>
                                        <div class="owner-profile">
                                            <img
                                                src="{{ $owner->photo
                                                    ? asset('storage/' . $owner->photo)
                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($owner->name) . '&background=EEF2FF&color=4338CA&bold=true&size=128' }}"
                                                alt="{{ $owner->name }}"
                                                class="owner-avatar"
                                                loading="lazy"
                                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($owner->name) }}&background=EEF2FF&color=4338CA&bold=true&size=128';"
                                            >

                                            <div>
                                                <p class="owner-name">
                                                    {{ $owner->name }}
                                                </p>

                                                <p class="mb-0 mt-1 text-secondary small fw-semibold">
                                                    Owner Account
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Email --}}
                                    <td>
                                        @if ($owner->email)
                                            <a
                                                href="mailto:{{ $owner->email }}"
                                                class="owner-email"
                                            >
                                                <i class="fas fa-envelope text-indigo-500"></i>
                                                {{ $owner->email }}
                                            </a>
                                        @else
                                            <span class="text-secondary">
                                                Not available
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Phone --}}
                                    <td>
                                        @if ($owner->phone)
                                            <a
                                                href="tel:{{ $owner->phone }}"
                                                class="owner-phone"
                                            >
                                                <i class="fas fa-phone text-indigo-500"></i>
                                                {{ $owner->phone }}
                                            </a>
                                        @else
                                            <span class="text-secondary">
                                                Not available
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <a
                                            href="{{ route('owner.status', ['owner' => $owner->id]) }}"
                                            class="status-badge {{ $owner->status == '1'
                                                ? 'status-active'
                                                : 'status-inactive' }}"
                                            title="Click to change status"
                                        >
                                            <span class="status-dot"></span>

                                            {{ $owner->status == '1'
                                                ? 'Active'
                                                : 'Inactive' }}
                                        </a>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        @role('super_admin|admin')
                                            <div class="owner-actions">
                                                <a
                                                    href="{{ route('owner.edit', ['owner' => $owner->id]) }}"
                                                    class="owner-action-button action-edit"
                                                    title="Edit Owner"
                                                >
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                                <a
                                                    href="{{ route('owner.plan', ['owner' => $owner->id]) }}"
                                                    class="owner-action-button action-plan"
                                                    title="Owner Plans"
                                                >
                                                    <i class="fas fa-layer-group"></i>
                                                </a>

                                                <form
                                                    action="{{ route('owner.delete', ['owner' => $owner->id]) }}"
                                                    method="POST"
                                                    class="delete-owner-form"
                                                    onsubmit="return confirmOwnerDelete(event, '{{ addslashes($owner->name) }}');"
                                                >
                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="owner-action-button action-delete"
                                                        title="Delete Owner"
                                                    >
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endrole
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if (method_exists($owners, 'links'))
                    <div class="owners-pagination">
                        {{ $owners->links() }}
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>

                    <h3 class="mt-4 mb-2 fs-5 fw-bold text-dark">
                        No owners found
                    </h3>

                    <p class="mb-4 text-secondary fw-semibold">
                        There are currently no owner accounts available.
                    </p>

                    @can('create owners')
                        <a
                            href="{{ route('owner.create') }}"
                            class="create-owner-button"
                        >
                            <i class="fas fa-user-plus"></i>
                            Create First Owner
                        </a>
                    @endcan
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmOwnerDelete(event, ownerName) {
            const confirmed = window.confirm(
                'Are you sure you want to delete "' +
                ownerName +
                '"?\n\nThis action cannot be undone.'
            );

            if (!confirmed) {
                event.preventDefault();

                return false;
            }

            const form = event.target;
            const deleteButton = form.querySelector('button[type="submit"]');

            if (deleteButton) {
                deleteButton.disabled = true;

                deleteButton.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i>';
            }

            return true;
        }
    </script>
@endpush
