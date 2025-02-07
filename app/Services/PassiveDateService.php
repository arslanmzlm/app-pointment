<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Models\Doctor;
use App\Models\PassiveDate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class PassiveDateService
{
    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(PassiveDate::query())
            ->search('start_date', 'due_date', 'description')
            ->sort('id', 'start_date', 'due_date')
            ->dateRange()
            ->paginate();
    }

    public function getPassiveDays(Doctor|int $doctor): array
    {
        if ($doctor instanceof Doctor) {
            $doctor = $doctor->id;
        }

        $dates = [];

        $passiveDates = PassiveDate::query()
            ->where('doctor_id', $doctor)
            ->where('start_date', '>', now()->endOfDay())
            ->whereRaw('HOUR(`start_date`) = 0')
            ->whereRaw('HOUR(`due_date`) = 23')
            ->get();

        foreach ($passiveDates as $passiveDate) {
            $dates = array_merge($dates, $passiveDate->getPeriod()->toArray());
        }

        return $dates;
    }

    public function store(array $data): PassiveDate
    {
        $passiveDate = new PassiveDate();
        $passiveDate->user_id = auth()->id();
        $passiveDate->doctor_id = auth()->user()->doctor_id ?? $data['doctor_id'];
        $passiveDate->hospital_id = $passiveDate->doctor->hospital_id;

        return $this->assignAttributes($passiveDate, $data);
    }

    public function update(PassiveDate $passiveDate, array $data): PassiveDate
    {
        return $this->assignAttributes($passiveDate, $data);
    }

    public function delete(PassiveDate $passiveDate): ?bool
    {
        return $passiveDate->delete();
    }

    private function assignAttributes(PassiveDate $passiveDate, array $data): PassiveDate
    {
        $passiveDate->start_date = Carbon::parse($data['start_date'])->setTimezone('Europe/Istanbul')->setSecond(0) ?? $passiveDate->start_date;
        $passiveDate->due_date = Carbon::parse($data['due_date'])->setTimezone('Europe/Istanbul')->setSecond(0) ?? $passiveDate->due_date;
        $passiveDate->description = $data['description'] ?? $passiveDate->description;

        $passiveDate->save();

        return $passiveDate;
    }
}
