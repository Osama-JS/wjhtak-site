<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;

class TripItinerarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trips = Trip::all();

        foreach ($trips as $trip) {
            // Check if itineraries already exist to avoid duplication
            if ($trip->itineraries()->count() > 0) {
                continue;
            }

            // Assign generic itineraries based on trip duration or title keywords
            if (str_contains($trip->title, 'Cairo') || str_contains($trip->title, 'القاهرة')) {
                $trip->itineraries()->createMany([
                    ['day_number' => 1, 'title' => 'الوصول إلى القاهرة', 'description' => 'الوصول إلى مطار القاهرة الدولي والاستقبال من قبل المندوب ثم التوجه إلى الفندق.'],
                    ['day_number' => 2, 'title' => 'زيارة الأهرامات', 'description' => 'جولة سياحية لزيارة أهرامات الجيزة وأبو الهول مع وجبة غداء فاخرة.'],
                    ['day_number' => 3, 'title' => 'المتحف المصري', 'description' => 'زيارة المتحف المصري في التحرير وجولة في خان الخليلي.'],
                    ['day_number' => 4, 'title' => 'رحلة نيلية', 'description' => 'عشاء فاخر على متن باخرة نيلية مع عروض فلكلورية.'],
                    ['day_number' => 5, 'title' => 'المغادرة', 'description' => 'التوجه إلى المطار للعودة إلى الوطن.'],
                ]);
            } elseif (str_contains($trip->title, 'Dubai') || str_contains($trip->title, 'دبي')) {
                $trip->itineraries()->createMany([
                    ['day_number' => 1, 'title' => 'الوصول إلى دبي', 'description' => 'الاستقبال في مطار دبي والتوصيل إلى فندق فاخر في وسط المدينة.'],
                    ['day_number' => 2, 'title' => 'برج خليفة ودبي مول', 'description' => 'زيارة برج خليفة أطول برج في العالم والتسوق في دبي مول.'],
                    ['day_number' => 3, 'title' => 'المغادرة', 'description' => 'وقت حر للتسوق ثم التوجه للمطار.'],
                ]);
            } else {
                // Default itinerary for other trips
                $trip->itineraries()->createMany([
                    ['day_number' => 1, 'title' => 'الوصول', 'description' => 'الوصول إلى الوجهة والاستقبال من المطار.'],
                    ['day_number' => 2, 'title' => 'جولة في المدينة', 'description' => 'جولة تعريفية بأهم معالم المدينة السياحية.'],
                    ['day_number' => 3, 'title' => 'وقت حر', 'description' => 'يوم حر للتسوق أو الاسترخاء.'],
                ]);
            }
        }
    }
}
