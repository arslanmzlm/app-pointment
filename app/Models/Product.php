<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stevebauman\Purify\Casts\PurifyHtmlOnSet;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'active',
        'category',
        'brand',
        'name',
        'slug',
        'code',
        'price',
        'description',
    ];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'float',
        'description' => PurifyHtmlOnSet::class,
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'product_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('order');
    }
}
