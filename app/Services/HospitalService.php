<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Models\Hospital;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HospitalService
{
    const IMAGE_PATH = 'hospitals/:id';

    public static function getLogoPath(Hospital $hospital): string
    {
        return Str::replace(':id', $hospital->id, self::IMAGE_PATH);
    }

    public function getAll(): Collection
    {
        return Hospital::query()->orderBy('name')->get();
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Hospital::query())
            ->search('name', 'title', 'owner', 'phone', 'email', 'address')
            ->sort('id', 'name', 'title')
            ->idFilter('province')
            ->paginate();
    }

    public function getInfo(): ?array
    {
        $hospital = auth()->user()->hospital;

        return $hospital ?
            [
                'start_work' => $hospital->start_work,
                'end_work' => $hospital->end_work,
                'duration' => $hospital->duration,
            ]
            : null;
    }

    public function store(array $data): ?Hospital
    {
        $hospital = new Hospital();

        return $this->assignAttributes($hospital, $data);
    }

    public function update(Hospital $hospital, array $data): ?Hospital
    {
        return $this->assignAttributes($hospital, $data);
    }

    public function delete(Hospital $hospital): bool
    {
        return $hospital->delete();
    }

    private function assignAttributes(Hospital $hospital, array $data): ?Hospital
    {
        $hospital->province_id = $data['province_id'] ?? $hospital->province_id;
        $hospital->name = $data['name'] ?? $hospital->name;
        $hospital->start_work = $data['start_work'] ?? $hospital->start_work;
        $hospital->end_work = $data['end_work'] ?? $hospital->end_work;
        $hospital->duration = $data['duration'] ?? $hospital->duration;
        $hospital->title = $data['title'] ?? $hospital->title;
        $hospital->description = $data['description'] ?? $hospital->description;
        $hospital->owner = $data['owner'] ?? $hospital->owner;
        $hospital->phone = $data['phone'] ?? $hospital->phone;
        $hospital->email = $data['email'] ?? $hospital->email;
        $hospital->address = $data['address'] ?? $hospital->address;
        $hospital->address_link = $data['address_link'] ?? $hospital->address_link;

        if (!empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
            if ($hospital->id === null) {
                $hospital->save();
            }

            $this->deleteLogo($hospital);
            $hospital->logo = $this->storeLogo($hospital, $data['logo']);
        }

        $hospital->save();

        return $hospital;
    }

    private function storeLogo(Hospital $hospital, UploadedFile $logo): string
    {
        $hospitalName = Str::slug($hospital->name);
        $rand = rand(100, 999);
        $extension = $logo->extension();
        $fileName = "{$hospitalName}-logo-{$rand}.{$extension}";
        $logo->storePubliclyAs($this->getLogoPath($hospital), $fileName, 'public');
        return $fileName;
    }

    private function deleteLogo(Hospital $hospital): void
    {
        if ($hospital->logo) {
            Storage::disk('public')->delete($this->getLogoPath($hospital) . "/{$hospital->logo}");
        }
    }
}
