@extends('dashboard.layout.root')

@section('title', 'Employees')


@push('styles')
<style>
    .employee-page{font-family:'Inter',sans-serif;color:#0f172a}
    .employee-hero{position:relative;overflow:hidden;border:1px solid #312e81;border-radius:26px;background:linear-gradient(135deg,#0f172a 0%,#172554 52%,#312e81 100%);box-shadow:0 22px 55px rgba(15,23,42,.28);isolation:isolate}
    .employee-hero:before{content:'';position:absolute;width:320px;height:320px;right:-100px;top:-125px;border-radius:999px;background:rgba(99,102,241,.34);filter:blur(12px);z-index:-1}
    .employee-hero:after{content:'';position:absolute;width:280px;height:280px;left:36%;bottom:-185px;border-radius:999px;background:rgba(6,182,212,.18);filter:blur(18px);z-index:-1}
    .employee-hero-badge{display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,.24);border-radius:999px;background:rgba(15,23,42,.55);color:#fff;padding:7px 12px;font-size:12px;font-weight:700}
    .employee-card{overflow:hidden;border:1px solid #d8e0ea;border-radius:22px;background:#fff;box-shadow:0 10px 30px rgba(15,23,42,.10)}
    .employee-card-header{border-bottom:1px solid #e2e8f0;background:linear-gradient(90deg,rgba(238,242,255,.88),rgba(236,254,255,.65))}
    .section-card{border:1px solid #e2e8f0;border-radius:18px;background:#fff;overflow:hidden}
    .section-title{display:flex;align-items:center;gap:12px;border-bottom:1px solid #e2e8f0;background:#f8fafc;padding:16px 18px}
    .section-icon{display:flex;width:42px;height:42px;align-items:center;justify-content:center;border-radius:13px;background:#e0e7ff;color:#4338ca;flex-shrink:0}
    .employee-label{display:block;margin-bottom:7px;color:#334155;font-size:13px;font-weight:800}
    .employee-input,.employee-select,.employee-textarea{width:100%;border:1px solid #dbe3ee!important;border-radius:13px!important;background:#fff!important;color:#0f172a!important;padding:11px 13px!important;font-size:13px!important;font-weight:500;outline:none;transition:border-color .2s,box-shadow .2s}
    .employee-input,.employee-select{min-height:46px}
    .employee-textarea{min-height:92px;resize:vertical}
    .employee-input:focus,.employee-select:focus,.employee-textarea:focus{border-color:#818cf8!important;box-shadow:0 0 0 4px rgba(99,102,241,.12)!important}
    .employee-input.input-error,.employee-select.input-error,.employee-textarea.input-error{border-color:#fb7185!important;background:#fff1f2!important}
    .employee-help{margin-top:6px;color:#64748b;font-size:11px;font-weight:500}
    .employee-error{display:flex;align-items:flex-start;gap:7px;margin-top:7px;color:#e11d48;font-size:12px;font-weight:700}
    .error-summary{border:1px solid #fecdd3;border-radius:16px;background:#fff1f2;padding:16px;color:#9f1239}
    .action-primary,.action-secondary,.action-dark,.action-warning{display:inline-flex;min-height:45px;align-items:center;justify-content:center;gap:8px;border-radius:13px;padding:10px 17px;font-size:13px;font-weight:800;text-decoration:none!important;transition:transform .2s,box-shadow .2s,filter .2s}
    .action-primary{border:0;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff!important;box-shadow:0 12px 25px rgba(79,70,229,.24)}
    .action-primary:hover{color:#fff!important;transform:translateY(-2px);filter:brightness(1.06);box-shadow:0 17px 32px rgba(79,70,229,.31)}
    .action-secondary{border:1px solid #dbe3ee;background:#f8fafc;color:#475569!important}
    .action-secondary:hover{color:#0f172a!important;background:#f1f5f9;transform:translateY(-2px)}
    .action-dark{border:1px solid #334155;background:#0f172a;color:#fff!important}
    .action-warning{border:1px solid #fde68a;background:#fffbeb;color:#92400e!important}
    .radio-card{display:flex;align-items:center;gap:10px;border:1px solid #e2e8f0;border-radius:13px;background:#f8fafc;padding:12px 14px;cursor:pointer}
    .radio-card:has(input:checked){border-color:#818cf8;background:#eef2ff}
    .table-scroll::-webkit-scrollbar{width:7px;height:7px}.table-scroll::-webkit-scrollbar-thumb{background:linear-gradient(90deg,#818cf8,#06b6d4);border-radius:999px}.table-scroll::-webkit-scrollbar-track{background:#f1f5f9;border-radius:999px}
    .employee-table{width:100%;min-width:1100px;border-collapse:separate;border-spacing:0}.employee-table th{border-bottom:1px solid #e2e8f0;background:#f8fafc;padding:15px 16px;text-align:left;color:#64748b;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}.employee-table td{border-bottom:1px solid #f1f5f9;padding:15px 16px;color:#334155;font-size:13px;vertical-align:middle}.employee-table tbody tr:hover{background:#f8fafc}.employee-table tbody tr:last-child td{border-bottom:0}
    .icon-action{display:inline-flex;width:39px;height:39px;align-items:center;justify-content:center;border:0;border-radius:11px;color:#fff!important;text-decoration:none!important;box-shadow:0 8px 18px rgba(15,23,42,.14);transition:transform .2s,filter .2s}
    .icon-action:hover{color:#fff!important;transform:translateY(-2px);filter:brightness(1.06)}
    .filter-control{width:100%;min-height:44px;border:1px solid #dbe3ee!important;border-radius:12px!important;background:#fff!important;color:#0f172a!important;padding:10px 12px!important;font-size:13px!important;outline:none}.filter-control:focus{border-color:#818cf8!important;box-shadow:0 0 0 4px rgba(99,102,241,.12)!important}
    @media(max-width:767px){.employee-hero{border-radius:20px}.employee-card{border-radius:18px}.action-primary,.action-secondary{width:100%}}
</style>
@endpush


@section('content')
<div class="employee-page space-y-6 pb-10">

    <section class="employee-hero p-6 sm:p-8">
        <div class="relative flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <span class="employee-hero-badge"><span class="h-2 w-2 animate-pulse rounded-full bg-green-400"></span>Workforce Management</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Employees</h1>
                <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">Search, filter और manage employees, offices, departments, status और permissions.</p>
                <div class="mt-5 flex flex-wrap gap-4 text-sm font-semibold text-slate-200">
                    <span><i class="fas fa-users mr-2 text-indigo-300"></i>{{ method_exists($employees, 'total') ? $employees->total() : $employees->count() }} Employees</span>
                    @if(!empty($unassignedCount))<span><i class="fas fa-user-clock mr-2 text-amber-300"></i>{{ $unassignedCount }} Register Requests</span>@endif
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                @can('create employee')
                    <a href="{{ route('employee.create') }}" class="action-primary"><i class="fas fa-user-plus"></i>Create Employee</a>
                @endcan
                <a href="{{ route('employee.index', array_merge(request()->except('office_id'), ['office_unassigned' => 1, 'status' => '0'])) }}" class="action-warning relative">
                    <i class="fas fa-user-clock"></i>Register Requests
                    @if(!empty($unassignedCount) && $unassignedCount > 0)<span class="absolute -right-2 -top-2 flex h-6 min-w-6 items-center justify-center rounded-full bg-rose-600 px-1 text-xs font-extrabold text-white">{{ $unassignedCount }}</span>@endif
                </a>
                @canany(['show departments', 'Show Departments'])
                    <a href="{{ route('departments.index') }}" class="action-dark"><i class="fas fa-sitemap"></i>Departments</a>
                @endcanany
            </div>
        </div>
    </section>

    <section class="employee-card">
        <div class="employee-card-header p-5 sm:p-6">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Employee Directory</h2>
                <p class="mt-1 text-sm font-medium text-slate-500">Use filters to find the required employee records.</p>
            </div>

            <form action="{{ route('employee.index') }}" method="GET" class="mt-5">
                @if(request('office_unassigned') == '1')<input type="hidden" name="office_unassigned" value="1">@endif
                <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    <div class="md:col-span-4"><label class="employee-label">Search</label><div class="relative"><i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input type="text" name="q" value="{{ request('q') }}" placeholder="Name, email or phone" class="filter-control pl-11"></div></div>
                    <div class="md:col-span-2"><label class="employee-label">Status</label><select name="status" class="filter-control"><option value="">All Status</option><option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option><option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option></select></div>
                    <div class="md:col-span-3"><label class="employee-label">Department</label><select name="department_id" class="filter-control"><option value="">All Departments</option>@foreach($departments as $department)<option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach</select></div>
                    <div class="md:col-span-3"><label class="employee-label">Office</label><select name="office_id" class="filter-control"><option value="">All Offices</option>@foreach($offices as $office)<option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>@endforeach</select></div>
                </div>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="action-primary"><i class="fas fa-filter"></i>Apply Filters</button>
                    <a href="{{ route('employee.index') }}" class="action-secondary"><i class="fas fa-rotate-left"></i>Clear</a>
                </div>
            </form>
        </div>

        <div class="table-scroll overflow-x-auto">
            <table class="employee-table">
                <thead>
                    <tr><th>#</th><th>Employee</th><th>Contact</th><th>Office</th><th>Department</th><th>Status</th><th>Location</th><th class="text-right">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        @php
                            $number = method_exists($employees, 'currentPage')
                                ? (($employees->currentPage() - 1) * $employees->perPage()) + $loop->iteration
                                : $loop->iteration;
                            $photoUrl = $employee->photo
                                ? asset('storage/'.$employee->photo)
                                : 'https://ui-avatars.com/api/?name='.urlencode($employee->name).'&background=4f46e5&color=fff&size=128';
                        @endphp
                        <tr>
                            <td><span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-xs font-extrabold text-indigo-600">{{ $number }}</span></td>
                            <td>
                                <div class="flex min-w-[220px] items-center gap-3">
                                    <img src="{{ $photoUrl }}" alt="{{ $employee->name }}" class="h-11 w-11 rounded-xl border-2 border-indigo-100 object-cover">
                                    <div class="min-w-0"><p class="truncate font-extrabold text-slate-900">{{ $employee->name }}</p><p class="mt-1 truncate text-xs font-medium text-slate-500">{{ $employee->email ?: ($employee->employee_id ?: 'No email') }}</p></div>
                                </div>
                            </td>
                            <td><div class="font-semibold text-slate-700">{{ $employee->phone ?: '—' }}</div></td>
                            <td><span class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-3 py-1.5 text-xs font-bold text-cyan-700"><i class="fas fa-building"></i>{{ $employee->office?->name ?? 'Unassigned' }}</span></td>
                            <td><span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700"><i class="fas fa-sitemap"></i>{{ $employee->department?->name ?? 'N/A' }}</span></td>
                            <td>
                                @can('change status of employee')
                                    <a href="{{ route('employee.status', ['employee' => $employee->id]) }}" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-extrabold {{ $employee->status == '1' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}"><span class="h-2 w-2 rounded-full {{ $employee->status == '1' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>{{ $employee->status == '1' ? 'Active' : 'Inactive' }}</a>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-extrabold {{ $employee->status == '1' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}"><span class="h-2 w-2 rounded-full {{ $employee->status == '1' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>{{ $employee->status == '1' ? 'Active' : 'Inactive' }}</span>
                                @endcan
                            </td>
                            <td><span class="rounded-full px-3 py-1.5 text-xs font-extrabold {{ $employee->location_required === 'yes' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600' }}">{{ ucfirst($employee->location_required ?: 'no') }}</span></td>
                            <td>
                                <div class="flex min-w-[225px] items-center justify-end gap-2">
                                    @can('edit employee')<a title="Edit" href="{{ route('employee.edit', ['employee' => $employee->id]) }}" class="icon-action bg-gradient-to-br from-blue-500 to-indigo-600"><i class="fas fa-pen"></i></a>@endcan
                                    @can('delete employee')<form action="{{ route('employee.delete', ['employee' => $employee->id]) }}" method="post" onsubmit="return confirm('Delete this employee?')">@csrf<button title="Delete" type="submit" class="icon-action bg-gradient-to-br from-rose-500 to-red-600"><i class="fas fa-trash"></i></button></form>@endcan
                                    @can('show profile of employee')<a title="Profile" href="{{ route('employee.profile', ['user' => $employee->id]) }}" class="icon-action bg-gradient-to-br from-emerald-500 to-green-600"><i class="fas fa-user"></i></a>@endcan
                                    @role('super_admin|admin|owner')<a title="Salary Setup" href="{{ route('salary.setupForm', ['employee' => $employee->id]) }}" class="icon-action bg-gradient-to-br from-slate-700 to-slate-950"><i class="fas fa-wallet"></i></a>@endrole
                                    @can('show permissions of employee')<a title="Permissions" href="{{ route('employee.permission', ['user' => $employee->id]) }}" class="icon-action bg-gradient-to-br from-violet-500 to-purple-700"><i class="fas fa-user-shield"></i></a>@endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-16 text-center"><div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-500"><i class="fas fa-users"></i></div><p class="mt-4 font-extrabold text-slate-800">No employees found</p><p class="mt-1 text-sm font-medium text-slate-500">Try changing the filters or create a new employee.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($employees, 'links'))
            <div class="border-t border-slate-200 px-5 py-5 sm:px-6">{{ $employees->appends(request()->except('page'))->links() }}</div>
        @endif
    </section>
</div>
@endsection
