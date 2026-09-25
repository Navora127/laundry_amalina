<?php

namespace App\Enums;

enum WaDeliveryStatus: string
{
    case Prepared = 'prepared';
    case Opened = 'opened';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Prepared => 'Siap dikirim',
            self::Opened => 'Tautan WhatsApp dibuka',
            self::Unknown => 'Status tidak diketahui',
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
