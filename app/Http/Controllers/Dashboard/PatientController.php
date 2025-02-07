<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\FieldService;
use App\Services\PatientService;
use App\Services\ProvinceService;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function __construct(private PatientService $patientService, private ProvinceService $provinceService, private FieldService $fieldService)
    {
    }

    public function list()
    {
        return Inertia::render('Dashboard/Patient/List', [
            'patients' => $this->patientService->filter(),
            'provinces' => $this->provinceService->getAll(),
            'genders' => Gender::getAll(),
        ]);
    }

    public function show(Patient $patient)
    {
        return Inertia::render('Dashboard/Patient/Show', [
            'patient' => $patient->withoutRelations(),
            'province' => $patient->province,
            'fields' => $this->fieldService->getValuesForView($patient),
            'treatments' => $patient->treatments->load('services.service', 'products.product'),
            'appointments' => $patient->appointments,
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Patient/Create', [
            'provinces' => $this->provinceService->getAll(),
            'genders' => Gender::getAll(),
            'fields' => $this->fieldService->getAll(),
        ]);
    }

    public function store(StorePatientRequest $request)
    {
        $this->patientService->store($request->validated());

        session()->flash('toast.success', trans('messages.patient.created'));

        return to_route('dashboard.patient.list');
    }

    public function edit(Patient $patient)
    {
        return Inertia::render('Dashboard/Patient/Edit', [
            'patient' => $patient,
            'provinces' => $this->provinceService->getAll(),
            'genders' => Gender::getAll(),
            'fields' => $this->fieldService->getAll(),
            'fieldValues' => $this->fieldService->getValuesForForm($patient),
        ]);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $this->patientService->update($patient, $request->validated());

        session()->flash('toast.success', trans('messages.patient.updated'));

        return to_route('dashboard.patient.list');
    }

    public function search()
    {
        return $this->patientService->search();
    }
}
