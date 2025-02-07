<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', Rule::phone()->country('TR'), Rule::unique('doctors', 'phone')],
            'email' => ['nullable', 'email', 'max:255'],
            'resume' => ['nullable', 'string'],
            'certificate' => ['nullable', 'string'],
        ];

        if (auth()->user() && !auth()->user()->hasHospital()) {
            $rules['hospital_id'] = ['required', 'integer', Rule::exists('hospitals', 'id')];
        }

        return $rules;
    }
}
