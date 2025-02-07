<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePassiveDateRequest extends FormRequest
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
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string', 'max:255'],
        ];

        if (auth()->user() && auth()->user()->isAdmin()) {
            if (auth()->user()->hasHospital()) {
                $rules['doctor_id'] = ['required', Rule::exists('doctors', 'id')->where('hospital_id', auth()->user()->hospital_id)];
            } else {
                $rules['doctor_id'] = ['required', Rule::exists('doctors', 'id')];
            }
        }

        return $rules;
    }
}
