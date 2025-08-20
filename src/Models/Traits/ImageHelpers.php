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
      $output = '<img src="/assets/img/placeholder.png" class="' . $className . '" alt="placeholder" '.$attributesString.'/>';
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

    if ($media && $media->disk !== 's3' && !in_array($extension, ['svg', 'webp'])) {
      if (! $this->imageHasTransparency($media->getPath(), $extension)) {
        $this->convertToWebp($media);
        $extension = 'webp';
      }
    }

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

  private function imageHasTransparency(string $path, string $extension): bool
  {
    $extension = strtolower($extension);

    if ($extension === 'png') {
      $image = @imagecreatefrompng($path);
      if (! $image) {
        return false;
      }
      $width = imagesx($image);
      $height = imagesy($image);
      for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
          $alpha = (imagecolorat($image, $x, $y) & 0x7F000000) >> 24;
          if ($alpha > 0) {
            imagedestroy($image);
            return true;
          }
        }
      }
      imagedestroy($image);
      return false;
    }

    if ($extension === 'gif') {
      $image = @imagecreatefromgif($path);
      if (! $image) {
        return false;
      }
      $transparentIndex = imagecolortransparent($image);
      imagedestroy($image);
      return $transparentIndex >= 0;
    }

    return false;
  }

  private function convertToWebp(Media $media): void
  {
    $path = $media->getPath();
    $extension = strtolower($media->extension);

    if ($extension === 'png') {
      $image = @imagecreatefrompng($path);
    } elseif ($extension === 'gif') {
      $image = @imagecreatefromgif($path);
    } else {
      $image = @imagecreatefromjpeg($path);
    }

    if (! $image) {
      return;
    }

    $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);
    imagewebp($image, $webpPath);
    imagedestroy($image);

    if (file_exists($path)) {
      unlink($path);
    }

    $media->file_name = pathinfo($webpPath, PATHINFO_BASENAME);
    $media->mime_type = 'image/webp';
    $media->extension = 'webp';
    $media->save();
  }
}
