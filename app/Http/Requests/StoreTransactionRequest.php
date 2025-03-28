<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
        $rules = [
            'type' => ['required', Rule::enum(TransactionType::class)],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'total' => ['required', 'numeric', 'min:1'],
            'patient_id' => ['nullable', Rule::exists('patients', 'id')],
            'description' => ['nullable', 'string', 'max:255'],
        ];

        if (auth()->user() && !auth()->user()->hasHospital()) {
            $rules['hospital_id'] = ['required', Rule::exists('hospitals', 'id')];
        }

        return $rules;
    }
}
