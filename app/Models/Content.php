<?php

namespace App\Models;

use App\Services\ContentService;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Casts\PurifyHtmlOnSet;

class Content extends Model
{
    protected $fillable = [
        'section',
        'active',
        'title',
        'slug',
        'subtitle',
        'top_title',
        'alt_title',
        'link',
        'link_label',
        'image',
        'mobile_image',
        'image_alt',
        'icon',
        'order',
        'description',
        'body',
    ];

    protected $casts = [
        'active' => 'boolean',
        'description' => PurifyHtmlOnSet::class,
        'body' => PurifyHtmlOnSet::class,
    ];

    protected $appends = ['image_src', 'mobile_image_src', 'icon_src'];

    public function getImageSrcAttribute(): ?string
    {
        return $this->image ? "/storage/" . ContentService::getImagePath($this->id) . "/{$this->image}" : null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? url($this->image_src) : null;
    }

    public function getMobileImageSrcAttribute(): ?string
    {
        return $this->mobile_image ? "/storage/" . ContentService::getImagePath($this->id) . "/{$this->mobile_image}" : null;
    }

    public function getMobileImageUrlAttribute(): ?string
    {
        return $this->mobile_image ? url($this->mobile_image_src) : null;
    }

    public function getIconSrcAttribute(): ?string
    {
        return $this->icon ? "/storage/" . ContentService::getImagePath($this->id) . "/{$this->icon}" : null;
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->icon ? url($this->icon_src) : null;
    }
}
