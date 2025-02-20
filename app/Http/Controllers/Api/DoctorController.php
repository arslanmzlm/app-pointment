<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Services\DoctorService;

class DoctorController extends Controller
{
    public function __construct(private DoctorService $doctorService)
    {
    }

    public function all()
    {
        return response()->json([
            'doctors' => DoctorResource::collection($this->doctorService->getActive()),
        ]);
    }
}
