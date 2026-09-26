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
            'status' => $this->input('status', '1'),

            'first_name' => $this->filled('first_name')
                ? trim((string) $this->input('first_name'))
                : null,

            'middle_name' => $this->filled('middle_name')
                ? trim((string) $this->input('middle_name'))
                : null,

            'last_name' => $this->filled('last_name')
                ? trim((string) $this->input('last_name'))
                : null,

            'phone' => $this->filled('phone')
                ? preg_replace('/\D+/', '', (string) $this->input('phone'))
                : null,

            'alternate_number' => $this->filled('alternate_number')
                ? preg_replace('/\D+/', '', (string) $this->input('alternate_number'))
                : null,

            'adhar_number' => $this->filled('adhar_number')
                ? preg_replace('/\D+/', '', (string) $this->input('adhar_number'))
                : null,

            'pan_number' => $this->filled('pan_number')
                ? strtoupper(preg_replace('/\s+/', '', (string) $this->input('pan_number')))
                : null,

            'ifsc_code' => $this->filled('ifsc_code')
                ? strtoupper(preg_replace('/\s+/', '', (string) $this->input('ifsc_code')))
                : null,

            'account_number' => $this->filled('account_number')
                ? preg_replace('/\D+/', '', (string) $this->input('account_number'))
                : null,

            'uan_number' => $this->filled('uan_number')
                ? preg_replace('/\D+/', '', (string) $this->input('uan_number'))
                : null,

            'esic_number' => $this->filled('esic_number')
                ? preg_replace('/\D+/', '', (string) $this->input('esic_number'))
                : null,

            'upi_id' => $this->filled('upi_id')
                ? strtolower(trim((string) $this->input('upi_id')))
                : null,
        ]);
    }



    public function rules(): array

    {

        return [
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],



            'email' => [

                'nullable',

                'email',

                'max:255',

                Rule::unique('users', 'email'),

            ],



            'phone' => [
                'required',
                'digits:10',
                'regex:/^[6-9][0-9]{9}$/',
            ],

            'alternate_number' => [
                'nullable',
                'digits:10',
                'regex:/^[6-9][0-9]{9}$/',
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

            \*/



            'status' => [

                'required',

                Rule::in(['0', '1', 0, 1]),

            ],



            /*

            |--------------------------------------------------------------------------

            | Aadhaar and PAN

            |--------------------------------------------------------------------------

            \*/



            'adhar_number' => [
                'nullable',
                'digits:12',
                'regex:/^[2-9][0-9]{11}$/',
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

            \*/



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
                'digits_between:9,18',
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

                'regex:/^[a-zA-Z0-9.\\-\_]{2,}@[a-zA-Z]{2,}$/',

            ],



            /*

            |--------------------------------------------------------------------------

            | Official numbers

            |--------------------------------------------------------------------------

            \*/



            'uan_number' => [
                'nullable',
                'digits:12',
            ],



            'esic_number' => [
                'nullable',
                'digits:10',
            ],



            /*

            |--------------------------------------------------------------------------

            | Attachments

            |--------------------------------------------------------------------------

            \*/



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

            \*/



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

            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be valid text.',
            'first_name.max' => 'First name may not be greater than 100 characters.',

            'middle_name.max' => 'Middle name may not be greater than 100 characters.',
            'last_name.max' => 'Last name may not be greater than 100 characters.',

            'phone.required' => 'Mobile number is required.',
            'phone.digits' => 'Mobile number must contain exactly 10 digits.',
            'phone.regex' => 'Please enter a valid 10 digit mobile number starting with 6, 7, 8 or 9.',

            'alternate_number.digits' => 'Alternate mobile number must contain exactly 10 digits.',
            'alternate_number.regex' => 'Please enter a valid alternate mobile number starting with 6, 7, 8 or 9.',

            'status.required' => 'Please select employee status.',

            'status.in' => 'Employee status must be Active or Inactive.',



            'adhar_number.digits' => 'Aadhaar number must contain exactly 12 digits.',
            'adhar_number.regex' => 'Please enter a valid 12 digit Aadhaar number.',

            'adhar_number.unique' => 'This Aadhaar number is already registered.',



            'pan_number.size' => 'PAN number must contain exactly 10 characters.',

            'pan_number.regex' => 'Please enter a valid PAN number, for example ABCDE1234F.',

            'pan_number.unique' => 'This PAN number is already registered.',



            'account_number.digits_between' => 'Bank account number must contain between 9 and 18 digits.',

            'uan_number.digits' => 'UAN number must contain exactly 12 digits.',
            'esic_number.digits' => 'ESIC number must contain exactly 10 digits.',

            'ifsc_code.size' => 'IFSC code must contain exactly 11 characters.',

            'ifsc_code.regex' => 'Please enter a valid IFSC code, for example SBIN0001234.',



            'upi_id.regex' => 'Please enter a valid UPI ID, for example name\@bank.',

        ];

    }

}