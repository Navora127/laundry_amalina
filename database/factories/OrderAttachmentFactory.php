<?php

namespace Database\Factories;

use App\Enums\AttachmentType;
use App\Models\Order;
use App\Models\OrderAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderAttachment>
 */
class OrderAttachmentFactory extends Factory
{
    protected $model = OrderAttachment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'type' => fake()->randomElement(AttachmentType::cases()),
            'disk' => 'local',
            'path' => 'attachments/'.fake()->uuid().'.jpg',
            'original_name' => 'bukti.jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(10000, 500000),
        ];
    }
}
