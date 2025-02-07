<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\Treatment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportService
{
    private Carbon $start;
    private Carbon $due;

    public function __construct()
    {
        $this->start = now()->startOfMonth();
        $this->due = now()->endOfMonth();

        if (request()->has('start_date') && request()->has('due_date')) {
            try {
                $this->start = request()->date('start_date');
                $this->start->setTimezone('Europe/Istanbul');
                $this->start->startOfDay();

                $this->due = request()->date('due_date');
                $this->due->setTimezone('Europe/Istanbul');
                $this->due->endOfDay();
            } catch (\Exception|\Error $exception) {
                $this->start = now()->startOfMonth();
                $this->due = now()->endOfMonth();
            }
        }
    }

    public function transactionReport(): array
    {
        $hospital_id = auth()->user()->hospital_id;

        $dateRange = "{$this->start->format('Y-m-d')}-{$this->due->format('Y-m-d')}";

        $cacheKey = $hospital_id !== null ?
            "report:transaction:{$hospital_id}:{$dateRange}" :
            "report:transaction:all:{$dateRange}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $query = Transaction::query()
            ->select(['hospital_id', 'type', 'method', DB::raw('SUM(total) as total')])
            ->groupBy(['hospital_id', 'type', 'method'])
            ->orderBy('hospital_id')
            ->orderBy('type')
            ->orderBy('method')
            ->whereBetween('created_at', [$this->start, $this->due]);

        if ($hospital_id !== null) {
            $query->where('hospital_id', $hospital_id);
        }

        $rows = $query->get();
        $data = [];

        foreach ($rows as $row) {
            $hospital_id = $row->hospital_id;

            if (!key_exists($hospital_id, $data)) {
                $data[$hospital_id] = [
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
                $data[$hospital_id]['income'] += $row->total;

                if ($row->method === PaymentMethod::CASH) {
                    $data[$hospital_id]['income_cash'] += $row->total;
                } elseif ($row->method === PaymentMethod::CARD) {
                    $data[$hospital_id]['income_card'] += $row->total;
                } elseif ($row->method === PaymentMethod::TRANSFER) {
                    $data[$hospital_id]['income_transfer'] += $row->total;
                }
            } elseif ($row->type === TransactionType::EXPENSE) {
                $data[$hospital_id]['expense'] += $row->total;

                if ($row->method === PaymentMethod::CASH) {
                    $data[$hospital_id]['expense_cash'] += $row->total;
                } elseif ($row->method === PaymentMethod::CARD) {
                    $data[$hospital_id]['expense_card'] += $row->total;
                } elseif ($row->method === PaymentMethod::TRANSFER) {
                    $data[$hospital_id]['expense_transfer'] += $row->total;
                }
            }

            $data[$hospital_id]['total'] = $data[$row->hospital_id]['income'] - $data[$row->hospital_id]['expense'];
        }

        Cache::put($cacheKey, $data, now()->addMinutes(30));

        return $data;
    }

    public function patientReport(): array
    {
        $hospital_id = auth()->user()->hospital_id;

        $dateRange = "{$this->start->format('Y-m-d')}-{$this->due->format('Y-m-d')}";

        $cacheKey = $hospital_id !== null ?
            "report:patient:{$hospital_id}:{$dateRange}" :
            "report:patient:all:{$dateRange}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $baseQuery = Patient::query();

        $total_count = $baseQuery->clone()->count();

        $baseQuery->whereBetween('created_at', [$this->start, $this->due]);

        $new_count = $baseQuery->clone()->count();
        $per_province = $baseQuery->clone()
            ->select('province_id', DB::raw('count(*) as total'))
            ->groupBy('province_id')
            ->get()
            ->toArray();
        $old_patient_count = $baseQuery->clone()->where('old', true)->count();
        $registered_own = $baseQuery->clone()->whereNull('created_by')->count();
        $per_gender = $baseQuery->clone()
            ->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->toArray();

        dd($total_count, $new_count, $per_province, $old_patient_count, $registered_own, $per_gender);
        $data = [];

//        Cache::put($cacheKey, $data, now()->addMinutes(30));

        return $data;
    }

    public function treatmentReport(): array
    {
        $hospital_id = auth()->user()->hospital_id;

        $dateRange = "{$this->start->format('Y-m-d')}-{$this->due->format('Y-m-d')}";

        $cacheKey = $hospital_id !== null ?
            "report:treatment:{$hospital_id}:{$dateRange}" :
            "report:treatment:all:{$dateRange}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $baseQuery = Treatment::query();
        if ($hospital_id !== null) {
            $baseQuery->where('hospital_id', $hospital_id);
        }

        $baseQuery->whereBetween('created_at', [$this->start, $this->due]);

        $new_count = $baseQuery->clone()->count();
        $per_doctor = $baseQuery->clone()
            ->select('doctor_id', DB::raw('count(*) as total'))
            ->groupBy('doctor_id')
            ->get()
            ->toArray();

        dd($new_count, $per_doctor);
        $data = [];

//        Cache::put($cacheKey, $data, now()->addMinutes(30));

        return $data;
    }
}
