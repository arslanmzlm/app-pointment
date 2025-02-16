<?php

namespace App\Traits\Service;

use Illuminate\Database\Eloquent\Builder;

trait HospitalQuery
{
    public function addHospitalToQuery(Builder $query): void
    {
        if (auth()->user() && auth()->user()->hasHospital()) {
            $query->where('hospital_id', auth()->user()->hospital_id);
        }
    }

    public function addHospitalToRequest(string $key = 'hospital'): void
    {
        if (auth()->user() && auth()->user()->hasHospital()) {
            request()->merge([$key => auth()->user()->hospital_id]);
        }
    }

    public function addDoctorToQuery(Builder $query): void
    {
        if (auth()->user() && auth()->user()->isDoctor()) {
            $query->where('doctor_id', auth()->user()->doctor_id);
        }
    }
}
