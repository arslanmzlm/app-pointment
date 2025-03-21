<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\HospitalService;
use App\Services\ProductService;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService, private HospitalService $hospitalService)
    {
    }

    public function list()
    {
        return Inertia::render('Dashboard/Product/List', [
            'products' => $this->productService->filter(),
            'categories' => $this->productService->getCategories(),
            'brands' => $this->productService->getBrands(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Product/Create', [
            'categories' => $this->productService->getCategories(),
            'brands' => $this->productService->getBrands(),
            'hospitals' => $this->hospitalService->getAll(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $this->productService->store($request->validated());

        session()->flash('toast.success', trans('messages.product.created'));

        return to_route('dashboard.product.list');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Dashboard/Product/Edit', [
            'product' => $product->load(['images']),
            'stocks' => $this->productService->getStocks($product),
            'categories' => $this->productService->getCategories(),
            'brands' => $this->productService->getBrands(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->productService->update($product, $request->validated());

        session()->flash('toast.success', trans('messages.product.updated'));

        return to_route('dashboard.product.list');
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        session()->flash('toast.success', trans('messages.product.deleted'));
    }
}
