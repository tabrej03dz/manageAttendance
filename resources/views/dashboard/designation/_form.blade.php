@php

    $editing =
        isset($designation)
        && $designation
        && $designation->exists;

    $currentOfficeId = old(
        'office_id',
        $editing
            ? $designation->office_id
            : ($selectedOfficeId ?? null)
    );

    $currentDepartmentId = old(
        'department_id',
        $editing
            ? $designation->department_id
            : null
    );

@endphp


{{-- Errors --}}
@if($errors->any())

    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">

        <div class="flex gap-3">

            <div class="mt-0.5 text-red-600">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <div>

                <h4 class="font-extrabold text-red-800">
                    Please fix the following errors
                </h4>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-medium text-red-700">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    </div>

@endif


<div class="grid grid-cols-1 gap-5 md:grid-cols-2">


    {{-- Office --}}
    <div>

        <label class="mb-2 block text-sm font-extrabold text-slate-700">

            Office
            <span class="text-red-500">*</span>

        </label>

        <select name="office_id"
                class="form-control-custom">

            <option value="">
                Select Office
            </option>

            @foreach($offices as $office)

                <option value="{{ $office->id }}"
                    @selected(
                        (string) $currentOfficeId
                        === (string) $office->id
                    )>

                    {{ $office->name }}

                </option>

            @endforeach

        </select>

        @error('office_id')
            <p class="mt-1 text-xs font-bold text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    {{-- Department --}}
    <div>

        <label class="mb-2 block text-sm font-extrabold text-slate-700">

            Department

        </label>

        <select name="department_id"
                class="form-control-custom">

            <option value="">
                No Department
            </option>

            @foreach($departments as $department)

                <option value="{{ $department->id }}"
                    @selected(
                        (string) $currentDepartmentId
                        === (string) $department->id
                    )>

                    {{ $department->name }}

                </option>

            @endforeach

        </select>

        @error('department_id')
            <p class="mt-1 text-xs font-bold text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    {{-- Name --}}
    <div>

        <label class="mb-2 block text-sm font-extrabold text-slate-700">

            Designation Name
            <span class="text-red-500">*</span>

        </label>

        <input type="text"
               name="name"
               value="{{ old(
                    'name',
                    $editing
                        ? $designation->name
                        : ''
               ) }}"
               class="form-control-custom"
               placeholder="Example: Sales Executive"
               required>

        @error('name')
            <p class="mt-1 text-xs font-bold text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    {{-- Code --}}
    <div>

        <label class="mb-2 block text-sm font-extrabold text-slate-700">

            Designation Code

        </label>

        <input type="text"
               name="code"
               value="{{ old(
                    'code',
                    $editing
                        ? $designation->code
                        : ''
               ) }}"
               class="form-control-custom uppercase"
               placeholder="Example: SE, TL, HRM">

        @error('code')
            <p class="mt-1 text-xs font-bold text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    {{-- Level --}}
    <div>

        <label class="mb-2 block text-sm font-extrabold text-slate-700">

            Hierarchy Level

        </label>

        <input type="number"
               name="level"
               min="1"
               value="{{ old(
                    'level',
                    $editing
                        ? $designation->level
                        : ''
               ) }}"
               class="form-control-custom"
               placeholder="Example: 1">

        <p class="mt-1 text-xs font-medium text-slate-500">

            Example: 1 = Director, 2 = Manager,
            3 = Team Leader

        </p>

    </div>


    {{-- Sort Order --}}
    <div>

        <label class="mb-2 block text-sm font-extrabold text-slate-700">

            Sort Order

        </label>

        <input type="number"
               name="sort_order"
               min="0"
               value="{{ old(
                    'sort_order',
                    $editing
                        ? $designation->sort_order
                        : 0
               ) }}"
               class="form-control-custom">

    </div>


    {{-- Description --}}
    <div class="md:col-span-2">

        <label class="mb-2 block text-sm font-extrabold text-slate-700">

            Description

        </label>

        <textarea name="description"
                  rows="5"
                  class="form-control-custom"
                  placeholder="Designation responsibility or description...">{{ old(
                    'description',
                    $editing
                        ? $designation->description
                        : ''
                  ) }}</textarea>

    </div>


    {{-- Status --}}
    <div class="md:col-span-2">

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

            <input type="hidden"
                   name="is_active"
                   value="0">


            <label class="flex cursor-pointer items-center gap-3">

                <input type="checkbox"
                       name="is_active"
                       value="1"
                       class="h-5 w-5 rounded border-slate-300 text-indigo-600"
                       @checked(
                            old(
                                'is_active',
                                $editing
                                    ? $designation->is_active
                                    : true
                            )
                       )>


                <div>

                    <div class="text-sm font-extrabold text-slate-800">

                        Active Designation

                    </div>

                    <div class="mt-0.5 text-xs font-medium text-slate-500">

                        Active designation employee form में selectable रहेगा।

                    </div>

                </div>

            </label>

        </div>

    </div>

</div>


<div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">

    <a href="{{ route('designations.index') }}"
       class="inline-flex min-h-[46px] items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-sm font-extrabold text-slate-700 hover:bg-slate-50">

        <i class="fas fa-arrow-left"></i>

        Cancel

    </a>


    <button type="submit"
            class="inline-flex min-h-[46px] items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 text-sm font-extrabold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700">

        <i class="fas fa-save"></i>

        {{ $submitText ?? 'Save Designation' }}

    </button>

</div>