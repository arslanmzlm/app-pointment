<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Services\HospitalService;
use App\Services\ReportService;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService, private HospitalService $hospitalService)
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

        return Inertia::render('Dashboard/Reports/Transaction', $data);
    }

    public function patient()
    {
        $data = [
            'reports' => $this->reportService->patientReport(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Reports/Patient', $data);
    }
}
