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
    public static function storeByTreatment(Treatment $treatment, PaymentMethod $method, int $hospitalId): Transaction
    {
        $transaction = new Transaction();
        $transaction->type = TransactionType::INCOME;
        $transaction->method = $method;
        $transaction->user_id = $treatment->user_id;
        $transaction->hospital_id = $hospitalId;
        $transaction->doctor_id = $treatment->doctor_id;
        $transaction->patient_id = $treatment->patient_id;
        $transaction->treatment_id = $treatment->id;
        $transaction->total = $treatment->total;

        $transaction->save();

        return $transaction;
    }

    public static function updateByTreatment(Treatment $treatment, PaymentMethod $method): ?Transaction
    {
        if ($transaction = $treatment->transaction) {
            $transaction->total = $treatment->total;
            $transaction->method = $method;

            $transaction->save();

            return $transaction;
        }

        return null;
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
