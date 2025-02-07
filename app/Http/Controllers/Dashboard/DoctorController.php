<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Services\DoctorService;
use App\Services\HospitalService;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function __construct(private DoctorService $doctorService, private HospitalService $hospitalService)
    {
    }

    public function list()
    {
        $data = [
            'doctors' => $this->doctorService->filter(),
            'branches' => $this->doctorService->getBranches(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Doctor/List', $data);
    }

    public function create()
    {
        $data = [
            'branches' => $this->doctorService->getBranches(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Doctor/Create', $data);
    }

    public function store(StoreDoctorRequest $request)
    {
        $this->doctorService->store($request->validated());

        session()->flash('toast.success', trans('messages.doctor.created'));

        return to_route('dashboard.doctor.list');
    }

    public function edit(Doctor $doctor)
    {
        return Inertia::render('Dashboard/Doctor/Edit', [
            'doctor' => $doctor,
            'branches' => $this->doctorService->getBranches(),
        ]);
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $this->doctorService->update($doctor, $request->validated());

        session()->flash('toast.success', trans('messages.doctor.updated'));

        return to_route('dashboard.doctor.list');
    }

    public function destroy(Doctor $doctor)
    {
        $this->doctorService->delete($doctor);

        session()->flash('toast.success', trans('messages.doctor.updated'));
    }
}
