
@extends('dashboard.layout.root')

@section('content')

<style>
    .note-page {
        padding: 20px 8px 35px;
    }

    .note-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .note-page-title {
        font-size: 24px;
        font-weight: 700;
        color: #273142;
        margin-bottom: 4px;
    }

    .note-page-subtitle {
        font-size: 13px;
        color: #8290a4;
        margin: 0;
    }

    .note-card {
        background: #fff;
        border: 1px solid #e6eaf0;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(30, 45, 65, .05);
        overflow: hidden;
    }

    .note-card-header {
        background: #fff;
        padding: 22px 25px;
        border-bottom: 1px solid #edf0f4;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .note-card-icon {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef3ff;
        color: #3168d7;
        border-radius: 10px;
        font-size: 19px;
    }

    .note-card-header h5 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #263449;
    }

    .note-card-header p {
        margin: 4px 0 0;
        color: #8c97a8;
        font-size: 12px;
    }

    .note-card-body {
        padding: 26px;
    }

    .note-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #334155;
        padding-bottom: 13px;
        margin-bottom: 22px;
        border-bottom: 1px solid #edf0f4;
    }

    .note-section-title i {
        color: #3168d7;
        margin-right: 8px;
    }

    .note-label {
        font-size: 13px;
        font-weight: 600;
        color: #455367;
        margin-bottom: 9px;
        display: block;
    }

    .note-field {
        width: 100%;
        height: 45px;
        border: 1px solid #dfe5ed;
        border-radius: 7px;
        padding: 9px 13px;
        color: #344054;
        background: #fff;
        font-size: 13px;
        transition: .2s;
    }

    .note-field:focus {
        border-color: #3168d7;
        outline: none;
        box-shadow: 0 0 0 3px rgba(49, 104, 215, .10);
    }

    .note-field:disabled {
        background: #f5f7fa;
        cursor: not-allowed;
    }

    textarea.note-field {
        height: auto;
        min-height: 110px;
        resize: vertical;
    }

    select[multiple].note-field {
        height: 175px;
        padding: 8px;
    }

    select[multiple].note-field option {
        padding: 8px;
        border-radius: 4px;
    }

    .note-helper {
        display: block;
        font-size: 11px;
        color: #98a2b3;
        margin-top: 7px;
    }

    .note-counter {
        font-size: 12px;
        font-weight: 600;
        color: #3168d7;
        background: #edf3ff;
        border-radius: 20px;
        padding: 4px 10px;
    }

    .note-action-footer {
        border-top: 1px solid #edf0f4;
        padding: 19px 26px;
        background: #fcfdff;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .note-btn {
        padding: 11px 22px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 7px;
        border: 1px solid transparent;
        transition: .2s;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .note-btn-primary {
        background: #3168d7;
        color: #fff;
    }

    .note-btn-primary:hover {
        background: #2455b8;
        color: #fff;
    }

    .note-btn-primary:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    .note-btn-light {
        background: #fff;
        color: #526075;
        border-color: #dfe5ed;
    }

    .note-btn-light:hover {
        background: #f4f6f9;
        color: #344054;
        text-decoration: none;
    }

    .note-select-all {
        cursor: pointer;
        color: #3168d7;
        font-size: 12px;
        font-weight: 600;
        background: transparent;
        border: 0;
        padding: 0;
    }

    .note-select-all:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .note-required {
        color: #e74c3c;
    }

    .note-alert {
        border-radius: 8px;
        padding: 14px 18px;
        font-size: 13px;
        margin-bottom: 20px;
    }

    .note-loading {
        display: none;
        color: #3168d7;
        font-size: 12px;
        margin-top: 8px;
    }

    .note-error {
        color: #dc3545;
        font-size: 12px;
        margin-top: 6px;
    }

    @media(max-width: 767px) {
        .note-card-body {
            padding: 18px;
        }

        .note-card-header {
            padding: 18px;
        }

        .note-page-title {
            font-size: 21px;
        }

        .note-action-footer {
            padding: 17px;
        }

        .note-action-footer .note-btn {
            flex: 1;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">

            <div class="note-page">

                {{-- Page Header --}}
                <div class="note-header">
                    <div>
                        <h2 class="note-page-title">
                            Create New Note
                        </h2>
                        <p class="note-page-subtitle">
                            Create and share notes with your office team.
                        </p>
                    </div>

                    <a href="{{ route('note.index') }}"
                       class="note-btn note-btn-light">
                        <i class="fas fa-arrow-left"></i>
                        Back to Notes
                    </a>
                </div>

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger note-alert">
                        <strong>
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Please correct the following errors:
                        </strong>

                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="alert alert-success note-alert">
                        <i class="fas fa-check-circle mr-1"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="note-card">

                    {{-- Card Header --}}
                    <div class="note-card-header">
                        <div class="note-card-icon">
                            <i class="fas fa-sticky-note"></i>
                        </div>

                        <div>
                            <h5>Note Information</h5>
                            <p>Fill the details to create a new note</p>
                        </div>
                    </div>

                    <form action="{{ route('note.store') }}"
                          method="POST"
                          id="createNoteForm">

                        @csrf

                        <div class="note-card-body">

                            {{-- Basic Information --}}
                            <div class="note-section-title">
                                <i class="fas fa-edit"></i>
                                Basic Information
                            </div>

                            <div class="row">

                                {{-- Title --}}
                                <div class="col-md-6 mb-4">
                                    <label for="title" class="note-label">
                                        Note Title
                                        <span class="note-required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="title"
                                        id="title"
                                        class="note-field @error('title') is-invalid @enderror"
                                        placeholder="Enter note title"
                                        value="{{ old('title') }}"
                                        maxlength="255"
                                        required
                                    >

                                    @error('title')
                                        <div class="note-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description --}}
                                <div class="col-md-6 mb-4">
                                    <label for="description" class="note-label">
                                        Description
                                    </label>

                                    <textarea
                                        name="description"
                                        id="description"
                                        class="note-field @error('description') is-invalid @enderror"
                                        rows="4"
                                        placeholder="Enter note description"
                                    >{{ old('description') }}</textarea>

                                    @error('description')
                                        <div class="note-error">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Note Visibility --}}
                            <div class="note-section-title mt-3">
                                <i class="fas fa-users"></i>
                                Note Visibility & Access
                            </div>

                            <div class="row">

                                {{-- Office --}}
                                <div class="col-md-6 mb-4">
                                    <label for="office_id" class="note-label">
                                        <i class="fas fa-building text-muted mr-1"></i>
                                        Select Office
                                    </label>

                                    <select
                                        name="office_id"
                                        id="office_id"
                                        class="note-field @error('office_id') is-invalid @enderror"
                                    >
                                        <option value="">
                                            Select office to show note
                                        </option>

                                        @foreach(collect($offices)->filter(fn($office) => $office instanceof \App\Models\Office) as $office)
                                            <option
                                                value="{{ $office->id }}"
                                                {{ (string) old('office_id') === (string) $office->id ? 'selected' : '' }}
                                            >
                                                {{ $office->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <small class="note-helper">
                                        Select the office where this note should appear.
                                    </small>

                                    @error('office_id')
                                        <div class="note-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Role --}}
                                <div class="col-md-6 mb-4">
                                    <label for="role" class="note-label">
                                        <i class="fas fa-user-shield text-muted mr-1"></i>
                                        Select Role
                                    </label>

                                    <select
                                        name="role"
                                        id="role"
                                        class="note-field @error('role') is-invalid @enderror"
                                    >
                                        <option value="">Select role</option>
                                    </select>

                                    <small class="note-helper">
                                        Roles will load after selecting an office.
                                    </small>

                                    @error('role')
                                        <div class="note-error">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Employees --}}
                            <div class="row">
                                <div class="col-md-12 mb-3">

                                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                                        <label for="employee" class="note-label mb-0">
                                            <i class="fas fa-user-friends text-muted mr-1"></i>
                                            Select Employees
                                        </label>

                                        <div class="d-flex align-items-center"
                                             style="gap:15px;">

                                            <span class="note-counter"
                                                  id="employeeCount">
                                                0 Selected
                                            </span>

                                            <button
                                                type="button"
                                                class="note-select-all"
                                                id="selectAllEmployees"
                                                disabled
                                            >
                                                <i class="fas fa-check-double mr-1"></i>
                                                Select All
                                            </button>

                                            <button
                                                type="button"
                                                class="note-select-all"
                                                id="clearEmployees"
                                                disabled
                                            >
                                                Clear
                                            </button>

                                        </div>
                                    </div>

                                    <select
                                        name="employees[]"
                                        id="employee"
                                        multiple
                                        class="note-field @error('employees') is-invalid @enderror @error('employees.*') is-invalid @enderror"
                                        disabled
                                    ></select>

                                    <small class="note-helper">
                                        Select multiple employees using Ctrl + Click.
                                        Use Select All to choose everyone.
                                    </small>

                                    <div class="note-loading" id="noteLoading">
                                        <i class="fas fa-spinner fa-spin mr-1"></i>
                                        Loading roles and employees...
                                    </div>

                                    <div class="note-error"
                                         id="noteAjaxError"
                                         style="display:none;">
                                    </div>

                                    @error('employees')
                                        <div class="note-error">{{ $message }}</div>
                                    @enderror

                                    @error('employees.*')
                                        <div class="note-error">{{ $message }}</div>
                                    @enderror

                                </div>
                            </div>

                        </div>

                        {{-- Footer --}}
                        <div class="note-action-footer">

                            <a href="{{ route('note.index') }}"
                               class="note-btn note-btn-light">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>

                            <button
                                type="submit"
                                id="submitNoteButton"
                                class="note-btn note-btn-primary"
                            >
                                <i class="fas fa-save"></i>
                                Create Note
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const officeSelect = document.getElementById('office_id');
    const roleSelect = document.getElementById('role');
    const employeeSelect = document.getElementById('employee');

    const selectAllBtn = document.getElementById('selectAllEmployees');
    const clearBtn = document.getElementById('clearEmployees');

    const employeeCount = document.getElementById('employeeCount');
    const loading = document.getElementById('noteLoading');
    const ajaxError = document.getElementById('noteAjaxError');

    const form = document.getElementById('createNoteForm');
    const submitBtn = document.getElementById('submitNoteButton');

    const oldRole = @json(old('role'));
    const oldEmployees = @json(old('employees', []));

    // Existing relative AJAX URL preserved.
    const endpointBase = @json(url('note/get-office-role-and-employee'));

    let activeController = null;
    let requestNumber = 0;

    function resetOptions() {
        roleSelect.replaceChildren(new Option('Select role', ''));

        employeeSelect.replaceChildren();
        employeeSelect.disabled = true;

        selectAllBtn.disabled = true;
        clearBtn.disabled = true;

        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = Array.from(employeeSelect.selectedOptions);

        employeeCount.textContent = selected.length + ' Selected';

        selectAllBtn.textContent =
            employeeSelect.options.length > 0 &&
            selected.length === employeeSelect.options.length
                ? 'All Selected'
                : 'Select All';
    }

    function showError(message) {
        ajaxError.textContent = message;
        ajaxError.style.display = 'block';
    }

    function populateData(data, restoreOld = false) {
        const roles = Array.isArray(data.roles) ? data.roles : [];
        const employees = Array.isArray(data.employees) ? data.employees : [];

        roleSelect.replaceChildren(new Option('Select role', ''));

        roles.forEach(function (role) {
            if (!role || role.name == null) return;

            roleSelect.add(new Option(
                String(role.name),
                String(role.name),
                false,
                restoreOld && String(oldRole) === String(role.name)
            ));
        });

        employeeSelect.replaceChildren();

        const selectedOldEmployees = Array.isArray(oldEmployees)
            ? oldEmployees.map(String)
            : [];

        // Existing backend supports employees[]=all.
        // Keep that value for compatibility.
        if (employees.length > 0) {
            employeeSelect.add(new Option('Select All', 'all'));

            employees.forEach(function (employee) {
                if (!employee || employee.id == null) return;

                employeeSelect.add(new Option(
                    String(employee.name ?? 'Employee'),
                    String(employee.id)
                ));
            });
        }

        if (restoreOld) {
            Array.from(employeeSelect.options).forEach(function (option) {
                option.selected = selectedOldEmployees.includes(option.value);
            });
        }

        employeeSelect.disabled = employees.length === 0;
        selectAllBtn.disabled = employees.length === 0;
        clearBtn.disabled = employees.length === 0;

        updateSelectedCount();
    }

    async function loadOfficeData(restoreOld = false) {
        const officeId = officeSelect.value;

        if (activeController) {
            activeController.abort();
        }

        const thisRequest = ++requestNumber;

        resetOptions();
        ajaxError.style.display = 'none';

        if (!officeId) {
            loading.style.display = 'none';
            return;
        }

        activeController = new AbortController();
        loading.style.display = 'block';

        try {
            const response = await fetch(
                endpointBase + '/' + encodeURIComponent(officeId),
                {
                    method: 'GET',
                    credentials: 'same-origin',
                    signal: activeController.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Unable to load office data (HTTP ' + response.status + ').');
            }

            const data = await response.json();

            if (thisRequest !== requestNumber) return;

            populateData(data, restoreOld);

        } catch (error) {
            if (error.name === 'AbortError') return;
            if (thisRequest !== requestNumber) return;

            showError(error.message || 'Error fetching office data.');
            resetOptions();

        } finally {
            if (thisRequest === requestNumber) {
                loading.style.display = 'none';
            }
        }
    }

    officeSelect.addEventListener('change', function () {
        loadOfficeData(false);
    });

    selectAllBtn.addEventListener('click', function () {
        Array.from(employeeSelect.options).forEach(function (option) {
            option.selected = option.value === 'all';
        });

        updateSelectedCount();
    });

    clearBtn.addEventListener('click', function () {
        Array.from(employeeSelect.options).forEach(function (option) {
            option.selected = false;
        });

        updateSelectedCount();
    });

    employeeSelect.addEventListener('change', function () {
        const allOption = Array.from(employeeSelect.options)
            .find(option => option.value === 'all');

        if (allOption && allOption.selected) {
            Array.from(employeeSelect.options).forEach(function (option) {
                if (option.value !== 'all') {
                    option.selected = false;
                }
            });
        }

        updateSelectedCount();
    });

    form.addEventListener('submit', function () {
        if (!form.checkValidity()) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> Saving Note...';
    });

    if (officeSelect.value) {
        loadOfficeData(true);
    }

});
</script>

@endsection
