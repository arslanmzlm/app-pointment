<?php

namespace App\Http\Requests;

use App\Rules\CheckAppointmentOverlap;
use App\Traits\Request\SharedRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    use SharedRequest;

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
        $rules = [
            'patient_id' => ['required', Rule::exists('patients', 'id')],
            'appointments' => ['required', 'array'],
            'appointments.*.appointment_type_id' => ['required', Rule::exists('appointment_types', 'id')],
            'appointments.*.start_date' => ['required', 'date'],
            'appointments.*.duration' => ['required', 'integer', 'min:1'],
            'appointments.*.title' => ['nullable', 'string', 'max:255'],
            'appointments.*' => ['array', new CheckAppointmentOverlap()],
        ];

        return $this->checkForDoctor($rules);
    }
}
