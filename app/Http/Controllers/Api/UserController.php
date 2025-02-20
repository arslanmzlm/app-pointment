<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $update = $this->userService->updatePassword($request->validated());

        if ($update) {
            $response = [
                'error' => false,
                'message' => trans('messages.user.password_updated'),
            ];
        } else {
            $response = [
                'error' => true,
                'message' => trans('messages.user.password_incorrect'),
            ];
        }

        return response()->json($response);
    }
}
