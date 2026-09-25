<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_only_view_their_own_order(): void
    {
        $owner = User::factory()->create();
        $ownerCustomer = Customer::factory()->create(['user_id' => $owner->id]);
        $order = Order::factory()->create(['customer_id' => $ownerCustomer->id]);

        $other = User::factory()->create();
        $otherCustomer = Customer::factory()->create(['user_id' => $other->id]);

        $this->actingAs($other)
            ->get(route('customer.orders.show', $order))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('customer.orders.show', $order))
            ->assertOk();
    }

    public function test_staff_can_view_any_order(): void
    {
        $order = Order::factory()->create();
        $staff = User::factory()->cashier()->create();

        $this->actingAs($staff)
            ->get(route('admin.orders.show', $order))
            ->assertOk();
    }
}
