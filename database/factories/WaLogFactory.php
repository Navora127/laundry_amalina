<?php

namespace Database\Factories;

use App\Enums\WaDeliveryStatus;
use App\Models\Order;
use App\Models\User;
use App\Models\WaLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WaLog>
 */
class WaLogFactory extends Factory
{
    protected $model = WaLog::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'channel' => 'wa_manual',
            'phone_e164' => '+62'.fake()->unique()->numerify('8##########'),
            'message' => fake()->sentence(),
            'delivery_status' => WaDeliveryStatus::Prepared,
            'prepared_at' => now(),
            'metadata' => null,
        ];
    }
}
