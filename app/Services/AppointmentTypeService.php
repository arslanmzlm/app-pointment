<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Models\AppointmentType;
use App\Traits\Service\HospitalQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AppointmentTypeService
{
    use HospitalQuery;

    public function getAll(): Collection
    {
        return AppointmentType::query()->orderBy('name')->get();
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(AppointmentType::query())
            ->search('name')
            ->sort('name')
            ->paginate();
    }

    public function store(array $data): AppointmentType
    {
        $appointmentType = new AppointmentType();
        $appointmentType->name = $data['name'];

        $appointmentType->save();

        return $appointmentType;
    }

    public function update(AppointmentType $appointmentType, array $data): AppointmentType
    {
        $appointmentType->name = $data['name'];

        $appointmentType->save();

        return $appointmentType;
    }

    public function delete(AppointmentType $appointmentType): bool
    {
        return $appointmentType->delete();
    }
}
