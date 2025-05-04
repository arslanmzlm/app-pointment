<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\Appointment;
use App\Rules\CheckAppointmentOverlap;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteAppointmentRequest extends FormRequest
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

        $this->merge(['doctor_id' => $appointment->doctor_id]);

        return [
            'note' => ['nullable', 'string'],
            'services' => ['array'],
            'services.*.service_id' => ['required', Rule::exists('services', 'id')->where('hospital_id', $appointment->hospital_id)],
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
            'appointments.*.service_id' => ['nullable', Rule::exists('services', 'id')->where('hospital_id', $appointment->hospital_id)],
            'appointments.*' => ['array', new CheckAppointmentOverlap()],
            'payments' => ['array'],
            'payments.*' => ['array'],
            'payments.*.method' => ['required', Rule::enum(PaymentMethod::class)],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
