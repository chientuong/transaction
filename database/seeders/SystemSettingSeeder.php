<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Domain\System\Infrastructure\Models\Setting::set('api_key_sepay', '', 'Sepay API Key', 'password');
        \App\Domain\System\Infrastructure\Models\Setting::set('api_system', 'secret_token_123', 'Hệ thống API Token (Authorization)', 'password');
        \App\Domain\System\Infrastructure\Models\Setting::set('setting_ttl', '15', 'Thời gian giao dịch', 'number');
    }
}
