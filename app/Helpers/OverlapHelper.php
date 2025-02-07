<?php /** @noinspection ALL */

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
    private ?int $passive_date = null;

    public function __construct(Doctor|int $doctor, Carbon|string $start_date, Carbon|int $due_date)
    {
        $this->doctor($doctor);
        $this->start($start_date);
        $this->due($due_date);
    }

    public static function check(Doctor|int $doctor, Carbon|string $start_date, Carbon|int $due_date): static
    {
        return new static($doctor, $start_date, $due_date);
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

    public function start(Carbon|string $start_date): self
    {
        $this->start = is_string($start_date) ? Carbon::parse($start_date)->setTimezone('Europe/Istanbul')->setSecond(0) : $start_date;

        return $this;
    }

    public function due(Carbon|int $due_date): self
    {
        $this->due = is_int($due_date) ? $this->start->clone()->addMinutes($due_date - 1)->setSecond(59) : $due_date;

        return $this;
    }

    public function appointment(Appointment|int $appointment): self
    {
        $this->appointment = $appointment instanceof Appointment ? $appointment->id : $appointment;

        return $this;
    }

    public function passiveDate(PassiveDate|int $passiveDate): self
    {
        $this->passive_date = $passiveDate instanceof PassiveDate ? $passiveDate->id : $passiveDate;

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
                $query->whereBetween('start_date', [$this->start, $this->due]);
                $query->orWhereBetween('due_date', [$this->start, $this->due]);
            });

        if ($this->passive_date) {
            $query->where('id', '!=', $this->passive_date);
        }

        return $query->exists();
    }
}
