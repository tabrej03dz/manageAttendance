<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DocumentType;
use App\Models\LetterTemplate;
use App\Models\Office;
use Illuminate\Http\Request;

class LetterTemplateController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = LetterTemplate::with(['documentType', 'department', 'office', 'creator']);

        // Office access filter
        if ($this->canChooseOffice()) {
            if ($request->filled('office_id')) {
                $query->where('office_id', $request->office_id);
            }
        } else {
            $query->where('office_id', $user->office_id);
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('body_html', 'like', "%{$search}%");
            });
        }

        $letterTemplates = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $departments   = Department::orderBy('name')->get();
        $offices       = $this->getOfficeListForUser();

        return view('letter_templates.index', compact(
            'letterTemplates',
            'documentTypes',
            'departments',
            'offices'
        ));
    }

    public function create()
    {
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $departments   = Department::orderBy('name')->get();
        $offices       = $this->getOfficeListForUser();

        $availableVariables = $this->availableVariables();

        return view('letter_templates.create', compact(
            'documentTypes',
            'departments',
            'offices',
            'availableVariables'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type_id' => ['required', 'exists:document_types,id'],
            'office_id'        => ['nullable', 'exists:offices,id'],
            'department_id'    => ['nullable', 'exists:departments,id'],
            'title'            => ['required', 'string', 'max:255'],
            'subject'          => ['nullable', 'string', 'max:255'],
            'body_html'        => ['required', 'string'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $officeId = $this->resolveOfficeId($request);

        if (!$officeId) {
            return back()
                ->withInput()
                ->withErrors([
                    'office_id' => 'Office not found for this user.',
                ]);
        }

        LetterTemplate::create([
            'document_type_id' => $request->document_type_id,
            'office_id'        => $officeId,
            'department_id'    => $request->department_id,
            'title'            => $request->title,
            'subject'          => $request->subject,
            'body_html'        => $request->body_html,
            'variables'        => $this->extractVariables($request->body_html . ' ' . $request->subject),
            'is_active'        => $request->boolean('is_active', true),
            'created_by'       => auth()->id(),
        ]);

        return redirect()
            ->route('letter-templates.index')
            ->with('success', 'Letter template created successfully.');
    }

    public function show(LetterTemplate $letterTemplate)
    {
        $this->authorizeOfficeAccess($letterTemplate);

        $letterTemplate->load(['documentType', 'department', 'office', 'creator']);

        return view('letter_templates.show', compact('letterTemplate'));
    }

    public function edit(LetterTemplate $letterTemplate)
    {
        $this->authorizeOfficeAccess($letterTemplate);

        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $departments   = Department::orderBy('name')->get();
        $offices       = $this->getOfficeListForUser();

        $availableVariables = $this->availableVariables();

        return view('letter_templates.edit', compact(
            'letterTemplate',
            'documentTypes',
            'departments',
            'offices',
            'availableVariables'
        ));
    }

    public function update(Request $request, LetterTemplate $letterTemplate)
    {
        $this->authorizeOfficeAccess($letterTemplate);

        $request->validate([
            'document_type_id' => ['required', 'exists:document_types,id'],
            'office_id'        => ['nullable', 'exists:offices,id'],
            'department_id'    => ['nullable', 'exists:departments,id'],
            'title'            => ['required', 'string', 'max:255'],
            'subject'          => ['nullable', 'string', 'max:255'],
            'body_html'        => ['required', 'string'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $officeId = $this->resolveOfficeId($request, $letterTemplate);

        if (!$officeId) {
            return back()
                ->withInput()
                ->withErrors([
                    'office_id' => 'Office not found for this user.',
                ]);
        }

        $letterTemplate->update([
            'document_type_id' => $request->document_type_id,
            'office_id'        => $officeId,
            'department_id'    => $request->department_id,
            'title'            => $request->title,
            'subject'          => $request->subject,
            'body_html'        => $request->body_html,
            'variables'        => $this->extractVariables($request->body_html . ' ' . $request->subject),
            'is_active'        => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('letter-templates.index')
            ->with('success', 'Letter template updated successfully.');
    }

    public function destroy(LetterTemplate $letterTemplate)
    {
        $this->authorizeOfficeAccess($letterTemplate);

        $letterTemplate->delete();

        return back()->with('success', 'Letter template deleted successfully.');
    }

    private function extractVariables(?string $content): array
    {
        preg_match_all('/{{\s*([a-zA-Z0-9_]+)\s*}}/', $content ?? '', $matches);

        return collect($matches[1] ?? [])
            ->unique()
            ->values()
            ->toArray();
    }

    private function availableVariables(): array
    {
        return [
            'company_name',
            'office_name',
            'department_name',
            'issue_date',
            'letter_no',

            'employee_name',
            'employee_email',
            'employee_phone',
            'address',
            'designation',
            'joining_date',
            'salary',

            'reporting_manager',
            'monthly_target',
            'working_hours',
            'week_offs',
            'notice_period',
            'probation_period',

            'authorized_signatory',
            'hr_name',
        ];
    }

    private function canChooseOffice(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'owner']);
    }

    private function getOfficeListForUser()
    {
        $user = auth()->user();

        if (!$user) {
            return collect();
        }

        if ($user->hasRole('super_admin')) {
            return Office::orderBy('name')->get();
        }

        if ($user->hasRole('owner')) {
            return Office::where('owner_id', $user->id)
                ->orderBy('name')
                ->get();
        }

        return collect();
    }

    private function resolveOfficeId(Request $request, ?LetterTemplate $letterTemplate = null): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($user->hasRole('super_admin')) {
            return $request->office_id
                ?: session('active_office_id')
                ?: $letterTemplate?->office_id
                ?: $user->office_id;
        }

        if ($user->hasRole('owner')) {
            $officeId = $request->office_id
                ?: session('active_office_id')
                ?: $letterTemplate?->office_id
                ?: $user->office_id;

            if (!$officeId) {
                return null;
            }

            $isOwnOffice = Office::where('id', $officeId)
                ->where('owner_id', $user->id)
                ->exists();

            if (!$isOwnOffice) {
                abort(403, 'You are not allowed to use this office.');
            }

            return (int) $officeId;
        }

        return $user->office_id ? (int) $user->office_id : null;
    }

    private function authorizeOfficeAccess(LetterTemplate $letterTemplate): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->hasRole('super_admin')) {
            return;
        }

        if ($user->hasRole('owner')) {
            $allowed = Office::where('id', $letterTemplate->office_id)
                ->where('owner_id', $user->id)
                ->exists();

            if (!$allowed) {
                abort(403, 'You are not allowed to access this template.');
            }

            return;
        }

        if ((int) $letterTemplate->office_id !== (int) $user->office_id) {
            abort(403, 'You are not allowed to access this template.');
        }
    }
}