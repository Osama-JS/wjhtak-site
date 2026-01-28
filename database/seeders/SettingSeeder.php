<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'site_name_en' => 'My Trip',
            'site_name_ar' => 'ماي تريب',
            'site_description_en' => 'Your ultimate travel companion.',
            'site_description_ar' => 'رفيقك الأمثل في السفر.',
            'site_logo' => 'images/logo.png',
            'site_favicon' => 'images/favicon.png',
            'maintenance_mode' => '0',
            'contact_email' => 'support@mytrip.com',
            'contact_phone' => '+966 500 000 000',
            'facebook_url' => 'https://facebook.com/mytrip',
            'twitter_url' => 'https://twitter.com/mytrip',
            'instagram_url' => 'https://instagram.com/mytrip',
            'primary_color' => '#3b4bd3',
            'app_min_version' => '1.0.0',
            'android_url' => 'https://play.google.com/store/apps/details?id=com.mytrip',
            'ios_url' => 'https://apps.apple.com/app/mytrip/id000000000',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
