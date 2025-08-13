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

        $q = request('search');
        if ($q) {
            $like = "%{$q}%";
            $query->whereLike('full_name', $like);
            $query->orWhereLike('phone', $like);
            $query->orWhereLike('email', $like);

            if (is_numeric($q)) $query->where('id', intval($q));
        }

        return $query->limit(100)->get();
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

    public function today(): Collection
    {
        return Patient::query()
            ->whereNull('created_by')
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->orderBy('created_at')
            ->get();
    }

    public function store(array $data): ?Patient
    {
        $patient = new Patient();
        $patient->old = $data['old'] ?? false;
        $patient->province_id = $data['province_id'] ?? null;
        $patient->name = $data['name'];
        $patient->surname = $data['surname'];
        $patient->full_name = "{$data['name']} {$data['surname']}";
        $patient->phone = $data['phone'] ?? null;
        $patient->email = $data['email'] ?? null;
        $patient->gender = $data['gender'] ?? null;
        $patient->birthday = !empty($data['birthday']) ? Carbon::parse($data['birthday'])->setTimezone('Europe/Istanbul') : $patient->birthday;
        $patient->notification = $data['notification'] ?? true;
        $patient->created_by = auth()->id();
        $patient->contact_phone = $data['contact_phone'] ?? null;

        $patient->save();

        if (!empty($data['fields'])) $this->updateFields($patient, $data['fields']);

        (new UserService())->storeOrUpdatePatient($patient);

        return $patient;
    }

    public function register(array $data): ?Patient
    {
        $patient = new Patient();
        $patient->old = false;
        $patient->name = $data['name'];
        $patient->surname = $data['surname'];
        $patient->full_name = "{$data['name']} {$data['surname']}";
        $patient->phone = $data['phone'];
        $patient->notification = true;

        $patient->save();

        (new UserService())->storeOrUpdatePatient($patient, $data['password']);

        return $patient;
    }

    public function update(Patient $patient, array $data): Patient
    {
        $patient->old = $data['old'] ?? $patient->old ?? false;
        $patient->province_id = $data['province_id'] ?? $patient->province_id;
        $patient->name = $data['name'] ?? $patient->name;
        $patient->surname = $data['surname'] ?? $patient->surname;
        $patient->full_name = !empty($data['name']) && !empty($data['surname']) ? "{$data['name']} {$data['surname']}" : $patient->full_name;
        $patient->phone = $data['phone'] ?? $patient->phone;
        $patient->email = $data['email'] ?? $patient->email;
        $patient->gender = $data['gender'] ?? $patient->gender;
        $patient->birthday = !empty($data['birthday']) ? Carbon::parse($data['birthday'])->setTimezone('Europe/Istanbul') : $patient->birthday;
        $patient->notification = $data['notification'] ?? $patient->notification ?? true;
        $patient->contact_phone = $data['contact_phone'] ?? $patient->contact_phone;

        $patient->save();

        if (!empty($data['fields'])) $this->updateFields($patient, $data['fields']);

        (new UserService())->storeOrUpdatePatient($patient);

        return $patient;
    }

    public function delete(Patient $patient): bool
    {
        if ($patient->user()->exists()) {
            (new UserService())->forceDelete($patient->user);
        }

        return $patient->forceDelete();
    }

    private function updateFields(Patient $patient, array $values): void
    {
        $fields = Field::all()->load('values');
        $patientFields = $patient->fields;

        foreach ($values as $row) {
            $id = $row['id'];
            $fieldId = $row['field_id'];
            $value = $row['value'] ?? null;

            if (empty($value) && !empty($id)) {
                $patientFields->find($id)->delete();
            } else if (!empty($value)) {
                $field = $fields->find($fieldId);

                $data = [
                    'field_id' => $field->id
                ];

                if ($field->input === FieldInput::RADIO_TEXT) {
                    $description = $value['description'] ? trim($value['description']) : null;
                    $data['value'] = !empty($description) ? $description : null;
                    if (!empty($value['selection']) && $fieldValue = $field->values->find($value['selection'])) {
                        $data['field_value_id'] = $fieldValue->id;
                    } else {
                        $data['field_value_id'] = null;
                    }
                } else if ($field->hasValues() && !is_array($value) && $fieldValue = $field->values->find($value)) {
                    $data['field_value_id'] = $fieldValue->id;
                } else if ($field->input === FieldInput::NUMBER) {
                    $data['value'] = intval($value);
                } else if ($field->input === FieldInput::DATE) {
                    $data['value'] = Carbon::parse($value)->setTimezone('Europe/Istanbul')->format('Y-m-d');
                } else {
                    $data['value'] = $value;
                }

                if ($patientField = $patientFields->find($row['id'])) {
                    if (empty($data['value']) && empty($data['field_value_id'])) {
                        $patientField->delete();
                    } else {
                        $patientField->update($data);
                    }
                } else if (!empty($data['value']) || !empty($data['field_value_id'])) {
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
