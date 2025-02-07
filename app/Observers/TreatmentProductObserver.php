<?php

namespace App\Observers;

use App\Models\TreatmentProduct;
use App\Services\ProductService;

class TreatmentProductObserver
{
    /**
     * Handle the TreatmentProduct "created" event.
     */
    public function created(TreatmentProduct $treatmentProduct): void
    {
        ProductService::decrementStock($treatmentProduct->product_id, $treatmentProduct->count);
    }

    /**
     * Handle the TreatmentProduct "deleted" event.
     */
    public function deleted(TreatmentProduct $treatmentProduct): void
    {
        ProductService::incrementStock($treatmentProduct->product_id, $treatmentProduct->count);
    }
}
