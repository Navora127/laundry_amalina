<?php

namespace App\Models;

use App\Enums\FulfillmentMethod;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'customer_id',
    'source',
    'fulfillment_method',
    'status',
    'payment_status',
    'currency',
    'customer_name',
    'customer_phone_e164',
    'customer_email',
    'estimated_subtotal',
    'final_subtotal',
    'discount_amount',
    'delivery_fee',
    'estimated_total',
    'final_total',
    'pickup_address_id',
    'delivery_address_id',
    'scheduled_at',
    'notes',
    'created_by',
    'cancelled_by',
    'cancellation_reason',
    'cancelled_at',
    'price_reviewed_at',
    'price_approved_at',
    'accepted_at',
    'started_at',
    'ready_at',
    'completed_at',
    'idempotency_key',
    'version',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source' => OrderSource::class,
            'fulfillment_method' => FulfillmentMethod::class,
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'estimated_subtotal' => 'integer',
            'final_subtotal' => 'integer',
            'discount_amount' => 'integer',
            'delivery_fee' => 'integer',
            'estimated_total' => 'integer',
            'final_total' => 'integer',
            'scheduled_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'price_reviewed_at' => 'datetime',
            'price_approved_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'ready_at' => 'datetime',
            'completed_at' => 'datetime',
            'version' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'pickup_address_id');
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'delivery_address_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function waLogs(): HasMany
    {
        return $this->hasMany(WaLog::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(OrderAttachment::class);
    }

    public function scopeForCustomer(Builder $query, Customer|int $customer): Builder
    {
        return $query->where(
            'customer_id',
            $customer instanceof Customer ? $customer->getKey() : $customer,
        );
    }

    public function isOwnedBy(Customer|int|null $customer): bool
    {
        $customerId = $customer instanceof Customer ? $customer->getKey() : $customer;

        return $customerId !== null && $this->customer_id === $customerId;
    }
}
