<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\Doctor;
use App\Rules\CheckAppointmentOverlap;
use App\Traits\Request\SharedRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTreatmentRequest extends FormRequest
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
            'patient_id' => ['required', Rule::exists('patients', 'id')],
            'note' => ['nullable', 'string'],
            'services' => ['array'],
            'services.*.price' => ['required', 'numeric'],
            'products' => ['array'],
            'products.*.product_id' => ['required', Rule::exists('products', 'id')],
            'products.*.count' => ['required', 'integer', 'min:1'],
            'products.*.price' => ['required', 'numeric'],
            'appointments' => ['array'],
            'appointments.*.appointment_type_id' => ['required', Rule::exists('appointment_types', 'id')],
            'appointments.*.start_date' => ['required', 'date'],
            'appointments.*.duration' => ['required', 'integer', 'min:1'],
            'appointments.*.note' => ['nullable', 'string', 'max:255'],
            'appointments.*' => ['array', new CheckAppointmentOverlap()],
            'payments' => ['array'],
            'payments.*' => ['array'],
            'payments.*.method' => ['required', Rule::enum(PaymentMethod::class)],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0'],
        ];

        if ($doctor !== null) {
            $rules['services.*.service_id'] = ['required', Rule::exists('services', 'id')->where('hospital_id', $doctor->hospital_id)];
            $rules['appointments.*.service_id'] = ['required', Rule::exists('services', 'id')->where('hospital_id', $doctor->hospital_id)];
        }

        return $this->checkForDoctor($rules);
    }
}
