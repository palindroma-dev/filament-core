<?php

namespace Filament\Core;

use Spatie\LaravelPackageTools\Package;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilamentCoreServiceProvider extends ServiceProvider
{
  public static string $name = 'filament-core';

  protected array $pages = [];

  protected array $resources = [];

  public function register(): void
  {
    $this->mergeConfigFrom(
      __DIR__ . '/../config/filament-core.php', 'filament-core'
    );
  }

  public function boot(): void
  {
    $this->publishes([
      __DIR__ . '/../config/filament-core.php' => config_path('filament-core.php'),
    ], 'config');

    $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    $this->loadFactories();
  }

  protected function loadFactories(): void
  {
    Factory::guessFactoryNamesUsing(function (string $modelName) {
      return 'Filament\\Core\\Database\\Factories\\' . class_basename($modelName) . 'Factory';
    });
  }

  public function packageConfigured(Package $package): void
  {

  }

  public function packageRegistered(): void
  {
  }

  public function packageBooted(): void
  {
  }

  protected function registerMacros(): void
  {

  }

  /**
   * Register the package's publishable resources.
   *
   * @return void
   */
  private function registerPublishableResources(): void
  {

  }

  /**
   * Register the package's namespaced migrations.
   *
   * @param array|string $migrations
   * @param string $namespace
   *
   * @return void
   */
  protected function registerNamespacedMigrations(array|string $migrations, string $namespace): void
  {

  }
}
