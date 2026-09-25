<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\WaTemplate;
use Illuminate\Database\Seeder;

class WaTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [OrderStatus::AwaitingConfirmation, 'Konfirmasi diterima', 'Halo {nama}, pesanan {kode} sudah kami terima dan menunggu konfirmasi.'],
            [OrderStatus::AwaitingPriceApproval, 'Persetujuan harga', 'Halo {nama}, ada penyesuaian harga untuk pesanan {kode}. Total sementara {total}. Mohon konfirmasi ya.'],
            [OrderStatus::AwaitingPickup, 'Menunggu penjemputan', 'Halo {nama}, pesanan {kode} sedang menunggu penjemputan.'],
            [OrderStatus::Processing, 'Sedang diproses', 'Halo {nama}, pesanan {kode} sedang kami proses.'],
            [OrderStatus::ReadyForPickup, 'Siap diambil', 'Halo {nama}, pesanan {kode} sudah siap diambil.'],
            [OrderStatus::ReadyForDelivery, 'Siap diantar', 'Halo {nama}, pesanan {kode} sudah siap diantar.'],
            [OrderStatus::OutForDelivery, 'Dalam pengiriman', 'Halo {nama}, pesanan {kode} sedang diantar.'],
            [OrderStatus::Completed, 'Pesanan selesai', 'Halo {nama}, pesanan {kode} telah selesai. Terima kasih sudah menggunakan layanan kami.'],
            [OrderStatus::Cancelled, 'Pesanan dibatalkan', 'Halo {nama}, pesanan {kode} telah dibatalkan.'],
        ];

        foreach ($templates as [$status, $name, $body]) {
            WaTemplate::updateOrCreate(
                ['status' => $status],
                ['name' => $name, 'body' => $body, 'is_active' => true],
            );
        }
    }
}
