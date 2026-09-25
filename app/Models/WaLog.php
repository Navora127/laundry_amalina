<?php

namespace App\Models;

use App\Enums\WaDeliveryStatus;
use Database\Factories\WaLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'user_id',
    'channel',
    'phone_e164',
    'message',
    'delivery_status',
    'prepared_at',
    'opened_at',
    'metadata',
])]
class WaLog extends Model
{
    /** @use HasFactory<WaLogFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delivery_status' => WaDeliveryStatus::class,
            'prepared_at' => 'datetime',
            'opened_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
