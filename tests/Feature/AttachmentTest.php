<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_upload_private_order_attachment(): void
    {
        Storage::fake('local');
        $order = Order::factory()->create();
        $staff = User::factory()->cashier()->create();

        $this->actingAs($staff)
            ->post(route('admin.orders.attachments.store', $order), [
                'type' => 'weighing_proof',
                'file' => UploadedFile::fake()->image('proof.jpg'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_attachments', [
            'order_id' => $order->id,
            'type' => 'weighing_proof',
            'disk' => 'local',
        ]);
    }
}
