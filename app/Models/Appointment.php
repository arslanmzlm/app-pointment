<?php

namespace App\Models;

use App\Enums\AppointmentState;
use App\Services\AppointmentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'hospital_id',
        'doctor_id',
        'patient_id',
        'treatment_id',
        'state',
        'start_date',
        'due_date',
        'duration',
        'title',
        'note',
    ];

    protected $casts = [
        'state' => AppointmentState::class,
        'start_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    protected $appends = ['state_label'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function isActive(): bool
    {
        return in_array($this->state, AppointmentService::ACTIVE_STATES);
    }

    public function getStateLabelAttribute(): string
    {
        return trans("enums.appointment.state.{$this->state->name}");
    }
}
