<?php

namespace Filament\Core\Actions\Forms;

use Filament\Actions\Concerns\HasTranslatableLocaleOptions;
use Filament\Forms\Components\Field;

class ModalLocaleSwitcher extends Field
{
  use HasTranslatableLocaleOptions;

  public function getOptions() {
    return config('app.locale_descriptions');
  }
}
