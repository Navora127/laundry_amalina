<?php

namespace App\Enums;

enum ServiceUnit: string
{
    case Kilogram = 'kg';
    case Piece = 'pcs';

    public function label(): string
    {
        return match ($this) {
            self::Kilogram => 'per kilogram',
            self::Piece => 'per pcs',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
