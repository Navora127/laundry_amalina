<?php

namespace App\Services;

use App\Enums\FulfillmentMethod;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly PricingService $pricing,
        private readonly OrderCodeGenerator $codeGenerator,
        private readonly OrderStateService $stateService,
        private readonly AuditLogger $auditLogger,
        private readonly PhoneNumberService $phoneNumbers,
    ) {}

    /**
     * @param  list<array{service_id: int|string, qty: int|float|string}>  $items
     */
    public function createWalkIn(
        string $customerName,
        string $customerPhone,
        ?string $customerEmail,
        array $items,
        FulfillmentMethod $fulfillmentMethod,
        User $actor,
        ?int $addressId = null,
        ?string $notes = null,
    ): Order {
        try {
            $phone = $this->phoneNumbers->normalize($customerPhone);
        } catch (\InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'customer_phone' => [$exception->getMessage()],
            ]);
        }

        $customer = Customer::query()->firstOrCreate(
            ['phone_e164' => $phone],
            ['user_id' => null],
        );
        $address = $addressId === null
            ? null
            : Address::query()->whereKey($addressId)->where('customer_id', $customer->id)->first();

        if ($addressId !== null && $address === null) {
            throw ValidationException::withMessages([
                'address_id' => ['Alamat tidak valid.'],
            ]);
        }

        if ($fulfillmentMethod === FulfillmentMethod::Delivery && $address === null) {
            throw ValidationException::withMessages([
                'address_id' => ['Pesanan antar wajib memiliki alamat.'],
            ]);
        }

        $pricing = $this->pricing->estimate(
            $items,
            0,
            $fulfillmentMethod === FulfillmentMethod::Delivery ? (int) config('laundryku.delivery_fee') : 0,
        );

        return DB::transaction(function () use (
            $customer,
            $customerName,
            $customerEmail,
            $phone,
            $pricing,
            $fulfillmentMethod,
            $address,
            $actor,
            $notes,
        ): Order {
            $order = Order::create([
                'code' => $this->codeGenerator->generate(),
                'customer_id' => $customer->id,
                'source' => OrderSource::WalkIn,
                'fulfillment_method' => $fulfillmentMethod,
                'status' => OrderStatus::AwaitingConfirmation,
                'payment_status' => 'unpaid',
                'currency' => config('laundryku.currency'),
                'customer_name' => trim($customerName),
                'customer_phone_e164' => $phone,
                'customer_email' => $customerEmail,
                'estimated_subtotal' => $pricing['subtotal'],
                'final_subtotal' => $pricing['subtotal'],
                'discount_amount' => $pricing['discount'],
                'delivery_fee' => $pricing['delivery_fee'],
                'estimated_total' => $pricing['total'],
                'final_total' => $pricing['total'],
                'pickup_address_id' => $fulfillmentMethod === FulfillmentMethod::Pickup ? $address?->id : null,
                'delivery_address_id' => $fulfillmentMethod === FulfillmentMethod::Delivery ? $address?->id : null,
                'notes' => $notes,
                'created_by' => $actor->id,
                'version' => 1,
            ]);

            foreach ($pricing['items'] as $line) {
                $order->items()->create([
                    'service_id' => $line['service_id'],
                    'service_name' => $line['name'],
                    'service_unit' => $line['unit'],
                    'estimated_qty' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                    'estimated_subtotal' => $line['subtotal'],
                    'final_subtotal' => $line['subtotal'],
                ]);
            }

            $order->statusLogs()->create([
                'from_status' => null,
                'to_status' => OrderStatus::AwaitingConfirmation,
                'note' => 'Pesanan walk-in dibuat oleh staff.',
                'created_by' => $actor->id,
            ]);
            $this->auditLogger->record($order, 'order.created', $actor, [], [
                'code' => $order->code,
                'source' => OrderSource::WalkIn->value,
            ]);

            return $order->load(['items', 'statusLogs']);
        }, 3);
    }

    /**
     * @param  list<array{service_id: int|string, qty: int|float|string}>  $items
     */
    public function createOnline(
        Customer $customer,
        array $items,
        FulfillmentMethod $fulfillmentMethod,
        int $addressId,
        User $actor,
        ?string $notes = null,
        ?string $idempotencyKey = null,
        ?string $scheduledAt = null,
    ): Order {
        if ($idempotencyKey !== null) {
            $existing = Order::query()
                ->where('idempotency_key', $idempotencyKey)
                ->where('customer_id', $customer->getKey())
                ->first();

            if ($existing !== null) {
                return $existing->load(['items', 'statusLogs']);
            }
        }

        $address = Address::query()
            ->whereKey($addressId)
            ->where('customer_id', $customer->getKey())
            ->first();

        if ($address === null) {
            throw ValidationException::withMessages([
                'address_id' => ['Alamat tidak ditemukan atau bukan milik pelanggan.'],
            ]);
        }

        $deliveryFee = $fulfillmentMethod === FulfillmentMethod::Delivery
            ? (int) config('laundryku.delivery_fee')
            : 0;
        $pricing = $this->pricing->estimate($items, 0, $deliveryFee);

        return DB::transaction(function () use (
            $customer,
            $actor,
            $fulfillmentMethod,
            $address,
            $pricing,
            $notes,
            $idempotencyKey,
            $scheduledAt,
        ): Order {
            $order = Order::create([
                'code' => $this->codeGenerator->generate(),
                'customer_id' => $customer->getKey(),
                'source' => OrderSource::Online,
                'fulfillment_method' => $fulfillmentMethod,
                'status' => OrderStatus::AwaitingConfirmation,
                'payment_status' => 'unpaid',
                'currency' => config('laundryku.currency'),
                'customer_name' => $actor->name,
                'customer_phone_e164' => $customer->phone_e164,
                'customer_email' => $actor->email,
                'estimated_subtotal' => $pricing['subtotal'],
                'final_subtotal' => $pricing['subtotal'],
                'discount_amount' => $pricing['discount'],
                'delivery_fee' => $pricing['delivery_fee'],
                'estimated_total' => $pricing['total'],
                'final_total' => $pricing['total'],
                'pickup_address_id' => $fulfillmentMethod === FulfillmentMethod::Pickup
                    ? $address->getKey()
                    : null,
                'delivery_address_id' => $fulfillmentMethod === FulfillmentMethod::Delivery
                    ? $address->getKey()
                    : null,
                'scheduled_at' => $scheduledAt,
                'notes' => $notes,
                'created_by' => $actor->getKey(),
                'idempotency_key' => $idempotencyKey,
                'version' => 1,
            ]);

            foreach ($pricing['items'] as $line) {
                $order->items()->create([
                    'service_id' => $line['service_id'],
                    'service_name' => $line['name'],
                    'service_unit' => $line['unit'],
                    'estimated_qty' => $line['qty'],
                    'actual_qty' => null,
                    'unit_price' => $line['unit_price'],
                    'estimated_subtotal' => $line['subtotal'],
                    'final_subtotal' => $line['subtotal'],
                ]);
            }

            $order->statusLogs()->create([
                'from_status' => null,
                'to_status' => OrderStatus::AwaitingConfirmation,
                'note' => 'Pesanan dibuat oleh pelanggan.',
                'created_by' => $actor->getKey(),
            ]);

            $this->auditLogger->record($order, 'order.created', $actor, [], [
                'code' => $order->code,
                'estimated_total' => $order->estimated_total,
            ]);

            return $order->load(['items', 'statusLogs']);
        }, 3);
    }

    /**
     * @param  array<int, int|float|string>  $actualQuantities
     */
    public function recalculateFinal(
        Order $order,
        array $actualQuantities,
        User $actor,
        ?string $note = null,
    ): Order {
        if (! $actor->isStaff()) {
            throw ValidationException::withMessages([
                'status' => ['Hanya staff yang dapat memperbarui harga final.'],
            ]);
        }

        return DB::transaction(function () use ($order, $actualQuantities, $actor, $note): Order {
            $locked = Order::query()
                ->lockForUpdate()
                ->with('items')
                ->findOrFail($order->getKey());

            if (! in_array(
                $locked->status,
                [OrderStatus::AwaitingConfirmation, OrderStatus::AwaitingPriceApproval],
                true,
            )) {
                throw ValidationException::withMessages([
                    'status' => ['Harga final tidak dapat diubah pada status saat ini.'],
                ]);
            }

            $pricing = $this->pricing->calculateFinal($locked, $actualQuantities);
            $oldValues = [
                'status' => $locked->status->value,
                'final_total' => $locked->final_total,
            ];

            foreach ($pricing['items'] as $line) {
                $locked->items()
                    ->whereKey($line['order_item_id'])
                    ->update([
                        'actual_qty' => $line['qty'],
                        'final_subtotal' => $line['subtotal'],
                        'updated_at' => now(),
                    ]);
            }

            $requiresApproval = $this->pricing->requiresApproval(
                $locked->estimated_total,
                $pricing['total'],
            );
            $next = $requiresApproval
                ? OrderStatus::AwaitingPriceApproval
                : OrderStatus::AwaitingPickup;

            $locked->status = $next;
            $locked->final_subtotal = $pricing['subtotal'];
            $locked->final_total = $pricing['total'];
            $locked->price_reviewed_at = now();
            $locked->price_approved_at = $requiresApproval ? null : now();
            $locked->accepted_at = $requiresApproval ? null : now();
            $locked->version++;
            $locked->save();

            if ($oldValues['status'] !== $next->value) {
                $locked->statusLogs()->create([
                    'from_status' => $oldValues['status'],
                    'to_status' => $next,
                    'note' => $note ?? 'Harga akhir diverifikasi oleh staff.',
                    'metadata' => [
                        'estimated_total' => $locked->estimated_total,
                        'final_total' => $locked->final_total,
                    ],
                    'created_by' => $actor->getKey(),
                ]);
            }

            $this->auditLogger->record($order, 'order.price_recalculated', $actor, $oldValues, [
                'status' => $next->value,
                'final_total' => $locked->final_total,
            ]);

            return $locked->fresh(['items', 'statusLogs']);
        }, 3);
    }

    public function approvePrice(Order $order, User $actor, ?string $note = null): Order
    {
        $order = $this->stateService->transition(
            $order,
            OrderStatus::AwaitingPickup,
            $actor,
            $note ?? 'Pelanggan menyetujui harga akhir.',
        );

        $order->forceFill([
            'price_approved_at' => now(),
        ])->save();

        return $order->fresh(['items', 'statusLogs']);
    }

    public function cancel(Order $order, User $actor, string $reason): Order
    {
        return $this->stateService->transition(
            $order,
            OrderStatus::Cancelled,
            $actor,
            $reason,
        );
    }
}
