<?php

namespace Filament\Core\Models\Traits;

use Filament\Core\Services\GoogleTranslateService;

trait AutoTranslate
{
  public static function bootAutoTranslate(): void
  {
    $currentLocale = session()->get('filament.translatable.activeLocale') ?? config('app.fallback_locale');
    $locales = config('app.locales');

    static::created(function ($model) use($currentLocale, $locales) {
      $nonTranslatableFields = ['is_published', 'slug'];

      $fieldsToTranslate = $model->translatable;

      $translateService = app(GoogleTranslateService::class);

      foreach ($locales as $locale) {
        if($locale === $currentLocale) {
          continue;
        }

        foreach ($fieldsToTranslate as $field) {
          if (in_array($field, $nonTranslatableFields)) {
            continue;
          }

          if(!$model->getTranslation($field, $currentLocale)) {
            continue;
          }

          if (!$model->hasTranslation($field, $locale)) {
            $translatedText = $translateService->translate($model->getTranslation($field, $currentLocale), $locale);
            $model->setTranslation($field, $locale, $translatedText);
          }
        }
      }

      $model->save();
    });
  }
}