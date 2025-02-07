<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTreatmentRequest;
use App\Http\Requests\UpdateTreatmentRequest;
use App\Models\Treatment;
use App\Services\AppointmentService;
use App\Services\PassiveDateService;
use App\Services\ProductService;
use App\Services\ServiceService;
use App\Services\TreatmentService;
use Inertia\Inertia;

class TreatmentController extends Controller
{
    public function __construct(
        private TreatmentService   $treatmentService,
        private ProductService     $productService,
        private ServiceService     $serviceService,
        private AppointmentService $appointmentService,
        private PassiveDateService $passiveDateService
    )
    {
    }

    public function list()
    {
        return Inertia::render('Dashboard/Treatment/List', [
            'treatments' => $this->treatmentService->filter(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Treatment/Create', [
            'services' => $this->serviceService->getAll(),
            'products' => $this->productService->getAll(),
            'paymentMethods' => PaymentMethod::getAll(),
            'passiveDates' => $this->passiveDateService->getPassiveDays(auth()->user()->doctor_id),
        ]);
    }

    public function store(StoreTreatmentRequest $request)
    {
        $data = $request->validated();
        $data['doctor_id'] = auth()->user()->doctor_id;

        $this->treatmentService->store($data);

        if ($appointments = $data['appointments']) {
            $this->appointmentService->storeMany($data['doctor_id'], $data['patient_id'], $appointments);
        }

        session()->flash('toast.success', trans('messages.treatment.created'));

        return to_route('dashboard.treatment.list');
    }

    public function edit(Treatment $treatment)
    {
        return Inertia::render('Dashboard/Treatment/Edit', [
            'treatment' => $treatment->load(['doctor', 'patient', 'services.service', 'products.product']),
            'services' => $this->serviceService->getAll($treatment->doctor->hospital_id),
            'products' => $this->productService->getAll($treatment->doctor->hospital_id),
            'paymentMethod' => $treatment->transaction->method,
            'paymentMethods' => PaymentMethod::getAll(),
        ]);
    }

    public function update(UpdateTreatmentRequest $request, Treatment $treatment)
    {
        $this->treatmentService->update($treatment, $request->validated());

        session()->flash('toast.success', trans('messages.treatment.updated'));

        return to_route('dashboard.treatment.list');
    }

    public function destroy(Treatment $treatment)
    {
        $this->treatmentService->delete($treatment);

        session()->flash('toast.success', trans('messages.treatment.deleted'));
    }
}
