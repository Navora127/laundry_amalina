<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\WaTemplate;
use App\Services\WaNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_whatsapp_is_logged_as_prepared(): void
    {
        $order = Order::factory()->create(['customer_phone_e164' => '+6281234567890']);
        $staff = User::factory()->cashier()->create();
        WaTemplate::factory()->create([
            'status' => $order->status,
            'body' => 'Halo {nama}, pesanan {kode} berstatus {status}.',
        ]);

        $result = app(WaNotificationService::class)->prepare($order, $staff);

        $this->assertStringContainsString('wa.me/6281234567890', $result['url']);
        $this->assertDatabaseHas('wa_logs', [
            'order_id' => $order->id,
            'delivery_status' => 'prepared',
        ]);
    }
}
