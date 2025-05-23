<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hospital_id',
        'doctor_id',
        'patient_id',
        'total',
        'note',
    ];

    protected $casts = [
        'total' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(TreatmentService::class, 'treatment_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(TreatmentProduct::class, 'treatment_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'treatment_id');
    }

    public function calculateTotal(): float
    {
        $total = 0;

        foreach ($this->services as $service) {
            if ($service->price) {
                $total += $service->price;
            }
        }

        foreach ($this->products as $product) {
            if ($product->count && $product->price) {
                $total += $product->price * $product->count;
            }
        }

        return $total;
    }
}
