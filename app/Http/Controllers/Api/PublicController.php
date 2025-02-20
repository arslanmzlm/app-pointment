<?php

namespace App\Http\Controllers\Api;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Services\ProvinceService;
use Stevebauman\Purify\Facades\Purify;
use Symfony\Component\HttpFoundation\JsonResponse;

class PublicController extends Controller
{
    public function __construct(private ProvinceService $provinceService)
    {
    }

    public function agreement(): JsonResponse
    {
        return response()->json(['agreement' => Purify::clean(fake()->randomHtml(10))]);
    }

    public function privacy(): JsonResponse
    {
        return response()->json(['privacy' => Purify::clean(fake()->randomHtml(10))]);
    }

    public function provinces(): JsonResponse
    {
        return response()->json(['provinces' => $this->provinceService->getAll()]);
    }

    public function genders()
    {
        return response()->json(['genders' => Gender::getAll()]);
    }
}
