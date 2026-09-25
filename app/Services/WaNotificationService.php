<?php

namespace App\Services;

use App\Enums\WaDeliveryStatus;
use App\Models\Order;
use App\Models\User;
use App\Models\WaLog;
use App\Models\WaTemplate;
use Illuminate\Validation\ValidationException;

class WaNotificationService
{
    public function __construct(
        private readonly PhoneNumberService $phoneNumbers,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return array{log: WaLog, url: string}
     */
    public function prepare(Order $order, User $actor): array
    {
        $template = WaTemplate::query()
            ->where('status', $order->status->value)
            ->where('is_active', true)
            ->first();

        $message = strtr($template?->body ?? 'Halo {nama}, pesanan {kode} saat ini berstatus *{status}*. Total: Rp{total}.', [
            '{nama}' => $order->customer_name,
            '{kode}' => $order->code,
            '{status}' => $order->status->label(),
            '{total}' => number_format($order->final_total, 0, ',', '.'),
            '{tanggal}' => $order->updated_at->format('d M Y H:i'),
        ]);

        try {
            $phone = $this->phoneNumbers->normalize($order->customer_phone_e164);
        } catch (\InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'phone' => ['Nomor WhatsApp pelanggan tidak valid.'],
            ]);
        }

        $log = $order->waLogs()->create([
            'user_id' => $actor->getKey(),
            'channel' => 'wa_manual',
            'phone_e164' => $phone,
            'message' => $message,
            'delivery_status' => WaDeliveryStatus::Prepared,
            'prepared_at' => now(),
        ]);

        $this->auditLogger->record($order, 'whatsapp.prepared', $actor, [], [
            'wa_log_id' => $log->id,
            'status' => $order->status->value,
        ]);

        $url = rtrim((string) config('laundryku.wa_base_url'), '/').'/'.ltrim($phone, '+').'?text='.rawurlencode($message);

        return ['log' => $log, 'url' => $url];
    }
}
