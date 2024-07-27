<?php

namespace Filament\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Filament\Core\Models\Permission;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Filament\Core\Models\Permission>
 */
class PermissionFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Permission::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => $this->faker->word,
    ];
  }
}
