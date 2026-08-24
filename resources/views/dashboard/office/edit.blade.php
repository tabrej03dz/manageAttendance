@extends('dashboard.layout.root')

@section('title', 'Edit Office')

@push('styles')
<style>
    .office-create-page{font-family:Arial,Helvetica,sans-serif;color:#333;font-size:12px}
    .office-summary,.form-panel{background:#fff;border:1px solid #bfc4c9;box-shadow:0 1px 4px rgba(0,0,0,.20)}
    .office-summary{padding:10px 14px;margin-bottom:12px}
    .summary-grid{display:grid;grid-template-columns:130px minmax(0,1fr);gap:18px;align-items:center}
    .office-logo-preview{display:flex;width:112px;height:112px;align-items:center;justify-content:center;border:1px solid #c7ccd1;border-radius:26px;background:#e7ebee;color:#334155;font-size:35px;font-weight:700;object-fit:cover;box-shadow:0 4px 12px rgba(0,0,0,.16)}
    .summary-title{margin:0 0 10px;color:#4b4b4b;font-size:22px;font-weight:700;line-height:1.1;text-transform:uppercase}
    .summary-fields{display:grid;grid-template-columns:repeat(4,minmax(130px,1fr));gap:8px 24px}
    .summary-label{display:block;margin-bottom:2px;color:#555;font-size:10px;font-weight:700;text-transform:uppercase}
    .summary-value{color:#777;font-size:11px;line-height:1.25}
    .completion-text{margin-top:9px;color:#d90000;font-size:12px;font-weight:700;text-transform:uppercase}
    .page-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;align-items:start}
    .form-panel{overflow:hidden;margin-bottom:10px}.form-panel.full-width{grid-column:1/-1}
    .panel-header{display:flex;align-items:center;justify-content:space-between;min-height:35px;padding:5px 12px;border-bottom:1px solid #d1d1d1;background:linear-gradient(#fff,#f4f4f4)}
    .panel-title{margin:0;color:#111;font-size:16px;font-weight:700}.panel-edit{color:#111;font-size:11px;font-weight:700}
    .panel-body{padding:10px 12px 12px}
    .compact-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:18px;row-gap:7px}.compact-grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}
    .field-row{display:grid;grid-template-columns:120px minmax(0,1fr);gap:8px;align-items:center;min-width:0}.field-row.top{align-items:start}.field-row.full{grid-column:1/-1}
    .field-label{color:#555;font-size:11px;font-weight:700;line-height:1.2}.required{color:#d00000}
    .form-control-compact{width:100%;height:30px;min-width:0;padding:3px 8px;border:1px solid #cfd3d7!important;border-radius:0!important;background:#fff!important;color:#444!important;font-size:11px!important;outline:none;box-shadow:none!important}
    textarea.form-control-compact{height:72px;padding-top:7px;resize:vertical}input[type=file].form-control-compact{height:auto;min-height:31px;padding:4px}
    .form-control-compact:focus{border-color:#7c9ab7!important;box-shadow:0 0 0 1px #7c9ab7!important}.has-error{border-color:#dc2626!important;background:#fff5f5!important}
    .field-error{grid-column:2;margin-top:-3px;color:#dc2626;font-size:10px;font-weight:700}.field-help{grid-column:2;margin-top:-3px;color:#777;font-size:10px;line-height:1.3}
    .setting-box{height:100%;border:1px solid #d9dde2;background:#fafafa;padding:10px 12px}.setting-title{display:flex;align-items:center;gap:8px;margin-bottom:4px;color:#222;font-size:13px;font-weight:700}.setting-description{margin:0 0 9px;color:#777;font-size:10px}
    .radio-group{display:flex;gap:18px;align-items:center;min-height:28px}.radio-label{display:inline-flex;align-items:center;gap:5px;color:#444;font-size:11px;font-weight:600;cursor:pointer}.radio-label input{accent-color:#3f78ad}
    .error-summary{margin-bottom:10px;padding:10px 13px;border:1px solid #e6a3a3;background:#fff0f0;color:#9b1515;font-size:12px}
    .form-actions{display:flex;justify-content:flex-end;gap:8px;padding:10px 12px;border-top:1px solid #d3d3d3;background:#f7f7f7}
    .btn-compact{display:inline-flex;min-width:118px;height:32px;align-items:center;justify-content:center;gap:6px;padding:0 14px;border:1px solid transparent;border-radius:2px;font-size:12px;font-weight:700;text-decoration:none!important;cursor:pointer}
    .btn-save{border-color:#315f8c;background:#3f78ad;color:#fff!important}.btn-save:hover{background:#315f8c;color:#fff!important}.btn-cancel{border-color:#aaa;background:#fff;color:#333!important}.btn-cancel:hover{background:#eee;color:#111!important}
    @media(max-width:1100px){.summary-fields,.compact-grid.three{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:800px){.summary-grid,.page-form-grid,.summary-fields,.compact-grid,.compact-grid.three{grid-template-columns:1fr}.form-panel.full-width{grid-column:auto}.summary-grid{text-align:center}.office-logo-preview{margin:auto}}
    @media(max-width:560px){.field-row{grid-template-columns:1fr;gap:4px}.field-error,.field-help{grid-column:1}.form-actions{flex-direction:column-reverse}.btn-compact{width:100%}}
</style>
@endpush

@section('content')
<div class="office-create-page">
    <section class="office-summary">
        <div class="summary-grid">
            <div>
                <img id="officeLogoPreview" class="office-logo-preview" src="{{ $office->logo ? asset('storage/'.$office->logo) : 'https://ui-avatars.com/api/?name='.urlencode($office->name).'&background=d8e0e6&color=333&size=200' }}" alt="{{ $office->name }}">
            </div>
            <div>
                <h1 class="summary-title" id="officeNamePreview">{{ strtoupper($office->name) }}</h1>
                <div class="summary-fields">
                    <div><span class="summary-label">Employee Prefix</span><div class="summary-value" id="prefixPreview">{{ $office->employee_prefix ?: 'Not Entered' }}</div></div>
                    <div><span class="summary-label">Next Employee ID</span><div class="summary-value" id="employeeIdPreview">{{ $office->employee_prefix ? strtoupper($office->employee_prefix).'-'.str_pad(((int)$office->employee_sequence)+1,4,'0',STR_PAD_LEFT) : 'Not Available' }}</div></div>
                    <div><span class="summary-label">Owner</span><div class="summary-value" id="ownerPreview">{{ $office->owner?->name ?? auth()->user()->name }}</div></div>
                    <div><span class="summary-label">Employees Limit</span><div class="summary-value" id="employeeLimitPreview">{{ $office->number_of_employees ?: 'Not Entered' }}</div></div>
                    <div><span class="summary-label">Latitude</span><div class="summary-value" id="latitudePreview">{{ $office->latitude ?: 'Not Entered' }}</div></div>
                    <div><span class="summary-label">Longitude</span><div class="summary-value" id="longitudePreview">{{ $office->longitude ?: 'Not Entered' }}</div></div>
                    <div><span class="summary-label">Radius</span><div class="summary-value" id="radiusPreview">{{ $office->radius ? $office->radius.' Metres' : 'Not Entered' }}</div></div>
                    <div><span class="summary-label">Radius Check</span><div class="summary-value" id="radiusRulePreview">{{ in_array(strtolower((string)$office->under_radius_required), ['1','yes','true'], true) ? 'Enabled' : 'Disabled' }}</div></div>
                    <div><span class="summary-label">OTP Login</span><div class="summary-value" id="otpPreview">{{ (int)($office->otp_enable ?? 0) === 1 ? 'Enabled' : 'Disabled' }}</div></div>
                </div>
                <div class="completion-text">Review all required fields before updating the office</div>
            </div>
        </div>
    </section>

    @if($errors->any())
        <div class="error-summary">
            <strong>Please correct the following errors:</strong>
            <ul style="margin:6px 0 0 18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('office.update', ['office' => $office->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="page-form-grid">
            <section class="form-panel full-width">
                <div class="panel-header"><h2 class="panel-title">Basic Office Information</h2><span class="panel-edit"><i class="fas fa-building"></i> Add</span></div>
                <div class="panel-body">
                    <div class="compact-grid three">
                        <div class="field-row">
                            <label class="field-label" for="name">Office Name <span class="required">*</span></label>
                            <input class="form-control-compact @error('name') has-error @enderror" id="name" name="name" type="text" value="{{ old('name', $office->name) }}" placeholder="Enter office name" required>
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="employee_prefix">Employee Prefix <span class="required">*</span></label>
                            <input class="form-control-compact @error('employee_prefix') has-error @enderror" id="employee_prefix" name="employee_prefix" type="text" value="{{ old('employee_prefix', $office->employee_prefix) }}" maxlength="20" pattern="[A-Za-z0-9]+" placeholder="Example: MO" autocomplete="off" required>
                            @error('employee_prefix')<div class="field-error">{{ $message }}</div>@enderror
                            <div class="field-help">Letters and numbers only. Employee ID example: MO-0001</div>
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="number_of_employees">Employee Limit</label>
                            <input class="form-control-compact @error('number_of_employees') has-error @enderror" id="number_of_employees" name="number_of_employees" type="number" min="0" value="{{ old('number_of_employees', $office->number_of_employees) }}" placeholder="Example: 25">
                            @error('number_of_employees')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="logo">Office Logo</label>
                            <input class="form-control-compact @error('logo') has-error @enderror" id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp">
                            @error('logo')<div class="field-error">{{ $message }}</div>@enderror
                            <div class="field-help">JPG, PNG or WEBP; maximum 2 MB.</div>
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="price_per_employee">Price Per Employee</label>
                            <input class="form-control-compact @error('price_per_employee') has-error @enderror" id="price_per_employee" name="price_per_employee" type="number" min="0" step="0.01" value="{{ old('price_per_employee', $office->price_per_employee) }}" placeholder="Example: 100">
                            @error('price_per_employee')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        @if($owners)
                            <div class="field-row">
                                <label class="field-label" for="owner_id">Owner</label>
                                <select class="form-control-compact @error('owner_id') has-error @enderror" id="owner_id" name="owner_id">
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" {{ old('owner_id', $office->owner_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                                @error('owner_id')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        @else
                            <input type="hidden" id="owner_id" name="owner_id" value="{{ $office->owner_id }}">
                        @endif
                    </div>
                </div>
            </section>

            <section class="form-panel full-width">
                <div class="panel-header"><h2 class="panel-title">Location Details</h2><span class="panel-edit"><i class="fas fa-map-marker-alt"></i> Add</span></div>
                <div class="panel-body">
                    <div class="compact-grid three">
                        <div class="field-row">
                            <label class="field-label" for="latitude">Latitude <span class="required">*</span></label>
                            <input class="form-control-compact @error('latitude') has-error @enderror" id="latitude" name="latitude" type="number" step="any" value="{{ old('latitude', $office->latitude) }}" placeholder="Example: 26.4499" required>
                            @error('latitude')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="longitude">Longitude <span class="required">*</span></label>
                            <input class="form-control-compact @error('longitude') has-error @enderror" id="longitude" name="longitude" type="number" step="any" value="{{ old('longitude', $office->longitude) }}" placeholder="Example: 80.3319" required>
                            @error('longitude')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="radius">Radius (Metres)</label>
                            <input class="form-control-compact @error('radius') has-error @enderror" id="radius" name="radius" type="number" min="0" step="any" value="{{ old('radius', $office->radius) }}" placeholder="Example: 100">
                            @error('radius')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field-row full top">
                            <label class="field-label" for="address">Full Address</label>
                            <textarea class="form-control-compact @error('address') has-error @enderror" id="address" name="address" placeholder="Enter full office address">{{ old('address', $office->address) }}</textarea>
                            @error('address')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header"><h2 class="panel-title">Location Verification</h2><span class="panel-edit"><i class="fas fa-location-crosshairs"></i> Setting</span></div>
                <div class="panel-body">
                    <div class="setting-box">
                        <div class="setting-title"><i class="fas fa-map-pin"></i>Attendance Radius Rule</div>
                        <p class="setting-description">Require employees to remain inside the office radius while marking attendance.</p>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="under_radius_required" value="1" {{ in_array(strtolower((string)old('under_radius_required', $office->under_radius_required)), ['1','yes','true'], true) ? 'checked' : '' }}> Enable</label>
                            <label class="radio-label"><input type="radio" name="under_radius_required" value="0" {{ !in_array(strtolower((string)old('under_radius_required', $office->under_radius_required)), ['1','yes','true'], true) ? 'checked' : '' }}> Disable</label>
                        </div>
                        @error('under_radius_required')<div class="field-error" style="grid-column:1;margin-top:5px">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>

            <section class="form-panel">
                <div class="panel-header"><h2 class="panel-title">Login Security</h2><span class="panel-edit"><i class="fas fa-shield-halved"></i> Setting</span></div>
                <div class="panel-body">
                    <div class="setting-box">
                        <div class="setting-title"><i class="fas fa-mobile-screen-button"></i>Phone OTP Login</div>
                        <p class="setting-description">Enable phone OTP login for users belonging to this office.</p>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="otp_enable" value="1" {{ (string)old('otp_enable', $office->otp_enable ?? 0) === '1' ? 'checked' : '' }}> Enable</label>
                            <label class="radio-label"><input type="radio" name="otp_enable" value="0" {{ (string)old('otp_enable', $office->otp_enable ?? 0) === '0' ? 'checked' : '' }}> Disable</label>
                        </div>
                        @error('otp_enable')<div class="field-error" style="grid-column:1;margin-top:5px">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>

            <section class="form-panel full-width">
                <div class="form-actions">
                    <a href="{{ route('office.index') }}" class="btn-compact btn-cancel"><i class="fas fa-times"></i>Cancel</a>
                    <button type="submit" class="btn-compact btn-save"><i class="fas fa-building-circle-check"></i>Update Office</button>
                </div>
            </section>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const byId = id => document.getElementById(id);
    const nameInput = byId('name');
    const prefixInput = byId('employee_prefix');
    const limitInput = byId('number_of_employees');
    const latitudeInput = byId('latitude');
    const longitudeInput = byId('longitude');
    const radiusInput = byId('radius');
    const ownerInput = byId('owner_id');
    const logoInput = byId('logo');
    const logoPreview = byId('officeLogoPreview');

    const setText = (id, value, fallback = 'Not Entered') => {
        const element = byId(id);
        if (element) element.textContent = value || fallback;
    };

    const refreshPreview = () => {
        const officeName = nameInput?.value.trim() || '';
        const prefix = prefixInput?.value.trim().toUpperCase() || '';
        setText('officeNamePreview', officeName || 'OFFICE DETAILS');
        setText('prefixPreview', prefix);
        const nextSequence = {{ ((int)$office->employee_sequence) + 1 }};
        setText('employeeIdPreview', prefix ? prefix + '-' + String(nextSequence).padStart(4, '0') : '', 'Not Available');
        setText('employeeLimitPreview', limitInput?.value ? limitInput.value + ' Employees' : '');
        setText('latitudePreview', latitudeInput?.value);
        setText('longitudePreview', longitudeInput?.value);
        setText('radiusPreview', radiusInput?.value ? radiusInput.value + ' Metres' : '');

        if (ownerInput?.tagName === 'SELECT') {
            setText(
                'ownerPreview',
                ownerInput.options[ownerInput.selectedIndex]?.text,
                '{{ $office->owner?->name ?? auth()->user()->name }}'
            );
        }

        const radiusRule = document.querySelector('input[name="under_radius_required"]:checked');
        const otpRule = document.querySelector('input[name="otp_enable"]:checked');
        setText('radiusRulePreview', radiusRule?.value === '1' ? 'Enabled' : 'Disabled');
        setText('otpPreview', otpRule?.value === '1' ? 'Enabled' : 'Disabled');
    };

    prefixInput?.addEventListener('input', function () {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        refreshPreview();
    });

    [nameInput, limitInput, latitudeInput, longitudeInput, radiusInput, ownerInput]
        .filter(Boolean)
        .forEach(element => element.addEventListener('input', refreshPreview));

    document.querySelectorAll('input[name="under_radius_required"],input[name="otp_enable"]')
        .forEach(element => element.addEventListener('change', refreshPreview));

    logoInput?.addEventListener('change', function () {
        const file = this.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = event => logoPreview.src = event.target.result;
        reader.readAsDataURL(file);
    });

    refreshPreview();
});
</script>
@endpush