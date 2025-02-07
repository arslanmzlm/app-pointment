<?php

namespace App\Services;

use App\Enums\FieldInput;
use App\Enums\Gender;
use App\Helpers\FilterHelper;
use App\Models\Field;
use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class PatientService
{
    public function search(): Collection
    {
        $query = Patient::query()->orderBy('full_name');

        $this->relationFilter($query);

        $q = request('search');
        if ($q) {
            $like = "%{$q}%";
            $query->whereLike('full_name', $like);
            $query->orWhereLike('phone', $like);
            $query->orWhereLike('email', $like);
        }

        return $query->get();
    }

    public function filter(): LengthAwarePaginator
    {
        $query = Patient::query();

        $this->relationFilter($query);

        return FilterHelper::filter($query)
            ->search('full_name', 'phone', 'email')
            ->sort('id', 'full_name')
            ->idFilter('province')
            ->bool('old', 'notification')
            ->date(['birthday' => 'Y-m-d'])
            ->enum(['gender' => Gender::class])
            ->paginate();
    }

    public function store(array $data): ?Patient
    {
        $patient = new Patient();
        $patient->created_by = auth()->id();

        $patient = $this->assignAttributes($patient, $data);

        if (!empty($data['fields'])) $this->updateFields($patient, $data['fields']);

        (new UserService())->storeOrUpdatePatient($patient);

        return $patient;
    }

    public function update(Patient $patient, array $data): Patient
    {
        $patient = $this->assignAttributes($patient, $data);

        if (!empty($data['fields'])) $this->updateFields($patient, $data['fields']);

        (new UserService())->storeOrUpdatePatient($patient);

        return $patient;
    }

    public function delete(Patient $patient): bool
    {
        return $patient->delete();
    }

    private function assignAttributes(Patient $patient, array $data): ?Patient
    {
        $patient->old = $data['old'] ?? $patient->old ?? false;
        $patient->province_id = $data['province_id'] ?? $patient->province_id;
        $patient->name = $data['name'] ?? $patient->name;
        $patient->surname = $data['surname'] ?? $patient->surname;
        $patient->full_name = !empty($data['name']) && !empty($data['surname']) ? "{$data['name']} {$data['surname']}" : $patient->full_name;
        $patient->phone = $data['phone'] ?? $patient->phone;
        $patient->email = $data['email'] ?? $patient->email;
        $patient->gender = $data['gender'] ?? $patient->gender;
        $patient->birthday = $data['birthday'] ? Carbon::parse($data['birthday'])->setTimezone('Europe/Istanbul') : $patient->birthday;
        $patient->notification = $data['notification'] ?? $patient->notification ?? true;

        $patient->save();

        return $patient;
    }

    private function updateFields(Patient $patient, array $values): void
    {
        $fields = Field::all()->load('values');
        $patientFields = $patient->fields;

        foreach ($values as $row) {
            $id = $row['id'];
            $field_id = $row['field_id'];
            $value = $row['value'] ?? null;

            if (empty($value) && !empty($id)) {
                $patientFields->find($id)->delete();
            } else if (!empty($value)) {
                $field = $fields->find($field_id);

                $data = [
                    'field_id' => $field->id
                ];

                if ($field->hasValues() && !is_array($value) && $fieldValue = $field->values->find($value)) {
                    $data['field_value_id'] = $fieldValue->id;
                } else if ($field->input === FieldInput::NUMBER) {
                    $data['value'] = intval($value);
                } else if ($field->input === FieldInput::DATE) {
                    $data['value'] = Carbon::parse($value)->setTimezone('Europe/Istanbul')->format('Y-m-d');
                } else {
                    $data['value'] = $value;
                }

                if ($patientField = $patientFields->find($row['id'])) {
                    $patientField->update($data);
                } else {
                    $patient->fields()->create($data);
                }
            }
        }
    }

    private function relationFilter(\Illuminate\Database\Eloquent\Builder $query): void
    {
        if (auth()->user()->isDoctor()) {
            $query->where(function ($query) {
                $query->whereIn('id', function ($query) {
                    $query->select('patient_id')
                        ->from('appointments')
                        ->where('doctor_id', auth()->user()->doctor_id);
                });
                $query->orWhereIn('id', function ($query) {
                    $query->select('patient_id')
                        ->from('transactions')
                        ->where('doctor_id', auth()->user()->doctor_id);
                });
                $query->orWhere('created_by', auth()->id());
            });
        } else if (auth()->user()->isAdmin() && auth()->user()->hasHospital()) {
            $query->where(function ($query) {
                $query->whereIn('id', function ($query) {
                    $query->select('patient_id')
                        ->from('appointments')
                        ->where('hospital_id', auth()->user()->hospital_id);
                });
                $query->orWhereIn('id', function ($query) {
                    $query->select('patient_id')
                        ->from('transactions')
                        ->where('hospital_id', auth()->user()->hospital_id);
                });
                $query->orWhere('created_by', auth()->id());
            });
        }
    }
}
