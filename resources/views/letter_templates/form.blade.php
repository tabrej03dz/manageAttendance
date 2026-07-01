@if ($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $authUser = auth()->user();
    $canChooseOffice = $authUser && $authUser->hasAnyRole(['super_admin', 'owner']);

    $userOffice = null;

    if (!$canChooseOffice && $authUser && $authUser->office_id) {
        $userOffice = \App\Models\Office::find($authUser->office_id);
    }
@endphp

<link href="https://cdn.jsdelivr.net/npm/suneditor@2.47.5/dist/css/suneditor.min.css" rel="stylesheet">

<style>
    .word-wrapper {
        background: #f3f4f6;
        border-radius: 18px;
        padding: 22px;
    }

    .sun-editor {
        border-radius: 14px !important;
        overflow: visible !important;
        border: 1px solid #d1d5db !important;
    }

    .sun-editor .se-toolbar {
        background: #ffffff !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .sun-editor .se-wrapper,
    .sun-editor .se-wrapper-inner {
        height: auto !important;
        min-height: auto !important;
        overflow: visible !important;
    }

    .sun-editor-editable {
        height: auto !important;
        min-height: 950px !important;
        overflow: visible !important;
        max-width: 794px;
        margin: 24px auto !important;
        background: #ffffff !important;
        padding: 60px 65px !important;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.16);
        font-family: Arial, sans-serif;
        font-size: 14px;
        line-height: 1.75;
    }

    .variable-chip {
        transition: all .2s ease;
    }

    .variable-chip:hover {
        transform: translateY(-1px);
    }

    .copied-toast {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 999999;
        background: #111827;
        color: #ffffff;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 12px 30px rgba(0,0,0,.25);
        display: none;
    }

    body.hr-editor-fullscreen {
        overflow: hidden;
    }

    body.hr-editor-fullscreen .left-settings-panel {
        display: none !important;
    }

    body.hr-editor-fullscreen .right-editor-panel {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: #f3f4f6;
        padding: 18px;
        overflow-y: auto;
    }

    body.hr-editor-fullscreen .hr-letter-main-grid {
        display: block !important;
    }

    body.hr-editor-fullscreen .right-editor-panel > .space-y-5 {
        max-width: 100%;
    }

    body.hr-editor-fullscreen .dynamic-fields-card {
        display: block !important;
        position: relative !important;
        top: auto !important;
        z-index: auto !important;
    }

    body.hr-editor-fullscreen .dynamic-fields-card .p-5 {
        padding: 14px !important;
    }

    body.hr-editor-fullscreen .dynamic-fields-card .grid {
        grid-template-columns: repeat(10, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    body.hr-editor-fullscreen .variable-chip {
        padding: 8px 10px !important;
        border-radius: 10px !important;
    }

    body.hr-editor-fullscreen .word-wrapper {
        padding: 18px;
        border-radius: 14px;
    }

    body.hr-editor-fullscreen .sun-editor-editable {
        min-height: calc(100vh - 360px) !important;
        max-width: 900px;
        overflow: visible !important;
    }

    .fullscreen-exit-btn {
        display: none;
    }

    body.hr-editor-fullscreen .fullscreen-open-btn {
        display: none !important;
    }

    body.hr-editor-fullscreen .fullscreen-exit-btn {
        display: inline-flex !important;
    }

    @media (max-width: 1280px) {
        body.hr-editor-fullscreen .dynamic-fields-card .grid {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 768px) {
        body.hr-editor-fullscreen .dynamic-fields-card .grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        body.hr-editor-fullscreen .sun-editor-editable {
            min-height: calc(100vh - 430px) !important;
            padding: 35px 28px !important;
        }
    }
</style>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6 hr-letter-main-grid">

    {{-- LEFT PANEL --}}
    <div class="xl:col-span-3 space-y-5 left-settings-panel">
        <div class="space-y-5">

            {{-- Template Settings --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-icons text-red-600 text-lg">settings</span>
                        Template Settings
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Letter ka type aur department select karein.</p>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">
                            Document Type <span class="text-red-600">*</span>
                        </label>
                        <select name="document_type_id"
                                class="mt-1 w-full h-11 rounded-xl border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"
                                required>
                            <option value="">Select Type</option>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ old('document_type_id', optional($letterTemplate)->document_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($canChooseOffice)
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Office</label>
                            <select name="office_id"
                                    class="mt-1 w-full h-11 rounded-xl border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                                <option value="">Select Office</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}"
                                        {{ old('office_id', optional($letterTemplate)->office_id) == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="office_id" value="{{ $authUser?->office_id }}">

                        <div>
                            <label class="text-xs font-semibold text-gray-600">Office</label>
                            <div class="mt-1 w-full min-h-11 rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-700">
                                {{ $userOffice?->name ?? 'Assigned Office Not Found' }}
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Office automatically aapke user account se save hoga.
                            </p>
                        </div>
                    @endif

                    <div>
                        <label class="text-xs font-semibold text-gray-600">Department</label>
                        <select name="department_id"
                                class="mt-1 w-full h-11 rounded-xl border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ old('department_id', optional($letterTemplate)->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600">
                            Template Title <span class="text-red-600">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               value="{{ old('title', optional($letterTemplate)->title) }}"
                               placeholder="Sales Offer Letter"
                               class="mt-1 w-full h-11 rounded-xl border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"
                               required>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600">Subject</label>
                        <input type="text"
                               name="subject"
                               value="{{ old('subject', optional($letterTemplate)->subject) }}"
                               placeholder="Offer of Employment - @{{designation}}"
                               class="mt-1 w-full h-11 rounded-xl border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
                    </div>

                    <label class="inline-flex items-center gap-2 pt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               class="rounded border-gray-300 text-red-600"
                               {{ old('is_active', optional($letterTemplate)->is_active ?? true) ? 'checked' : '' }}>
                        <span class="text-sm font-semibold text-gray-700">Active Template</span>
                    </label>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <div class="space-y-3">
                    <button type="button"
                            onclick="insertDefaultOfferLetter()"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition">
                        <span class="material-icons text-base">description</span>
                        Use Offer Letter Format
                    </button>

                    <button type="submit"
                            onclick="saveEditorBeforeSubmit()"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl bg-green-600 text-white text-sm font-bold shadow hover:bg-green-700 transition">
                        <span class="material-icons text-base">save</span>
                        Save Template
                    </button>

                    <a href="{{ route('letter-templates.index') }}"
                       class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-bold hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="xl:col-span-9 space-y-5 right-editor-panel">
        <div class="space-y-5">

            {{-- Dynamic Fields --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm dynamic-fields-card">
                <div class="px-5 py-4 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-icons text-red-600 text-lg">dynamic_form</span>
                            Dynamic Fields
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Field click karne par editor me insert bhi hoga aur copy bhi ho jayega.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-50 text-blue-800 border border-blue-200 text-xs font-semibold">
                        <span class="material-icons text-base">content_copy</span>
                        Insert + Copy
                    </div>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2">
                        @foreach($availableVariables as $variable)
                            @php
                                $variableText = '{{' . $variable . '}}';

                                $labels = [
                                    'company_name' => 'Company',
                                    'office_name' => 'Office',
                                    'department_name' => 'Department',
                                    'issue_date' => 'Issue Date',
                                    'letter_no' => 'Letter No',
                                    'employee_name' => 'Employee Name',
                                    'employee_email' => 'Email',
                                    'employee_phone' => 'Phone',
                                    'address' => 'Address',
                                    'designation' => 'Designation',
                                    'joining_date' => 'Joining Date',
                                    'salary' => 'Salary',
                                    'reporting_manager' => 'Reporting Manager',
                                    'monthly_target' => 'Monthly Target',
                                    'working_hours' => 'Working Hours',
                                    'week_offs' => 'Week Offs',
                                    'notice_period' => 'Notice Period',
                                    'probation_period' => 'Probation',
                                    'authorized_signatory' => 'Signatory',
                                    'hr_name' => 'HR Name',
                                ];

                                $label = $labels[$variable] ?? ucwords(str_replace('_', ' ', $variable));
                            @endphp

                            <button type="button"
                                    onclick="handleVariableClick(@js($variableText))"
                                    class="variable-chip text-left rounded-xl border border-gray-200 bg-gray-50 hover:bg-red-50 hover:border-red-200 px-3 py-2">
                                <span class="block text-xs font-bold text-gray-800">{{ $label }}</span>
                                <span class="block text-[11px] text-gray-400 truncate">{{ $variableText }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Word Editor --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-icons text-red-600 text-lg">article</span>
                            MS Word Style Letter Editor
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Word ki tarah type karein, format karein, table banayein aur content paste karein.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                                onclick="toggleEditorFullScreen()"
                                class="fullscreen-open-btn inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-indigo-50 text-indigo-800 border border-indigo-200 text-xs font-bold hover:bg-indigo-100 transition">
                            <span class="material-icons text-base">fullscreen</span>
                            Editor Full Screen
                        </button>

                        <button type="button"
                                onclick="toggleEditorFullScreen()"
                                class="fullscreen-exit-btn items-center gap-2 px-3 py-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold hover:bg-amber-100 transition">
                            <span class="material-icons text-base">fullscreen_exit</span>
                            Exit
                        </button>

                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
                            <span class="material-icons text-base">print</span>
                            A4 page preview
                        </div>
                    </div>
                </div>

                <div class="word-wrapper">
                    <textarea id="body_html"
                              name="body_html"
                              required>{{ old('body_html', optional($letterTemplate)->body_html) }}</textarea>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="copiedToast" class="copied-toast"></div>

<script src="https://cdn.jsdelivr.net/npm/suneditor@2.47.5/dist/suneditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/suneditor@2.47.5/src/lang/en.js"></script>

<script>
    let letterEditor = null;

    document.addEventListener('DOMContentLoaded', function () {
        letterEditor = SUNEDITOR.create('body_html', {
            lang: SUNEDITOR_LANG['en'],
            height: 'auto',
            width: '100%',
            resizingBar: false,
            showPathLabel: false,
            charCounter: false,
            stickyToolbar: 0,
            font: [
                'Arial',
                'Calibri',
                'Times New Roman',
                'Georgia',
                'Verdana',
                'Tahoma'
            ],
            fontSize: [
                10, 11, 12, 14, 16, 18, 20, 22, 24, 28, 32, 36
            ],
            formats: [
                'p',
                'blockquote',
                'h1',
                'h2',
                'h3',
                'h4'
            ],
            buttonList: [
                ['undo', 'redo'],
                ['font', 'fontSize', 'formatBlock'],
                ['bold', 'underline', 'italic', 'strike'],
                ['fontColor', 'hiliteColor'],
                ['removeFormat'],
                ['outdent', 'indent'],
                ['align', 'horizontalRule', 'list', 'lineHeight'],
                ['table', 'link'],
                ['showBlocks', 'codeView']
            ]
        });

        setTimeout(function () {
            makeEditorAutoHeight();
        }, 500);
    });

    function makeEditorAutoHeight() {
        const editable = document.querySelector('.sun-editor-editable');
        const wrapper = document.querySelector('.sun-editor .se-wrapper');
        const wrapperInner = document.querySelector('.sun-editor .se-wrapper-inner');

        if (editable) {
            editable.style.height = 'auto';
            editable.style.overflow = 'visible';

            editable.style.minHeight = document.body.classList.contains('hr-editor-fullscreen')
                ? 'calc(100vh - 360px)'
                : '950px';
        }

        if (wrapper) {
            wrapper.style.height = 'auto';
            wrapper.style.overflow = 'visible';
        }

        if (wrapperInner) {
            wrapperInner.style.height = 'auto';
            wrapperInner.style.overflow = 'visible';
        }
    }

    function saveEditorBeforeSubmit() {
        if (letterEditor) {
            letterEditor.save();
        }
    }

    document.addEventListener('submit', function () {
        saveEditorBeforeSubmit();
    });

    function handleVariableClick(text) {
        insertVariable(text);
        copyVariable(text);
    }

    function insertVariable(text) {
        if (!letterEditor) {
            return;
        }

        letterEditor.insertHTML(text);
        letterEditor.core.focus();

        setTimeout(makeEditorAutoHeight, 100);
    }

    function copyVariable(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function () {
                showCopiedToast(text + ' copied and inserted');
            }).catch(function () {
                fallbackCopyVariable(text);
            });
        } else {
            fallbackCopyVariable(text);
        }
    }

    function fallbackCopyVariable(text) {
        const input = document.createElement('textarea');
        input.value = text;
        input.style.position = 'fixed';
        input.style.left = '-9999px';
        input.style.top = '-9999px';

        document.body.appendChild(input);
        input.focus();
        input.select();

        try {
            document.execCommand('copy');
            showCopiedToast(text + ' copied and inserted');
        } catch (e) {
            showCopiedToast(text + ' inserted');
        }

        document.body.removeChild(input);
    }

    function showCopiedToast(message) {
        const toast = document.getElementById('copiedToast');

        if (!toast) {
            return;
        }

        toast.innerText = message;
        toast.style.display = 'block';

        clearTimeout(window.__copyToastTimer);

        window.__copyToastTimer = setTimeout(function () {
            toast.style.display = 'none';
        }, 1600);
    }

    function toggleEditorFullScreen() {
        document.body.classList.toggle('hr-editor-fullscreen');

        setTimeout(function () {
            makeEditorAutoHeight();

            if (letterEditor) {
                letterEditor.core.focus();
            }
        }, 200);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && document.body.classList.contains('hr-editor-fullscreen')) {
            document.body.classList.remove('hr-editor-fullscreen');
            setTimeout(makeEditorAutoHeight, 150);
        }
    });

    function insertDefaultOfferLetter() {
        if (!letterEditor) {
            return;
        }

        if (!confirm('Current content replace karke default offer letter format add karna hai?')) {
            return;
        }

        const defaultHtml = `
            <p style="text-align:right;"><strong>Date:</strong> @{{issue_date}}</p>

            <p>
                <strong>To,</strong><br>
                <strong>Name:</strong> @{{employee_name}}<br>
                <strong>Address:</strong> @{{address}}<br>
                <strong>Email:</strong> @{{employee_email}}<br>
                <strong>Phone:</strong> @{{employee_phone}}
            </p>

            <p><strong>Subject:</strong> Offer of Employment - @{{designation}}</p>

            <p>Dear @{{employee_name}},</p>

            <p>
                Congratulations! We are pleased to offer you the position of
                <strong>@{{designation}}</strong> at <strong>@{{company_name}}</strong>.
                We believe your skills and experience will be valuable for our organization.
            </p>

            <h3>1. Appointment & Reporting</h3>
            <p>
                You will be appointed as <strong>@{{designation}}</strong> in the
                <strong>@{{department_name}}</strong> department.
                You will report to <strong>@{{reporting_manager}}</strong>.
            </p>

            <h3>2. Compensation</h3>
            <p>
                Your salary/compensation will be <strong>INR @{{salary}}</strong>.
                Monthly target, if applicable, will be <strong>@{{monthly_target}}</strong>.
            </p>

            <h3>3. Joining & Working Hours</h3>
            <p>
                Your joining date will be <strong>@{{joining_date}}</strong>.<br>
                Working hours: <strong>@{{working_hours}}</strong><br>
                Week offs: <strong>@{{week_offs}}</strong>
            </p>

            <h3>4. Notice Period</h3>
            <p>
                Your notice period will be <strong>@{{notice_period}}</strong>.
            </p>

            <h3>5. Confidentiality & Company Policy</h3>
            <p>
                You are required to maintain confidentiality of company data, client information,
                business process, pricing, and internal documents. You must follow all company
                rules, policies, and code of conduct.
            </p>

            <h3>6. Acceptance</h3>
            <p>
                Kindly sign and return a copy of this letter as a token of your acceptance.
            </p>

            <br>

            <table style="width:100%; border:0;">
                <tr>
                    <td style="width:50%; vertical-align:top;">
                        <p>
                            <strong>Accepted By</strong><br><br>
                            Signature: ___________________<br>
                            Name: @{{employee_name}}<br>
                            Date: ___________________
                        </p>
                    </td>
                    <td style="width:50%; text-align:right; vertical-align:top;">
                        <p>
                            <strong>For @{{company_name}}</strong><br><br>
                            Signature: ___________________<br>
                            Authorized Signatory<br>
                            @{{authorized_signatory}}
                        </p>
                    </td>
                </tr>
            </table>
        `;

        letterEditor.setContents(defaultHtml);
        letterEditor.core.focus();

        setTimeout(makeEditorAutoHeight, 100);
    }
</script>