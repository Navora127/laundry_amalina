<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function record(
        Order $order,
        PaymentMethod $method,
        int $amount,
        User $actor,
        ?string $referenceNumber = null,
        ?UploadedFile $proof = null,
    ): Payment {
        if ($amount <= 0 || $amount > $order->final_total) {
            throw ValidationException::withMessages([
                'amount' => ['Nominal pembayaran tidak valid.'],
            ]);
        }

        return DB::transaction(function () use ($order, $method, $amount, $actor, $referenceNumber, $proof): Payment {
            $proofPath = $proof?->store('order-payment-proofs', config('laundryku.uploads.disk'));
            $status = $method === PaymentMethod::Cash ? PaymentStatus::Paid : PaymentStatus::Pending;

            $payment = $order->payments()->create([
                'method' => $method,
                'status' => $status,
                'amount' => $amount,
                'reference_number' => $referenceNumber,
                'proof_disk' => $proof ? config('laundryku.uploads.disk') : null,
                'proof_path' => $proofPath,
                'verified_by' => $status === PaymentStatus::Paid ? $actor->getKey() : null,
                'verified_at' => $status === PaymentStatus::Paid ? now() : null,
                'paid_at' => $status === PaymentStatus::Paid ? now() : null,
            ]);

            $this->syncOrderPaymentStatus($order);
            $this->auditLogger->record($order, 'payment.recorded', $actor, [], [
                'payment_id' => $payment->id,
                'amount' => $amount,
                'status' => $status->value,
            ]);

            return $payment->fresh();
        });
    }

    public function verify(Payment $payment, User $actor): Payment
    {
        return DB::transaction(function () use ($payment, $actor): Payment {
            $locked = Payment::query()->lockForUpdate()->with('order')->findOrFail($payment->getKey());

            if ($locked->status === PaymentStatus::Paid) {
                return $locked;
            }

            $locked->forceFill([
                'status' => PaymentStatus::Paid,
                'verified_by' => $actor->getKey(),
                'verified_at' => now(),
                'paid_at' => now(),
            ])->save();

            $this->syncOrderPaymentStatus($locked->order);
            $this->auditLogger->record($locked->order, 'payment.verified', $actor, [], [
                'payment_id' => $locked->id,
                'amount' => $locked->amount,
            ]);

            return $locked->fresh('order');
        });
    }

    private function syncOrderPaymentStatus(Order $order): void
    {
        $paid = (int) $order->payments()->where('status', PaymentStatus::Paid->value)->sum('amount');
        $pending = $order->payments()->where('status', PaymentStatus::Pending->value)->exists();
        $status = $paid >= $order->final_total && $order->final_total > 0
            ? PaymentStatus::Paid
            : ($pending ? PaymentStatus::Pending : PaymentStatus::Unpaid);

        $order->forceFill(['payment_status' => $status])->save();
    }
}
