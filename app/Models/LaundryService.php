<?php

namespace App\Models;

use App\Enums\ServiceUnit;
use Database\Factories\LaundryServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'name',
    'slug',
    'description',
    'unit',
    'price',
    'duration_minutes',
    'is_active',
])]
class LaundryService extends Model
{
    /** @use HasFactory<LaundryServiceFactory> */
    use HasFactory;

    protected $table = 'services';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit' => ServiceUnit::class,
            'price' => 'integer',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'service_id');
    }
}
