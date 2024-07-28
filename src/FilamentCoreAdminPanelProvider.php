<?php

namespace Filament\Core;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Illuminate\Validation\Rules\Password;

abstract class FilamentCoreAdminPanelProvider extends PanelProvider
{
  protected function defaultPanel(Panel $panel): Panel
  {
    return $panel
      ->plugin(
        FilamentSpatieRolesPermissionsPlugin::make()
      )
      ->plugin(
        SpatieLaravelTranslatablePlugin::make()
          ->defaultLocales(config('app.locales'))
      )
      ->plugin(
        BreezyCore::make()
        ->myProfile()
        ->enableTwoFactorAuthentication(force: true)
        ->passwordUpdateRules(
          rules: [Password::default()->mixedCase()->uncompromised(3)], // you may pass an array of validation rules as well. (default = ['min:8'])
        )
      );
  }
}
