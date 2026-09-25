<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store.name', 'value' => 'LaundryKu', 'type' => 'string'],
            ['key' => 'store.phone_e164', 'value' => '+6281234567890', 'type' => 'string'],
            ['key' => 'store.address', 'value' => 'Alamat toko laundry', 'type' => 'string'],
            ['key' => 'order.price_approval_threshold', 'value' => '10', 'type' => 'integer'],
            ['key' => 'order.auto_archive_days', 'value' => '90', 'type' => 'integer'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
