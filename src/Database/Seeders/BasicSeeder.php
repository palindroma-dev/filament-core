<?php

namespace Filament\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;

class BasicSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  protected function addMedia($items, $numberOfImages = 1, $folder = '', $collection = 'images'): void
  {
    $allImages = File::files(public_path('assets/faker' . $folder));

    $items->each(function ($item) use ($allImages, $collection, $numberOfImages) {
      for ($i = 0; $i < $numberOfImages; $i++) {
        $randomImage = $allImages[array_rand($allImages)];

        $tempImage = tempnam(sys_get_temp_dir(), 'image') . '.' . File::extension($randomImage);
        File::copy($randomImage, $tempImage);

        $item
          ->addMedia($tempImage)
          ->withCustomProperties([
            'original_width' => ImageManager::imagick()->read($tempImage)->width(),
            'original_height' => ImageManager::imagick()->read($tempImage)->height(),
          ])
          ->toMediaCollection($collection);
      }
    });
  }
}
