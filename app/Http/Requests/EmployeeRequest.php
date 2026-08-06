<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            // Status ko boolean() se convert mat karein.
            // Direct 0/1 string ko preserve karna zaroori hai.
            'status' => $this->input('status', '1'),

            'adhar_number' => $this->filled('adhar_number')
                ? preg_replace('/\D+/', '', (string) $this->input('adhar_number'))
                : null,

            'pan_number' => $this->filled('pan_number')
                ? strtoupper(trim((string) $this->input('pan_number')))
                : null,

            'ifsc_code' => $this->filled('ifsc_code')
                ? strtoupper(trim((string) $this->input('ifsc_code')))
                : null,

            'account_number' => $this->filled('account_number')
                ? trim((string) $this->input('account_number'))
                : null,

            'upi_id' => $this->filled('upi_id')
                ? strtolower(trim((string) $this->input('upi_id')))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'dob' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'joining_date' => [
                'nullable',
                'date',
            ],

            'employee_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'employee_id'),
            ],

            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
            ],

            'designation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'responsibility' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'salary' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'check_in_time' => [
                'required',
                'date_format:H:i',
            ],

            'check_out_time' => [
                'required',
                'date_format:H:i',
            ],

            'break' => [
                'required',
                'integer',
                'min:0',
                'max:1440',
            ],

            'location_required' => [
                'required',
                Rule::in(['yes', 'no']),
            ],

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',
                Rule::in(['0', '1', 0, 1]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Aadhaar and PAN
            |--------------------------------------------------------------------------
            */

            'adhar_number' => [
                'nullable',
                'digits:12',
                Rule::unique('users', 'adhar_number'),
            ],

            'pan_number' => [
                'nullable',
                'string',
                'size:10',
                'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
                Rule::unique('users', 'pan_number'),
            ],

            /*
            |--------------------------------------------------------------------------
            | Bank details
            |--------------------------------------------------------------------------
            */

            'account_holder_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'bank_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'bank_branch' => [
                'nullable',
                'string',
                'max:255',
            ],

            'account_number' => [
                'nullable',
                'string',
                'min:6',
                'max:30',
            ],

            'ifsc_code' => [
                'nullable',
                'string',
                'size:11',
                'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            ],

            'account_type' => [
                'nullable',
                Rule::in([
                    'savings',
                    'current',
                    'salary',
                    'other',
                ]),
            ],

            'upi_id' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9.\-_]{2,}@[a-zA-Z]{2,}$/',
            ],

            /*
            |--------------------------------------------------------------------------
            | Official numbers
            |--------------------------------------------------------------------------
            */

            'uan_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            'esic_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            /*
            |--------------------------------------------------------------------------
            | Attachments
            |--------------------------------------------------------------------------
            */

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'aadhar_attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],

            'pan_attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],

            'other_attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx',
                'max:10240',
            ],

            /*
            |--------------------------------------------------------------------------
            | Salary structure
            |--------------------------------------------------------------------------
            */

            'basic_salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'house_rent_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'transport_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'medical_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'special_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'dearness_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'relieving_charge' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'additional_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'provident_fund' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'employee_state_insurance_corporation' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Please select employee status.',
            'status.in' => 'Employee status must be Active or Inactive.',

            'adhar_number.digits' => 'Aadhaar number must contain exactly 12 digits.',
            'adhar_number.unique' => 'This Aadhaar number is already registered.',

            'pan_number.size' => 'PAN number must contain exactly 10 characters.',
            'pan_number.regex' => 'Please enter a valid PAN number, for example ABCDE1234F.',
            'pan_number.unique' => 'This PAN number is already registered.',

            'ifsc_code.size' => 'IFSC code must contain exactly 11 characters.',
            'ifsc_code.regex' => 'Please enter a valid IFSC code, for example SBIN0001234.',

            'upi_id.regex' => 'Please enter a valid UPI ID, for example name@bank.',
        ];
    }
}