<?php

namespace App\Http\Requests;

use App\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
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
        /** @var Doctor $doctor */
        $doctor = $this->route('doctor');

        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', Rule::phone()->country('TR'), Rule::unique('doctors', 'phone')->ignore($doctor->id)],
            'email' => ['nullable', 'email', 'max:255'],
            'resume' => ['nullable', 'string'],
            'certificate' => ['nullable', 'string'],
        ];
    }
}
