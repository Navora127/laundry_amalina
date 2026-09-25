<?php

namespace Tests\Feature;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\ServiceUnit;
use App\Models\LaundryService;
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
}
