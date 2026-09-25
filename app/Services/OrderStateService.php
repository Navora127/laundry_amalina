<?php

namespace App\Services;

use App\Enums\FulfillmentMethod;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class OrderStateService
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function transition(
        Order $order,
        OrderStatus $next,
        User $actor,
        ?string $note = null,
        array $metadata = [],
    ): Order {
        return DB::transaction(function () use ($order, $next, $actor, $note, $metadata): Order {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            $current = $locked->status;
            $customerPriceApproval = $next === OrderStatus::AwaitingPickup
                && $current === OrderStatus::AwaitingPriceApproval
                && $actor->customer !== null
                && $locked->isOwnedBy($actor->customer);

            if ($next !== OrderStatus::Cancelled && ! $actor->isStaff() && ! $customerPriceApproval) {
                throw new AuthorizationException('Hanya staff atau pemilik pesanan yang dapat melakukan perubahan ini.');
            }

            if (! $current->canTransitionTo($next)) {
                throw new AuthorizationException("Status {$current->value} tidak dapat diubah menjadi {$next->value}.");
            }

            $this->assertFulfillmentTransition($locked, $current, $next);

            if ($next === OrderStatus::Cancelled && blank($note)) {
                throw new AuthorizationException('Alasan pembatalan wajib diisi.');
            }

            $oldValues = ['status' => $current->value];

            $locked->status = $next;
            $locked->version++;
            $this->applyStatusTimestamps($locked, $next);

            $locked->save();

            $locked->statusLogs()->create([
                'from_status' => $current,
                'to_status' => $next,
                'note' => $note,
                'metadata' => $metadata ?: null,
                'created_by' => $actor->getKey(),
            ]);

            $this->auditLogger->record(
                $locked,
                'order.status_changed',
                $actor,
                $oldValues,
                ['status' => $next->value],
            );

            return $locked->fresh(['items', 'statusLogs', 'payments']);
        });
    }

    private function assertFulfillmentTransition(
        Order $order,
        OrderStatus $current,
        OrderStatus $next,
    ): void {
        if ($current !== OrderStatus::Processing) {
            return;
        }

        $validNext = match ($order->fulfillment_method) {
            FulfillmentMethod::Pickup => $next === OrderStatus::ReadyForPickup,
            FulfillmentMethod::Delivery => in_array(
                $next,
                [OrderStatus::ReadyForDelivery],
                true,
            ),
        };

        if (! $validNext) {
            throw new AuthorizationException('Status siap tidak sesuai metode pengambilan pesanan.');
        }
    }

    private function applyStatusTimestamps(Order $order, OrderStatus $status): void
    {
        match ($status) {
            OrderStatus::AwaitingPickup => $order->accepted_at ??= now(),
            OrderStatus::Processing => $order->started_at ??= now(),
            OrderStatus::ReadyForPickup, OrderStatus::ReadyForDelivery => $order->ready_at ??= now(),
            OrderStatus::Completed => $order->completed_at ??= now(),
            OrderStatus::Cancelled => $order->cancelled_at = now(),
            default => null,
        };
    }
}
