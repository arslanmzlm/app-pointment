<?php

namespace App\Services;

use App\Enums\SettingKeys;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Stevebauman\Purify\Facades\Purify;

class SettingService
{
    public static function getValue(SettingKeys|string $key)
    {
        if ($key instanceof SettingKeys) {
            $key = $key->value;
        }

        return Setting::query()
            ->where('key', $key)
            ->first()
            ->value;
    }

    public static function getPurifyValue(SettingKeys|string $key): null
    {
        $value = self::getValue($key);
        return is_string($value) && !empty($value) ? Purify::clean($value) : null;
    }

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
