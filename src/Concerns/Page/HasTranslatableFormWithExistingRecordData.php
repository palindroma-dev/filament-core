<?php

namespace Filament\Core\Concerns\Page;

use Livewire\Attributes\Locked;

trait HasTranslatableFormWithExistingRecordData
{
  #[Locked]
  public $otherLocaleData = [];

//  protected function fillForm(): void
//  {
//    $this->activeLocale = $this->getDefaultTranslatableLocale();
//
//    $record = $this->getRecord();
//    $translatableAttributes = static::getTranslatableAttributes();
//
//    foreach ($this->getTranslatableLocales() as $locale) {
//      $translatedData = [];
//
//      foreach ($translatableAttributes as $attribute) {
//        $translatedData[$attribute] = $record->getTranslation($attribute, $locale, useFallbackLocale: false);
//      }
//
//      if ($locale !== $this->activeLocale) {
//        $this->otherLocaleData[$locale] = $this->mutateFormDataBeforeFill($translatedData);
//
//        continue;
//      }
//
//      /** @internal Read the DocBlock above the following method. */
//      $this->fillFormWithDataAndCallHooks($record, $translatedData);
//    }
//  }

  protected function getDefaultTranslatableLocale(): string
  {
    return config('app.fallback_locale');
  }
}
