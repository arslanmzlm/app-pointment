<?php

namespace App\Services;

use App\Helpers\FilterHelper;
use App\Helpers\UploadHelper;
use App\Models\Content;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentService
{
    const IMAGE_PATH = 'content/:id';

    public static function getImagePath(int $contentId): string
    {
        return Str::replace(':id', $contentId, self::IMAGE_PATH);
    }

    public function getAll(): Collection
    {
        return Content::query()->get();
    }

    public function getGrouped(): Collection
    {
        return Content::query()
            ->where('active', true)
            ->orderBy('order')
            ->get()
            ->append(['image_url', 'mobile_image_url', 'icon_url']);
    }

    public function getSections(): \Illuminate\Support\Collection
    {
        return Content::query()
            ->groupBy('section')
            ->orderBy('section')
            ->get('section')
            ->pluck('section')
            ->unique();
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Content::query())
            ->search('id', 'title', 'slug', 'subtitle', 'top_title', 'alt_title', 'description', 'body')
            ->sort('id', 'section', 'updated_at')
            ->basicFilter('section')
            ->bool('active')
            ->paginate();
    }

    public function store(array $data): Content
    {
        $content = new Content();
        return $this->assignAttributes($content, $data);
    }

    public function update(Content $content, array $data): Content
    {
        return $this->assignAttributes($content, $data);
    }

    public function delete(Content $content): bool
    {
        $this->deleteImages($content);
        return $content->delete();
    }

    private function assignAttributes(Content $content, array $data): Content
    {
        $content->section = $data['section'] ?? $content->section;
        $content->active = $data['active'] ?? $content->active;
        $content->title = $data['title'] ?? $content->title;
        $content->slug = $data['slug'] ?? $content->slug;
        $content->subtitle = $data['subtitle'] ?? $content->subtitle;
        $content->top_title = $data['top_title'] ?? $content->top_title;
        $content->alt_title = $data['alt_title'] ?? $content->alt_title;
        $content->image_alt = $data['image_alt'] ?? $content->image_alt;
        $content->order = !empty($data['order']) && is_numeric($data['order']) ? intval($data['order']) : null;
        $content->link = $data['link'] ?? $content->link;
        $content->link_label = $data['link_label'] ?? $content->link_label;
        $content->description = $data['description'] ?? $content->description;
        $content->body = $data['body'] ?? $content->body;

        $content->save();

        $this->updateImages($content, $data);

        return $content;
    }

    private function updateImages(Content $content, array $data): void
    {
        $storage = Storage::disk('public');
        $imagePath = self::getImagePath($content->id);

        // TODO: Delete null images while update

        try {
            if (!empty($data['image']) && $data['image'] instanceof UploadedFile) {
                $this->deleteImages($content, 'image', $storage, $imagePath);

                $fileName = "image-" . Str::uuid()->toString();
                $content->image = UploadHelper::image($data['image'], $imagePath, $fileName);;
            }
        } catch (\Exception|\Error $e) {
        }

        try {
            if (!empty($data['mobile_image']) && $data['mobile_image'] instanceof UploadedFile) {
                $this->deleteImages($content, 'mobile_image', $storage, $imagePath);

                $fileName = "mobile-image-" . Str::uuid()->toString();
                $content->mobile_image = UploadHelper::image($data['mobile_image'], $imagePath, $fileName);;
            }
        } catch (\Exception|\Error $e) {
        }

        try {
            if (!empty($data['icon']) && $data['icon'] instanceof UploadedFile) {
                $this->deleteImages($content, 'icon', $storage, $imagePath);

                $fileName = "icon-" . Str::uuid()->toString();
                $content->icon = UploadHelper::image($data['icon'], $imagePath, $fileName);;
            }
        } catch (\Exception|\Error $e) {
        }

        $content->save();
    }

    private function deleteImages(Content $content, string $delete = '*', ?Filesystem $storage = null, ?string $imagePath = null): void
    {
        if ($storage === null) {
            $storage = Storage::disk('public');
        }

        if ($imagePath === null) {
            $imagePath = self::getImagePath($content->id);
        }

        if ($delete === '*' || $delete === 'image') {
            if ($content->image && $storage->exists("{$imagePath}/{$content->image}")) {
                $storage->delete("{$imagePath}/{$content->image}");
            }
        }

        if ($delete === '*' || $delete === 'mobile_image') {
            if ($content->mobile_image && $storage->exists("{$imagePath}/{$content->mobile_image}")) {
                $storage->delete("{$imagePath}/{$content->mobile_image}");
            }
        }

        if ($delete === '*' || $delete === 'icon') {
            if ($content->icon && $storage->exists("{$imagePath}/{$content->icon}")) {
                $storage->delete("{$imagePath}/{$content->icon}");
            }
        }
    }
}
