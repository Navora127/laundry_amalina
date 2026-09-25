<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function store(StorePaymentRequest $request, Order $order): RedirectResponse
    {
        Gate::authorize('update', $order);

        $validated = $request->validated();
        $this->payments->record(
            order: $order,
            method: PaymentMethod::from($validated['method']),
            amount: (int) $validated['amount'],
            actor: $request->user(),
            referenceNumber: $validated['reference_number'] ?? null,
            proof: $request->file('proof'),
        );

        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('update', $payment->order);
        $this->payments->verify($payment, $request->user());

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }
}
