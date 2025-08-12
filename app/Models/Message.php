<?php

namespace App\Models;

use App\Enums\MessageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'patient_id',
        'user_id',
        'type',
        'phone',
        'message',
        'job_id',
    ];

    protected $casts = [
        'patient_id' => 'int',
        'user_id' => 'int',
        'type' => MessageType::class,
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
