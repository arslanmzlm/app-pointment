<?php

namespace App\Http\Requests;

use App\Traits\Request\SharedRequest;
use Illuminate\Foundation\Http\FormRequest;

class StorePassiveDateRequest extends FormRequest
{
    use SharedRequest;

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

        return $this->checkForDoctor($rules);
    }
}
