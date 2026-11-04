<?php

namespace Database\Seeders;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'label' => 'Nama Instansi', 'type' => SettingType::Text, 'value' => 'Profil Instansi'],
            ['group' => 'general', 'key' => 'site_tagline', 'label' => 'Tagline', 'type' => SettingType::Text, 'value' => 'Melayani dengan sepenuh hati'],
            ['group' => 'general', 'key' => 'site_description', 'label' => 'Deskripsi Singkat', 'type' => SettingType::Textarea, 'value' => 'Situs resmi informasi dan layanan publik.'],
            ['group' => 'general', 'key' => 'site_logo', 'label' => 'Logo', 'type' => SettingType::Image, 'value' => null],
            ['group' => 'general', 'key' => 'questions_enabled', 'label' => 'Terima Pertanyaan Masyarakat', 'type' => SettingType::Boolean, 'value' => '1'],
            ['group' => 'contact', 'key' => 'contact_address', 'label' => 'Alamat', 'type' => SettingType::Textarea, 'value' => 'Jl. Contoh No. 1, Kota, Provinsi'],
            ['group' => 'contact', 'key' => 'contact_phone', 'label' => 'Telepon', 'type' => SettingType::Text, 'value' => '(021) 1234567'],
            ['group' => 'contact', 'key' => 'contact_email', 'label' => 'Email', 'type' => SettingType::Text, 'value' => 'info@example.com'],
            ['group' => 'contact', 'key' => 'contact_whatsapp', 'label' => 'WhatsApp', 'type' => SettingType::Text, 'value' => '6281234567890'],
            ['group' => 'contact', 'key' => 'office_hours', 'label' => 'Jam Layanan', 'type' => SettingType::Json, 'value' => json_encode([
                ['day' => 'Senin - Kamis', 'hours' => '08.00 - 16.00'],
                ['day' => 'Jumat', 'hours' => '08.00 - 16.30'],
            ])],
            ['group' => 'social', 'key' => 'social_facebook', 'label' => 'Facebook', 'type' => SettingType::Text, 'value' => null],
            ['group' => 'social', 'key' => 'social_instagram', 'label' => 'Instagram', 'type' => SettingType::Text, 'value' => null],
            ['group' => 'social', 'key' => 'social_youtube', 'label' => 'YouTube', 'type' => SettingType::Text, 'value' => null],
            ['group' => 'social', 'key' => 'social_x', 'label' => 'X (Twitter)', 'type' => SettingType::Text, 'value' => null],
        ];

        foreach ($settings as $setting) {
            Setting::query()->firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
