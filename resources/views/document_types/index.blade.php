@extends('dashboard.layout.root')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="p-4 sm:p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Document Types</h1>
                    <p class="text-sm text-gray-500 mt-1">Offer letter, appointment letter, experience letter and other HR document masters</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('document-types.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition">
                        <span class="material-icons text-base">add</span> Create
                    </a>
                    <a href="{{ route('letter-templates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold shadow hover:bg-black transition">
                        <span class="material-icons text-base">description</span> Templates
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mt-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('document-types.index') }}" method="GET" class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-8">
                        <label class="text-xs font-semibold text-gray-600">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name / slug / description" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                    </div>
                    <div class="md:col-span-4 flex flex-wrap items-end gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold shadow hover:bg-green-700 transition">
                            <span class="material-icons text-base">search</span> Apply
                        </button>
                        <a href="{{ route('document-types.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition">
                            <span class="material-icons text-base">refresh</span> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($documentTypes as $documentType)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ ($documentTypes->currentPage()-1) * $documentTypes->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $documentType->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $documentType->slug }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ Str::limit($documentType->description, 80) ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $documentType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $documentType->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a title="Edit" href="{{ route('document-types.edit', $documentType) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                                            <span class="material-icons text-base">edit</span>
                                        </a>
                                        <form action="{{ route('document-types.destroy', $documentType) }}" method="POST" onsubmit="return confirm('Delete this document type?')">
                                            @csrf
                                            @method('DELETE')
                                            <button title="Delete" type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                                                <span class="material-icons text-base">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">No document types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $documentTypes->appends(request()->except('page'))->links() }}</div>
        </div>
    </div>
</div>
@endsection
