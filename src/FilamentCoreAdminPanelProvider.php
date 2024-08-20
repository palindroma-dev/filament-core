<?php

namespace Filament\Core;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Illuminate\Validation\Rules\Password;
use Filament\Support\Colors\Color;

abstract class FilamentCoreAdminPanelProvider extends PanelProvider
{
  protected function defaultPanel(Panel $panel): Panel
  {
    return $panel
      ->colors([
        'danger' => Color::Rose,
        'gray' => Color::Gray,
        'info' => Color::Blue,
        'primary' => 'rgb(31, 41, 55)',
        'success' => Color::Emerald,
        'warning' => Color::Orange,
      ])
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
        ->enableTwoFactorAuthentication(force: config('filament-core.force_two_factor_auth'))
        ->passwordUpdateRules(
          rules: [Password::default()->mixedCase()->uncompromised(3)], // you may pass an array of validation rules as well. (default = ['min:8'])
        )
      )
      ->brandLogo(fn () => view('filament.brand'));
  }
}
