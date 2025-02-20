<?php

namespace App\Models;

use App\Casts\PhoneCast;
use App\Services\DoctorService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hospital_id',
        'name',
        'surname',
        'full_name',
        'branch',
        'avatar',
        'title',
        'phone',
        'email',
        'resume',
        'certificate',
    ];

    protected $casts = [
        'phone' => PhoneCast::class,
    ];

    protected $appends = ['avatar_src'];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'doctor_id');
    }

    public function getAvatarSrcAttribute(): ?string
    {
        return $this->avatar ? "/storage/" . DoctorService::IMAGE_PATH . "/{$this->avatar}" : null;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? url($this->avatar_src) : null;
    }
}
