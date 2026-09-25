<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $todayRevenue = Order::query()
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->whereNotIn('status', [OrderStatus::Cancelled->value])
            ->sum('final_total');

        $stats = [
            'total_orders' => Order::query()->count(),
            'pending_orders' => Order::query()
                ->whereIn('status', [
                    OrderStatus::AwaitingConfirmation->value,
                    OrderStatus::AwaitingPriceApproval->value,
                    OrderStatus::AwaitingPickup->value,
                ])
                ->count(),
            'processing_orders' => Order::query()->where('status', OrderStatus::Processing->value)->count(),
            'ready_orders' => Order::query()->whereIn('status', [
                OrderStatus::ReadyForPickup->value,
                OrderStatus::ReadyForDelivery->value,
            ])->count(),
            'uncollected_orders' => Order::query()
                ->where('status', OrderStatus::ReadyForPickup->value)
                ->where('created_at', '<', now()->subDays(3))
                ->count(),
            'today_revenue' => $todayRevenue,
        ];

        $recentOrders = Order::query()
            ->with('customer.user')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
