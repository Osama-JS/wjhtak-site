<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'title_ar' => 'عروض الصيف الساخنة',
             'title_en' => 'Hot Summer Offers',
            'description_ar' => 'احصل على خصم 20% على جميع الرحلات إلى أوروبا',
            'description_en' => 'Get 20% off on all trips to Europe',
            'image_path' => 'banners/dummy1.jpg',
            'active' => true,
        ]);

        Banner::create([
            'title_ar' => 'اكتشف جمال العلا',
            'title_en' => 'Discover Al Ula',
            'description_ar' => 'رحلات خاصة إلى العلا ومدائن صالح بأسعار مميزة',
            'description_en' => 'Special trips to Al Ula and Madain Saleh at special prices',
            'image_path' => 'banners/dummy2.jpg',
            'active' => true,
        ]);

        Banner::create([
            'title_ar' => 'رحلات شهر العسل',
             'title_en' => 'Honeymoon Trips',
            'description_ar' => 'باقات خاصة للعرسان تشمل تذاكر الطيران والإقامة الفاخرة',
            'description_en' => 'Special packages for newlyweds including flight tickets and luxury accommodation',
            'image_path' => 'banners/dummy3.jpg',
            'active' => true,
        ]);
    }
}
