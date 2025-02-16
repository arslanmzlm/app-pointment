<?php

namespace App\Models;

use App\Services\ProductService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'file', 'order'];

    protected $appends = ['file_src'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFileSrcAttribute(): ?string
    {
        return $this->file ? "/storage/" . ProductService::IMAGE_PATH . "/{$this->file}" : null;
    }
}
