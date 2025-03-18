<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'section' => $this->section,
            'title' => $this->title,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'top_title' => $this->top_title,
            'alt_title' => $this->alt_title,
            'link' => $this->link,
            'link_label' => $this->link_label,
            'image_url' => $this->image_url,
            'mobile_image_url' => $this->mobile_image_url,
            'image_alt' => $this->image_alt,
            'icon_url' => $this->icon_url,
            'order' => $this->order,
            'description' => $this->description,
            'body' => $this->body,
        ];
    }
}
