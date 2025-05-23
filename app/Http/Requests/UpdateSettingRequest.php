<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
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
            'contact_phone' => ['required', 'phone:TR', 'max:255'],
            'agreement_policy' => ['required', 'string'],
            'privacy_policy' => ['required', 'string'],
            'consent_form_description' => ['required', 'string'],
        ];
    }
}
