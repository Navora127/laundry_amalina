<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderCodeGenerator
{
    public function generate(): string
    {
        do {
            $code = 'LDR-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Order::query()->where('code', $code)->exists());

        return $code;
    }
}
