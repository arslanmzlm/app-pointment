<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function get(): JsonResponse
    {
        return response()->json([
            'notifications' => NotificationResource::collection($this->notificationService->get(auth()->id())),
        ]);
    }

    public function seen(): JsonResponse
    {
        $this->notificationService->seen(auth()->id());

        return response()->json([
            'error' => false,
        ]);
    }

    public function deleteAll(): JsonResponse
    {
        $this->notificationService->deleteAll(auth()->id());

        return response()->json([
            'error' => false,
        ]);
    }
}
