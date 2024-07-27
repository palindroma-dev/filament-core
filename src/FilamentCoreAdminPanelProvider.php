<?php

namespace Filament\Core;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\PanelProvider;
use Filament\Panel;
use Filament\SpatieLaravelTranslatablePlugin;

abstract class FilamentCoreAdminPanelProvider extends PanelProvider
{
  protected function defaultPanel(Panel $panel): Panel
  {
    return $panel
      ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
      ->plugin(SpatieLaravelTranslatablePlugin::make()->defaultLocales(config('app.locales')));
  }
}
