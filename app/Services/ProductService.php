<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Models\Product;
use App\Traits\Service\HospitalQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProductService
{
    use HospitalQuery;

    public static function incrementStock(int $product_id, int $count): void
    {
        Product::query()
            ->where('id', $product_id)
            ->whereNotNull('stock')
            ->increment('stock', $count);
    }

    public static function decrementStock(int $product_id, int $count): void
    {
        Product::query()
            ->where('id', $product_id)
            ->whereNotNull('stock')
            ->decrement('stock', $count);
    }

    public function getAll(?int $hospital_id = null): Collection
    {
        $query = Product::query()->orderBy('name');

        if ($hospital_id) {
            $query->where('hospital_id', $hospital_id);
        } else {
            $this->addHospitalToQuery($query);
        }

        return $query->get();
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Product::query())
            ->search('id', 'category', 'name', 'code', 'stock', 'price')
            ->sort('id', 'category', 'name', 'stock', 'price', 'updated_at')
            ->basicFilter('category')
            ->idFilter('hospital')
            ->hasHospital()
            ->paginate();
    }

    public function getCategories(): \Illuminate\Support\Collection
    {
        $query = Product::query()->groupBy('category')->orderBy('category');

        $this->addHospitalToQuery($query);

        return $query->get('category')->pluck('category')->unique();
    }

    public function store(array $data): Product
    {
        $product = new Product();
        $product->hospital_id = auth()->user()->hospital_id ?? $data['hospital_id'];

        return $this->assignAttributes($product, $data);
    }

    public function update(Product $product, array $data): Product
    {
        return $this->assignAttributes($product, $data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    private function assignAttributes(Product $product, array $data): Product
    {
        $product->active = $data['active'] ?? $product->active;
        $product->category = !empty($data['category']) ? Str::title($data['category']) : $product->category;
        $product->name = $data['name'] ?? $product->name;
        $product->code = $data['code'] ?? $product->code;
        $product->stock = !empty($data['stock']) ? intval($data['stock']) : $product->stock;
        $product->price = $data['price'] ?? $product->price ?? null;

        $product->save();

        return $product;
    }
}
