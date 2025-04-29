<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Helpers\FilterHelper;
use App\Models\Treatment;
use App\Traits\Service\HospitalQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TreatmentService
{
    use HospitalQuery;

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Treatment::query()->with(['doctor', 'patient', 'services.service', 'products.product']))
            ->search('total', 'note')
            ->sort('id')
            ->idFilter('patient')
            ->hasHospital()
            ->paginate();
    }

    public function today(): Collection
    {
        $query = Treatment::query()->with('patient');

        if (!auth()->user()->hasHospital()) {
            $query->with('hospital');
        }

        if (!auth()->user()->isDoctor()) {
            $query->with('doctor');
        }

        $this->addHospitalToQuery($query);
        $this->addDoctorToQuery($query);

        $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
        $query->orderBy('created_at');

        return $query->get();
    }

    public function store(array $data): Treatment
    {
        $treatment = new Treatment();
        $treatment->user_id = auth()->id();
        $treatment->doctor_id = $data['doctor_id'];
        $treatment->hospital_id = $treatment->doctor->hospital_id;
        $treatment->patient_id = $data['patient_id'];
        $treatment->note = $data['note'] ?? null;
        $treatment->total = $this->calculateTotalFromData($data);

        $treatment->save();

        if (isset($data['services']) && is_array($data['services'])) $this->storeServices($treatment, $data['services']);

        if (isset($data['products']) && is_array($data['products'])) $this->storeProducts($treatment, $data['products']);

        TransactionService::storeByTreatment($treatment, PaymentMethod::from($data['payment_method']), $treatment->doctor->hospital_id);

        return $treatment;
    }

    public function update(Treatment $treatment, array $data): Treatment
    {
        $treatment->note = $data['note'] ?? null;
        $treatment->total = $this->calculateTotalFromData($data);

        $treatment->save();

        if (isset($data['services']) && is_array($data['services'])) $this->updateServices($treatment, $data['services']);

        if (isset($data['products']) && is_array($data['products'])) $this->updateProducts($treatment, $data['products']);

        TransactionService::updateByTreatment($treatment, PaymentMethod::from($data['payment_method']));

        return $treatment;
    }

    public function delete(Treatment $treatment): bool
    {
        return $treatment->delete();
    }

    private function calculateTotalFromData(array $data): float|int
    {
        $total = 0;

        if (isset($data['services']) && is_array($data['services'])) {
            foreach ($data['services'] as $item) {
                if (!empty($item['price'])) {
                    $total += floatval($item['price']);
                }
            }
        }

        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $item) {
                if (!empty($item['count']) && !empty($item['price'])) {
                    $total += intval($item['count']) * floatval($item['price']);
                }
            }
        }

        return $total;
    }

    private function storeServices(Treatment $treatment, array $values): void
    {
        foreach ($values as $item) {
            $treatment->services()->create([
                'service_id' => $item['service_id'],
                'price' => $item['price'],
            ]);
        }
    }

    private function updateServices(Treatment $treatment, array $values): void
    {
        if (empty($values)) {
            $treatment->services()->delete();
        } else {
            $services = $treatment->services;

            $ids = collect($values)->whereNotNull('id')->pluck('id')->toArray();
            $treatment->services()->whereNotIn('id', $ids)->delete();

            foreach ($values as $item) {
                if ($service = $services->find($item['id'])) {
                    $service->price = $item['price'];

                    $service->save();
                } else {
                    $treatment->services()->create([
                        'service_id' => $item['service_id'],
                        'price' => $item['price']
                    ]);
                }
            }
        }
    }

    private function storeProducts(Treatment $treatment, array $values): void
    {
        foreach ($values as $item) {
            $treatment->products()->create([
                'product_id' => $item['product_id'],
                'count' => $item['count'],
                'price' => $item['price'],
                'total' => $item['count'] * $item['price'],
            ]);
        }
    }

    private function updateProducts(Treatment $treatment, array $values): void
    {
        if (empty($values)) {
            $treatment->products()->delete();
        } else {
            $products = $treatment->products;

            $ids = collect($values)->whereNotNull('id')->pluck('id')->toArray();
            $treatment->products()->whereNotIn('id', $ids)->delete();

            foreach ($values as $item) {
                if ($product = $products->find($item['id'])) {
                    $count = intval($item['count']);

                    if ($product->count !== $count) {
                        if ($product->count - $item['count'] > 0) {
                            ProductService::incrementStock($treatment->hospital_id, $product->product_id, $product->count - $count);
                        } else {
                            ProductService::decrementStock($treatment->hospital_id, $product->product_id, $count - $product->count);
                        }
                    }

                    $product->count = $count;
                    $product->price = $item['price'];
                    $product->total = $product->count * $product->price;
                    $product->save();
                } else {
                    $treatment->products()->create([
                        'count' => $item['count'],
                        'price' => $item['price'],
                        'total' => $item['count'] * $item['price'],
                    ]);
                }
            }
        }
    }
}
