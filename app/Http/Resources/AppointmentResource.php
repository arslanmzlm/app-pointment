<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'hospital_name' => $this->whenLoaded('hospital', function () {
                return $this->hospital->name;
            }),
            'doctor_id' => $this->doctor_id,
            'doctor_name' => $this->whenLoaded('doctor', function () {
                return $this->doctor->full_name;
            }),
            'treatment_id' => $this->treatment_id,
            'state' => $this->state,
            'state_label' => $this->state_label,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'duration' => $this->duration,
        ];
    }
}
