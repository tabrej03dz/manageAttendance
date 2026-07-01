@if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-12 gap-4">
    <div class="md:col-span-4">
        <label class="text-xs font-semibold text-gray-600">Document Type</label>
        <select id="document_type_id" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
            <option value="">Filter by type</option>
            @foreach($documentTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-4">
        <label class="text-xs font-semibold text-gray-600">Office</label>
        <select name="office_id" id="office_id" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
            <option value="">Select Office</option>
            @foreach($offices as $office)<option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>@endforeach
        </select>
    </div>
    <div class="md:col-span-4">
        <label class="text-xs font-semibold text-gray-600">Department</label>
        <select name="department_id" id="department_id" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
            <option value="">Select Department</option>
            @foreach($departments as $department)<option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach
        </select>
    </div>

    <div class="md:col-span-6">
        <label class="text-xs font-semibold text-gray-600">Template <span class="text-red-600">*</span></label>
        <select name="letter_template_id" id="letter_template_id" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400" required>
            <option value="">Select Template</option>
            @foreach($templates as $template)
                <option value="{{ $template->id }}" data-type="{{ $template->document_type_id }}" data-office="{{ $template->office_id }}" data-department="{{ $template->department_id }}" {{ old('letter_template_id') == $template->id ? 'selected' : '' }}>
                    {{ $template->title }} - {{ $template->documentType?->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-6">
        <label class="text-xs font-semibold text-gray-600">Existing Employee</label>
        <select name="user_id" id="user_id" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
            <option value="">Manual Candidate / Select Employee</option>
            @foreach($users as $user)<option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} {{ $user->phone ? ' - '.$user->phone : '' }}</option>@endforeach
        </select>
    </div>

    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Name <span class="text-red-600">*</span></label><input type="text" name="employee_name" id="employee_name" value="{{ old('employee_name') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400" required></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Email</label><input type="email" name="employee_email" id="employee_email" value="{{ old('employee_email') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Phone</label><input type="text" name="employee_phone" id="employee_phone" value="{{ old('employee_phone') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>

    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Designation</label><input type="text" name="designation" id="designation" value="{{ old('designation') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Joining Date</label><input type="date" name="joining_date" id="joining_date" value="{{ old('joining_date') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Salary</label><input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>

    <div class="md:col-span-8"><label class="text-xs font-semibold text-gray-600">Address</label><textarea name="address" id="address" rows="3" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">{{ old('address') }}</textarea></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Issue Date <span class="text-red-600">*</span></label><input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400" required><label class="text-xs font-semibold text-gray-600 mt-3 block">Status</label><select name="status" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"><option value="issued" {{ old('status') == 'issued' ? 'selected' : '' }}>Issued</option><option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option><option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option></select></div>

    <div class="md:col-span-12 border-t border-gray-200 pt-4 mt-2"><h3 class="text-base font-bold text-gray-900">Extra Dynamic Details</h3></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Reporting Manager</label><input type="text" name="reporting_manager" value="{{ old('reporting_manager') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Monthly Target</label><input type="text" name="monthly_target" value="{{ old('monthly_target') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">Working Hours</label><input type="text" name="working_hours" value="{{ old('working_hours') }}" placeholder="10:00 AM to 7:00 PM" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-3"><label class="text-xs font-semibold text-gray-600">Week Offs</label><input type="text" name="week_offs" value="{{ old('week_offs') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-3"><label class="text-xs font-semibold text-gray-600">Notice Period</label><input type="text" name="notice_period" value="{{ old('notice_period') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-3"><label class="text-xs font-semibold text-gray-600">Probation Period</label><input type="text" name="probation_period" value="{{ old('probation_period') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-3"><label class="text-xs font-semibold text-gray-600">Authorized Signatory</label><input type="text" name="authorized_signatory" value="{{ old('authorized_signatory') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
    <div class="md:col-span-4"><label class="text-xs font-semibold text-gray-600">HR Name</label><input type="text" name="hr_name" value="{{ old('hr_name') }}" class="mt-1 w-full h-11 rounded-lg border border-gray-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400"></div>
</div>

<div class="mt-6 flex flex-wrap items-center gap-2"><button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-green-600 text-white font-semibold shadow hover:bg-green-700 transition"><span class="material-icons text-base">save</span> Generate & Save</button><a href="{{ route('employee-letters.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition">Cancel</a></div>

<script>
document.getElementById('user_id').addEventListener('change', function () {
    let userId = this.value;
    if (!userId) return;
    fetch("{{ url('employee-letters/user-data') }}/" + userId)
        .then(response => response.json())
        .then(res => {
            if (!res.status) return;
            const d = res.data;
            document.getElementById('employee_name').value = d.employee_name || '';
            document.getElementById('employee_email').value = d.employee_email || '';
            document.getElementById('employee_phone').value = d.employee_phone || '';
            document.getElementById('designation').value = d.designation || '';
            document.getElementById('address').value = d.address || '';
            document.getElementById('salary').value = d.salary || '';
            document.getElementById('joining_date').value = d.joining_date || '';
            if (d.office_id) document.getElementById('office_id').value = d.office_id;
            if (d.department_id) document.getElementById('department_id').value = d.department_id;
        });
});

function filterTemplates() {
    let typeId = document.getElementById('document_type_id').value;
    let officeId = document.getElementById('office_id').value;
    let departmentId = document.getElementById('department_id').value;
    let options = document.querySelectorAll('#letter_template_id option');
    options.forEach(function(option) {
        if (!option.value) return;
        let matchType = !typeId || option.dataset.type === typeId;
        let matchOffice = !officeId || !option.dataset.office || option.dataset.office === officeId;
        let matchDepartment = !departmentId || !option.dataset.department || option.dataset.department === departmentId;
        option.hidden = !(matchType && matchOffice && matchDepartment);
    });
}
['document_type_id','office_id','department_id'].forEach(id => document.getElementById(id).addEventListener('change', filterTemplates));
</script>
