@extends('dashboard.layout.root')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="p-4 sm:p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Document Type</h1>
                <p class="text-sm text-gray-500 mt-1">Update document master</p>
            </div>
            <a href="{{ route('document-types.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition">
                <span class="material-icons text-base">arrow_back</span> Back
            </a>
        </div>

        <form action="{{ route('document-types.update', $documentType) }}" method="POST" class="p-4 sm:p-6">
            @csrf
            @method('PUT')
            @include('document_types.form', ['documentType' => $documentType])
        </form>
    </div>
</div>
@endsection
