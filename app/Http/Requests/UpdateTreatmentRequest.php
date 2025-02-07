<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\Treatment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTreatmentRequest extends FormRequest
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
        /** @var Treatment $treatment */
        $treatment = $this->route('treatment');

        return [
            'note' => ['nullable', 'string'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'services' => ['array'],
            'services.*.id' => ['nullable', Rule::exists('treatment_services', 'id')],
            'services.*.service_id' => ['required', Rule::exists('services', 'id')->where('hospital_id', $treatment->doctor->hospital_id)],
            'services.*.price' => ['required', 'numeric'],
            'products' => ['array'],
            'products.*.id' => ['nullable', Rule::exists('treatment_products', 'id')],
            'products.*.product_id' => ['required', Rule::exists('products', 'id')->where('hospital_id', $treatment->doctor->hospital_id)],
            'products.*.count' => ['required', 'integer', 'min:1'],
            'products.*.price' => ['required', 'numeric'],
        ];
    }
}
