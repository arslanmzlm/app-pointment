<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Traits\Service\HospitalQuery;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorService
{
    use HospitalQuery;

    const IMAGE_PATH = 'doctors';

    public function getAll(): Collection
    {
        $query = Doctor::query()->orderBy('full_name');

        $this->addHospitalToQuery($query);

        return $query->get();
    }

    public function getActive(): Collection
    {
        return Doctor::query()
            ->orderBy('full_name')
            ->get()
            ->load('hospital');
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Doctor::query())
            ->search('full_name', 'branch', 'title', 'phone', 'email')
            ->sort('id', 'full_name')
            ->idFilter('hospital')
            ->basicFilter('branch')
            ->hasHospital()
            ->deleted()
            ->paginate();
    }

    public function getBranches(): \Illuminate\Support\Collection
    {
        $query = Doctor::query()->groupBy('branch')->orderBy('branch');

        $this->addHospitalToQuery($query);

        return $query->get('branch')->pluck('branch')->unique();
    }

    public function getAvailableDates(Doctor $doctor): array
    {
        $hospital = $doctor->hospital;

        /*** @var $datePeriod \Carbon\Carbon[] */
        $datePeriod = CarbonPeriod::create('now', '+10 day');

        $dates = [];

        foreach ($datePeriod as $date) {
            if (!empty($hospital->disabled_days) && in_array($date->dayOfWeekIso, $hospital->disabled_days)) {
                $dates[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->day,
                    'dayLabel' => $date->shortDayName,
                    'times' => [],
                ];
            } else {
                $startHour = intval(explode(':', $hospital->start_work)[0]);
                $startMinute = intval(explode(':', $hospital->start_work)[1]);
                $endHour = intval(explode(':', $hospital->end_work)[0]);
                $endMinute = intval(explode(':', $hospital->end_work)[1]);
                $duration = $hospital->duration;
                $timePeriod = CarbonPeriod::create(
                    $date->copy()->setTime($startHour, $startMinute),
                    $date->copy()->setTime($endHour, $endMinute)->subMinutes($duration),
                    "{$duration} minutes"
                );

                $dates[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->day,
                    'dayLabel' => $date->shortDayName,
                    'times' => $this->filterAvailableTimes($doctor, $date, $timePeriod),
                ];
            }
        }

        return $dates;
    }

    public function store(array $data): ?Doctor
    {
        $doctor = new Doctor();

        if (auth()->user()->hospital_id !== null) {
            $doctor->hospital_id = auth()->user()->hospital_id;
        } else {
            $doctor->hospital_id = $data['hospital_id'];
        }

        $doctor = $this->assignAttributes($doctor, $data);

        (new UserService())->storeDoctor($doctor, [
            'username' => $data['username'],
            'password' => $data['password'],
        ]);

        return $doctor;
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        $doctor = $this->assignAttributes($doctor, $data);

        (new UserService())->updateDoctor($doctor);

        return $doctor;
    }

    public function delete(Doctor $doctor): bool
    {
        if ($doctor->user) {
            $doctor->user->delete();
        }

        return $doctor->delete();
    }

    private function filterAvailableTimes(Doctor $doctor, \Carbon\Carbon $date, CarbonPeriod $timePeriod): array
    {
        $now = now();
        $availableTimes = [];

        $appointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('start_date', $date->format('Y-m-d'))
            ->whereIn('state', AppointmentService::ACTIVE_STATES)
            ->orderBy('start_date')
            ->get();

        $bookedTimes = $appointments->map(function ($appointment) {
            return [
                'start' => Carbon::parse($appointment->start_date),
                'end' => Carbon::parse($appointment->due_date),
            ];
        });

        foreach ($timePeriod as $time) {
            if ($date->isToday() && $time->lt($now)) {
                continue;
            }

            $isAvailable = true;

            foreach ($bookedTimes as $bookedTime) {
                if ($time->between($bookedTime['start'], $bookedTime['end'])) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableTimes[] = $time->format('H:i');
            }
        }

        return $availableTimes;
    }

    private function assignAttributes(Doctor $doctor, array $data): ?Doctor
    {
        $doctor->name = $data['name'] ?? $doctor->name;
        $doctor->surname = $data['surname'] ?? $doctor->surname;
        $doctor->full_name = !empty($data['name']) && !empty($data['surname']) ? "{$data['name']} {$data['surname']}" : $doctor->full_name;
        $doctor->branch = $data['branch'] ?? $doctor->branch;
        $doctor->title = $data['title'] ?? $doctor->title;
        $doctor->phone = $data['phone'] ?? $doctor->phone;
        $doctor->email = $data['email'] ?? $doctor->email;
        $doctor->resume = $data['resume'] ?? $doctor->resume;
        $doctor->certificate = $data['certificate'] ?? $doctor->certificate;

        if (!empty($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $this->deleteAvatar($doctor);
            $doctor->avatar = $this->storeAvatar($doctor, $data['avatar']);
        }

        $doctor->save();

        return $doctor;
    }

    private function storeAvatar(Doctor $doctor, UploadedFile $avatar): string
    {
        $doctorName = Str::slug($doctor->full_name);
        $rand = rand(100, 999);
        $extension = $avatar->extension();
        $fileName = "{$doctorName}-avatar-{$rand}.{$extension}";
        $avatar->storePubliclyAs(self::IMAGE_PATH, $fileName, 'public');
        return $fileName;
    }

    private function deleteAvatar(Doctor $doctor): void
    {
        if ($doctor->avatar) {
            Storage::disk('public')->delete(self::IMAGE_PATH . "/{$doctor->avatar}");
        }
    }
}
