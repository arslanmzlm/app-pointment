<?php

namespace App\Http\Requests;

use App\Models\Doctor;
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
        if (auth()->user()->isDoctor()) {
            $doctor = auth()->user()->doctor;
        } else {
            $doctor = Doctor::find($this->input('doctor_id'));
        }

        $rules = [
            'patient_id' => ['nullable', Rule::exists('patients', 'id')],
            'patient_name' => ['required_without:patient_id', 'nullable', 'string', 'max:255'],
            'patient_surname' => ['required_without:patient_id', 'nullable', 'string', 'max:255'],
            'patient_phone' => ['required_without:patient_id', 'nullable', 'string', Rule::phone()->country('TR'), Rule::unique('patients', 'phone')],
            'appointments' => ['required', 'array'],
            'appointments.*.appointment_type_id' => ['required', Rule::exists('appointment_types', 'id')],
            'appointments.*.start_date' => ['required', 'date'],
            'appointments.*.duration' => ['required', 'integer', 'min:1'],
            'appointments.*.note' => ['nullable', 'string', 'max:255'],
            'appointments.*.call' => ['boolean'],
            'appointments.*' => ['array', new CheckAppointmentOverlap()],
        ];

        if ($doctor !== null) {
            $rules['appointments.*.service_id'] = ['nullable', Rule::exists('services', 'id')->where('hospital_id', $doctor->hospital_id)];
        }

        return $this->checkForDoctor($rules);
    }
}
