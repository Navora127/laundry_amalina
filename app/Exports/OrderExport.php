<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        private readonly CarbonInterface $from,
        private readonly CarbonInterface $to,
    ) {}

    /**
     * @return Builder<Order>
     */
    public function query()
    {
        return Order::query()
            ->with('customer.user')
            ->whereBetween('created_at', [$this->from, $this->to])
            ->latest();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return ['Kode', 'Pelanggan', 'Telepon', 'Status', 'Metode', 'Total', 'Dibuat'];
    }

    /**
     * @return list<string>
     */
    public function map(Order $order): array
    {
        return [
            $order->code,
            $order->customer_name,
            $order->customer_phone_e164,
            $order->status->label(),
            $order->fulfillment_method->label(),
            (string) $order->final_total,
            $order->created_at->format('Y-m-d H:i'),
        ];
    }
}
