<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganisationRequest extends FormRequest
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
    public function rules()
    {
        $id = $this->route('organisation') ? $this->route('organisation')->id : null;

        return [
            'company_logo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'company_name'   => 'required|string|max:255',
            'company_email'  => 'nullable|email|max:255|unique:organisations,company_email,' . $id,
            'address'        => 'required|string|max:255',
            'gst_details'    => 'nullable|string|max:255',
            'status'         => 'required|in:active,inactive',
        ];
    }
}
