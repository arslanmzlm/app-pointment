<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    protected $fillable = ['hospital_id', 'product_id', 'stock'];

    protected $casts = [
        'hospital_id' => 'int',
        'product_id' => 'int',
    ];

    protected $appends = ['hospital_name'];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getHospitalNameAttribute()
    {
        return $this->hospital->name;
    }
}
