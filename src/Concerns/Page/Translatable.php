<?php

namespace Filament\Core\Concerns\Page;

use Filament\Resources\Concerns\HasActiveLocaleSwitcher;
use Filament\Resources\Pages\Concerns\HasTranslatableRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

trait Translatable
{
  use HasActiveLocaleSwitcher;
  use HasTranslatableFormWithExistingRecordData;
  use HasTranslatableRecord;

  protected ?string $oldActiveLocale = null;

  public function getTranslatableLocales(): array
  {
    return filament('spatie-laravel-translatable')->getDefaultLocales();
  }

  public function updatingActiveLocale($locale): void
  {
    session()->put('filament.translatable.activeLocale', $locale);
    $this->oldActiveLocale = $this->activeLocale;
  }

  public function updatedActiveLocale(): void
  {
    if (blank($this->oldActiveLocale)) {
      return;
    }

    $this->resetValidation();

    $translatableAttributes = static::getTranslatableAttributes();

    $this->otherLocaleData[$this->oldActiveLocale] = Arr::only($this->data, $translatableAttributes);

    if (isset($this->otherLocaleData[$this->activeLocale]) && method_exists($this, 'mutateOtherLocaleData')) {
      $this->otherLocaleData[$this->activeLocale] = $this->mutateOtherLocaleData($this->otherLocaleData[$this->activeLocale]);
    }

    $this->data = [
      ...Arr::except($this->data, $translatableAttributes),
      ...$this->otherLocaleData[$this->activeLocale] ?? [],
    ];

    unset($this->otherLocaleData[$this->activeLocale]);
  }

  public function setActiveLocale(string $locale): void
  {
    $this->updatingActiveLocale($locale);
    $this->activeLocale = $locale;
    $this->updatedActiveLocale();
  }

  protected static function localizeAttribute($attributeName): string
  {
    $translatableAttributes = static::getTranslatableAttributes();

    if(in_array($attributeName, $translatableAttributes)) {
      return $attributeName . '_' . self::getLocale();
    }

    return $attributeName;
  }

  protected static function localizeLabel($label): string
  {
    return $label . ' ' . config('app.locale_labels')[self::getLocale()];
  }

  private static function getLocale()
  {
    return session()->get('filament.translatable.activeLocale') ?? config('app.fallback_locale');
  }
}
