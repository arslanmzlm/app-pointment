<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePassiveDateRequest;
use App\Http\Requests\UpdatePassiveDateRequest;
use App\Models\PassiveDate;
use App\Services\DoctorService;
use App\Services\HospitalService;
use App\Services\PassiveDateService;
use Inertia\Inertia;

class PassiveDateController extends Controller
{
    public function __construct(private PassiveDateService $passiveDateService, private HospitalService $hospitalService, private DoctorService $doctorService)
    {
    }

    public function list()
    {
        $data = [
            'passiveDates' => $this->passiveDateService->filter(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        if (!auth()->user()->isDoctor()) {
            $data['doctors'] = $this->doctorService->getAll();
        }

        return Inertia::render('Dashboard/PassiveDate/List', $data);
    }

    public function create()
    {
        $data = [];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        if (auth()->user()->isAdmin()) {
            $data['doctors'] = $this->doctorService->getAll();
        }

        return Inertia::render('Dashboard/PassiveDate/Create', $data);
    }

    public function store(StorePassiveDateRequest $request)
    {
        $this->passiveDateService->store($request->validated());

        session()->flash('toast.success', trans('messages.passive.date.created'));

        return to_route('dashboard.passive.date.list');
    }

    public function edit(PassiveDate $passiveDate)
    {
        return Inertia::render('Dashboard/PassiveDate/Edit', [
            'passiveDate' => $passiveDate
        ]);
    }

    public function update(UpdatePassiveDateRequest $request, PassiveDate $passiveDate)
    {
        $this->passiveDateService->update($passiveDate, $request->validated());

        session()->flash('toast.success', trans('messages.passive.date.updated'));

        return to_route('dashboard.passive.date.list');
    }

    public function destroy(PassiveDate $passiveDate)
    {
        $this->passiveDateService->delete($passiveDate);

        session()->flash('toast.success', trans('messages.passive.date.deleted'));
    }
}
