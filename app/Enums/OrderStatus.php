<?php

namespace App\Enums;

enum OrderStatus: string
{
    case AwaitingConfirmation = 'awaiting_confirmation';
    case AwaitingPriceApproval = 'awaiting_price_approval';
    case AwaitingPickup = 'awaiting_pickup';
    case Processing = 'processing';
    case ReadyForPickup = 'ready_for_pickup';
    case ReadyForDelivery = 'ready_for_delivery';
    case OutForDelivery = 'out_for_delivery';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::AwaitingConfirmation => 'Menunggu konfirmasi',
            self::AwaitingPriceApproval => 'Menunggu persetujuan harga',
            self::AwaitingPickup => 'Menunggu penjemputan',
            self::Processing => 'Sedang diproses',
            self::ReadyForPickup => 'Siap diambil',
            self::ReadyForDelivery => 'Siap diantar',
            self::OutForDelivery => 'Sedang diantar',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::AwaitingConfirmation, self::AwaitingPriceApproval, self::AwaitingPickup => 'bg-amber-100 text-amber-800 ring-amber-200',
            self::Processing, self::OutForDelivery => 'bg-blue-100 text-blue-800 ring-blue-200',
            self::ReadyForPickup, self::ReadyForDelivery => 'bg-cyan-100 text-cyan-800 ring-cyan-200',
            self::Completed => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            self::Cancelled => 'bg-rose-100 text-rose-800 ring-rose-200',
        };
    }

    /**
     * @return list<self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::AwaitingConfirmation => [
                self::AwaitingPriceApproval,
                self::AwaitingPickup,
                self::Cancelled,
            ],
            self::AwaitingPriceApproval => [
                self::AwaitingPickup,
                self::Cancelled,
            ],
            self::AwaitingPickup => [
                self::Processing,
                self::Cancelled,
            ],
            self::Processing => [
                self::ReadyForPickup,
                self::ReadyForDelivery,
            ],
            self::ReadyForPickup => [self::Completed],
            self::ReadyForDelivery => [self::OutForDelivery, self::Completed],
            self::OutForDelivery => [self::Completed],
            self::Completed, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNext(), true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled], true);
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
