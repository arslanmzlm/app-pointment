<?php

namespace App\Traits\Request;

use Illuminate\Validation\Rule;

trait SharedRequest
{
    public function checkForDoctor(array $rules): array
    {
        if (auth()->user()->isAdmin()) {
            if (auth()->user()->hasHospital()) {
                $rules['doctor_id'] = ['required', Rule::exists('doctors', 'id')->where('hospital_id', auth()->user()->hospital_id)];
            } else {
                $rules['doctor_id'] = ['required', Rule::exists('doctors', 'id')];
            }
        }

        return $rules;
    }
}
