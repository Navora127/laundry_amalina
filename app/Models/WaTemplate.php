<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\WaTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['status', 'name', 'body', 'is_active'])]
class WaTemplate extends Model
{
    /** @use HasFactory<WaTemplateFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'is_active' => 'boolean',
        ];
    }
}
