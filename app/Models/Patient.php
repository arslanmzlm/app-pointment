<?php

namespace App\Models;

use App\Casts\PhoneCast;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'province_id',
        'old',
        'name',
        'surname',
        'full_name',
        'phone',
        'email',
        'gender',
        'birthday',
        'notification',
    ];

    protected $casts = [
        'old' => 'boolean',
        'phone' => PhoneCast::class,
        'gender' => Gender::class,
        'birthday' => 'date:Y-m-d',
        'notification' => 'boolean',
    ];

    protected $appends = ['gender_label'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'patient_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(PatientField::class, 'patient_id');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class, 'patient_id')->orderByDesc('id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'patient_id')->orderByDesc('id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id')->orderByDesc('start_date');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender ? trans("enums.gender.{$this->gender->name}") : trans("enums.default");
    }
}
