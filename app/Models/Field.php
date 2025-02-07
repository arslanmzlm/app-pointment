<?php

namespace App\Models;

use App\Enums\FieldInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    protected $fillable = [
        'active',
        'name',
        'input',
        'description',
        'order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'input' => FieldInput::class,
    ];
    protected $appends = ['input_label'];

    public function values(): HasMany
    {
        return $this->hasMany(FieldValue::class);
    }

    public function hasValues(): bool
    {
        return in_array($this->input, FieldInput::hasValues());
    }

    public function getInputLabelAttribute(): string
    {
        return trans("enums.field.input.{$this->input->name}");
    }
}
