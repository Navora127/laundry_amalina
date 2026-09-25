<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecalculateOrderRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\OrderStateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly OrderStateService $state,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Order::class);

        $status = $request->string('status')->toString();
        $search = $request->string('search')->trim()->toString();
        $query = Order::query()->with(['customer.user', 'creator'])->latest();

        if ($status !== '' && OrderStatus::tryFrom($status) !== null) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone_e164', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
            'selectedStatus' => $status,
            'search' => $search,
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['items', 'statusLogs.creator', 'payments.verifier', 'customer.user', 'attachments']);
        $paidAmount = $order->payments
            ->filter(fn ($payment): bool => $payment->status === PaymentStatus::Paid)
            ->sum('amount');

        return view('admin.orders.show', compact('order', 'paidAmount'));
    }

    public function recalculate(RecalculateOrderRequest $request, Order $order): RedirectResponse
    {
        Gate::authorize('update', $order);

        $quantities = collect($request->validated('items'))
            ->mapWithKeys(fn (array $item): array => [(int) $item['order_item_id'] => $item['actual_qty']])
            ->all();

        $this->orders->recalculateFinal(
            $order,
            $quantities,
            $request->user(),
            $request->validated('note'),
        );

        return back()->with('success', 'Harga aktual berhasil diperbarui.');
    }

    public function transition(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $status = OrderStatus::from($request->validated('status'));

        if ($status === OrderStatus::Cancelled) {
            $this->orders->cancel($order, $request->user(), $request->validated('note'));
        } else {
            $this->state->transition(
                $order,
                $status,
                $request->user(),
                $request->validated('note'),
            );
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function approvePrice(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('approvePrice', $order);

        $this->orders->approvePrice(
            $order,
            $request->user(),
            $request->input('note'),
        );

        return back()->with('success', 'Harga akhir disetujui.');
    }
}
