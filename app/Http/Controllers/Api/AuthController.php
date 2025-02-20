<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private PatientService $patientService)
    {
    }

    public function user()
    {
        return response()->json([
            'user' => UserResource::make(auth()->user()),
        ]);
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate();

        return response()->json([
            'token' => auth()->user()->createToken('mobile-token')->plainTextToken,
            'user' => UserResource::make(auth()->user()),
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $this->patientService->register($request->validated());

        return response()->json(['error' => false]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['error' => false]);
    }
}
