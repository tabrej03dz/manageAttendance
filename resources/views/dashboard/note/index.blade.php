
@extends('dashboard.layout.root')

@section('content')

<style>
    /* ===============================
       NOTES INDEX PAGE
    =============================== */

    .notes-page {
        width: 100%;
        max-width: 100%;
        padding: 20px 8px 35px;
    }

    .notes-page * {
        box-sizing: border-box;
    }

    .notes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 22px;
    }

    .notes-page-title {
        font-size: 24px;
        font-weight: 700;
        color: #273142;
        margin: 0 0 5px;
    }

    .notes-subtitle {
        font-size: 13px;
        color: #8290a4;
        margin: 0;
    }

    .notes-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 19px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 7px;
        border: 1px solid transparent;
        text-decoration: none !important;
        cursor: pointer;
        transition: .2s;
    }

    .notes-btn-primary {
        background: #3168d7;
        color: #fff !important;
    }

    .notes-btn-primary:hover {
        background: #2455b8;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(49,104,215,.2);
    }

    /* Summary Cards */

    .notes-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .notes-stat-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #fff;
        border: 1px solid #e6eaf0;
        border-radius: 11px;
        padding: 20px;
        box-shadow: 0 3px 15px rgba(30,45,65,.04);
    }

    .notes-stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #8895a7;
        margin-bottom: 8px;
    }

    .notes-stat-value {
        font-size: 27px;
        font-weight: 700;
        color: #263449;
        line-height: 1.2;
    }

    .notes-stat-icon {
        width: 49px;
        height: 49px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 11px;
        font-size: 20px;
        flex-shrink: 0;
    }

    .notes-icon-blue {
        background: #edf3ff;
        color: #3168d7;
    }

    .notes-icon-green {
        background: #eaf9f0;
        color: #22a66c;
    }

    .notes-icon-red {
        background: #fff0f0;
        color: #e75b62;
    }

    /* Main Card */

    .notes-card {
        width: 100%;
        background: #fff;
        border: 1px solid #e6eaf0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(30,45,65,.05);
    }

    .notes-card-header {
        padding: 22px 24px;
        border-bottom: 1px solid #edf0f4;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
    }

    .notes-card-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .notes-heading-icon {
        width: 43px;
        height: 43px;
        background: #eef3ff;
        color: #3168d7;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 18px;
    }

    .notes-card-heading h5 {
        font-size: 17px;
        color: #263449;
        font-weight: 700;
        margin: 0;
    }

    .notes-card-heading p {
        font-size: 12px;
        color: #8c97a8;
        margin: 4px 0 0;
    }

    /* Filters */

    .notes-toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .notes-search-wrap {
        position: relative;
    }

    .notes-search-wrap i {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        font-size: 13px;
        color: #98a2b3;
    }

    .notes-search {
        width: 260px;
        height: 41px;
        border: 1px solid #dfe5ed;
        border-radius: 7px;
        padding: 9px 12px 9px 37px;
        background: #fff;
        color: #344054;
        font-size: 13px;
        outline: none;
    }

    .notes-search:focus,
    .notes-filter:focus {
        border-color: #3168d7;
        box-shadow: 0 0 0 3px rgba(49,104,215,.1);
        outline: none;
    }

    .notes-filter {
        width: 150px;
        height: 41px;
        border: 1px solid #dfe5ed;
        border-radius: 7px;
        padding: 8px 12px;
        color: #344054;
        background: #fff;
        font-size: 13px;
        cursor: pointer;
    }

    /* Table */

    .notes-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .notes-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        font-size: 13px;
    }

    .notes-table thead {
        background: #f8fafc;
    }

    .notes-table thead th {
        padding: 16px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #69788c;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 1px solid #edf0f4;
        white-space: nowrap;
    }

    .notes-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f2f5;
        color: #526075;
        vertical-align: middle;
    }

    .notes-table tbody tr:hover {
        background: #f9fbff;
    }

    .notes-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .notes-row-number {
        color: #9aa5b5;
        font-weight: 600;
    }

    .notes-title-cell {
        font-weight: 600;
        color: #273142;
        max-width: 260px;
        overflow-wrap: anywhere;
    }

    .notes-description {
        max-width: 430px;
        color: #778397;
        line-height: 1.6;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    /* Status */

    .notes-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none !important;
        white-space: nowrap;
        transition: .2s;
    }

    .notes-status-active {
        background: #e9f9f0;
        color: #179c61 !important;
    }

    .notes-status-active:hover {
        background: #d4f2e1;
    }

    .notes-status-inactive {
        background: #fff0f0;
        color: #df575d !important;
    }

    .notes-status-inactive:hover {
        background: #ffe2e2;
    }

    .notes-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    /* Actions */

    .notes-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notes-action-btn {
        width: 35px;
        height: 35px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        text-decoration: none !important;
        font-size: 13px;
        cursor: pointer;
        transition: .2s;
    }

    .notes-action-delete {
        background: #fff0f0;
        color: #e05259 !important;
    }

    .notes-action-delete:hover {
        background: #e05259;
        color: #fff !important;
    }

    /* Footer */

    .notes-card-footer {
        border-top: 1px solid #edf0f4;
        background: #fcfdff;
        padding: 17px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .notes-results-info {
        color: #8995a5;
        font-size: 12px;
    }

    .notes-empty {
        padding: 55px 20px !important;
        text-align: center;
    }

    .notes-empty-icon {
        width: 65px;
        height: 65px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f4fa;
        color: #a3afbf;
        border-radius: 50%;
        font-size: 25px;
        margin: 0 auto 15px;
    }

    .notes-empty h5 {
        font-size: 16px;
        color: #334155;
        font-weight: 700;
    }

    .notes-empty p {
        color: #98a2b3;
        font-size: 13px;
        margin-bottom: 0;
    }

    /* Responsive */

    @media (max-width: 900px) {
        .notes-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .notes-card-header {
            align-items: flex-start;
        }
    }

    @media (max-width: 650px) {
        .notes-page {
            padding: 15px 0 25px;
        }

        .notes-page-title {
            font-size: 21px;
        }

        .notes-stats {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .notes-stat-card {
            padding: 16px;
        }

        .notes-card-header {
            padding: 18px;
        }

        .notes-toolbar {
            width: 100%;
        }

        .notes-search-wrap {
            flex: 1;
            min-width: 180px;
        }

        .notes-search {
            width: 100%;
        }

        .notes-filter {
            flex: 1;
        }

        .notes-table thead th,
        .notes-table tbody td {
            padding: 13px 14px;
        }

        .notes-card-footer {
            padding: 15px;
        }
    }
</style>

{{-- Existing layout already contains content-wrapper --}}
<section class="content">
    <div class="container-fluid">
        <div class="notes-page">

            @php
                $notesCollection = collect(
                    method_exists($notes, 'getCollection')
                        ? $notes->getCollection()
                        : $notes
                );

                $totalNotes = $notesCollection->count();

                $activeNotes = $notesCollection->filter(
                    fn($note) => (string) $note->status === '1'
                )->count();

                $inactiveNotes = $totalNotes - $activeNotes;

                $firstNumber = method_exists($notes, 'firstItem')
                    ? ($notes->firstItem() ?? 0)
                    : 1;
            @endphp

            {{-- PAGE HEADER --}}
            <div class="notes-header">
                <div>
                    <h2 class="notes-page-title">
                        Notes Management
                    </h2>

                    <p class="notes-subtitle">
                        Manage and organize notes shared with your office team.
                    </p>
                </div>

                <a href="{{ route('note.create') }}"
                   class="notes-btn notes-btn-primary">
                    <i class="fas fa-plus"></i>
                    Create New Note
                </a>
            </div>

            {{-- FLASH MESSAGES --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-1"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- SUMMARY CARDS --}}
            <div class="notes-stats">

                {{-- Total Notes --}}
                <div class="notes-stat-card">
                    <div>
                        <div class="notes-stat-label">
                            Total Notes
                        </div>

                        <div class="notes-stat-value" id="statTotal">
                            {{ $totalNotes }}
                        </div>
                    </div>

                    <div class="notes-stat-icon notes-icon-blue">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                </div>

                {{-- Active Notes --}}
                <div class="notes-stat-card">
                    <div>
                        <div class="notes-stat-label">
                            Active Notes
                        </div>

                        <div class="notes-stat-value" id="statActive">
                            {{ $activeNotes }}
                        </div>
                    </div>

                    <div class="notes-stat-icon notes-icon-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                {{-- Inactive Notes --}}
                <div class="notes-stat-card">
                    <div>
                        <div class="notes-stat-label">
                            Inactive Notes
                        </div>

                        <div class="notes-stat-value" id="statInactive">
                            {{ $inactiveNotes }}
                        </div>
                    </div>

                    <div class="notes-stat-icon notes-icon-red">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>

            </div>

            {{-- MAIN CARD --}}
            <div class="notes-card">

                {{-- CARD HEADER --}}
                <div class="notes-card-header">

                    <div class="notes-card-heading">
                        <div class="notes-heading-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>

                        <div>
                            <h5>All Notes</h5>
                            <p>View, filter and manage your notes</p>
                        </div>
                    </div>

                    {{-- SEARCH AND FILTER --}}
                    <div class="notes-toolbar">

                        <div class="notes-search-wrap">
                            <i class="fas fa-search"></i>

                            <input
                                type="text"
                                id="notesSearch"
                                class="notes-search"
                                placeholder="Search notes..."
                                autocomplete="off"
                                aria-label="Search notes"
                            >
                        </div>

                        <select id="notesStatusFilter"
                                class="notes-filter"
                                aria-label="Filter notes by status">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                    </div>

                </div>

                {{-- NOTES TABLE --}}
                <div class="notes-table-wrapper">

                    <table class="notes-table" id="notesTable">

                        <thead>
                            <tr>
                                <th style="width:65px;">#</th>
                                <th>Note Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th style="width:100px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="notesTableBody">

                            @forelse($notes as $note)

                                <tr
                                    class="note-data-row"
                                    data-status="{{ (string) $note->status === '1' ? 'active' : 'inactive' }}"
                                >

                                    {{-- Serial Number --}}
                                    <td>
                                        <span class="notes-row-number">
                                            {{ $firstNumber + $loop->index }}
                                        </span>
                                    </td>

                                    {{-- Title --}}
                                    <td>
                                        <div class="notes-title-cell">
                                            {{ $note->title }}
                                        </div>
                                    </td>

                                    {{-- Description --}}
                                    <td>
                                        <div class="notes-description"
                                             title="{{ $note->description }}">
                                            {{ \Illuminate\Support\Str::limit($note->description ?: 'No description', 110) }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td>

                                        <a
                                            href="{{ route('note.status', ['note' => $note->id]) }}"
                                            class="notes-status {{ (string) $note->status === '1' ? 'notes-status-active' : 'notes-status-inactive' }}"
                                            title="Click to change status"
                                            onclick="return confirm('Are you sure you want to change this note status?')"
                                        >
                                            <span class="notes-status-dot"></span>

                                            {{ (string) $note->status === '1' ? 'Active' : 'Inactive' }}
                                        </a>

                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="notes-actions">

                                            @can('delete office')
                                                <a
                                                    href="{{ route('note.delete', ['note' => $note->id]) }}"
                                                    class="notes-action-btn notes-action-delete"
                                                    title="Delete Note"
                                                    onclick="return confirm('Are you sure you want to delete this note? This action cannot be undone.')"
                                                >
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            @endcan

                                        </div>
                                    </td>

                                </tr>

                            @empty

                                <tr id="notesInitialEmpty">
                                    <td colspan="5" class="notes-empty">

                                        <div class="notes-empty-icon">
                                            <i class="fas fa-sticky-note"></i>
                                        </div>

                                        <h5>No Notes Found</h5>

                                        <p>
                                            You haven't created any notes yet.
                                        </p>

                                    </td>
                                </tr>

                            @endforelse

                            {{-- DYNAMIC FILTER EMPTY --}}
                            <tr id="notesFilterEmpty" style="display:none;">
                                <td colspan="5" class="notes-empty">

                                    <div class="notes-empty-icon">
                                        <i class="fas fa-search"></i>
                                    </div>

                                    <h5>No Matching Notes</h5>

                                    <p>
                                        Try another search keyword or status.
                                    </p>

                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                {{-- FOOTER --}}
                <div class="notes-card-footer">

                    <div class="notes-results-info" id="notesResultsInfo">
                        Showing {{ $totalNotes }} notes on this page
                    </div>

                    {{-- Pagination (if paginator provided) --}}
                    @if(method_exists($notes, 'links'))
                        <div>
                            {{ $notes->links() }}
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('notesSearch');
    const statusFilter = document.getElementById('notesStatusFilter');
    const tableBody = document.getElementById('notesTableBody');

    const rows = Array.from(
        tableBody.querySelectorAll('.note-data-row')
    );

    const noResults = document.getElementById('notesFilterEmpty');
    const resultsInfo = document.getElementById('notesResultsInfo');

    function filterNotes() {

        const search = searchInput.value.trim().toLowerCase();
        const status = statusFilter.value;

        let visibleCount = 0;

        rows.forEach(function (row) {

            const text = row.innerText.toLowerCase();
            const rowStatus = row.getAttribute('data-status');

            const matchesSearch = text.includes(search);
            const matchesStatus = status === 'all'
                || rowStatus === status;

            const show = matchesSearch && matchesStatus;

            row.style.display = show ? '' : 'none';

            if (show) {
                visibleCount++;
            }
        });

        // Show "no matching notes" only when data exists
        noResults.style.display =
            rows.length > 0 && visibleCount === 0
                ? ''
                : 'none';

        resultsInfo.textContent =
            'Showing ' + visibleCount +
            ' of ' + rows.length +
            ' notes on this page';
    }

    searchInput.addEventListener('input', filterNotes);
    statusFilter.addEventListener('change', filterNotes);

});
</script>

@endsection
