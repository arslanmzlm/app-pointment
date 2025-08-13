<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
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
        /** @var Appointment $appointment */
        $appointment = $this->route('appointment');

        return [
            'appointment_type_id' => ['required', Rule::exists('appointment_types', 'id')],
            'start_date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', Rule::exists('services', 'id')->where('hospital_id', $appointment->hospital_id)],
            'call' => ['boolean'],
        ];
    }
}
