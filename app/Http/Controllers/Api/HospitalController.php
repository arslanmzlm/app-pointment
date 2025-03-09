<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HospitalResource;
use App\Services\HospitalService;

class HospitalController extends Controller
{
    public function __construct(private HospitalService $hospitalService)
    {
    }

    public function all()
    {
        return response()->json([
            'hospitals' => HospitalResource::collection($this->hospitalService->getAll()->load('province')),
        ]);
    }
}
