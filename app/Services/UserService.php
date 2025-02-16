<?php

namespace App\Services;

use App\Enums\UserType;
use App\Helpers\FilterHelper;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(User::query())
            ->search('username', 'name', 'phone', 'email')
            ->sort('id', 'username', 'name')
            ->idFilter('hospital')
            ->enumMultiple(['type' => UserType::class])
            ->hasHospital()
            ->deleted()
            ->paginate();
    }

    public function store(array $data, UserType $userType): User
    {
        $user = new User();
        $user->type = $userType;

        if (auth()->user()->hospital_id !== null) {
            $user->hospital_id = auth()->user()->hospital_id;
        }

        return $this->assignAttributes($user, $data);
    }

    public function update(User $user, array $data): User
    {
        return $this->assignAttributes($user, $data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function storeOrUpdateDoctor(Doctor $doctor): ?User
    {
        $data = $this->getDataForRelatedCreate($doctor);

        if ($doctor->user) {
            return $this->update($doctor->user, $data);
        } else if (!User::query()->where('username', $data['username'])->withTrashed()->exists()) {
            return $this->store($data, UserType::DOCTOR);
        }

        return null;
    }

    public function storeOrUpdatePatient(Patient $patient): ?User
    {
        $data = $this->getDataForRelatedCreate($patient);

        if ($patient->user) {
            return $this->update($patient->user, $data);
        } else if (!User::query()->where('username', $data['username'])->withTrashed()->exists()) {
            return $this->store($data, UserType::PATIENT);
        }

        return null;
    }

    private function assignAttributes(User $user, array $data): User
    {
        $user->hospital_id = $data['hospital_id'] ?? $user->hospital_id;
        $user->doctor_id = $data['doctor_id'] ?? $user->doctor_id;
        $user->patient_id = $data['patient_id'] ?? $user->patient_id;
        $user->username = $data['username'] ?? $user->username;
        $user->name = $data['name'] ?? $user->name;
        $user->phone = $data['phone'] ?? $user->phone;
        $user->email = $data['email'] ?? $user->email;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']) ?? $user->password;
        }

        $user->save();

        return $user;
    }

    private function getDataForRelatedCreate(Patient|Doctor $model): array
    {
        $phoneClean = preg_replace('/^(?:\+90|90|0)\s*/', '', $model->phone->getRawNumber());

        if (!$model->user) {
            if ($model instanceof Patient) {
                $data['patient_id'] = $model->id;
            } else {
                $data['doctor_id'] = $model->id;
                $data['hospital_id'] = $model->hospital_id;
            }
            $data['password'] = substr($phoneClean, -6);
        }

        $data['username'] = $phoneClean;
        $data['name'] = $model->full_name;
        $data['phone'] = $model->phone;
        $data['email'] = $model->email;

        return $data;
    }
}
