<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageUploadStorer
{
    /**
     * Store an uploaded file to the given disk/directory, downscaling it
     * first if it's an image wider than $maxWidth (uploads from phone
     * cameras are often several MB at full resolution). Non-image files
     * (e.g. PDFs) are stored as-is.
     */
public static function store(UploadedFile $file, string $directory, string $disk = 'public', int $maxWidth = 1600): string
{
  \Log::info('GD Debug', [
    'gd_loaded' => extension_loaded('gd'),
    'gd_info_exists' => function_exists('gd_info'),
    'mime' => $file->getMimeType(),
    'ext' => $file->getClientOriginalExtension(),
    'ini_file' => php_ini_loaded_file(),
    'php_sapi' => php_sapi_name(),
    'php_version' => PHP_VERSION,
]);

    $extension = strtolower($file->getClientOriginalExtension());
        $isImage   = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);

        if (! $isImage) {
            return $file->store($directory, $disk);
        }

        $manager = new ImageManager(new Driver());
        $image   = $manager->read($file->getRealPath());

        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        $filename = Str::random(40) . '.' . $extension;
        $path     = trim($directory, '/') . '/' . $filename;

        Storage::disk($disk)->put($path, (string) $image->encode());

        return $path;
    }
}
