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

    public function updatePassword(array $data): bool
    {
        $user = auth()->user();
        $check = Hash::check($data['password'], $user->password);

        if ($check) {
            $user->password = Hash::make($data['new_password']);
            $user->save();
            return true;
        }

        return false;
    }

    public function storeDoctor(Doctor $doctor, array $data): User
    {
        $create = [
            'doctor_id' => $doctor->id,
            'hospital_id' => $doctor->hospital_id,
            'username' => $data['username'],
            'password' => $data['password'],
            'name' => $doctor->full_name,
            'phone' => $doctor->phone,
            'email' => $doctor->email,
        ];

        return $this->store($create, UserType::DOCTOR);
    }

    public function updateDoctor(Doctor $doctor): ?User
    {
        if ($doctor->user) {
            $data = [
                'name' => $doctor->full_name,
                'phone' => $doctor->phone,
                'email' => $doctor->email,
            ];

            return $this->update($doctor->user, $data);
        }

        return null;
    }

    public function storeOrUpdatePatient(Patient $patient, ?string $password = null): ?User
    {
        $phoneClean = preg_replace('/^(?:\+90|90|0)\s*/', '', $patient->phone->getRawNumber());

        $data = [
            'username' => $phoneClean,
            'name' => $patient->full_name,
            'phone' => $patient->phone,
            'email' => $patient->email,
        ];

        if (!$patient->user) {
            $data['patient_id'] = $patient->id;
            $data['password'] = $password ?? substr($phoneClean, -6);
        }

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
}
