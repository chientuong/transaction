<?php

namespace Database\Seeders;

use App\Domain\System\Infrastructure\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'api_system' => 'system_token_placeholder_' . str()->random(10),
            'setting_ttl' => 15,
            'sepay_auth_token' => 'sepay_webhook_token_placeholder',
            'api_key_sepay' => '',
            'bank_list' => [
                ['bank_code' => 'Vietcombank', 'bank_name' => 'Vietcombank'],
                ['bank_code' => 'Techcombank', 'bank_name' => 'Techcombank'],
                ['bank_code' => 'MBBank', 'bank_name' => 'MBBank'],
                ['bank_code' => 'BIDV', 'bank_name' => 'BIDV'],
                ['bank_code' => 'VietinBank', 'bank_name' => 'VietinBank'],
                ['bank_code' => 'VPBank', 'bank_name' => 'VPBank'],
                ['bank_code' => 'ACB', 'bank_name' => 'ACB'],
                ['bank_code' => 'Sacombank', 'bank_name' => 'Sacombank'],
                ['bank_code' => 'TPBank', 'bank_name' => 'TPBank'],
                ['bank_code' => 'VIB', 'bank_name' => 'VIB'],
                ['bank_code' => 'Agribank', 'bank_name' => 'Agribank'],
            ],
            'webhook_configs' => [],
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
