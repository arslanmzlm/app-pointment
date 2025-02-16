<?php

namespace App\Rules;

use App\Helpers\OverlapHelper;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckAppointmentOverlap implements DataAwareRule, ValidationRule
{
    private mixed $doctorId;
    private ?int $appointmentId = null;
    private ?int $passiveDateId = null;

    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !empty($this->doctorId) && is_int($this->doctorId) && !empty($value['start_date']) && !empty($value['duration']) && is_numeric($value['duration'])
        ) {
            $helper = OverlapHelper::check($this->doctorId, $value['start_date'], $value['duration']);

            if ($this->appointmentId) {
                $helper->appointment($this->appointmentId);
            }

            if ($this->passiveDateId) {
                $helper->passiveDate($this->passiveDateId);
            }

            $result = $helper->get();

            switch ($result) {
                case 1:
                    $fail('validation.has_appointment')->translate();
                    break;
                case 2:
                    $fail('validation.has_passive_date')->translate();
                    break;
            }
        }
    }

    public function setData(array $data): self
    {
        $this->doctorId = auth()->user()->doctor_id ?? $data['doctor_id'];

        return $this;
    }

    public function appointment(int $id): self
    {
        $this->appointmentId = $id;

        return $this;
    }

    public function passiveDate(int $id): self
    {
        $this->passiveDateId = $id;

        return $this;
    }
}
