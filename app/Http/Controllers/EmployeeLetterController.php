<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DocumentType;
use App\Models\EmployeeLetter;
use App\Models\LetterTemplate;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeLetterController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeLetter::with([
            'documentType',
            'template',
            'employee',
            'department',
            'office',
            'issuedBy',
        ]);

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('letter_no', 'like', "%{$search}%")
                    ->orWhere('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_email', 'like', "%{$search}%")
                    ->orWhere('employee_phone', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        $employeeLetters = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $departments   = Department::orderBy('name')->get();
        $offices       = Office::orderBy('name')->get();

        return view('employee_letters.index', compact(
            'employeeLetters',
            'documentTypes',
            'departments',
            'offices'
        ));
    }

    public function create(Request $request)
    {
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $departments   = Department::orderBy('name')->get();
        $offices       = Office::orderBy('name')->get();

        $users = User::query()
            ->when(method_exists(User::class, 'office'), fn ($q) => $q->with('office'))
            ->orderBy('name')
            ->get();

        $templates = LetterTemplate::with(['documentType', 'department', 'office'])
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('employee_letters.create', compact(
            'documentTypes',
            'departments',
            'offices',
            'users',
            'templates'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_template_id' => ['required', 'exists:letter_templates,id'],
            'user_id'            => ['nullable', 'exists:users,id'],

            'office_id'          => ['nullable', 'exists:offices,id'],
            'department_id'      => ['nullable', 'exists:departments,id'],

            'employee_name'      => ['required', 'string', 'max:255'],
            'employee_email'     => ['nullable', 'email', 'max:255'],
            'employee_phone'     => ['nullable', 'string', 'max:30'],
            'designation'        => ['nullable', 'string', 'max:255'],
            'address'            => ['nullable', 'string'],

            'joining_date'       => ['nullable', 'date'],
            'salary'             => ['nullable', 'numeric', 'min:0'],
            'issue_date'         => ['required', 'date'],

            'reporting_manager'  => ['nullable', 'string', 'max:255'],
            'monthly_target'     => ['nullable', 'string', 'max:255'],
            'working_hours'      => ['nullable', 'string', 'max:255'],
            'week_offs'          => ['nullable', 'string', 'max:255'],
            'notice_period'      => ['nullable', 'string', 'max:255'],
            'probation_period'   => ['nullable', 'string', 'max:255'],
            'authorized_signatory' => ['nullable', 'string', 'max:255'],
            'hr_name'            => ['nullable', 'string', 'max:255'],

            'status'             => ['nullable', 'in:draft,issued,cancelled'],
        ]);

        $template = LetterTemplate::with(['documentType', 'department', 'office'])
            ->where('is_active', true)
            ->findOrFail($request->letter_template_id);

        $letter = DB::transaction(function () use ($request, $template) {
            $letterNo = $this->generateLetterNo($template->documentType?->slug);

            $officeId = $request->office_id ?: $template->office_id;
            $departmentId = $request->department_id ?: $template->department_id;

            $office = $officeId ? Office::find($officeId) : null;
            $department = $departmentId ? Department::find($departmentId) : null;

            $extraData = [
                'reporting_manager'    => $request->reporting_manager,
                'monthly_target'       => $request->monthly_target,
                'working_hours'        => $request->working_hours,
                'week_offs'            => $request->week_offs,
                'notice_period'        => $request->notice_period,
                'probation_period'     => $request->probation_period,
                'authorized_signatory' => $request->authorized_signatory,
                'hr_name'              => $request->hr_name,
            ];

            $variables = [
                'company_name'      => config('app.name'),
                'office_name'       => $office?->name,
                'department_name'   => $department?->name,
                'issue_date'        => date('d-m-Y', strtotime($request->issue_date)),
                'letter_no'         => $letterNo,

                'employee_name'     => $request->employee_name,
                'employee_email'    => $request->employee_email,
                'employee_phone'    => $request->employee_phone,
                'address'           => $request->address,
                'designation'       => $request->designation,
                'joining_date'      => $request->joining_date ? date('d-m-Y', strtotime($request->joining_date)) : null,
                'salary'            => $request->salary ? number_format((float) $request->salary, 2) : null,

                'reporting_manager' => $request->reporting_manager,
                'monthly_target'    => $request->monthly_target,
                'working_hours'     => $request->working_hours,
                'week_offs'         => $request->week_offs,
                'notice_period'     => $request->notice_period,
                'probation_period'  => $request->probation_period,

                'authorized_signatory' => $request->authorized_signatory,
                'hr_name'              => $request->hr_name,
            ];

            $renderedSubject = $this->renderTemplate($template->subject, $variables);
            $renderedHtml = $this->renderTemplate($template->body_html, $variables);

            return EmployeeLetter::create([
                'letter_no'          => $letterNo,
                'document_type_id'   => $template->document_type_id,
                'letter_template_id' => $template->id,

                'office_id'          => $officeId,
                'department_id'      => $departmentId,
                'user_id'            => $request->user_id,

                'employee_name'      => $request->employee_name,
                'employee_email'     => $request->employee_email,
                'employee_phone'     => $request->employee_phone,
                'designation'        => $request->designation,
                'address'            => $request->address,

                'joining_date'       => $request->joining_date,
                'salary'             => $request->salary,
                'issue_date'         => $request->issue_date,

                'extra_data'         => array_filter($extraData, fn ($value) => $value !== null && $value !== ''),
                'rendered_subject'   => $renderedSubject,
                'rendered_html'      => $renderedHtml,

                'status'             => $request->status ?: 'issued',
                'issued_by'          => auth()->id(),
            ]);
        });

        return redirect()
            ->route('employee-letters.show', $letter)
            ->with('success', 'Letter generated successfully.');
    }

    public function show(EmployeeLetter $employeeLetter)
    {
        $employeeLetter->load([
            'documentType',
            'template',
            'employee',
            'department',
            'office',
            'issuedBy',
        ]);

        return view('employee_letters.show', compact('employeeLetter'));
    }

    public function edit(EmployeeLetter $employeeLetter)
    {
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        $departments   = Department::orderBy('name')->get();
        $offices       = Office::orderBy('name')->get();
        $users         = User::orderBy('name')->get();

        $templates = LetterTemplate::with(['documentType', 'department', 'office'])
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('employee_letters.edit', compact(
            'employeeLetter',
            'documentTypes',
            'departments',
            'offices',
            'users',
            'templates'
        ));
    }

    public function update(Request $request, EmployeeLetter $employeeLetter)
    {
        $request->validate([
            'status' => ['required', 'in:draft,issued,cancelled'],
        ]);

        $employeeLetter->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->route('employee-letters.show', $employeeLetter)
            ->with('success', 'Letter status updated successfully.');
    }

    public function destroy(EmployeeLetter $employeeLetter)
    {
        $employeeLetter->delete();

        return redirect()
            ->route('employee-letters.index')
            ->with('success', 'Letter deleted successfully.');
    }

    public function print(EmployeeLetter $employeeLetter)
    {
        $employeeLetter->load([
            'documentType',
            'template',
            'employee',
            'department',
            'office',
            'issuedBy',
        ]);

        return view('employee_letters.print', compact('employeeLetter'));
    }

    public function getTemplates(Request $request)
    {
        $request->validate([
            'document_type_id' => ['nullable', 'exists:document_types,id'],
            'department_id'    => ['nullable', 'exists:departments,id'],
            'office_id'        => ['nullable', 'exists:offices,id'],
        ]);

        $templates = LetterTemplate::query()
            ->where('is_active', true)
            ->when($request->document_type_id, fn ($q) => $q->where('document_type_id', $request->document_type_id))
            ->when($request->department_id, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('department_id', $request->department_id)
                        ->orWhereNull('department_id');
                });
            })
            ->when($request->office_id, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('office_id', $request->office_id)
                        ->orWhereNull('office_id');
                });
            })
            ->orderBy('title')
            ->get(['id', 'title', 'subject', 'document_type_id', 'department_id', 'office_id']);

        return response()->json([
            'status' => true,
            'data'   => $templates,
        ]);
    }

    public function getUserData(User $user)
    {
        $user->load(['department', 'office']);

        return response()->json([
            'status' => true,
            'data' => [
                'user_id'        => $user->id,
                'employee_name'  => $user->name,
                'employee_email' => $user->email,
                'employee_phone' => $user->phone ?? null,
                'designation'    => $user->designation ?? null,
                'address'        => $user->address ?? null,
                'office_id'      => $user->office_id ?? null,
                'department_id'  => $user->department_id ?? null,
                'salary'         => $user->salary ?? null,
                'joining_date'   => $user->joining_date ?? null,
            ],
        ]);
    }

    private function renderTemplate(?string $content, array $variables): string
    {
        $content = $content ?? '';

        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', e((string) $value), $content);
            $content = str_replace('{{ ' . $key . ' }}', e((string) $value), $content);
        }

        return $content;
    }

    private function generateLetterNo(?string $typeSlug = null): string
    {
        $prefix = 'HR';

        if ($typeSlug) {
            $prefix = strtoupper(substr(str_replace('-', '', $typeSlug), 0, 3));
        }

        $date = now()->format('Ymd');

        $count = EmployeeLetter::whereDate('created_at', today())->count() + 1;

        return $prefix . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}