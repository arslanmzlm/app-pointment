<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Models\Service;
use App\Traits\Service\HospitalQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    use HospitalQuery;

    public function getAll(?int $hospitalId = null): Collection
    {
        $query = Service::query()->orderBy('name');

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        } else {
            $this->addHospitalToQuery($query);
        }

        return $query->get();
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Service::query())
            ->search('name', 'code')
            ->sort('id', 'name', 'code', 'price')
            ->idFilter('hospital')
            ->bool('active')
            ->hasHospital()
            ->paginate();
    }

    public function store(array $data): ?Service
    {
        $service = new Service();
        $service->hospital_id = auth()->user()->hospital_id ?? $data['hospital_id'];

        return $this->assignAttributes($service, $data);
    }

    public function update(Service $service, array $data): Service
    {
        return $this->assignAttributes($service, $data);
    }

    public function delete(Service $service): bool
    {
        return $service->delete();
    }

    private function assignAttributes(Service $service, array $data): Service
    {
        $service->active = $data['active'] ?? $service->active ?? false;
        $service->name = $data['name'] ?? $service->name;
        $service->code = $data['code'] ?? $service->code;
        $service->price = $data['price'] ?? $service->price;

        $service->save();

        return $service;
    }
}
