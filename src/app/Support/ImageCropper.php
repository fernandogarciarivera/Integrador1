<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCropper
{
    public static function cropAndStore(UploadedFile $file, array $crop, string $disk = 'public', string $directory = 'trabajadores'): ?string
    {
        $crop = self::normalizeCrop($crop);

        if ($crop['width'] <= 0 || $crop['height'] <= 0) {
            return Storage::url($file->store($directory, $disk));
        }

        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        if ($image === false) {
            return Storage::url($file->store($directory, $disk));
        }

        $cropped = imagecreatetruecolor((int) round($crop['width']), (int) round($crop['height']));
        imagecopy(
            $cropped,
            $image,
            0,
            0,
            (int) round($crop['x']),
            (int) round($crop['y']),
            (int) round($crop['width']),
            (int) round($crop['height'])
        );

        $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
        $name = Str::uuid() . '.' . $extension;
        $path = $directory . '/' . $name;
        Storage::disk($disk)->makeDirectory($directory);
        $stored = false;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $stored = imagejpeg($cropped, Storage::disk($disk)->path($path));
                break;
            case 'gif':
                $stored = imagegif($cropped, Storage::disk($disk)->path($path));
                break;
            case 'webp':
                $stored = imagewebp($cropped, Storage::disk($disk)->path($path));
                break;
            default:
                $stored = imagepng($cropped, Storage::disk($disk)->path($path));
        }

        imagedestroy($image);
        imagedestroy($cropped);

        if (! $stored) {
            return null;
        }

        return Storage::url($path);
    }

    private static function normalizeCrop(array $crop): array
    {
        $defaults = ['x' => 0, 'y' => 0, 'width' => 0, 'height' => 0];
        $normalized = [];

        foreach ($defaults as $key => $default) {
            $value = (float) ($crop[$key] ?? $default);
            $normalized[$key] = max(0, $value);
        }

        return $normalized;
    }
}
