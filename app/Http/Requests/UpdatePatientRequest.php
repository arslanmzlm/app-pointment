<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
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
        /** @var Patient $patient */
        $patient = $this->route('patient');

        return [
            'old' => ['boolean'],
            'province_id' => ['required', 'integer', Rule::exists('provinces', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', Rule::phone()->country('TR'), Rule::unique('patients', 'phone')->ignore($patient->id)],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'birthday' => ['nullable', 'date'],
            'notification' => ['boolean'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'fields' => ['array'],
            'fields.*.id' => ['nullable', Rule::exists('patient_fields', 'id')],
            'fields.*.field_id' => ['nullable', Rule::exists('fields', 'id')],
            'fields.*.value' => ['nullable'],
        ];
    }
}
