@extends('dashboard.layout.root')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<div class="p-4 sm:p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div><h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $employeeLetter->letter_no }}</h1><p class="text-sm text-gray-500 mt-1">{{ $employeeLetter->documentType?->name }} for {{ $employeeLetter->employee_name }}</p></div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('employee-letters.print', $employeeLetter) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold shadow hover:bg-black transition"><span class="material-icons text-base">print</span> Print</a>
                <a href="{{ route('employee-letters.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition"><span class="material-icons text-base">arrow_back</span> Back</a>
            </div>
        </div>
        @if(session('success'))<div class="mx-4 sm:mx-6 mt-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>@endif
        <div class="p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-4 space-y-3">
                <div class="rounded-xl border border-gray-200 p-4"><div class="text-xs font-bold text-gray-500 uppercase">Employee / Candidate</div><div class="mt-1 text-sm font-semibold text-gray-900">{{ $employeeLetter->employee_name }}</div><div class="text-xs text-gray-500">{{ $employeeLetter->employee_email }}</div><div class="text-xs text-gray-500">{{ $employeeLetter->employee_phone }}</div></div>
                <div class="rounded-xl border border-gray-200 p-4"><div class="text-xs font-bold text-gray-500 uppercase">Details</div><div class="mt-2 text-sm text-gray-700 space-y-1"><div><strong>Designation:</strong> {{ $employeeLetter->designation ?: '—' }}</div><div><strong>Office:</strong> {{ $employeeLetter->office?->name ?? '—' }}</div><div><strong>Department:</strong> {{ $employeeLetter->department?->name ?? '—' }}</div><div><strong>Joining:</strong> {{ optional($employeeLetter->joining_date)->format('d-m-Y') ?: '—' }}</div><div><strong>Salary:</strong> {{ $employeeLetter->salary ? number_format($employeeLetter->salary, 2) : '—' }}</div><div><strong>Issue Date:</strong> {{ optional($employeeLetter->issue_date)->format('d-m-Y') }}</div></div></div>
                <div class="rounded-xl border border-gray-200 p-4"><div class="text-xs font-bold text-gray-500 uppercase">Status</div><div class="mt-2"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $employeeLetter->status == 'issued' ? 'bg-green-100 text-green-800' : ($employeeLetter->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($employeeLetter->status) }}</span></div><form action="{{ route('employee-letters.update', $employeeLetter) }}" method="POST" class="mt-3 flex gap-2">@csrf @method('PUT')<select name="status" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm"><option value="draft" {{ $employeeLetter->status == 'draft' ? 'selected' : '' }}>Draft</option><option value="issued" {{ $employeeLetter->status == 'issued' ? 'selected' : '' }}>Issued</option><option value="cancelled" {{ $employeeLetter->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option></select><button class="px-3 rounded-lg bg-blue-600 text-white font-semibold">Save</button></form></div>
            </div>
            <div class="lg:col-span-8"><div class="rounded-xl border border-gray-200 p-8 bg-white"><div class="mb-4 pb-4 border-b border-gray-200"><h2 class="text-lg font-bold text-gray-900">{{ $employeeLetter->rendered_subject }}</h2></div><div class="letter-content text-gray-900 leading-7">{!! $employeeLetter->rendered_html !!}</div></div></div>
        </div>
    </div>
</div>
@endsection
