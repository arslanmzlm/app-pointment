<?php

namespace App\Rules;

use App\Helpers\OverlapHelper;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckAppointmentOverlap implements DataAwareRule, ValidationRule
{
    private mixed $doctor_id;
    private ?int $appointment_id = null;
    private ?int $passive_date_id = null;

    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !empty($this->doctor_id) && is_int($this->doctor_id) && !empty($value['start_date']) && !empty($value['duration']) && is_numeric($value['duration'])
        ) {
            $helper = OverlapHelper::check($this->doctor_id, $value['start_date'], $value['duration']);

            if ($this->appointment_id) {
                $helper->appointment($this->appointment_id);
            }

            if ($this->passive_date_id) {
                $helper->passiveDate($this->passive_date_id);
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
        $this->doctor_id = $data['doctor_id'] ?? auth()->user()->doctor_id;

        return $this;
    }

    public function appointment(int $id): self
    {
        $this->appointment_id = $id;

        return $this;
    }

    public function passiveDate(int $id): self
    {
        $this->passive_date_id = $id;

        return $this;
    }
}
