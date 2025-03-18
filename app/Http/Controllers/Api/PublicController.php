<?php

namespace App\Http\Controllers\Api;

use App\Enums\Gender;
use App\Enums\SettingKeys;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContentResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\HospitalResource;
use App\Http\Resources\ProductResource;
use App\Services\ContentService;
use App\Services\DoctorService;
use App\Services\HospitalService;
use App\Services\ProductService;
use App\Services\ProvinceService;
use App\Services\SettingService;
use Symfony\Component\HttpFoundation\JsonResponse;

class PublicController extends Controller
{
    public function __construct(private ProvinceService $provinceService, private ContentService $contentService)
    {
    }

    public function initial()
    {
        $settingService = new SettingService();
        $hospitalService = new HospitalService();
        $doctorService = new DoctorService();
        $productService = new ProductService();

        return response()->json([
            'contents' => ContentResource::collection($this->contentService->getGrouped())->collection->groupBy('section'),
            'settings' => $settingService->getAllByKey(),
            'hospitals' => HospitalResource::collection($hospitalService->getAll()->load('province')),
            'doctors' => DoctorResource::collection($doctorService->getActive()),
            'products' => ProductResource::collection($productService->getActive()),
        ]);
    }

    public function contents(): JsonResponse
    {
        return response()->json(['contents' => $this->contentService->getGrouped()]);
    }

    public function agreement(): JsonResponse
    {
        return response()->json([
            'agreement' => SettingService::getPurifyValue(SettingKeys::AGREEMENT_POLICY),
        ]);
    }

    public function privacy(): JsonResponse
    {
        return response()->json([
            'privacy' => SettingService::getPurifyValue(SettingKeys::PRIVACY_POLICY),
        ]);
    }

    public function provinces(): JsonResponse
    {
        return response()->json(['provinces' => $this->provinceService->getAll()]);
    }

    public function genders(): JsonResponse
    {
        return response()->json(['genders' => Gender::getAll()]);
    }
}
