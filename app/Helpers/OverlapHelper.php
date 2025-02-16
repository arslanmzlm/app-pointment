<?php

namespace App\Helpers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\PassiveDate;
use App\Services\AppointmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class OverlapHelper
{
    private int $doctor;
    private Carbon $start;
    private Carbon $due;
    private ?int $appointment = null;
    private ?int $passiveDate = null;

    public function __construct(Doctor|int $doctor, Carbon|string $startDate, Carbon|int $dueDate)
    {
        $this->doctor($doctor);
        $this->start($startDate);
        $this->due($dueDate);
    }

    public static function check(Doctor|int $doctor, Carbon|string $startDate, Carbon|int $dueDate): static
    {
        return new static($doctor, $startDate, $dueDate);
    }

    public function get(): int
    {
        if ($this->checkAppointment()) {
            return 1;
        } else if ($this->checkPassiveDate()) {
            return 2;
        } else {
            return 0;
        }
    }

    public function doctor(Doctor|int $doctor): self
    {
        $this->doctor = $doctor instanceof Doctor ? $doctor->id : $doctor;

        return $this;
    }

    public function start(Carbon|string $startDate): self
    {
        $this->start = is_string($startDate) ? Carbon::parse($startDate)->setTimezone('Europe/Istanbul')->setSecond(0) : $startDate;

        return $this;
    }

    public function due(Carbon|int $dueDate): self
    {
        $this->due = is_int($dueDate) ? $this->start->clone()->addMinutes($dueDate - 1)->setSecond(59) : $dueDate;

        return $this;
    }

    public function appointment(Appointment|int $appointment): self
    {
        $this->appointment = $appointment instanceof Appointment ? $appointment->id : $appointment;

        return $this;
    }

    public function passiveDate(PassiveDate|int $passiveDate): self
    {
        $this->passiveDate = $passiveDate instanceof PassiveDate ? $passiveDate->id : $passiveDate;

        return $this;
    }

    public function checkAppointment(): bool
    {
        $query = Appointment::query()
            ->where('doctor_id', $this->doctor)
            ->whereIn('state', AppointmentService::ACTIVE_STATES)
            ->where(function (Builder $query) {
                $query->whereBetween('start_date', [$this->start, $this->due]);
                $query->orWhereBetween('due_date', [$this->start, $this->due]);
            });

        if ($this->appointment) {
            $query->where('id', '!=', $this->appointment);
        }

        return $query->exists();
    }

    public function checkPassiveDate(): bool
    {
        $query = PassiveDate::query()
            ->where('doctor_id', $this->doctor)
            ->where(function (Builder $query) {
                $query->whereRaw("'{$this->start}' BETWEEN `start_date` AND `due_date`");
                $query->orWhereRaw("'{$this->due}' BETWEEN `start_date` AND `due_date`");
            });

        if ($this->passiveDate) {
            $query->where('id', '!=', $this->passiveDate);
        }

        return $query->exists();
    }
}
