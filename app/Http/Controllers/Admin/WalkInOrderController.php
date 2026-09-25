<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FulfillmentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWalkInOrderRequest;
use App\Models\ServiceCategory;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WalkInOrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function create(): View
    {
        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->with(['services' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.orders.walk-in', compact('categories'));
    }

    public function store(StoreWalkInOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $order = $this->orders->createWalkIn(
            customerName: $validated['customer_name'],
            customerPhone: $validated['customer_phone'],
            customerEmail: $validated['customer_email'] ?? null,
            items: $validated['items'],
            fulfillmentMethod: FulfillmentMethod::from($validated['fulfillment_method']),
            actor: $request->user(),
            addressId: isset($validated['address_id']) ? (int) $validated['address_id'] : null,
            notes: $validated['notes'] ?? null,
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Pesanan walk-in berhasil dibuat.');
    }
}
