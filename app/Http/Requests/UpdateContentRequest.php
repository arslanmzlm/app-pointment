<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContentRequest extends FormRequest
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
            'section' => ['required', 'string', 'max:255'],
            'active' => ['boolean'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'top_title' => ['nullable', 'string', 'max:255'],
            'alt_title' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:255'],
            'link_label' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'mobile_image' => ['nullable', 'image', 'max:5120'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'max:5120'],
            'order' => ['nullable', 'numeric', 'min:0', 'max:255'],
            'description' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
        ];
    }
}
