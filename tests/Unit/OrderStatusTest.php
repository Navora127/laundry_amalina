<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_status_machine_rejects_invalid_transition(): void
    {
        $this->assertFalse(OrderStatus::AwaitingConfirmation->canTransitionTo(OrderStatus::Completed));
        $this->assertTrue(OrderStatus::AwaitingConfirmation->canTransitionTo(OrderStatus::AwaitingPickup));
        $this->assertTrue(OrderStatus::ReadyForPickup->isTerminal() === false);
        $this->assertTrue(OrderStatus::Completed->isTerminal());
    }
}
