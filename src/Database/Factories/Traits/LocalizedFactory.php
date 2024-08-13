<?php

namespace Filament\Core\Database\Factories\Traits;

trait LocalizedFactory {
  private function createLocalizedData($callback, $locales): array {
    return array_combine($locales, array_map($callback, $locales));
  }
}