<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_payment_marks_order_as_paid(): void
    {
        $order = Order::factory()->create(['final_total' => 50000]);
        $staff = User::factory()->cashier()->create();

        $payment = app(PaymentService::class)->record(
            order: $order,
            method: PaymentMethod::Cash,
            amount: 50000,
            actor: $staff,
        );

        $this->assertSame(PaymentStatus::Paid, $payment->status);
        $this->assertSame(PaymentStatus::Paid, $order->fresh()->payment_status);
    }

    public function test_transfer_payment_requires_verification(): void
    {
        $order = Order::factory()->create(['final_total' => 50000]);
        $staff = User::factory()->cashier()->create();
        $payment = app(PaymentService::class)->record(
            order: $order,
            method: PaymentMethod::Transfer,
            amount: 50000,
            actor: $staff,
        );

        $this->assertSame(PaymentStatus::Pending, $payment->status);
        $this->assertSame(PaymentStatus::Pending, $order->fresh()->payment_status);

        $verified = app(PaymentService::class)->verify($payment, $staff);
        $this->assertSame(PaymentStatus::Paid, $verified->status);
        $this->assertSame(PaymentStatus::Paid, $order->fresh()->payment_status);
    }
}
