<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Models\Doctor;
use App\Traits\Service\HospitalQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
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

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Doctor::query())
            ->search('full_name', 'branch', 'title', 'phone', 'email')
            ->sort('id', 'full_name')
            ->idFilter('hospital')
            ->basicFilter('branch')
            ->hasHospital()
            ->paginate();
    }

    public function getBranches(): \Illuminate\Support\Collection
    {
        $query = Doctor::query()->groupBy('branch')->orderBy('branch');

        $this->addHospitalToQuery($query);

        return $query->get('branch')->pluck('branch')->unique();
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

        (new UserService())->storeOrUpdateDoctor($doctor);

        return $doctor;
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        $doctor = $this->assignAttributes($doctor, $data);

        (new UserService())->storeOrUpdateDoctor($doctor);

        return $doctor;
    }

    public function delete(Doctor $doctor): bool
    {
        return $doctor->delete();
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
