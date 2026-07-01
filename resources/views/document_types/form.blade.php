@if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="text-xs font-semibold text-gray-600">Name <span class="text-red-600">*</span></label>
        <input type="text" name="name" value="{{ old('name', $documentType->name ?? '') }}" placeholder="Offer Letter" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400" required>
    </div>

    <div>
        <label class="text-xs font-semibold text-gray-600">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $documentType->slug ?? '') }}" placeholder="auto generated if blank" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
    </div>

    <div class="md:col-span-2">
        <label class="text-xs font-semibold text-gray-600">Description</label>
        <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">{{ old('description', $documentType->description ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-red-600" {{ old('is_active', $documentType->is_active ?? true) ? 'checked' : '' }}>
            <span class="text-sm font-semibold text-gray-700">Active</span>
        </label>
    </div>
</div>

<div class="mt-6 flex flex-wrap items-center gap-2">
    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-green-600 text-white font-semibold shadow hover:bg-green-700 transition">
        <span class="material-icons text-base">save</span> Save
    </button>
    <a href="{{ route('document-types.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition">
        Cancel
    </a>
</div>
