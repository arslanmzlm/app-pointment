<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function hospitals(): HasMany
    {
        return $this->hasMany(Hospital::class, 'province_id');
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'province_id');
    }
}
