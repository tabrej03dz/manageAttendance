@extends('dashboard.layout.root')

@section('title', 'Offices')

@push('styles')
<style>
    .office-page{font-family:'Inter',sans-serif;color:#0f172a}
    .office-hero{position:relative;overflow:hidden;border:1px solid #312e81;border-radius:26px;background:linear-gradient(135deg,#0f172a 0%,#172554 52%,#312e81 100%);box-shadow:0 22px 55px rgba(15,23,42,.28);isolation:isolate}
    .office-hero:before{content:'';position:absolute;width:320px;height:320px;right:-100px;top:-125px;border-radius:999px;background:rgba(99,102,241,.34);filter:blur(12px);z-index:-1}
    .office-hero:after{content:'';position:absolute;width:280px;height:280px;left:36%;bottom:-185px;border-radius:999px;background:rgba(6,182,212,.18);filter:blur(18px);z-index:-1}
    .office-hero-badge{display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,.24);border-radius:999px;background:rgba(15,23,42,.55);color:#fff;padding:7px 12px;font-size:12px;font-weight:700}
    .office-card{overflow:hidden;border:1px solid #d8e0ea;border-radius:22px;background:#fff;box-shadow:0 10px 30px rgba(15,23,42,.10)}
    .office-card-header{border-bottom:1px solid #e2e8f0;background:linear-gradient(90deg,rgba(238,242,255,.88),rgba(236,254,255,.65))}
    .office-label{display:block;margin-bottom:7px;color:#334155;font-size:13px;font-weight:800}
    .filter-control{width:100%;min-height:44px;border:1px solid #dbe3ee!important;border-radius:12px!important;background:#fff!important;color:#0f172a!important;padding:10px 12px!important;font-size:13px!important;outline:none;transition:border-color .2s,box-shadow .2s}
    .filter-control:focus{border-color:#818cf8!important;box-shadow:0 0 0 4px rgba(99,102,241,.12)!important}
    .action-primary,.action-secondary,.action-danger{display:inline-flex;min-height:45px;align-items:center;justify-content:center;gap:8px;border-radius:13px;padding:10px 17px;font-size:13px;font-weight:800;text-decoration:none!important;transition:transform .2s,box-shadow .2s,filter .2s}
    .action-primary{border:0;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff!important;box-shadow:0 12px 25px rgba(79,70,229,.24)}
    .action-primary:hover{color:#fff!important;transform:translateY(-2px);filter:brightness(1.06);box-shadow:0 17px 32px rgba(79,70,229,.31)}
    .action-secondary{border:1px solid #dbe3ee;background:#f8fafc;color:#475569!important}
    .action-secondary:hover{color:#0f172a!important;background:#f1f5f9;transform:translateY(-2px)}
    .action-danger{border:1px solid #fecaca;background:#fff1f2;color:#be123c!important}
    .action-danger:hover{background:#ffe4e6;color:#9f1239!important;transform:translateY(-2px)}
    .switch-alert{display:flex;align-items:center;justify-content:space-between;gap:16px;border:1px solid #c4b5fd;border-radius:18px;background:linear-gradient(90deg,#f5f3ff,#eef2ff);padding:15px 18px;box-shadow:0 8px 22px rgba(76,29,149,.08)}
    .table-scroll::-webkit-scrollbar{width:7px;height:7px}.table-scroll::-webkit-scrollbar-thumb{background:linear-gradient(90deg,#818cf8,#06b6d4);border-radius:999px}.table-scroll::-webkit-scrollbar-track{background:#f1f5f9;border-radius:999px}
    .office-table{width:100%;min-width:1080px;border-collapse:separate;border-spacing:0}
    .office-table th{border-bottom:1px solid #e2e8f0;background:#f8fafc;padding:15px 16px;text-align:left;color:#64748b;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}
    .office-table td{border-bottom:1px solid #f1f5f9;padding:15px 16px;color:#334155;font-size:13px;vertical-align:middle}
    .office-table tbody tr:hover{background:#f8fafc}.office-table tbody tr:last-child td{border-bottom:0}
    .icon-action{display:inline-flex;width:39px;height:39px;align-items:center;justify-content:center;border:0;border-radius:11px;color:#fff!important;text-decoration:none!important;box-shadow:0 8px 18px rgba(15,23,42,.14);cursor:pointer;transition:transform .2s,filter .2s}
    .icon-action:hover{color:#fff!important;transform:translateY(-2px);filter:brightness(1.06)}
    @media(max-width:767px){.office-hero{border-radius:20px}.office-card{border-radius:18px}.action-primary,.action-secondary,.action-danger{width:100%}.switch-alert{align-items:flex-start;flex-direction:column}.switch-alert form,.switch-alert button{width:100%}}
</style>
@endpush

@section('content')
<div class="office-page space-y-6 pb-10">

    @if(auth()->user()->hasRole('super_admin') && session('active_office_id'))
        <section class="switch-alert">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-violet-600 text-white shadow-lg shadow-violet-200">
                    <i class="fas fa-building"></i>
                </span>
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-violet-600">Office View Active</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">Currently viewing records for <span class="text-violet-700">{{ session('active_office_name') }}</span></p>
                </div>
            </div>
            <form action="{{ route('office.clearSwitch') }}" method="POST">
                @csrf
                <button type="submit" class="action-danger"><i class="fas fa-right-from-bracket"></i>Exit Office View</button>
            </form>
        </section>
    @endif

    <section class="office-hero p-6 sm:p-8">
        <div class="relative flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <span class="office-hero-badge"><span class="h-2 w-2 animate-pulse rounded-full bg-green-400"></span>Office Management</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Offices</h1>
                <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-blue-100 sm:text-base">Manage office locations, attendance radius, status and office-level access from one place.</p>
                <div class="mt-5 flex flex-wrap gap-4 text-sm font-semibold text-slate-200">
                    <span><i class="fas fa-building mr-2 text-indigo-300"></i>{{ method_exists($offices, 'total') ? $offices->total() : $offices->count() }} Offices</span>
                    <span><i class="fas fa-location-dot mr-2 text-cyan-300"></i>Location & Radius Control</span>
                </div>
            </div>

            @can('create office')
                <a href="{{ route('office.create') }}" class="action-primary"><i class="fas fa-plus"></i>Create Office</a>
            @endcan
        </div>
    </section>

    <section class="office-card">
        <div class="office-card-header p-5 sm:p-6">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Office Directory</h2>
                <p class="mt-1 text-sm font-medium text-slate-500">Search and filter office records using location, status and radius settings.</p>
            </div>

            <form method="GET" action="{{ route('office.index') }}" class="mt-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    <div class="md:col-span-5">
                        <label class="office-label" for="search">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Office name, latitude, longitude or radius" class="filter-control pl-11">
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="office-label" for="status">Status</label>
                        <select id="status" name="status" class="filter-control">
                            <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="office-label" for="under_radius_required">Under Radius Required</label>
                        <select id="under_radius_required" name="under_radius_required" class="filter-control">
                            <option value="">All Radius Rules</option>
                            <option value="1" {{ request('under_radius_required') === '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ request('under_radius_required') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="action-primary"><i class="fas fa-filter"></i>Apply Filters</button>
                    <a href="{{ route('office.index') }}" class="action-secondary"><i class="fas fa-rotate-left"></i>Clear</a>
                </div>
            </form>
        </div>

        <div class="table-scroll overflow-x-auto">
            <table class="office-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Office</th>
                        <th>Coordinates</th>
                        <th>Radius</th>
                        <th>Radius Rule</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offices as $office)
                        @php
                            $number = method_exists($offices, 'currentPage')
                                ? (($offices->currentPage() - 1) * $offices->perPage()) + $loop->iteration
                                : $loop->iteration;
                            $radiusRequired = in_array(
                                strtolower((string) $office->under_radius_required),
                                ['1', 'yes', 'true'],
                                true
                            );
                            $isActive = $office->status === 'active';
                        @endphp
                        <tr>
                            <td><span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-xs font-extrabold text-indigo-600">{{ $number }}</span></td>
                            <td>
                                <div class="flex min-w-[220px] items-center gap-3">
                                    <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-100"><i class="fas fa-building"></i></span>
                                    <div class="min-w-0">
                                        <p class="truncate font-extrabold text-slate-900">{{ $office->name }}</p>
                                        <p class="mt-1 truncate text-xs font-medium text-slate-500">Office ID: {{ $office->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="min-w-[190px] space-y-1 text-xs font-semibold text-slate-600">
                                    <p><i class="fas fa-location-crosshairs mr-2 w-4 text-indigo-500"></i>{{ $office->latitude ?? '—' }}</p>
                                    <p><i class="fas fa-map-pin mr-2 w-4 text-cyan-500"></i>{{ $office->longitude ?? '—' }}</p>
                                </div>
                            </td>
                            <td><span class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-3 py-1.5 text-xs font-extrabold text-cyan-700"><i class="fas fa-bullseye"></i>{{ $office->radius ?? 0 }} m</span></td>
                            <td><span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-extrabold {{ $radiusRequired ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600' }}"><span class="h-2 w-2 rounded-full {{ $radiusRequired ? 'bg-blue-500' : 'bg-slate-400' }}"></span>{{ $radiusRequired ? 'Required' : 'Not Required' }}</span></td>
                            <td>
                                @can('office status change')
                                    <a href="{{ route('office.status', ['office' => $office->id]) }}" onclick="return confirm('Are you sure you want to change this office status?')" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-extrabold {{ $isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}"><span class="h-2 w-2 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>{{ $isActive ? 'Active' : 'Inactive' }}</a>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-extrabold {{ $isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}"><span class="h-2 w-2 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>{{ $isActive ? 'Active' : 'Inactive' }}</span>
                                @endcan
                            </td>
                            <td>
                                <div class="flex min-w-[190px] items-center justify-end gap-2">
                                    @can('edit office')<a title="Edit" href="{{ route('office.edit', ['office' => $office->id]) }}" class="icon-action bg-gradient-to-br from-blue-500 to-indigo-600"><i class="fas fa-pen"></i></a>@endcan
                                    @can('delete office')<a title="Delete" href="{{ route('office.delete', ['office' => $office->id]) }}" onclick="return confirm('Delete this office?')" class="icon-action bg-gradient-to-br from-rose-500 to-red-600"><i class="fas fa-trash"></i></a>@endcan
                                    @can('show office details')<a title="Office Details" href="{{ route('office.detail', ['office' => $office->id]) }}" class="icon-action bg-gradient-to-br from-emerald-500 to-green-600"><i class="fas fa-eye"></i></a>@endcan
                                    @if(auth()->user()->hasRole('super_admin'))
                                        <form action="{{ route('office.switch', $office->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="View as this office" class="icon-action bg-gradient-to-br from-violet-500 to-purple-700"><i class="fas fa-right-to-bracket"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-500"><i class="fas fa-building"></i></div>
                                <p class="mt-4 font-extrabold text-slate-800">No offices found</p>
                                <p class="mt-1 text-sm font-medium text-slate-500">Try changing the filters or create a new office.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($offices, 'links'))
            <div class="border-t border-slate-200 px-5 py-5 sm:px-6">{{ $offices->appends(request()->except('page'))->links() }}</div>
        @endif
    </section>
</div>
@endsection