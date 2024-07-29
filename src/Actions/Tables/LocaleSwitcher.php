<?php

namespace Filament\Core\Actions\Tables;

use Filament\Tables\Actions\SelectAction;
use Filament\Actions\Concerns;

class LocaleSwitcher extends SelectAction
{
  use Concerns\HasTranslatableLocaleOptions;

  public static function getDefaultName(): ?string
  {
    return 'activeLocale';
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->label(__('filament-spatie-laravel-translatable-plugin::actions.active_locale.label'));

    $this->setTranslatableLocaleOptions();

    $this->view('filament-core::actions.locale-action');
  }
}
