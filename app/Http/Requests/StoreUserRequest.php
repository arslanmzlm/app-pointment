<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'hospital_id' => ['nullable', 'integer', Rule::exists('hospitals', 'id')],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', Rule::phone()->country('TR'), Rule::unique('users', 'phone')],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'confirmed', new Password(5)],
        ];
    }
}
