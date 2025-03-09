<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MakeAppointmentRequest;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $appointmentService)
    {
    }

    public function make(MakeAppointmentRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $validation = $this->appointmentService->validateForMake($request->user(), $data);

            if ($validation === 1) {
                $response = [
                    'error' => true,
                    'message' => trans('messages.appointment.has_appointment'),
                ];
            } else if ($validation === 2) {
                $response = [
                    'error' => true,
                    'message' => trans('messages.appointment.overlap'),
                ];
            } else {
                $this->appointmentService->make($data);

                $response = [
                    'error' => false,
                    'message' => trans('messages.appointment.made'),
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'error' => true,
                'message' => trans('messages.errors.500'),
            ];
        }


        return response()->json($response);
    }
}
