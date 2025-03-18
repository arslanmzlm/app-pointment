<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;

class SettingService
{
    public function getAllByKey(): Collection
    {
        return Setting::query()
            ->get()
            ->mapWithKeys(fn(Setting $setting, int $key) => [$setting->key => $setting->value]);
    }

    public function update(array $data): void
    {
        foreach ($data as $setting => $value) {
            Setting::updateOrCreate(['key' => $setting], ['value' => $value]);
        }
    }
}
