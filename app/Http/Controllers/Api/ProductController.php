<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    public function all()
    {
        return response()->json([
            'products' => ProductResource::collection($this->productService->getActive()),
        ]);
    }
}
