<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'app_name' => 'Aplikatif Base',
            'app_logo' => null,
            'app_favicon' => null,
            'primary_color' => '#4F46E5',
            'timezone' => 'Asia/Jakarta',
            'mail_from_name' => 'Aplikatif Base',
            'mail_from_address' => 'noreply@aplikatif.com',
            'maintenance_mode' => '0',
            'session_lifetime' => '120',
            'pwa_enabled' => '1',
            'pwa_short_name' => 'Aplikatif',
            'pwa_theme_color' => '#4F46E5',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
