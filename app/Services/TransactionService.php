<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use App\Helpers\FilterHelper;
use App\Models\Transaction;
use App\Models\Treatment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionService
{
    public static function storeByTreatment(Treatment $treatment, array $data): array
    {
        $transactions = [];

        foreach ($data as $row) {
            if (!empty($row['amount']) && $row['amount'] > 0) {
                $transaction = new Transaction();
                $transaction->type = TransactionType::INCOME;
                $transaction->method = $row['method'];
                $transaction->user_id = $treatment->user_id;
                $transaction->hospital_id = $treatment->hospital_id;
                $transaction->doctor_id = $treatment->doctor_id;
                $transaction->patient_id = $treatment->patient_id;
                $transaction->treatment_id = $treatment->id;
                $transaction->total = $row['amount'];

                $transaction->save();

                $transactions[] = $transaction;
            }
        }

        return $transactions;
    }

    public static function updateByTreatment(Treatment $treatment, array $data): array
    {
        $transactions = [];

        foreach ($data as $row) {
            $method = $row['method'];
            $amount = $row['amount'];

            $transaction = $treatment->transactions()->where('method', $method)->first();

            if (!empty($amount) && $amount > 0) {
                if (!$transaction) {
                    $transaction = new Transaction();
                    $transaction->type = TransactionType::INCOME;
                    $transaction->method = $method;
                    $transaction->user_id = $treatment->user_id;
                    $transaction->hospital_id = $treatment->hospital_id;
                    $transaction->doctor_id = $treatment->doctor_id;
                    $transaction->patient_id = $treatment->patient_id;
                    $transaction->treatment_id = $treatment->id;
                }
                $transaction->total = $amount;

                $transaction->save();

                $transactions[] = $transaction;
            } else {
                if ($transaction) {
                    $transaction->delete();
                }
            }
        }

        return $transactions;
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Transaction::query()->with(['user', 'patient']))
            ->search('id', 'total', 'description')
            ->sort('id', 'total')
            ->basicFilter('category')
            ->enum(['type' => TransactionType::class])
            ->enumMultiple(['method' => PaymentMethod::class])
            ->idFilter('hospital', 'doctor', 'patient')
            ->dateRange('created_at')
            ->hasHospital()
            ->paginate();
    }

    public function store(array $data): Transaction
    {
        $transaction = new Transaction();
        $transaction->user_id = auth()->id();
        $transaction->hospital_id = auth()->user()->hospital_id ?? $data['hospital_id'];
        $transaction->doctor_id = auth()->user()->doctor_id ?? $data['doctor_id'] ?? null;

        return $this->assignAttributes($transaction, $data);
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        return $this->assignAttributes($transaction, $data);
    }

    public function delete(Transaction $transaction): bool
    {
        return $transaction->delete();
    }

    private function assignAttributes(Transaction $transaction, array $data): Transaction
    {
        $transaction->type = $data['type'] ?? $transaction->type;
        $transaction->method = $data['method'] ?? $transaction->method;
        $transaction->patient_id = $data['patient_id'] ?? $transaction->patient_id;
        $transaction->total = $data['total'] ?? $transaction->total;
        $transaction->description = $data['description'] ?? $transaction->description;

        $transaction->save();

        return $transaction;
    }
}
