<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentType::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documentTypes = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('document_types.index', compact('documentTypes'));
    }

    public function create()
    {
        return view('document_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:document_types,slug'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        DocumentType::create([
            'name'        => $request->name,
            'slug'        => $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('document-types.index')
            ->with('success', 'Document type created successfully.');
    }

    public function edit(DocumentType $documentType)
    {
        return view('document_types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:document_types,slug,' . $documentType->id],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $documentType->update([
            'name'        => $request->name,
            'slug'        => $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('document-types.index')
            ->with('success', 'Document type updated successfully.');
    }

    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();

        return back()->with('success', 'Document type deleted successfully.');
    }
}