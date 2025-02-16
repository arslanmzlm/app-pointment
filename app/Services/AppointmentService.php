<?php

namespace App\Services;

use App\Enums\AppointmentState;
use App\Helpers\FilterHelper;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\PassiveDate;
use App\Models\Patient;
use App\Models\Treatment;
use App\Traits\Service\HospitalQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AppointmentService
{
    use HospitalQuery;

    const ACTIVE_STATES = [
        AppointmentState::PENDING,
        AppointmentState::CONFIRMED,
        AppointmentState::RESCHEDULED
    ];

    public static function previewDates(Doctor|int $doctor, array $dates): array|\Illuminate\Support\Collection
    {
        if (count($dates)) {
            if ($doctor instanceof Doctor) {
                $doctor = $doctor->id;
            }

            $checks = [];

            foreach ($dates as $date) {
                $startDate = Carbon::parse($date)->setTimezone('Europe/Istanbul')->setHour(0)->setMinute(0)->setSecond(0);
                $dueDate = $startDate->clone()->setHour(23)->setMinute(59)->setSecond(59);

                $checks[] = [
                    'start_date' => $startDate,
                    'due_date' => $dueDate,
                ];
            }

            $checks = collect($checks)->unique('start_date')->toArray();

            $dates = collect();

            $appointments = Appointment::query()
                ->where('doctor_id', $doctor)
                ->where('start_date', '>=', today())
                ->whereIn('state', self::ACTIVE_STATES)
                ->where(function (Builder $query) use ($checks) {
                    foreach ($checks as $index => $check) {
                        if ($index === 0) {
                            $query->whereBetween('start_date', [$check['start_date'], $check['due_date']]);
                        } else {
                            $query->orWhereBetween('start_date', [$check['start_date'], $check['due_date']]);
                        }
                    }
                })
                ->orderBy('start_date')
                ->get();

            foreach ($appointments as $appointment) {
                $dates->push([
                    'type' => 'appointment',
                    'start_date' => $appointment->start_date,
                    'due_date' => $appointment->due_date,
                ]);
            }

            $passiveDates = PassiveDate::query()
                ->where('doctor_id', $doctor)
                ->where('start_date', '>=', today())
                ->where(function (Builder $query) use ($checks) {
                    foreach ($checks as $index => $check) {
                        if ($index === 0) {
                            $query->whereRaw("'{$check['start_date']->format('Y-m-d')}' BETWEEN `start_date` AND `due_date`");
                        } else {
                            $query->orWhereRaw("'{$check['start_date']->format('Y-m-d')}' BETWEEN `start_date` AND `due_date`");
                        }
                    }
                })
                ->orderBy('start_date')
                ->get();

            foreach ($passiveDates as $passiveDate) {
                $dates->push([
                    'type' => 'passive_date',
                    'start_date' => $passiveDate->start_date,
                    'due_date' => $passiveDate->due_date,
                ]);
            }

            return $dates;
        }

        return [];
    }

    public static function getActiveByDoctor(Doctor|int $doctor): ?Appointment
    {
        if ($doctor instanceof Doctor) {
            $doctor = $doctor->id;
        }

        return Appointment::query()
            ->with('patient')
            ->where('doctor_id', $doctor)
            ->whereIn('state', self::ACTIVE_STATES)
            ->whereBetween('due_date', [now(), now()->endOfDay()])
            ->orderBy('start_date')
            ->first();
    }

    public function getActiveByPatient(Patient|int $patient): Collection
    {
        if ($patient instanceof Patient) {
            $patient = $patient->id;
        }

        return Appointment::query()
            ->with('doctor')
            ->whereIn('state', self::ACTIVE_STATES)
            ->where('patient_id', $patient)
            ->get();
    }

    public function filter(): LengthAwarePaginator
    {
        $query = Appointment::query()->with('patient');

        return FilterHelper::filter($query)
            ->search('title', 'note')
            ->sort('start_date', 'due_date', 'duration')
            ->idFilter('hospital', 'doctor', 'patient')
            ->enumMultiple(['state' => AppointmentState::class])
            ->dateRange()
            ->paginate();
    }

    public function dateRange(): Collection
    {
        $query = Appointment::query()->with('patient');

        return FilterHelper::filter($query)
            ->dateRange(default: 0)
            ->query()
            ->get();
    }

    public function today(): Collection
    {
        $query = Appointment::query()->with('patient');

        if (!auth()->user()->hasHospital()) {
            $query->with('hospital');
        }

        if (!auth()->user()->isDoctor()) {
            $query->with('doctor');
            $startDate = now()->startOfDay();
        } else {
            $startDate = now();
        }

        $this->addHospitalToQuery($query);
        $this->addDoctorToQuery($query);

        $query->whereBetween('due_date', [$startDate, now()->endOfDay()]);
        $query->orderBy('start_date');

        return $query->get();
    }

    public function storeMany(int $doctorId, int $patientId, array $data): void
    {
        $data = collect($data)->sortBy('start_date');

        $data->each(function ($item) use ($doctorId, $patientId) {
            $this->store(array_merge($item, ['doctor_id' => $doctorId, 'patient_id' => $patientId]));
        });
    }

    public function store(array $data): Appointment
    {
        $appointment = new Appointment();
        $appointment->user_id = auth()->id();
        $appointment->appointment_type_id = $data['appointment_type_id'];
        $appointment->doctor_id = $data['doctor_id'];
        $appointment->hospital_id = $appointment->doctor->hospital_id;
        $appointment->patient_id = $data['patient_id'];
        $appointment->state = AppointmentState::CONFIRMED;
        $appointment->start_date = Carbon::parse($data['start_date'])->setTimezone('Europe/Istanbul')->setSecond(0);
        $appointment->due_date = $appointment->start_date->clone()->addMinutes($data['duration'] - 1)->setSecond(59);
        $appointment->duration = $data['duration'];
        $appointment->title = $data['title'] ?? null;
        $appointment->note = $data['note'] ?? null;

        $appointment->save();

        return $appointment;
    }

    public function update(Appointment $appointment, array $data): Appointment
    {
        if ($appointment->isActive() && $data['start_date']) {
            $startDate = Carbon::parse($data['start_date'])->setTimezone('Europe/Istanbul')->setSecond(0);

            if (!$startDate->equalTo($appointment->start_date) || $data['duration'] !== $appointment->duration) {
                $appointment->start_date = Carbon::parse($data['start_date'])->setTimezone('Europe/Istanbul')->setSecond(0);
                $appointment->due_date = $appointment->start_date->clone()->addMinutes($data['duration'] - 1)->setSecond(59);
                $appointment->state = AppointmentState::RESCHEDULED;
                $appointment->duration = $data['duration'];
            }
        }

        $appointment->appointment_type_id = $data['appointment_type_id'];
        $appointment->title = $data['title'] ?? $appointment->title;
        $appointment->note = $data['note'] ?? $appointment->note;

        $appointment->save();

        return $appointment;
    }

    public function delete(Appointment $appointment): bool
    {
        return $appointment->delete();
    }

    public function completeByTreatment(Appointment $appointment, Treatment|int $treatment): void
    {
        if ($treatment instanceof Treatment) {
            $treatment = $treatment->id;
        }

        $appointment->state = AppointmentState::COMPLETED;
        $appointment->treatment_id = $treatment;
        $appointment->save();
    }

    public function cancel(Appointment $appointment): void
    {
        $appointment->state = AppointmentState::CANCELED;
        $appointment->save();
    }
}
