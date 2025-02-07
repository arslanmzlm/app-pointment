<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
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
            'old' => ['boolean'],
            'province_id' => ['required', 'integer', Rule::exists('provinces', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'phone' => ['required', Rule::phone()->country('TR'), Rule::unique('patients', 'phone')],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'birthday' => ['nullable', 'date'],
            'notification' => ['boolean'],
            'fields' => ['array'],
            'fields.*.id' => ['nullable', Rule::exists('patient_fields', 'id')],
            'fields.*.field_id' => ['nullable', Rule::exists('fields', 'id')],
            'fields.*.value' => ['nullable'],
        ];
    }
}
