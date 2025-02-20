<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\UserResource;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(private PatientService $patientService)
    {
    }

    public function patient(Request $request)
    {
        return response()->json([
            'patient' => PatientResource::make($request->user()->patient),
        ]);
    }

    public function update(UpdatePatientRequest $request)
    {
        $patient = $this->patientService->update($request->user()->patient, $request->validated());

        return response()->json([
            'error' => false,
            'message' => trans('messages.generic.updated'),
            'user' => UserResource::make($patient->user),
        ]);
    }
}
