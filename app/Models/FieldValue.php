<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldValue extends Model
{
    protected $fillable = ['field_id', 'value'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
