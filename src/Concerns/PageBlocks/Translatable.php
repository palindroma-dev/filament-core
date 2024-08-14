<?php

namespace Filament\Core\Concerns\PageBlocks;

trait Translatable
{
  public static function localizeData(array $data): array
  {
    $locale = app()->getLocale();

    foreach(self::$translatable as $attribute) {
      if(str_contains($attribute, '.')) {
        [ $firstPart ] = explode('.', $attribute);
        $data[$firstPart] = $data[$locale][$firstPart];
      } else {
        $data[$attribute] = $data[$locale][$attribute] ?? '';
      }
    }

    return $data;
  }

  public static function mutateFormDataBeforeSave(array $data): array
  {
    foreach(config('app.locales') as $locale) {
      $data[$locale] = [];

      foreach(self::$translatable as $attribute) {
        if(str_contains($attribute, '.')) {
          [ $firstPart, $secondPart ] = explode('.', $attribute);
          $data[$locale][$firstPart] = $data[$locale][$firstPart] ?? [];
          $data[$locale][$firstPart] = array_map(function ($item) use($locale, $firstPart, $secondPart) {
            $item[$secondPart] = $item[$locale.'_'.$secondPart] ?? '';
            unset($item[$locale.'_'.$secondPart]);
            return $item;
          }, $data[$firstPart]);
        } else {
          $data[$locale][$attribute] = $data[$locale . '_' . $attribute] ?? '';
          unset($data[$locale . '_' . $attribute]);
        }
      }
    }

    return $data;
  }

  public static function mutateDataBeforeFill(array $data): array
  {
    foreach(config('app.locales') as $locale) {
      foreach(self::$translatable as $attribute) {
        $data[$locale.'_'.$attribute] = $data[$locale][$attribute] ?? '';
      }
    }

    return $data;
  }

  protected static function localizeAttribute($name): string
  {
    return self::getLocale().'_'.$name;
  }

  private static function getLocale()
  {
    return session()->get('filament.translatable.activeLocale') ?? config('app.fallback_locale');
  }
}
