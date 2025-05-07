<?php

namespace App\Models;

use App\Casts\PhoneCast;
use App\Services\HospitalService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'name',
        'start_work',
        'end_work',
        'duration',
        'disabled_days',
        'title',
        'logo',
        'description',
        'owner',
        'phone',
        'email',
        'address',
        'address_link',
    ];

    protected $casts = [
        'phone' => PhoneCast::class,
        'disabled_days' => 'json',
    ];

    protected $appends = ['logo_src'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'hospital_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'hospital_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'hospital_id');
    }

    public function getLogoSrcAttribute(): ?string
    {
        return $this->logo ? "/storage/" . HospitalService::getLogoPath($this) . "/{$this->logo}" : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? url($this->logo_src) : null;
    }

    public function getPhoneNormalizedAttribute(): ?string
    {
        return $this->phone ? Str::replace(['(', ')'], '', $this->phone->formatNational()) : null;
    }
}
