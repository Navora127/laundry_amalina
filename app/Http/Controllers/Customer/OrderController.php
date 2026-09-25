<?php

namespace App\Http\Controllers\Customer;

use App\Enums\FulfillmentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CancelOrderRequest;
use App\Http\Requests\Customer\StoreOrderRequest;
use App\Models\Order;
use App\Models\ServiceCategory;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Order::class);

        $orders = Order::query()
            ->forCustomer($request->user()->customer)
            ->with(['items', 'statusLogs'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $customer = request()->user()->customer;
        abort_if($customer === null, 403, 'Profil pelanggan tidak ditemukan.');

        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->with(['services' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->orderBy('sort_order')
            ->get();

        $addresses = $customer->addresses()->orderByDesc('is_default')->get();
        $selectedServiceId = request()->integer('service') ?: null;

        return view('customer.orders.create', compact('categories', 'addresses', 'selectedServiceId'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $customer = $request->user()->customer;
        abort_if($customer === null, 403, 'Profil pelanggan tidak ditemukan.');

        $order = $this->orders->createOnline(
            customer: $customer,
            items: $validated['items'],
            fulfillmentMethod: FulfillmentMethod::from($validated['fulfillment_method']),
            addressId: (int) $validated['address_id'],
            actor: $request->user(),
            notes: $validated['notes'] ?? null,
            idempotencyKey: $validated['idempotency_key'] ?? null,
            scheduledAt: $validated['scheduled_at'] ?? null,
        );

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat dan menunggu konfirmasi.');
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['items', 'statusLogs.creator', 'payments']);

        return view('customer.orders.show', compact('order'));
    }

    public function status(Request $request, Order $order)
    {
        Gate::authorize('view', $order);

        $order->load('statusLogs');

        return response()->json([
            'status' => $order->status->value,
            'label' => $order->status->label(),
            'updated_at' => $order->updated_at?->toIso8601String(),
            'logs' => $order->statusLogs->map(fn ($log): array => [
                'status' => $log->to_status->value,
                'label' => $log->to_status->label(),
                'note' => $log->note,
                'created_at' => $log->created_at?->toIso8601String(),
            ])->values(),
        ]);
    }

    public function approvePrice(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('approvePrice', $order);

        $this->orders->approvePrice($order, $request->user());

        return back()->with('success', 'Harga akhir telah disetujui.');
    }

    public function cancel(CancelOrderRequest $request, Order $order): RedirectResponse
    {
        $this->orders->cancel($order, $request->user(), $request->validated('reason'));

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
