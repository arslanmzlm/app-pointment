<?php

namespace App\Models;

use App\Observers\TreatmentProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([TreatmentProductObserver::class])]
class TreatmentProduct extends Model
{
    protected $fillable = ['treatment_id', 'product_id', 'count', 'price', 'total'];

    protected $casts = [
        'treatment_id' => 'int',
        'product_id' => 'int',
        'count' => 'int',
        'price' => 'float',
        'total' => 'float',
    ];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
