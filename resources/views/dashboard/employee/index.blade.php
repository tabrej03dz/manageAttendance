@extends('dashboard.layout.root')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="p-4 sm:p-6">
    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        {{-- Header + Filters --}}
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Employees</h1>
                    <p class="text-sm text-gray-500 mt-1">Search, filter and manage employees</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @can('create employee')
                        <a href="{{ route('employee.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition">
                            <span class="material-icons text-base">add</span> Create
                        </a>
                    @endcan

                    <a href="{{ route('employee.index', array_merge(request()->except('office_id'), ['office_unassigned' => 1, 'status' => '0'])) }}"
                       class="relative inline-flex items-center gap-2 px-4 py-2 rounded-lg border font-semibold transition
                       {{ request('office_unassigned') == '1' ? 'bg-gray-900 text-white border-gray-900' : 'bg-amber-50 text-amber-900 border-amber-200 hover:bg-amber-100' }}">
                        <span class="material-icons text-base">assignment_late</span>
                        Register Request

                        @if(!empty($unassignedCount) && $unassignedCount > 0)
                            <span class="absolute -top-2 -right-2 min-w-[22px] h-[22px] px-1.5 flex items-center justify-center
                                         bg-red-600 text-white text-xs font-bold rounded-full shadow">
                                {{ $unassignedCount }}
                            </span>
                        @endif
                    </a>

                    @canany(['show departments', 'Show Departments'])
                        <a href="{{ route('departments.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold shadow hover:bg-black transition">
                            <span class="material-icons text-base">apartment</span> Departments
                        </a>
                    @endcanany
                </div>
            </div>

            {{-- Filters Row --}}
            <form action="{{ route('employee.index') }}" method="GET" class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    {{-- Search --}}
                    <div class="md:col-span-4">
                        <label class="text-xs font-semibold text-gray-600">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Search name / email / phone"
                               class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400" />
                    </div>





                    {{-- Status --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600">Status</label>
                        <select name="status"
                                class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                            <option value="">All</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Department --}}
                    <div class="md:col-span-3">
                        <label class="text-xs font-semibold text-gray-600">Department</label>
                        <select name="department_id"
                                class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                            <option value="">All</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Office --}}
                    <div class="md:col-span-3">
                        <label class="text-xs font-semibold text-gray-600">Office</label>
                        <select name="office_id"
                                class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                            <option value="">All</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="md:col-span-12 flex flex-wrap items-center gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold shadow hover:bg-green-700 transition">
                            <span class="material-icons text-base">search</span> Apply
                        </button>

                        <a href="{{ route('employee.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition">
                            <span class="material-icons text-base">refresh</span> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Contact</th>
                            <th class="px-4 py-3">Office</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Location R</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($employees as $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ ($employees->currentPage()-1) * $employees->perPage() + $loop->iteration }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img
                                            src="{{ $employee->photo ? asset('storage/'. $employee->photo) : 'https://via.placeholder.com/80' }}"
                                            alt="{{ $employee->name }}"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $employee->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $employee->phone }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $employee->office?->name ?? '—' }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $employee->department?->name ?? 'N/A' }}
                                </td>

                                <td class="px-4 py-3">
                                    @can('change status of employee')
                                        <a href="{{ route('employee.status', ['employee' => $employee->id]) }}"
                                           class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                           {{ $employee->status == '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $employee->status == '1' ? 'Active' : 'Inactive' }}
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                           {{ $employee->status == '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $employee->status == '1' ? 'Active' : 'Inactive' }}
                                        </span>
                                    @endcan
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $employee->location_required }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('edit employee')
                                            <a title="Edit" href="{{ route('employee.edit', ['employee' => $employee->id]) }}"
                                               class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                                                <span class="material-icons text-base">edit</span>
                                            </a>
                                        @endcan

                                        @can('delete employee')
                                            <form action="{{ route('employee.delete', ['employee' => $employee->id]) }}" method="post"
                                                  onsubmit="return confirm('Delete this employee?')">
                                                @csrf
                                                <button title="Delete" type="submit"
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                                    <span class="material-icons text-base">delete</span>
                                                </button>
                                            </form>
                                        @endcan

                                        @can('show profile of employee')
                                            <a title="Profile" href="{{ route('employee.profile', ['user' => $employee->id]) }}"
                                               class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                                <span class="material-icons text-base">account_circle</span>
                                            </a>
                                        @endcan

                                        @role('super_admin|admin|owner')
                                            <a title="Salary Setup" href="{{ route('salary.setupForm', ['employee' => $employee->id]) }}"
                                               class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-900 text-white hover:bg-black transition">
                                                <span class="material-icons text-base">payments</span>
                                            </a>
                                        @endrole

                                        @can('show permissions of employee')
                                            <a title="Permissions" href="{{ route('employee.permission', ['user' => $employee->id]) }}"
                                               class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                                <span class="material-icons text-base">admin_panel_settings</span>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500">
                                    No employees found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $employees->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
