<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHospitalRequest;
use App\Http\Requests\UpdateHospitalRequest;
use App\Models\Hospital;
use App\Services\HospitalService;
use App\Services\ProvinceService;
use Inertia\Inertia;

class HospitalController extends Controller
{
    public function __construct(private HospitalService $hospitalService, private ProvinceService $provinceService)
    {
    }

    public function list()
    {
        return Inertia::render('Dashboard/Hospital/List', [
            'hospitals' => $this->hospitalService->filter(),
            'provinces' => $this->provinceService->getAll(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Hospital/Create', [
            'provinces' => $this->provinceService->getAll()
        ]);
    }

    public function store(StoreHospitalRequest $request)
    {
        $this->hospitalService->store($request->validated());

        session()->flash('toast.success', trans('messages.hospital.created'));

        return to_route('dashboard.hospital.list');
    }

    public function edit(Hospital $hospital)
    {
        return Inertia::render('Dashboard/Hospital/Edit', [
            'hospital' => $hospital,
            'provinces' => $this->provinceService->getAll()
        ]);
    }

    public function update(UpdateHospitalRequest $request, Hospital $hospital)
    {
        $this->hospitalService->update($hospital, $request->validated());

        session()->flash('toast.success', trans('messages.hospital.updated'));

        return to_route('dashboard.hospital.list');
    }

    public function destroy(Hospital $hospital)
    {
        $this->hospitalService->delete($hospital);

        session()->flash('toast.success', trans('messages.hospital.updated'));

        return to_route('dashboard.hospital.list');
    }

    public function info()
    {
        return response()->json($this->hospitalService->getInfo());
    }
}
