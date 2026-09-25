<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\WaTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WaTemplate>
 */
class WaTemplateFactory extends Factory
{
    protected $model = WaTemplate::class;

    public function definition(): array
    {
        return [
            'status' => fake()->unique()->randomElement(OrderStatus::cases()),
            'name' => fake()->words(2, true),
            'body' => 'Halo {nama}, pesanan {kode} berstatus {status}.',
            'is_active' => true,
        ];
    }
}
