<?php

namespace Filament\Core\Concerns\CreateRecord;

use Filament\Facades\Filament;
use Filament\Resources\Concerns\HasActiveLocaleSwitcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

trait Translatable
{
  use HasActiveLocaleSwitcher;

  protected ?string $oldActiveLocale = null;

  #[Locked]
  public $otherLocaleData = [];

  public function mountTranslatable(): void
  {
    $this->activeLocale = static::getResource()::getDefaultTranslatableLocale();
  }

  public function getTranslatableLocales(): array
  {
    return static::getResource()::getTranslatableLocales();
  }

  protected function handleRecordCreation(array $data): Model
  {
    $record = app(static::getModel());

    $translatableAttributes = static::getResource()::getTranslatableAttributes();

    $record->fill(Arr::except($data, $translatableAttributes));

    foreach (Arr::only($data, $translatableAttributes) as $key => $value) {
      $record->setTranslation($key, $this->activeLocale, $value);
    }

    $originalData = $this->data;

    foreach ($this->otherLocaleData as $locale => $localeData) {
      $this->data = [
        ...$this->data,
        ...$localeData,
      ];

      try {
        $this->form->validate();
      } catch (ValidationException $exception) {
        continue;
      }

      $localeData = $this->mutateFormDataBeforeCreate($localeData);

      foreach (Arr::only($localeData, $translatableAttributes) as $key => $value) {
        if(($value['type'] ?? null) == 'doc' && isset($value['content'])) {
          $value = tiptap_converter()->asHTML($value);
        } else {
          if(is_array($value) && (array_values($value)[0] ?? null)) {
            $value = array_values($value);
          }

          if(is_array($value)) {
            foreach($value as &$item) {
              if(is_array($item)) {
                foreach($item as $key2 => $value2) {
                  if(is_array($value2) && (array_values($value2)[0] ?? null)) {
                    $item[$key2] = array_values($value2)[0];
                  }
                }
              }
            }
          }
        }

        $record->setTranslation($key, $locale, $value);
      }
    }

    $this->data = $originalData;

    if (
      static::getResource()::isScopedToTenant() &&
      ($tenant = Filament::getTenant())
    ) {
      return $this->associateRecordWithTenant($record, $tenant);
    }

    $record->save();

    return $record;
  }

  public function updatingActiveLocale($locale): void
  {
    session()->put('filament.translatable.activeLocale', $locale);
    $this->oldActiveLocale = $this->activeLocale;
  }

  public function updatedActiveLocale(string $newActiveLocale): void
  {
    if (blank($this->oldActiveLocale)) {
      return;
    }

    $this->resetValidation();

    $translatableAttributes = static::getResource()::getTranslatableAttributes();

    $this->otherLocaleData[$this->oldActiveLocale] = Arr::only($this->data, $translatableAttributes);

    $this->data = [
      ...Arr::except($this->data, $translatableAttributes),
      ...$this->otherLocaleData[$this->activeLocale] ?? [],
    ];

    unset($this->otherLocaleData[$this->activeLocale]);
  }
}
