<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientField extends Model
{
    protected $fillable = ['patient_id', 'field_id', 'field_value_id', 'value'];

    protected $casts = ['value' => 'json'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function fieldValue(): BelongsTo
    {
        return $this->belongsTo(FieldValue::class);
    }
}
