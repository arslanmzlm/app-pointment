<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'active' => ['boolean'],
            'category' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'stock' => ['required', 'integer'],
            'price' => ['nullable', 'numeric'],
        ];

        if (auth()->user() && !auth()->user()->hasHospital()) {
            $rules['hospital_id'] = ['required', Rule::exists('hospitals', 'id')];
        }

        return $rules;
    }
}
