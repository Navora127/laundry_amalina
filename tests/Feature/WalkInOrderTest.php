<?php

namespace Tests\Feature;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\ServiceUnit;
use App\Models\LaundryService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalkInOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_walk_in_order_without_user_account(): void
    {
        $staff = User::factory()->cashier()->create();
        $service = LaundryService::factory()->create([
            'unit' => ServiceUnit::Kilogram,
            'price' => 10000,
        ]);

        $response = $this->actingAs($staff)->post(route('admin.orders.walk-in.store'), [
            'customer_name' => 'Pelanggan Toko',
            'customer_phone' => '0812 9876 5432',
            'fulfillment_method' => 'pickup',
            'items' => [
                ['service_id' => $service->id, 'qty' => 2],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'source' => OrderSource::WalkIn->value,
            'status' => OrderStatus::AwaitingConfirmation->value,
            'customer_name' => 'Pelanggan Toko',
        ]);
        $this->assertDatabaseHas('customers', [
            'phone_e164' => '+6281298765432',
            'user_id' => null,
        ]);
    }

    public function test_walk_in_form_is_reachable_for_staff(): void
    {
        $staff = User::factory()->cashier()->create();

        $this->actingAs($staff)
            ->get(route('admin.orders.walk-in.create'))
            ->assertOk()
            ->assertSee('Input laundry manual', escape: false);
    }

    public function test_walk_in_total_is_calculated_from_server_side_pricing(): void
    {
        $staff = User::factory()->cashier()->create();
        LaundryService::factory()->create([
            'unit' => ServiceUnit::Kilogram,
            'price' => 12000,
        ]);

        $response = $this->actingAs($staff)->post(route('admin.orders.walk-in.store'), [
            'customer_name' => 'Pelanggan Toko',
            'customer_phone' => '0812 5555 1111',
            'fulfillment_method' => 'pickup',
            'items' => [
                ['service_id' => LaundryService::query()->value('id'), 'qty' => 3.5],
            ],
        ]);

        $response->assertRedirect();

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertSame(42000, (int) $order->final_total);
    }

    public function test_customer_cannot_open_walk_in_form(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.orders.walk-in.create'))
            ->assertForbidden();
    }
}
