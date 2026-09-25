<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value', 'type'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    protected function typedValue(): Attribute
    {
        return Attribute::make(
            get: function (): mixed {
                return match ($this->type) {
                    'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOL),
                    'integer' => (int) $this->value,
                    'json' => json_decode($this->value ?: 'null', true),
                    default => $this->value,
                };
            },
        );
    }
}
