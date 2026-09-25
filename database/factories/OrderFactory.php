<?php

namespace Database\Factories;

use App\Enums\FulfillmentMethod;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $estimatedSubtotal = fake()->numberBetween(20000, 500000);
        $deliveryFee = fake()->boolean(30) ? 15000 : 0;

        return [
            'code' => 'LDR-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'customer_id' => Customer::factory(),
            'source' => OrderSource::Online,
            'fulfillment_method' => FulfillmentMethod::Pickup,
            'status' => OrderStatus::AwaitingConfirmation,
            'payment_status' => 'unpaid',
            'currency' => 'IDR',
            'customer_name' => fake()->name(),
            'customer_phone_e164' => '+62'.fake()->unique()->numerify('8##########'),
            'customer_email' => fake()->safeEmail(),
            'estimated_subtotal' => $estimatedSubtotal,
            'final_subtotal' => $estimatedSubtotal,
            'discount_amount' => 0,
            'delivery_fee' => $deliveryFee,
            'estimated_total' => $estimatedSubtotal + $deliveryFee,
            'final_total' => $estimatedSubtotal + $deliveryFee,
            'version' => 1,
        ];
    }

    public function walkIn(): static
    {
        return $this->state(fn (array $attributes): array => [
            'source' => OrderSource::WalkIn,
            'customer_id' => null,
        ]);
    }

    public function status(OrderStatus $status): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => $status,
        ]);
    }
}
