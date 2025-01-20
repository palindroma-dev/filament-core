<?php

namespace Filament\Core\Models\Traits;

use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class ManagesUserRightsTrait
 * @package App\Models\Traits
 */
trait ImageHelpers
{
  public function renderFirstImage($cfg = [])
  {
    // If the first argument is string, it's treated as size parameter
    if (!is_array($cfg)) {
      $cfg = [
        'size' => $cfg
      ];
    }

    $size = $cfg['size'] ?? '';
    $className = $cfg['class'] ?? null;
    $title = $cfg['title'] ?? null;
    $print = $cfg['print'] ?? true;
    $collection = $cfg['collection'] ?? 'images';
    $attributes = $cfg['attributes'] ?? [];
    
    $attributesString = '';
    foreach($attributes as $key => $value) {
      $attributesString .= $key . '="' . $value . '" ';
    }

    $firstImage = $this->getFirstMedia($collection);
    if ($firstImage) {
      $attrs = [];
      if ($className) {
        $attrs['class'] = $className;
      }
      if ($title) {
        $attrs['title'] = $title;
      }
      if($firstImage->extension == 'svg') {
        $output = $firstImage->img('', array_merge($attrs, $attributes));
      } else {
        $output = $firstImage->img($size, array_merge($attrs, $attributes))->lazy();
      }
    } else {
      $output = '<img src="assets/img/placeholder.png" class="' . $className . '" alt="placeholder" '.$attributesString.'/>';
    }

    if ($print) {
      echo $output;
    } else {
      return $output;
    }
  }

  private function registerMediaConversionsFromImageHelpers(Media $media = null, $cfg = null): void
  {
    $collection = $cfg['collection'] ?? 'images';
    $conversions = $cfg['conversions'] ?? ['small', 'medium', 'large', 'thumbnail'];
    $conversionVariants = [
      'large' => 1920,
      'medium' => 1200,
      'small' => 768,
      'thumbnail' => 300,
    ];

    $width = $media?->getCustomProperty('original_width');
    $extension = $media->extension;

    if($extension == 'svg') {
      $conversionVariants = [];
    } else if (!$width) {
      if($media->disk == 's3') {
        $path = ltrim($media->getPath(),  config('filesystems.disks.s3.root').'/');
        $s3Url = Storage::disk($media->disk)->url($path);
        $imageSize = getimagesize($s3Url);
      } else {
        $imageSize = getimagesize($media->getPath());
      }

      if ($imageSize) {
        $width = $imageSize[0];
        $height = $imageSize[1];
        $media->update(['custom_properties' => ['original_width' => $width, 'original_height' => $height]]);
      }
    }

    foreach ($conversionVariants as $conversionVariant => $maxSize) {
      if (in_array($conversionVariant, $conversions)) {
        $this->addMediaConversion($conversionVariant)
          ->width(min($width, $maxSize))
          ->keepOriginalImageFormat()
          ->optimize()
          ->nonQueued()
          ->performOnCollections($collection);
      }
    }
  }
}
