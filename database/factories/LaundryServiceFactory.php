<?php

namespace Database\Factories;

use App\Enums\ServiceUnit;
use App\Models\LaundryService;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LaundryService>
 */
class LaundryServiceFactory extends Factory
{
    protected $model = LaundryService::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'category_id' => ServiceCategory::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 999999),
            'description' => fake()->optional()->sentence(),
            'unit' => fake()->randomElement(ServiceUnit::cases()),
            'price' => fake()->numberBetween(8000, 100000),
            'duration_minutes' => fake()->numberBetween(60, 2880),
            'is_active' => true,
        ];
    }
}
