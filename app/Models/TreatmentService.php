<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentService extends Model
{
    protected $fillable = ['treatment_id', 'service_id', 'price'];

    protected $casts = [
        'treatment_id' => 'int',
        'service_id' => 'int',
        'price' => 'float',
    ];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
