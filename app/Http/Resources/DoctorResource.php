<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'hospital_id' => $this->hospital_id,
            'full_name' => $this->full_name,
            'branch' => $this->branch,
            'title' => $this->title,
            'resume' => $this->resume,
            'certificate' => $this->certificate,
            'avatar_url' => $this->avatar_url,
            'hospital_name' => $this->whenLoaded('hospital', function () {
                return $this->hospital->name;
            }),
        ];
    }
}
