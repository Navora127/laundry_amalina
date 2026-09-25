<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    /**
     * @return array<string, int>
     */
    public function summary(CarbonInterface $from, CarbonInterface $to): array
    {
        $base = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', [OrderStatus::Cancelled->value]);

        return [
            'total_orders' => (clone $base)->count(),
            'completed_orders' => (clone $base)->where('status', OrderStatus::Completed->value)->count(),
            'cancelled_orders' => Order::query()->whereBetween('created_at', [$from, $to])->where('status', OrderStatus::Cancelled->value)->count(),
            'revenue' => (int) (clone $base)->where('payment_status', PaymentStatus::Paid->value)->sum('final_total'),
            'active_customers' => (clone $base)->whereNotNull('customer_id')->distinct()->count('customer_id'),
            'uncollected_orders' => Order::query()
                ->where('status', OrderStatus::ReadyForPickup->value)
                ->where('created_at', '<', $to->copy()->subDays(3))
                ->count(),
        ];
    }

    /**
     * @return Builder<Order>
     */
    public function ordersQuery(CarbonInterface $from, CarbonInterface $to)
    {
        return Order::query()
            ->with(['customer.user', 'creator'])
            ->whereBetween('created_at', [$from, $to])
            ->latest();
    }
}
