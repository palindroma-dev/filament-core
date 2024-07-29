<?php

namespace Filament\Core\Concerns\Resource;

use Filament\Resources\Pages\ListRecords;

class TranslatableListRecords extends ListRecords
{
  protected function transformTranslatableData($data): array
  {
    foreach(config('app.locales') as $locale) {
      foreach($data[$locale] as $key => $value) {
        $data[$key] = $data[$key] ?? [];
        $data[$key][$locale] = $value;
      }
      unset($data[$locale]);
    }

    return $data;
  }
}