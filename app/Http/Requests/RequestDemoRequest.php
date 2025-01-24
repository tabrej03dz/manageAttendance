<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestDemoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'compan_name' => 'nullable|string|max:255',
            'owner_name' => 'required|string|max:255',
            'number' => 'required|string|max:15|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:255',
            'company_address' => 'nullable|string|max:500',
            'emp_size' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'owner_name.required' => 'The owner name field is required.',
            'number.required' => 'The contact number field is required.',
            'number.regex' => 'The contact number must contain only digits.',
            'email.email' => 'Please enter a valid email address.',
            'pin_code.regex' => 'The pin code must contain only digits.',
        ];
    }
}
