<?php

namespace Database\Factories;

use App\Enums\ServiceUnit;
use App\Models\LaundryService;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $service = LaundryService::factory();
        $quantity = fake()->randomFloat(2, 0.5, 10);
        $price = fake()->numberBetween(8000, 100000);
        $subtotal = (int) round($quantity * $price);

        return [
            'order_id' => Order::factory(),
            'service_id' => $service,
            'service_name' => $service->name ?? 'Layanan laundry',
            'service_unit' => ServiceUnit::Kilogram,
            'estimated_qty' => $quantity,
            'actual_qty' => null,
            'unit_price' => $price,
            'estimated_subtotal' => $subtotal,
            'final_subtotal' => $subtotal,
        ];
    }
}
