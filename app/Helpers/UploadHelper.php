<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class UploadHelper
{
    public static function getStorage(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk('public');
    }

    public static function image(UploadedFile $file, string $path, string $name, ?int $scaleDownWidth = null, ?int $scaleDownHeight = null): string
    {
        $storage = self::getStorage();
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'svg') {
            $fileName = "{$name}.svg";
            $file->storeAs($path, $fileName, 'public');

            return $fileName;
        } else if ($extension === 'png') {
            $image = Image::read($file)->scaleDown(width: $scaleDownWidth, height: $scaleDownHeight)->toPng();
            $fileName = "{$name}.png";
            $storage->put("{$path}/{$fileName}", $image);

            return $fileName;
        } else {
            $image = Image::read($file)->scaleDown(width: $scaleDownWidth, height: $scaleDownHeight)->toJpeg();
            $fileName = "{$name}.jpg";
            $storage->put("{$path}/{$fileName}", $image);

            return $fileName;
        }

    }
}
