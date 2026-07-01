@extends('dashboard.layout.root')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<div class="p-4 sm:p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div><h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $letterTemplate->title }}</h1><p class="text-sm text-gray-500 mt-1">{{ $letterTemplate->documentType?->name }} • {{ $letterTemplate->office?->name ?? 'All Offices' }} • {{ $letterTemplate->department?->name ?? 'All Departments' }}</p></div>
            <div class="flex flex-wrap items-center gap-2"><a href="{{ route('letter-templates.edit', $letterTemplate) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition"><span class="material-icons text-base">edit</span> Edit</a><a href="{{ route('letter-templates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition"><span class="material-icons text-base">arrow_back</span> Back</a></div>
        </div>
        <div class="p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-4 space-y-3">
                <div class="rounded-xl border border-gray-200 p-4"><div class="text-xs font-bold text-gray-500 uppercase">Subject</div><div class="mt-1 text-sm text-gray-900">{{ $letterTemplate->subject ?: '—' }}</div></div>
                <div class="rounded-xl border border-gray-200 p-4"><div class="text-xs font-bold text-gray-500 uppercase">Variables</div><div class="mt-2 flex flex-wrap gap-2">@forelse($letterTemplate->variables ?? [] as $variable)<span class="px-2 py-1 rounded bg-gray-100 text-xs font-semibold text-gray-700">{{ '{{'.$variable.'}}' }}</span>@empty<span class="text-sm text-gray-500">No variables found.</span>@endforelse</div></div>
                <div class="rounded-xl border border-gray-200 p-4"><div class="text-xs font-bold text-gray-500 uppercase">Status</div><div class="mt-1"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $letterTemplate->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $letterTemplate->is_active ? 'Active' : 'Inactive' }}</span></div></div>
            </div>
            <div class="lg:col-span-8"><div class="rounded-xl border border-gray-200 p-6 bg-white prose max-w-none">{!! $letterTemplate->body_html !!}</div></div>
        </div>
    </div>
</div>
@endsection
