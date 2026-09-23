<div class="mb-6">
    <div class="inline-flex w-full rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm sm:w-auto">

        {{-- Departments --}}
        <a href="{{ route('departments.index') }}"
           class="flex flex-1 items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-extrabold transition sm:flex-none
           {{ request()->routeIs('departments.*')
                ? 'bg-indigo-600 text-white shadow-md'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="fas fa-sitemap"></i>

            <span>
                Departments
            </span>
        </a>

        {{-- Designations --}}
        <a href="{{ route('designations.index') }}"
           class="flex flex-1 items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-extrabold transition sm:flex-none
           {{ request()->routeIs('designations.*')
                ? 'bg-indigo-600 text-white shadow-md'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">

            <i class="fas fa-id-badge"></i>

            <span>
                Designations
            </span>
        </a>

    </div>
</div>