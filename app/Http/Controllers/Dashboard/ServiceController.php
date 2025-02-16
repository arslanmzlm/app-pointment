<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Services\HospitalService;
use App\Services\ServiceService;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function __construct(private ServiceService $serviceService, private HospitalService $hospitalService)
    {
    }

    public function list()
    {
        $data = [
            'services' => $this->serviceService->filter(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Service/List', $data);
    }

    public function create()
    {
        $data = [];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Service/Create', $data);
    }

    public function store(StoreServiceRequest $request)
    {
        $this->serviceService->store($request->validated());

        session()->flash('toast.success', trans('messages.service.created'));

        return to_route('dashboard.service.list');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Dashboard/Service/Edit', [
            'service' => $service,
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $this->serviceService->update($service, $request->validated());

        session()->flash('toast.success', trans('messages.service.updated'));

        return to_route('dashboard.service.list');
    }

    public function destroy(Service $service)
    {
        $this->serviceService->delete($service);

        session()->flash('toast.success', trans('messages.service.deleted'));
    }
}
