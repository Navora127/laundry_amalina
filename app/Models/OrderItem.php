<?php

namespace App\Models;

use App\Enums\ServiceUnit;
use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'service_id',
    'service_name',
    'service_unit',
    'estimated_qty',
    'actual_qty',
    'unit_price',
    'estimated_subtotal',
    'final_subtotal',
])]
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'service_unit' => ServiceUnit::class,
            'estimated_qty' => 'decimal:2',
            'actual_qty' => 'decimal:2',
            'unit_price' => 'integer',
            'estimated_subtotal' => 'integer',
            'final_subtotal' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(LaundryService::class, 'service_id');
    }
}
