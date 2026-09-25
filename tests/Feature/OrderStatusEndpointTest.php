<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_poll_owned_order_status(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($user)
            ->getJson(route('customer.orders.status', $order))
            ->assertOk()
            ->assertJsonPath('status', $order->status->value)
            ->assertJsonStructure(['status', 'label', 'updated_at', 'logs']);
    }
}
