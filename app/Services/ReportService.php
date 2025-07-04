<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\Treatment;
use App\Models\TreatmentProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportService
{
    const BAGS = [
        'transaction',
        'patient',
        'treatment',
        'appointment',
    ];

    private ?int $hospitalId;
    private ?Carbon $start = null;
    private ?Carbon $due = null;
    private bool $entire = false;

    public function __construct()
    {
        $this->hospitalId = auth()->user()->hospital_id;

        if (request()->has('start_date')) {
            try {
                $this->start = request()->date('start_date');
                $this->start->setTimezone('Europe/Istanbul');
                $this->start->startOfDay();

                if (request()->has('due_date')) {
                    $this->due = request()->date('due_date');
                    $this->due->setTimezone('Europe/Istanbul');
                    $this->due->endOfDay();
                } else {
                    $this->due = $this->start->clone()->endOfDay();
                }

            } catch (\Exception|\Error $exception) {
                $this->start = now()->startOfMonth();
                $this->due = now()->endOfMonth();
            }
        } else if (request()->has('entire') && request()->boolean('entire')) {
            $this->entire = true;
        } else {
            $this->start = now()->startOfMonth();
            $this->due = now()->endOfMonth();
        }
    }

    public function clearCache(): void
    {
        $key = request('key');

        if (in_array($key, self::BAGS)) {
            Cache::forget($this->getCacheKey("report:{$key}"));
        }
    }

    public function transactionReport(): ?array
    {
        $cacheKey = $this->getCacheKey('report:transaction');

        if (app()->isProduction() && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $query = Transaction::query()
            ->select(['hospital_id', 'type', 'method', DB::raw('SUM(`total`) as total')])
            ->groupBy(['hospital_id', 'type', 'method'])
            ->orderBy('hospital_id')
            ->orderBy('type')
            ->orderBy('method');

        if ($this->hospitalId !== null) {
            $query->where('hospital_id', $this->hospitalId);
        }

        $this->addDateToQuery($query);

        $rows = $query->get();
        $data = [];

        foreach ($rows as $row) {
            $hospitalId = $row->hospital_id;

            if (!key_exists($hospitalId, $data)) {
                $data[$hospitalId] = [
                    'income_cash' => 0,
                    'expense_cash' => 0,
                    'income' => 0,
                    'income_card' => 0,
                    'expense_card' => 0,
                    'expense' => 0,
                    'income_transfer' => 0,
                    'expense_transfer' => 0,
                    'total' => 0,
                ];
            }

            if ($row->type === TransactionType::INCOME) {
                $data[$hospitalId]['income'] += $row->total;

                if ($row->method === PaymentMethod::CASH) {
                    $data[$hospitalId]['income_cash'] += $row->total;
                } elseif ($row->method === PaymentMethod::CARD) {
                    $data[$hospitalId]['income_card'] += $row->total;
                } elseif ($row->method === PaymentMethod::TRANSFER) {
                    $data[$hospitalId]['income_transfer'] += $row->total;
                }
            } elseif ($row->type === TransactionType::EXPENSE) {
                $data[$hospitalId]['expense'] += $row->total;

                if ($row->method === PaymentMethod::CASH) {
                    $data[$hospitalId]['expense_cash'] += $row->total;
                } elseif ($row->method === PaymentMethod::CARD) {
                    $data[$hospitalId]['expense_card'] += $row->total;
                } elseif ($row->method === PaymentMethod::TRANSFER) {
                    $data[$hospitalId]['expense_transfer'] += $row->total;
                }
            }

            $data[$hospitalId]['total'] = $data[$row->hospital_id]['income'] - $data[$row->hospital_id]['expense'];
        }

        if (count($data) > 1) {
            $data[0] = [
                'income_cash' => 0,
                'expense_cash' => 0,
                'income' => 0,
                'income_card' => 0,
                'expense_card' => 0,
                'expense' => 0,
                'income_transfer' => 0,
                'expense_transfer' => 0,
                'total' => 0,
            ];

            foreach ($data as $index => $item) {
                if ($index === 0) {
                    continue;
                }

                $data[0]['income_cash'] += $item['income_cash'];
                $data[0]['expense_cash'] += $item['expense_cash'];
                $data[0]['income'] += $item['income'];
                $data[0]['income_card'] += $item['income_card'];
                $data[0]['expense_card'] += $item['expense_card'];
                $data[0]['expense'] += $item['expense'];
                $data[0]['income_transfer'] += $item['income_transfer'];
                $data[0]['expense_transfer'] += $item['expense_transfer'];
                $data[0]['total'] += $item['total'];
            }
        }

        if (app()->isProduction()) {
            Cache::put($cacheKey, $data, now()->addMinutes(20));
        }

        return $data;
    }

    public function patientReport(): ?array
    {
        $cacheKey = $this->getCacheKey('report:patient', true);

        if (app()->isProduction() && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $query = Patient::query();

        $this->addDateToQuery($query);

        $total = $query->clone()->count();
        $old_patient = $query->clone()->where('old', true)->count();
        $registered_own = $query->clone()->whereNull('created_by')->count();
        $per_province = $query->clone()
            ->select('province_id', DB::raw('COUNT(*) as count'))
            ->groupBy('province_id')
            ->get()
            ->toArray();
        $per_gender = $query->clone()
            ->select('gender', DB::raw('COUNT(*) as count'))
            ->groupBy('gender')
            ->get()
            ->toArray();

        $data = compact('total', 'old_patient', 'registered_own', 'per_province', 'per_gender');

        if (app()->isProduction()) {
            Cache::put($cacheKey, $data, now()->addMinutes(20));
        }

        return $data;
    }

    public function treatmentReport(): ?array
    {
        $cacheKey = $this->getCacheKey('report:treatment');

        if (app()->isProduction() && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // DOCTORS
        $per_doctor = Treatment::query()
            ->join('doctors', 'doctors.id', '=', 'treatments.doctor_id')
            ->select('treatments.hospital_id', 'doctor_id', 'doctors.full_name', 'doctors.branch', DB::raw('COUNT(*) as count'), DB::raw('SUM(`total`) as total'))
            ->groupBy('treatments.hospital_id', 'doctor_id')
            ->orderBy('treatments.hospital_id');

        if ($this->hospitalId !== null) {
            $per_doctor->where('treatments.hospital_id', $this->hospitalId);
        }

        $this->addDateToQuery($per_doctor, 'treatments');

        $per_doctor = $per_doctor->get()->groupBy('hospital_id')->toArray();

        // SERVICES
        $per_service = \App\Models\TreatmentService::query()
            ->join('services', 'services.id', '=', 'treatment_services.service_id')
            ->select('services.hospital_id', 'service_id', 'services.code', 'services.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(`treatment_services`.`price`) as total'))
            ->groupBy('services.hospital_id', 'service_id')
            ->orderBy('services.hospital_id');

        if ($this->hospitalId !== null) {
            $per_service->where('services.hospital_id', $this->hospitalId);
        }

        $this->addDateToQuery($per_service, 'treatment_services');

        $per_service = $per_service->get()->groupBy('hospital_id')->toArray();

        // PRODUCTS
        $per_product = TreatmentProduct::query()
            ->join('products', 'products.id', '=', 'treatment_products.product_id')
            ->select('product_id', 'products.code', 'products.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(`count`) as count_total'), DB::raw('SUM(`total`) as total'))
            ->groupBy('product_id');

        $this->addDateToQuery($per_product, 'treatment_products');

        $per_product = $per_product->get()->toArray();

        $hospitalIds = array_unique(array_merge(array_keys($per_doctor), array_keys($per_service)));
        sort($hospitalIds);

        $data = [];

        foreach ($hospitalIds as $hospitalId) {
            $data[$hospitalId] = [];
            if (isset($per_doctor[$hospitalId])) $data[$hospitalId]['per_doctor'] = $per_doctor[$hospitalId];
            if (isset($per_service[$hospitalId])) $data[$hospitalId]['per_service'] = $per_service[$hospitalId];
        }

        $response = [
            'reports' => $data,
            'perProductReport' => $per_product,
        ];

        if (app()->isProduction()) {
            Cache::put($cacheKey, $data, now()->addMinutes(20));
        }

        return $response;
    }

    public function appointmentReport(): ?array
    {
        $cacheKey = $this->getCacheKey('report:appointment');

        if (app()->isProduction() && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // STATE
        $per_state = Appointment::query()
            ->select('state', DB::raw('COUNT(*) as count'))
            ->groupBy('state')
            ->orderBy('state');

        if ($this->hospitalId !== null) {
            $per_state->where('appointments.hospital_id', $this->hospitalId);
        }

        $this->addDateToQuery($per_state);

        $per_state = $per_state->get()->makeHidden('type_name')->toArray();

        // USER
        $per_patient = Appointment::query()
            ->join('users', 'users.id', '=', 'appointments.user_id')
            ->where('users.type', UserType::PATIENT)
            ->whereNotNull('users.patient_id')
            ->whereNotNull('appointments.user_id');

        if ($this->hospitalId !== null) {
            $per_patient->where('appointments.hospital_id', $this->hospitalId);
        }

        $this->addDateToQuery($per_patient, 'appointments');

        $per_patient = $per_patient->count();

        // DOCTORS
        $per_doctor = Appointment::query()
            ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
            ->select('appointments.hospital_id', 'doctor_id', 'state', 'doctors.full_name', 'doctors.branch', DB::raw('COUNT(*) as count'))
            ->groupBy('appointments.hospital_id', 'doctor_id', 'state')
            ->orderBy('appointments.hospital_id')
            ->orderBy('doctor_id')
            ->orderBy('state');

        if ($this->hospitalId !== null) {
            $per_doctor->where('appointments.hospital_id', $this->hospitalId);
        }

        $this->addDateToQuery($per_doctor, 'appointments');

        $per_doctor = $per_doctor->get()
            ->makeHidden('type_name')
            ->groupBy('hospital_id')
            ->toArray();

        $data = [
            'per_state' => $per_state,
            'per_patient' => $per_patient,
            'per_doctor' => $per_doctor,
        ];

        if (app()->isProduction()) {
            Cache::put($cacheKey, $data, now()->addMinutes(20));
        }

        return $data;
    }

    private function addDateToQuery(Builder $query, ?string $table = null): void
    {
        if (!$this->entire && $this->start && $this->due) {
            $column = $table ? "{$table}.created_at" : 'created_at';
            $query->whereBetween($column, [$this->start, $this->due]);
        }
    }

    private function getCacheKey(string $prefix, bool $all = false): string
    {
        $hospital = $all || $this->hospitalId === null ? 'all' : $this->hospitalId;

        if ($this->entire) {
            $dateRange = 'entire';
        } else {
            $dateRange = "{$this->start->format('Y-m-d')}-{$this->due->format('Y-m-d')}";
        }

        return "{$prefix}:{$hospital}:{$dateRange}";
    }
}
