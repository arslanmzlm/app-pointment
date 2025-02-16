<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\AppointmentState;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteAppointmentRequest;
use App\Http\Requests\PreviewAppointmentsRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Rules\CheckAppointmentOverlap;
use App\Services\AppointmentService;
use App\Services\AppointmentTypeService;
use App\Services\DoctorService;
use App\Services\HospitalService;
use App\Services\PassiveDateService;
use App\Services\ProductService;
use App\Services\ServiceService;
use App\Services\TreatmentService;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService     $appointmentService,
        private AppointmentTypeService $appointmentTypeService,
        private PassiveDateService     $passiveDateService,
        private HospitalService        $hospitalService,
        private DoctorService          $doctorService,
        private ServiceService         $serviceService,
        private ProductService         $productService,
        private TreatmentService       $treatmentService
    )
    {
    }

    public function list()
    {
        $data = [
            'appointments' => $this->appointmentService->filter(),
            'appointmentTypes' => $this->appointmentTypeService->getAll(),
            'appointmentStates' => AppointmentState::getAll(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        if (!auth()->user()->isDoctor()) {
            $data['doctors'] = $this->doctorService->getAll();
        }

        return Inertia::render('Dashboard/Appointment/List', $data);
    }

    public function calendar()
    {
        $data = [
            'appointments' => $this->appointmentService->dateRange(),
            'appointmentTypes' => $this->appointmentTypeService->getAll(),
            'appointmentStates' => AppointmentState::getAll(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        if (!auth()->user()->isDoctor()) {
            $data['doctors'] = $this->doctorService->getAll();
        }

        return Inertia::render('Dashboard/Appointment/Calendar', $data);
    }

    public function create()
    {
        $data = [
            'appointmentTypes' => $this->appointmentTypeService->getAll(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        if (auth()->user()->isDoctor()) {
            $data['passiveDates'] = $this->passiveDateService->getPassiveDays(auth()->user()->doctor_id);
        } else {
            $data['doctors'] = $this->doctorService->getAll();
        }

        return Inertia::render('Dashboard/Appointment/Create', $data);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $doctorId = auth()->user()->doctor_id ?? $request->input('doctor_id');
        $this->appointmentService->storeMany($doctorId, $request->validated('patient_id'), $request->validated('appointments'));

        session()->flash('toast.success', trans('messages.appointment.created'));

        return to_route('dashboard.appointment.list');
    }

    public function edit(Appointment $appointment)
    {
        return Inertia::render('Dashboard/Appointment/Edit', [
            'appointment' => $appointment->load(['patient']),
            'appointmentTypes' => $this->appointmentTypeService->getAll(),
            'passiveDates' => $this->passiveDateService->getPassiveDays($appointment->doctor_id),
            'appointments' => $this->appointmentService->getActiveByPatient($appointment->patient_id),
        ]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        Validator::make(
            ['appointment' => $request->validated(), 'doctor_id' => $appointment->doctor_id],
            ['appointment' => (new CheckAppointmentOverlap())->appointment($appointment->id)]
        )->validate();

        $this->appointmentService->update($appointment, $request->validated());

        session()->flash('toast.success', trans('messages.appointment.updated'));

        return to_route('dashboard.appointment.list');
    }

    public function destroy(Appointment $appointment)
    {
        $this->appointmentService->delete($appointment);

        session()->flash('toast.success', trans('messages.appointment.deleted'));
    }

    public function process(Appointment $appointment)
    {
        if (!$appointment->isActive()) {
            session()->flash('toast.error', trans('messages.appointment.finalized'));

            return back();
        }

        return Inertia::render('Dashboard/Appointment/Process', [
            'appointment' => $appointment->load('patient'),
            'appointmentTypes' => $this->appointmentTypeService->getAll(),
            'services' => $this->serviceService->getAll(auth()->user()->hospital_id),
            'products' => $this->productService->getAll(),
            'paymentMethods' => PaymentMethod::getAll(),
            'passiveDates' => $this->passiveDateService->getPassiveDays(auth()->user()->doctor_id),
            'appointments' => $this->appointmentService->getActiveByPatient($appointment->patient_id),
        ]);
    }

    public function complete(CompleteAppointmentRequest $request, Appointment $appointment)
    {
        if (!$appointment->isActive()) {
            session()->flash('toast.warn', trans('messages.appointment.finalized'));

            return back();
        }

        $data = $request->validated();
        $data['doctor_id'] = auth()->user()->doctor_id;
        $data['patient_id'] = $appointment->patient_id;

        $treatment = $this->treatmentService->store($data);

        if ($appointments = $data['appointments']) {
            $this->appointmentService->storeMany($data['doctor_id'], $data['patient_id'], $appointments);
        }

        $this->appointmentService->completeByTreatment($appointment, $treatment);

        session()->flash('toast.success', trans('messages.appointment.completed'));

        return to_route('dashboard.treatment.list');
    }

    public function cancel(Appointment $appointment)
    {
        if (!$appointment->isActive()) {
            session()->flash('toast.warn', trans('messages.appointment.finalized'));

            return;
        }

        $this->appointmentService->cancel($appointment);

        session()->flash('toast.success', trans('messages.appointment.canceled'));
    }

    public function schedule(PreviewAppointmentsRequest $request)
    {
        $doctorId = auth()->user()->doctor_id ?? $request->input('doctor_id');

        return response()->json([
            'dates' => $doctorId ? $this->appointmentService->previewDates($doctorId, $request->validated('dates')) : [],
        ]);
    }
}
