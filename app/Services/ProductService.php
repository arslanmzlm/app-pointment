<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProductService
{
    const IMAGE_PATH = 'product/:id';

    public static function getImagePath(int $productId): string
    {
        return Str::replace(':id', $productId, self::IMAGE_PATH);
    }

    public static function incrementStock(int $hospitalId, int $productId, int $count): int
    {
        return ProductStock::query()
            ->where('hospital_id', $hospitalId)
            ->where('product_id', $productId)
            ->whereNotNull('stock')
            ->increment('stock', $count);
    }

    public static function decrementStock(int $hospitalId, int $productId, int $count): int
    {
        return ProductStock::query()
            ->where('hospital_id', $hospitalId)
            ->where('product_id', $productId)
            ->whereNotNull('stock')
            ->decrement('stock', $count);
    }

    public function getAll(): Collection
    {
        return Product::query()->orderBy('name')->get();
    }

    public function getActive(): Collection
    {
        return Product::query()
            ->where('active', true)
            ->orderBy('name')
            ->orderBy('category')
            ->orderBy('brand')
            ->get();
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Product::query())
            ->search('id', 'category', 'name', 'brand', 'code', 'price', 'description')
            ->sort('id', 'category', 'name', 'price', 'updated_at')
            ->basicFilter('category')
            ->bool('active')
            ->paginate();
    }

    public function getStocks(Product $product): array
    {
        $hospitals = (new HospitalService())->getAll();
        $stocks = $product->stocks;

        $data = [];

        foreach ($hospitals as $hospital) {
            $data[] = [
                'hospital_id' => $hospital->id,
                'hospital_name' => $hospital->name,
                'stock' => $stocks->where('hospital_id', $hospital->id)->first()?->stock ?? null,
            ];
        }


        return $data;
    }

    public function getCategories(): \Illuminate\Support\Collection
    {
        return Product::query()
            ->groupBy('category')
            ->orderBy('category')
            ->get('category')
            ->pluck('category')
            ->unique();
    }

    public function getBrands(): \Illuminate\Support\Collection
    {
        return Product::query()
            ->groupBy('brand')
            ->orderBy('brand')
            ->get('brand')
            ->pluck('brand')
            ->unique();
    }

    public function store(array $data): Product
    {
        $product = new Product();
        $product = $this->assignAttributes($product, $data);

        if (!empty($data['stocks'])) $this->updateStocks($product, $data['stocks']);
        if (!empty($data['images'])) $this->storeImages($product, $data['images']);

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $product = $this->assignAttributes($product, $data);

        if (!empty($data['stocks'])) $this->updateStocks($product, $data['stocks']);

        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    private function assignAttributes(Product $product, array $data): Product
    {
        $product->active = $data['active'] ?? $product->active;
        $product->category = !empty($data['category']) ? Str::title($data['category']) : $product->category;
        $product->brand = !empty($data['brand']) ? Str::title($data['brand']) : $product->brand;
        $product->name = $data['name'] ?? $product->name;
        $product->code = $data['code'] ?? $product->code;
        $product->price = $data['price'] ?? $product->price ?? null;
        $product->description = $data['description'] ?? $product->description;

        $product->save();

        if ($product->slug !== $data['slug']) {
            $slugInput = !empty($data['slug']) ? $data['slug'] : $data['name'];
            $product->slug = Str::slug("{$slugInput}-{$product->id}") ?? $product->slug;
            $product->save();
        }

        return $product;
    }

    private function updateStocks(Product $product, array $items): void
    {
        $stocks = $product->stocks;

        foreach ($items as $item) {
            if ($stock = $stocks->where('hospital_id', $item['hospital_id'])->first()) {
                $stock->update(['stock' => $item['stock']]);
            } else {
                $product->stocks()->create(['hospital_id' => $item['hospital_id'], 'stock' => $item['stock']]);
            }
        }
    }

    private function storeImages(Product $product, array $images): void
    {
        $storage = Storage::disk('public');
        $imagePath = self::getImagePath($product->id);

        foreach ($images as $index => $file) {
            $imageIndex = $index + 1;
            $image = Image::read($file)->scaleDown(height: 1080)->toJpeg();
            $fileName = "{$product->id}-{$imageIndex}-" . Str::orderedUuid()->toString() . ".jpg";
            $storage->put("{$imagePath}/{$fileName}", $image);

            $attributes = [
                'product_id' => $product->id,
                'file' => $fileName,
                'order' => $imageIndex,
            ];

            ProductImage::create($attributes);
        }
    }

    private function uploadImages(Product $product, array $images): void
    {
        $images = collect($images)->sortBy('order');
        $storage = Storage::disk('public');
        $imagePath = self::getImagePath($product->id);

        $uploaded = [];

        foreach ($images as $index => $item) {
            $imageIndex = $index + 1;

            $id = $item['id'] ?? null;
            /** @var UploadedFile $file */
            $file = $item['file'];
            $order = $item['order'] ?? $imageIndex;

            if (!empty($file)) {
                if ($file instanceof UploadedFile) {
                    $image = Image::read($file)->scaleDown(height: 1080)->toJpeg();
                    $fileName = "{$product->id}-{$imageIndex}-" . Str::orderedUuid()->toString() . ".jpg";
                    $storage->put("{$imagePath}/{$fileName}", $image);

                    $attributes = [
                        'product_id' => $product->id,
                        'file' => $fileName,
                        'order' => $order,
                    ];

                    if ($id && $image = $product->images()->find($id)) {
                        if ($storage->exists("{$imagePath}/{$image->file}")) {
                            $storage->delete("{$imagePath}/{$image->file}");
                        }

                        $image->update($attributes);
                    } else {
                        $image = $product->images()->create($attributes);
                    }

                    $uploaded[] = $image->id;
                } else {
                    $image = $product->images()->find($id);
                    if ($image->order !== $order) {
                        $image->order = $order;
                        $image->save();
                    }

                    $uploaded[] = $id;
                }
            }
        }

        $deleted = $product->images()->whereNotIn('id', $uploaded)->get();

        foreach ($deleted as $image) {
            if ($storage->exists("{$imagePath}/{$image->file}")) {
                $storage->delete("{$imagePath}/{$image->file}");
            }
            $image->delete();
        }

        $this->reOrderImages($product);
    }

    private function reOrderImages(Product $product): void
    {
        foreach ($product->images as $index => $image) {
            $image->order = $index + 1;
            $image->save();
        }
    }
}
