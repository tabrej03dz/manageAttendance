<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'name' => 'required',
            'email' => '',
            'phone' => 'required|min:10',
            'address' => '',
            'photo' => '',
            'joining_date' => '',
            'designation' => '',
            'responsibility' => '',
            'salary' => '',
            'check_in_time' => 'required',
            'check_out_time' => 'required',
            'password' => 'min:8',
            'confirm_password' => 'same:password|min:8',
        ];
    }
}
