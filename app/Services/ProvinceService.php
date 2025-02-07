<?php

namespace App\Services;

use App\Models\Province;
use Illuminate\Database\Eloquent\Collection;

class ProvinceService
{
    public function getAll(): Collection
    {
        return Province::query()->orderBy('name')->get();
    }
}
