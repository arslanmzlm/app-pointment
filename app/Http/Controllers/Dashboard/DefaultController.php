<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use App\Services\PatientService;
use App\Services\TreatmentService;
use Inertia\Inertia;

class DefaultController extends Controller
{
    public function __construct(private PatientService $patientService, private TreatmentService $treatmentService, private AppointmentService $appointmentService)
    {
    }

    public function index()
    {
        return Inertia::render('Dashboard/Index', [
            'patients' => $this->patientService->today(),
            'treatments' => $this->treatmentService->today(),
            'appointments' => $this->appointmentService->today()
        ]);
    }
}
