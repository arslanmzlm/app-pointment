<?php

namespace App\Http\Requests;

use App\Rules\CheckAppointmentOverlap;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
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
            'patient_id' => ['required', Rule::exists('patients', 'id')],
            'appointments' => ['required', 'array'],
            'appointments.*.start_date' => ['required', 'date'],
            'appointments.*.duration' => ['required', 'integer', 'min:1'],
            'appointments.*.title' => ['nullable', 'string', 'max:255'],
            'appointments.*' => ['array', new CheckAppointmentOverlap()],
        ];
    }
}
