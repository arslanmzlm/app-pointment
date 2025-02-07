<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\HospitalService;
use App\Services\TransactionService;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService, private HospitalService $hospitalService)
    {
    }

    public function list()
    {
        $data = [
            'transactions' => $this->transactionService->filter(),
            'transactionTypes' => TransactionType::getAll(),
            'paymentMethods' => PaymentMethod::getAll(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Transaction/List', $data);
    }

    public function create()
    {
        $data = [
            'transactionTypes' => TransactionType::getAll(),
            'paymentMethods' => PaymentMethod::getAll(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/Transaction/Create', $data);
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->transactionService->store($request->validated());

        session()->flash('toast.success', trans('messages.transaction.created'));

        return to_route('dashboard.transaction.list');
    }

    public function edit(Transaction $transaction)
    {
        return Inertia::render('Dashboard/Transaction/Edit', [
            'transaction' => $transaction,
            'transactionTypes' => TransactionType::getAll(),
            'paymentMethods' => PaymentMethod::getAll(),
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->transactionService->update($transaction, $request->validated());

        session()->flash('toast.success', trans('messages.transaction.updated'));

        return to_route('dashboard.transaction.list');
    }

    public function destroy(Transaction $transaction)
    {
        $this->transactionService->delete($transaction);

        session()->flash('toast.success', trans('messages.transaction.deleted'));
    }
}
