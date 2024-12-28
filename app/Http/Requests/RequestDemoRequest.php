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
            'compan_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'number' => 'required',
            'email' => 'required|email|max:255',
            'company_address' => 'required|string|max:255',
            'emp_size' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
        ];
    }
}
