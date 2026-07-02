@extends('dashboard.layout.root')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="p-4 sm:p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Generated Letters</h1>
                    <p class="text-sm text-gray-500 mt-1">Search, filter and print employee/candidate letters</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('employee-letters.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition"><span class="material-icons text-base">add</span> Generate</a>
                    <a href="{{ route('letter-templates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold shadow hover:bg-black transition"><span class="material-icons text-base">description</span> Templates</a>
                </div>
            </div>

            @if(session('success'))<div class="mt-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>@endif

            <form action="{{ route('employee-letters.index') }}" method="GET" class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-3"><label class="text-xs font-semibold text-gray-600">Search</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Letter no / name / phone" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
                    <div class="md:col-span-3"><label class="text-xs font-semibold text-gray-600">Document Type</label><select name="document_type_id" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"><option value="">All</option>@foreach($documentTypes as $type)<option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>@endforeach</select></div>
                    <div class="md:col-span-2"><label class="text-xs font-semibold text-gray-600">Department</label><select name="department_id" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"><option value="">All</option>@foreach($departments as $department)<option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach</select></div>
                    <div class="md:col-span-2"><label class="text-xs font-semibold text-gray-600">Office</label><select name="office_id" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"><option value="">All</option>@foreach($offices as $office)<option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>@endforeach</select></div>
                    <div class="md:col-span-2"><label class="text-xs font-semibold text-gray-600">Status</label><select name="status" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"><option value="">All</option><option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option><option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option><option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option></select></div>
                    <div class="md:col-span-2"><label class="text-xs font-semibold text-gray-600">From Date</label><input type="date" name="from_date" value="{{ request('from_date') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
                    <div class="md:col-span-2"><label class="text-xs font-semibold text-gray-600">To Date</label><input type="date" name="to_date" value="{{ request('to_date') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
                    <div class="md:col-span-8 flex flex-wrap items-end gap-2"><button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold shadow hover:bg-green-700 transition"><span class="material-icons text-base">search</span> Apply</button><a href="{{ route('employee-letters.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition"><span class="material-icons text-base">refresh</span> Clear</a></div>
                </div>
            </form>
        </div>

        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50"><tr class="text-left text-xs font-bold text-gray-600 uppercase tracking-wider"><th class="px-4 py-3">#</th><th class="px-4 py-3">Letter</th><th class="px-4 py-3">Employee/Candidate</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Office</th><th class="px-4 py-3">Department</th><th class="px-4 py-3">Issue Date</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Action</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employeeLetters as $letter)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ ($employeeLetters->currentPage()-1) * $employeeLetters->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-3"><div class="text-sm font-semibold text-gray-900">{{ $letter->letter_no }}</div><div class="text-xs text-gray-500">{{ $letter->rendered_subject ?: '—' }}</div></td>
                                <td class="px-4 py-3"><div class="text-sm font-semibold text-gray-900">{{ $letter->employee_name }}</div><div class="text-xs text-gray-500">{{ $letter->employee_phone ?: $letter->employee_email }}</div></td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $letter->documentType?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $letter->office?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $letter->department?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ optional($letter->issue_date)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $letter->status == 'issued' ? 'bg-green-100 text-green-800' : ($letter->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($letter->status) }}</span></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">

                                        {{-- Normal Details View --}}
                                        {{-- <a title="Details"
                                        href="{{ route('employee-letters.show', $letter) }}"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                            <span class="material-icons text-base">visibility</span>
                                        </a> --}}

                                        {{-- Letter Preview Like Print View --}}
                                        <a title="Preview"
                                        href="{{ route('employee-letters.preview', $letter) }}"
                                        target="_blank"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                                            <span class="material-icons text-base">article</span>
                                        </a>

                                        {{-- Print --}}
                                        <a title="Print"
                                        href="{{ route('employee-letters.print', $letter) }}"
                                        target="_blank"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-900 text-white hover:bg-black transition">
                                            <span class="material-icons text-base">print</span>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('employee-letters.destroy', $letter) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this letter?')">
                                            @csrf
                                            @method('DELETE')

                                            <button title="Delete"
                                                    type="submit"
                                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                                <span class="material-icons text-base">delete</span>
                                            </button>
                                        </form>

                                    </div>
                                </td>                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500">No letters found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $employeeLetters->appends(request()->except('page'))->links() }}</div>
        </div>
    </div>
</div>
@endsection
