<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\City;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Countries
        $sa = Country::firstOrCreate(
            ['nicename' => 'Saudi Arabia'],
            [
                'name' => 'المملكة العربية السعودية',
                'numcode' => '966',
                'phonecode' => '966',
                'active' => true,
            ]
        );

        $eg = Country::firstOrCreate(
            ['nicename' => 'Egypt'],
            [
                 'name' => 'جمهورية مصر العربية',
                 'numcode' => '20',
                 'phonecode' => '20',
                 'active' => true,
            ]
        );

         $ae = Country::firstOrCreate(
            ['nicename' => 'UAE'],
            [
                 'name' => 'الإمارات العربية المتحدة',
                 'numcode' => '971',
                 'phonecode' => '971',
                 'active' => true,
            ]
        );

        // Cities for SA
        City::firstOrCreate(['title' => 'الرياض', 'country_id' => $sa->id], ['active' => true]);
        City::firstOrCreate(['title' => 'جدة', 'country_id' => $sa->id], ['active' => true]);
        City::firstOrCreate(['title' => 'مكة المكرمة', 'country_id' => $sa->id], ['active' => true]);
        City::firstOrCreate(['title' => 'المدينة المنورة', 'country_id' => $sa->id], ['active' => true]);

        // Cities for EG
        City::firstOrCreate(['title' => 'القاهرة', 'country_id' => $eg->id], ['active' => true]);
        City::firstOrCreate(['title' => 'شرم الشيخ', 'country_id' => $eg->id], ['active' => true]);

         // Cities for UAE
        City::firstOrCreate(['title' => 'دبي', 'country_id' => $ae->id], ['active' => true]);
        City::firstOrCreate(['title' => 'أبو ظبي', 'country_id' => $ae->id], ['active' => true]);
    }
}
