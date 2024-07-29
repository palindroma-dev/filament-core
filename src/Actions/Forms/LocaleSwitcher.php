<?php

namespace Filament\Core\Actions\Forms;

use Filament\Actions\Action;
use Filament\Actions\Concerns;
use Filament\Actions\Concerns\HasSelect;

class LocaleSwitcher extends Action
{
  use Concerns\HasTranslatableLocaleOptions, HasSelect;

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
