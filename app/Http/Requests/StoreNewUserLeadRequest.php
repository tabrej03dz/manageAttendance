<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewUserLeadRequest extends FormRequest
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
            'company_name'     => 'required|string|max:255',
            'owner_name'       => 'required|string|max:255',
            'number'           => 'required|digits:10',
            'pincode'          => 'required|digits:6',
            'email'            => 'required|email|unique:new_user_leads,email',
            'company_address'  => 'required|string|max:255',
            'emp_size'         => 'required|string',
            'designation'      => 'required|string',
        ];
    }
}
