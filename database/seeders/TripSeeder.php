<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\Company;
use App\Models\Country;
use App\Models\City;
use App\Models\TripImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        $countries = Country::all();

        if ($companies->isEmpty() || $countries->isEmpty()) {
            return;
        }

        $sa = Country::where('nicename', 'Saudi Arabia')->first();
        $eg = Country::where('nicename', 'Egypt')->first();
        $ae = Country::where('nicename', 'UAE')->first();

        // Trip 1: Riyadh to Cairo
        $trip1 = Trip::create([
            'title' => 'رحلة الأهرامات المميزة',
            'description' => 'استمتع برحلة لا تنسى إلى القاهرة لمدة 5 أيام وزيارة الأهرامات والمتاحف المصرية.',
            'price' => 3500.00,
            'price_before_discount' => 4000.00,
            'duration' => '5 أيام',
            'tickets' => 'تذكرة طيران ذهاب وعودة',
            'company_id' => $companies->first()->id,
            'from_country_id' => $sa->id,
            'from_city_id' => $sa->cities->first()->id,
            'to_country_id' => $eg->id,
            'personnel_capacity' => 20,
            'is_public' => true,
            'is_ad' => true,
            'active' => true,
            'expiry_date' => Carbon::now()->addMonths(2),
            'admin_id' => 1,
        ]);

        // Trip 2: Riyadh to Dubai
        $trip2 = Trip::create([
            'title' => 'ويكند في دبي',
            'description' => 'اقضِ عطلة نهاية أسبوع فاخرة في دبي مع إقامة في فنادق 5 نجوم.',
            'price' => 2800.00,
            'price_before_discount' => 3000.00,
            'duration' => '3 أيام',
            'tickets' => 'شامل الطيران والفندق',
            'company_id' => $companies->last()->id,
            'from_country_id' => $sa->id,
            'from_city_id' => $sa->cities->first()->id,
            'to_country_id' => $ae->id,
            'personnel_capacity' => 15,
            'is_public' => true,
            'is_ad' => true,
            'active' => true,
            'expiry_date' => Carbon::now()->addMonths(1),
             'admin_id' => 1,
        ]);

        // Trip 3: Jeddah to Sharm El Sheikh
        $trip3 = Trip::create([
            'title' => 'استجمام في شرم الشيخ',
            'description' => 'رحلة بحرية رائعة في شرم الشيخ مع أنشطة الغوص والسباحة.',
            'price' => 2200.00,
            'price_before_discount' => 0,
            'duration' => '4 أيام',
            'tickets' => 'طيران + فندق',
            'company_id' => $companies->first()->id,
            'from_country_id' => $sa->id,
            'from_city_id' => $sa->cities->where('title', 'جدة')->first()->id ?? $sa->cities->first()->id,
            'to_country_id' => $eg->id,
            'personnel_capacity' => 25,
            'is_public' => true,
            'is_ad' => false,
            'active' => true,
            'expiry_date' => Carbon::now()->addMonths(3),
             'admin_id' => 1,
        ]);

        // Add Itineraries
        $trip1->itineraries()->createMany([
            ['day_number' => 1, 'title' => 'الوصول إلى القاهرة', 'description' => 'الوصول إلى مطار القاهرة الدولي والاستقبال من قبل المندوب ثم التوجه إلى الفندق.'],
            ['day_number' => 2, 'title' => 'زيارة الأهرامات', 'description' => 'جولة سياحية لزيارة أهرامات الجيزة وأبو الهول مع وجبة غداء فاخرة.'],
            ['day_number' => 3, 'title' => 'المتحف المصري', 'description' => 'زيارة المتحف المصري في التحرير وجولة في خان الخليلي.'],
            ['day_number' => 4, 'title' => 'رحلة نيلية', 'description' => 'عشاء فاخر على متن باخرة نيلية مع عروض فلكلورية.'],
            ['day_number' => 5, 'title' => 'المغادرة', 'description' => 'التوجه إلى المطار للعودة إلى الوطن.'],
        ]);

        $trip2->itineraries()->createMany([
            ['day_number' => 1, 'title' => 'الوصول إلى دبي', 'description' => 'الاستقبال في مطار دبي والتوصيل إلى فندق فاخر في وسط المدينة.'],
            ['day_number' => 2, 'title' => 'برج خليفة ودبي مول', 'description' => 'زيارة برج خليفة أطول برج في العالم والتسوق في دبي مول.'],
            ['day_number' => 3, 'title' => 'المغادرة', 'description' => 'وقت حر للتسوق ثم التوجه للمطار.'],
        ]);
    }
}
