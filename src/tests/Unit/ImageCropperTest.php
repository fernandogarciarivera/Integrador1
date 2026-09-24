<?php

namespace Tests\Unit;

use App\Support\ImageCropper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageCropperTest extends TestCase
{
    public function test_it_crops_uploaded_image_using_selected_rectangle(): void
    {
        Storage::fake('public');

        $image = imagecreatetruecolor(200, 120);
        $white = imagecolorallocate($image, 255, 255, 255);
        $red = imagecolorallocate($image, 255, 0, 0);
        imagefilledrectangle($image, 0, 0, 199, 119, $white);
        imagefilledrectangle($image, 30, 20, 149, 89, $red);

        $tempPath = tempnam(sys_get_temp_dir(), 'crop_');
        imagepng($image, $tempPath);
        imagedestroy($image);

        $file = new UploadedFile($tempPath, 'sample.png', 'image/png', null, true);

        $url = ImageCropper::cropAndStore($file, [
            'x' => 30,
            'y' => 20,
            'width' => 120,
            'height' => 70,
        ], 'public', 'trabajadores');

        $this->assertNotNull($url);
        $this->assertStringContainsString('/storage/trabajadores/', $url);

        $storedPath = str_replace('/storage/', '', $url);
        $this->assertTrue(Storage::disk('public')->exists($storedPath));
    }
}
