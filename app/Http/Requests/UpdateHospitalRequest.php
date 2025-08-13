<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHospitalRequest extends FormRequest
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
        return [
            'province_id' => ['required', 'integer', Rule::exists('provinces', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'start_work' => ['required', 'date_format:H:i',],
            'end_work' => ['required', 'date_format:H:i', 'after:start_work'],
            'duration' => ['required', 'integer', 'min:0'],
            'disabled_days' => ['nullable', 'array'],
            'disabled_days.*' => ['required', 'integer', 'min:1', 'max:7'],
            'title' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:1024'],
            'description' => ['nullable', 'string'],
            'owner' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'phone:TR', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_link' => ['nullable', 'string', 'url', 'max:255'],
        ];
    }
}
