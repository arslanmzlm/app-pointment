<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentTypeRequest;
use App\Http\Requests\UpdateAppointmentTypeRequest;
use App\Models\AppointmentType;
use App\Services\AppointmentTypeService;
use Inertia\Inertia;

class AppointmentTypeController extends Controller
{
    public function __construct(
        private AppointmentTypeService $appointmentTypeService,
    )
    {
    }

    public function list()
    {
        return Inertia::render('Dashboard/AppointmentType/List', [
            'appointmentTypes' => $this->appointmentTypeService->filter()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/AppointmentType/Create');
    }

    public function store(StoreAppointmentTypeRequest $request)
    {
        $this->appointmentTypeService->store($request->validated());

        session()->flash('toast.success', trans('messages.appointment.type.created'));

        return to_route('dashboard.appointment.type.list');
    }

    public function edit(AppointmentType $appointmentType)
    {
        return Inertia::render('Dashboard/AppointmentType/Edit', [
            'appointmentType' => $appointmentType,
        ]);
    }

    public function update(UpdateAppointmentTypeRequest $request, AppointmentType $appointmentType)
    {
        $this->appointmentTypeService->update($appointmentType, $request->validated());

        session()->flash('toast.success', trans('messages.appointment.type.updated'));

        return to_route('dashboard.appointment.type.list');
    }
}
