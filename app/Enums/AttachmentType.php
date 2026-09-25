<?php

namespace App\Enums;

enum AttachmentType: string
{
    case WeighingProof = 'weighing_proof';
    case PaymentProof = 'payment_proof';
    case OrderPhoto = 'order_photo';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::WeighingProof => 'Bukti timbangan',
            self::PaymentProof => 'Bukti pembayaran',
            self::OrderPhoto => 'Foto pesanan',
            self::Other => 'Lainnya',
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
