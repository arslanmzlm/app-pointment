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
        private AppointmentService $appointmentService,
        private PassiveDateService $passiveDateService,
        private HospitalService    $hospitalService,
        private DoctorService      $doctorService,
        private ServiceService     $serviceService,
        private ProductService     $productService,
        private TreatmentService   $treatmentService
    )
    {
    }

    public
    function list()
    {
        $data = [
            'appointments' => $this->appointmentService->filter(),
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
        $data = [];

        if (auth()->user()->doctor_id) {
            $data = [
                'passiveDates' => $this->passiveDateService->getPassiveDays(auth()->user()->doctor_id),
            ];
        }

        return Inertia::render('Dashboard/Appointment/Create', $data);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $this->appointmentService->storeMany(auth()->user()->doctor_id, $request->validated('patient_id'), $request->validated('appointments'));

        session()->flash('toast.success', trans('messages.appointment.created'));

        return to_route('dashboard.appointment.list');
    }

    public function edit(Appointment $appointment)
    {
        return Inertia::render('Dashboard/Appointment/Edit', [
            'appointment' => $appointment->load('patient'),
            'passiveDates' => $this->passiveDateService->getPassiveDays(auth()->user()->doctor_id),
            'appointments' => $this->appointmentService->getActiveByPatient($appointment->patient_id),
        ]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        Validator::make(
            ['appointment' => $request->validated()],
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
            session()->flash('toast.error', trans('messages.treatment.already_completed'));

            return back();
        }

        return Inertia::render('Dashboard/Appointment/Process', [
            'appointment' => $appointment->load('patient'),
            'services' => $this->serviceService->getAll(auth()->user()->hospital_id),
            'products' => $this->productService->getAll(auth()->user()->hospital_id),
            'paymentMethods' => PaymentMethod::getAll(),
            'passiveDates' => $this->passiveDateService->getPassiveDays(auth()->user()->doctor_id),
            'appointments' => $this->appointmentService->getActiveByPatient($appointment->patient_id),
        ]);
    }

    public function complete(CompleteAppointmentRequest $request, Appointment $appointment)
    {
        if (!$appointment->isActive()) {
            session()->flash('toast.error', trans('messages.treatment.already_completed'));

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

        session()->flash('toast.success', trans('messages.treatment.completed'));

        return to_route('dashboard.treatment.list');
    }

    public function schedule(PreviewAppointmentsRequest $request)
    {
        return response()->json([
            'dates' => $this->appointmentService->previewDates(auth()->user()->doctor_id, $request->validated('dates')),
        ]);
    }

}
