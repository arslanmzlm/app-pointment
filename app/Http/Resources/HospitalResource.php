<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalResource extends JsonResource
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
            'province_id' => $this->province_id,
            'province_name' => $this->whenLoaded('province', function () {
                return $this->province->name;
            }),
            'name' => $this->name,
            'start_work' => $this->start_work,
            'end_work' => $this->end_work,
            'title' => $this->title,
            'logo_url' => $this->logo_url,
            'description' => $this->description,
            'owner' => $this->owner,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'address_link' => $this->address_link,
        ];
    }
}
