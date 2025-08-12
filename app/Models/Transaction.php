<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'method',
        'total',
        'user_id',
        'doctor_id',
        'patient_id',
        'treatment_id',
        'description',
    ];

    protected $casts = [
        'user_id' => 'int',
        'doctor_id' => 'int',
        'patient_id' => 'int',
        'treatment_id' => 'int',
        'type' => TransactionType::class,
        'method' => PaymentMethod::class,
        'total' => 'float',
    ];

    protected $appends = ['type_label', 'method_label'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return trans("enums.transaction.type.{$this->type->name}");
    }

    public function getMethodLabelAttribute(): string
    {
        return trans("enums.payment.method.{$this->method->name}");
    }
}
