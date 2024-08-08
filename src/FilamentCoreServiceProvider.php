<?php

namespace Filament\Core;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use RyanChandler\FilamentNavigation\Models\Navigation;
use Spatie\LaravelPackageTools\Package;

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

    $this->mergeConfigFrom(
      __DIR__ . '/../config/permission.php', 'permission'
    );
  }

  public function boot(): void
  {
    $this->publishes([
      __DIR__ . '/../config/filament-core.php' => config_path('filament-core.php'),
    ], 'config');

    $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    $this->loadFactories();

    $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-core');

    Filament::serving(function () {
      Filament::registerNavigationItems([
        ...Navigation::orderBy('id', 'ASC')->get()->map(function (Navigation $navigation) {
          return NavigationItem::make($navigation->name)
            ->url(route('filament.admin.resources.navigations.edit', $navigation->id))
            ->icon('heroicon-o-bars-3')
            ->group('Navigation');
        })->toArray(),
      ]);

      Filament::registerNavigationGroups([
        NavigationGroup::make('Navigation')->collapsed(),
        NavigationGroup::make('Site Content')->collapsed(),
        NavigationGroup::make('Configurations')->collapsed(),
        NavigationGroup::make('Taxonomies')->collapsed(),
        NavigationGroup::make('Roles and Permissions')->collapsed(),
      ]);
    });
  }

  protected function loadFactories(): void
  {
    Factory::guessFactoryNamesUsing(function (string $modelName) {
      $defaultFactoryNamespace = 'Database\\Factories\\' . class_basename($modelName) . 'Factory';

      if (class_exists($defaultFactoryNamespace)) {
        return $defaultFactoryNamespace;
      }

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
