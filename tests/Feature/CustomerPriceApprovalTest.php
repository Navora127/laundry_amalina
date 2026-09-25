<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPriceApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_approve_price_for_own_order(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::AwaitingPriceApproval,
        ]);

        $this->actingAs($user)
            ->post(route('customer.orders.approve-price', $order))
            ->assertRedirect();

        $this->assertSame(OrderStatus::AwaitingPickup, $order->fresh()->status);
    }

    public function test_customer_cannot_approve_another_customers_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::AwaitingPriceApproval,
        ]);

        $this->actingAs($user)
            ->post(route('customer.orders.approve-price', $order))
            ->assertForbidden();
    }
}
