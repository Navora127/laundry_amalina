<?php

namespace App\Services;

use App\Enums\ServiceUnit;
use App\Models\LaundryService;
use App\Models\Order;
use Illuminate\Validation\ValidationException;

class PricingService
{
    /**
     * @param  list<array{service_id: int|string, qty: int|float|string}>  $requestedItems
     * @return array{
     *     items: list<array{service_id: int, name: string, unit: ServiceUnit, qty: float, unit_price: int, subtotal: int}>,
     *     subtotal: int,
     *     discount: int,
     *     delivery_fee: int,
     *     total: int
     * }
     */
    public function estimate(array $requestedItems, int $discount = 0, int $deliveryFee = 0): array
    {
        if ($requestedItems === [] || count($requestedItems) > 20) {
            throw ValidationException::withMessages([
                'items' => ['Pesanan harus berisi 1–20 jenis layanan.'],
            ]);
        }

        $quantities = [];
        foreach ($requestedItems as $item) {
            $serviceId = (int) ($item['service_id'] ?? 0);
            $quantities[$serviceId] = ($quantities[$serviceId] ?? 0) + (float) ($item['qty'] ?? 0);
        }

        $services = LaundryService::query()
            ->whereIn('id', array_keys($quantities))
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->get()
            ->keyBy('id');

        if ($services->count() !== count($quantities)) {
            throw ValidationException::withMessages([
                'items' => ['Satu atau lebih layanan tidak tersedia.'],
            ]);
        }

        $lines = [];
        $subtotal = 0;

        foreach ($quantities as $serviceId => $quantity) {
            /** @var LaundryService $service */
            $service = $services->get((int) $serviceId);
            $quantityMilli = $this->quantityToMilli($quantity, $service->unit, (string) $serviceId);
            $lineSubtotal = $this->multiplyPrice($quantityMilli, $service->price);
            $normalizedQty = round($quantityMilli / 1000, 2);

            $lines[] = [
                'service_id' => $service->id,
                'name' => $service->name,
                'unit' => $service->unit,
                'qty' => $normalizedQty,
                'unit_price' => $service->price,
                'subtotal' => $lineSubtotal,
            ];

            $subtotal += $lineSubtotal;
        }

        $this->validateAdjustments($subtotal, $discount, $deliveryFee);

        return [
            'items' => $lines,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'delivery_fee' => $deliveryFee,
            'total' => $subtotal - $discount + $deliveryFee,
        ];
    }

    /**
     * @param  array<int, int|float|string>  $actualQuantities
     * @return array{
     *     items: list<array{order_item_id: int, qty: float, subtotal: int}>,
     *     subtotal: int,
     *     discount: int,
     *     delivery_fee: int,
     *     total: int
     * }
     */
    public function calculateFinal(Order $order, array $actualQuantities): array
    {
        $order->loadMissing('items');
        $itemsById = $order->items->keyBy('id');
        $subtotal = 0;
        $lines = [];

        foreach ($order->items as $item) {
            $quantity = $actualQuantities[$item->id] ?? null;
            $quantityMilli = $this->quantityToMilli($quantity, $item->service_unit, (string) $item->id);
            $lineSubtotal = $this->multiplyPrice($quantityMilli, $item->unit_price);
            $normalizedQty = round($quantityMilli / 1000, 2);

            $lines[] = [
                'order_item_id' => $item->id,
                'qty' => $normalizedQty,
                'subtotal' => $lineSubtotal,
            ];

            $subtotal += $lineSubtotal;
        }

        if ($itemsById->count() !== count($actualQuantities)) {
            throw ValidationException::withMessages([
                'items' => ['Data aktual tidak sesuai item pesanan.'],
            ]);
        }

        $this->validateAdjustments($subtotal, $order->discount_amount, $order->delivery_fee);

        return [
            'items' => $lines,
            'subtotal' => $subtotal,
            'discount' => $order->discount_amount,
            'delivery_fee' => $order->delivery_fee,
            'total' => $subtotal - $order->discount_amount + $order->delivery_fee,
        ];
    }

    public function requiresApproval(int $estimatedTotal, int $finalTotal): bool
    {
        if ($estimatedTotal <= 0) {
            return false;
        }

        $threshold = (int) config('laundryku.price_approval_threshold_percent', 10);
        $changePercent = abs($finalTotal - $estimatedTotal) / $estimatedTotal * 100;

        return $changePercent >= $threshold;
    }

    private function quantityToMilli(mixed $quantity, ServiceUnit $unit, string $key): int
    {
        if (! is_numeric($quantity)) {
            throw ValidationException::withMessages([
                "items.$key.qty" => ['Jumlah tidak valid.'],
            ]);
        }

        $value = (float) $quantity;
        $milli = (int) round($value * 1000);

        if ($value <= 0 || $milli <= 0 || $value > 9999.999) {
            throw ValidationException::withMessages([
                "items.$key.qty" => ['Jumlah harus lebih dari 0 dan maksimal 9.999,999.'],
            ]);
        }

        if ($unit === ServiceUnit::Piece && $milli % 1000 !== 0) {
            throw ValidationException::withMessages([
                "items.$key.qty" => ['Layanan ini harus menggunakan jumlah bulat.'],
            ]);
        }

        if ($milli % 10 !== 0) {
            throw ValidationException::withMessages([
                "items.$key.qty" => ['Jumlah maksimal memiliki 2 angka desimal.'],
            ]);
        }

        return $milli;
    }

    private function multiplyPrice(int $quantityMilli, int $price): int
    {
        return intdiv(($quantityMilli * $price) + 500, 1000);
    }

    private function validateAdjustments(int $subtotal, int $discount, int $deliveryFee): void
    {
        if ($discount < 0 || $discount > $subtotal) {
            throw ValidationException::withMessages([
                'discount' => ['Diskon tidak valid.'],
            ]);
        }

        if ($deliveryFee < 0) {
            throw ValidationException::withMessages([
                'delivery_fee' => ['Biaya antar tidak valid.'],
            ]);
        }
    }
}
