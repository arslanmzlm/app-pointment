<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Rules\CheckAppointmentOverlap;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTreatmentRequest extends FormRequest
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
        $doctor = auth()->user()->doctor;

        return [
            'patient_id' => ['required', Rule::exists('patients', 'id')],
            'note' => ['nullable', 'string'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'services' => ['array'],
            'services.*.service_id' => ['required', Rule::exists('services', 'id')->where('hospital_id', $doctor->hospital_id)],
            'services.*.price' => ['required', 'numeric'],
            'products' => ['array'],
            'products.*.product_id' => ['required', Rule::exists('products', 'id')->where('hospital_id', $doctor->hospital_id)],
            'products.*.count' => ['required', 'integer', 'min:1'],
            'products.*.price' => ['required', 'numeric'],
            'appointments' => ['array'],
            'appointments.*.start_date' => ['required', 'date'],
            'appointments.*.duration' => ['required', 'integer', 'min:1'],
            'appointments.*.title' => ['nullable', 'string', 'max:255'],
            'appointments.*' => ['array', new CheckAppointmentOverlap()],
        ];
    }
}
