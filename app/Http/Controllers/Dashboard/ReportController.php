<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Gender;
use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Services\HospitalService;
use App\Services\ProvinceService;
use App\Services\ReportService;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService, private HospitalService $hospitalService, private ProvinceService $provinceService)
    {
    }

    public function transaction()
    {
        $data = [
            'reports' => $this->reportService->transactionReport(),
            'transactionTypes' => TransactionType::getAll(),
            'paymentMethods' => PaymentMethod::getAll(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Report/Transaction', $data);
    }

    public function patient()
    {
        return Inertia::render('Dashboard/Report/Patient', [
            'reports' => $this->reportService->patientReport(),
            'provinces' => $this->provinceService->getAll(),
            'genders' => Gender::getAll(),
        ]);
    }

    public function treatment()
    {
        $data = $this->reportService->treatmentReport();

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Report/Treatment', $data);
    }

    public function appointment()
    {
        $data = [
            'reports' => $this->reportService->appointmentReport(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Report/Appointment', $data);
    }
}
