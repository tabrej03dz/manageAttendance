@extends('dashboard.layout.root')
@section('content')

@php
    $authUser = auth()->user();
    $canChooseOffice = $authUser && $authUser->hasAnyRole(['super_admin', 'owner']);
@endphp

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="p-4 sm:p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">

        {{-- Header --}}
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Letter Templates</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Department-wise dynamic templates for HR letters
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('letter-templates.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition">
                        <span class="material-icons text-base">add</span>
                        Create
                    </a>

                    <a href="{{ route('employee-letters.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold shadow hover:bg-green-700 transition">
                        <span class="material-icons text-base">post_add</span>
                        Generate Letter
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mt-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <form action="{{ route('letter-templates.index') }}" method="GET" class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">

                    <div class="{{ $canChooseOffice ? 'md:col-span-3' : 'md:col-span-4' }}">
                        <label class="text-xs font-semibold text-gray-600">Search</label>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Title / subject"
                               class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                    </div>

                    <div class="{{ $canChooseOffice ? 'md:col-span-3' : 'md:col-span-4' }}">
                        <label class="text-xs font-semibold text-gray-600">Document Type</label>
                        <select name="document_type_id"
                                class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                            <option value="">All</option>
                            @foreach($documentTypes as $documentType)
                                <option value="{{ $documentType->id }}"
                                    {{ request('document_type_id') == $documentType->id ? 'selected' : '' }}>
                                    {{ $documentType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="{{ $canChooseOffice ? 'md:col-span-3' : 'md:col-span-4' }}">
                        <label class="text-xs font-semibold text-gray-600">Department</label>
                        <select name="department_id"
                                class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                            <option value="">All</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($canChooseOffice)
                        <div class="md:col-span-3">
                            <label class="text-xs font-semibold text-gray-600">Office</label>
                            <select name="office_id"
                                    class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                                <option value="">All Offices</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}"
                                        {{ request('office_id') == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="md:col-span-12 flex flex-wrap items-center gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold shadow hover:bg-green-700 transition">
                            <span class="material-icons text-base">search</span>
                            Apply
                        </button>

                        <a href="{{ route('letter-templates.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition">
                            <span class="material-icons text-base">refresh</span>
                            Clear
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
                            <th class="px-4 py-3">Template</th>
                            <th class="px-4 py-3">Type</th>

                            @if($canChooseOffice)
                                <th class="px-4 py-3">Office</th>
                            @endif

                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Variables</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($letterTemplates as $template)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ ($letterTemplates->currentPage() - 1) * $letterTemplates->perPage() + $loop->iteration }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $template->title }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $template->subject ?: 'No subject' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $template->documentType?->name ?? '—' }}
                                </td>

                                @if($canChooseOffice)
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $template->office?->name ?? '—' }}
                                    </td>
                                @endif

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $template->department?->name ?? 'All Departments' }}
                                </td>

                                <td class="px-4 py-3 text-xs text-gray-700">
                                    @php
                                        $variables = $template->variables ?? [];
                                    @endphp

                                    @if(!empty($variables))
                                        {{ collect($variables)->take(4)->implode(', ') }}
                                        {{ count($variables) > 4 ? '...' : '' }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a title="View"
                                           href="{{ route('letter-templates.show', $template) }}"
                                           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                            <span class="material-icons text-base">visibility</span>
                                        </a>

                                        <a title="Edit"
                                           href="{{ route('letter-templates.edit', $template) }}"
                                           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                                            <span class="material-icons text-base">edit</span>
                                        </a>

                                        <form action="{{ route('letter-templates.destroy', $template) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this template?')">
                                            @csrf
                                            @method('DELETE')

                                            <button title="Delete"
                                                    type="submit"
                                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                                <span class="material-icons text-base">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canChooseOffice ? 8 : 7 }}"
                                    class="px-4 py-10 text-center text-sm text-gray-500">
                                    No templates found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $letterTemplates->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection