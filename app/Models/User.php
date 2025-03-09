<?php

namespace App\Models;

use App\Casts\PhoneCast;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'type',
        'hospital_id',
        'doctor_id',
        'patient_id',
        'username',
        'name',
        'phone',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'type' => UserType::class,
        'phone' => PhoneCast::class,
    ];

    protected $appends = ['type_label'];

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

    public function checkHospital(int $hospitalId): bool
    {
        if ($this->isDoctor()) {
            return $this->hospital_id === $hospitalId;
        } else if ($this->isAdmin()) {
            return $this->hospital_id === null || $this->hospital_id === $hospitalId;
        }

        return false;
    }

    public function isPatient(): bool
    {
        return $this->type === UserType::PATIENT && $this->patient_id !== null;
    }

    public function isDoctor(): bool
    {
        return $this->type === UserType::DOCTOR || $this->doctor_id !== null;
    }

    public function isAdmin(): bool
    {
        return $this->type === UserType::ADMIN && $this->doctor_id === null;
    }

    public function hasHospital(): bool
    {
        return $this->hospital_id !== null;
    }

    public function getTypeLabelAttribute(): string
    {
        return trans("enums.user.type.{$this->type->name}");
    }
}
