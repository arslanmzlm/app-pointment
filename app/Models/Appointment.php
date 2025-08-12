<?php

namespace App\Models;

use App\Enums\AppointmentState;
use App\Services\AppointmentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_type_id',
        'user_id',
        'hospital_id',
        'doctor_id',
        'patient_id',
        'treatment_id',
        'state',
        'start_date',
        'due_date',
        'duration',
        'note',
        'service_id',
    ];

    protected $casts = [
        'appointment_type_id' => 'int',
        'user_id' => 'int',
        'hospital_id' => 'int',
        'doctor_id' => 'int',
        'patient_id' => 'int',
        'treatment_id' => 'int',
        'service_id' => 'int',
        'state' => AppointmentState::class,
        'start_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    protected $appends = ['type_name', 'state_label'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type_id');
    }

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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function isActive(): bool
    {
        return in_array($this->state, AppointmentService::ACTIVE_STATES);
    }

    public function getTypeNameAttribute()
    {
        return $this->type->name;
    }

    public function getStateLabelAttribute(): string
    {
        return trans("enums.appointment.state.{$this->state->name}");
    }
}
