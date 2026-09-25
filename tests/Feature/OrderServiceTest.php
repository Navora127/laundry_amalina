<?php

namespace Tests\Feature;

use App\Enums\FulfillmentMethod;
use App\Enums\OrderStatus;
use App\Enums\ServiceUnit;
use App\Models\Address;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\User;
use App\Services\OrderService;
use App\Services\OrderStateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created_recalculated_and_completed(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id]);
        $service = LaundryService::factory()->create([
            'unit' => ServiceUnit::Kilogram,
            'price' => 10000,
        ]);
        $staff = User::factory()->cashier()->create();

        $order = app(OrderService::class)->createOnline(
            customer: $customer,
            items: [['service_id' => $service->id, 'qty' => 2.5]],
            fulfillmentMethod: FulfillmentMethod::Pickup,
            addressId: $address->id,
            actor: $user,
            idempotencyKey: 'test-idempotency-1',
        );

        $this->assertSame(25000, $order->estimated_total);
        $this->assertSame(OrderStatus::AwaitingConfirmation, $order->status);

        $actualQuantities = [$order->items->first()->id => 3];
        $order = app(OrderService::class)->recalculateFinal($order, $actualQuantities, $staff);

        $this->assertSame(30000, $order->final_total);
        $this->assertSame(OrderStatus::AwaitingPriceApproval, $order->status);

        $order = app(OrderService::class)->approvePrice($order, $staff);
        $this->assertSame(OrderStatus::AwaitingPickup, $order->status);

        $state = app(OrderStateService::class);
        $order = $state->transition($order, OrderStatus::Processing, $staff);
        $order = $state->transition($order, OrderStatus::ReadyForPickup, $staff);
        $order = $state->transition($order, OrderStatus::Completed, $staff);

        $this->assertSame(OrderStatus::Completed, $order->status);
        $this->assertCount(6, $order->statusLogs);
    }
}
