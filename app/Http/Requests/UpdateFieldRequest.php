<?php

namespace App\Http\Requests;

use App\Enums\FieldInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFieldRequest extends FormRequest
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
            'input' => ['required', Rule::enum(FieldInput::class)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'max:65535'],
            'values' => ['nullable', 'array'],
            'values.*.id' => ['nullable', Rule::exists('field_values', 'id')],
            'values.*.value' => ['required', 'string', 'max:255'],
        ];
    }
}
