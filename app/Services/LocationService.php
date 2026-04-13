<?php

namespace App\Services;

use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LocationService
{
    protected $tboService;

    public function __construct()
    {
        if(env('APP_API_MODE') === 'mock') {
            $this->tboService = new \App\Services\MockTBOHotelService();
        } else {
            $this->tboService = app(\App\Services\TBOHotelService::class);
        }
    }

    /**
     * Sync countries and cities from TBO API to local database.
     */
    public function syncLocationsFromApi()
    {
        return Cache::remember('tbo_locations_synced_v2', 60 * 24, function () {
            try {
                $commonCountries = ['SA', 'AE', 'EG', 'JO', 'TR', 'QA', 'BH', 'KW', 'OM'];
                $allApiCities = [];

                foreach ($commonCountries as $iso) {
                    $cities = $this->tboService->getCityList($iso);
                    $allApiCities = array_merge($allApiCities, $cities);
                }

                // 1. Sync Countries (Derived from Cities)
                foreach ($allApiCities as $apiCity) {
                    Country::updateOrCreate(
                        ['iso' => $apiCity['CountryCode']],
                        [
                            'name' => $apiCity['CountryName'],
                            'nicename' => $apiCity['CountryName'],
                            'name_ar' => $this->translateToArabic($apiCity['CountryName'], 'country'),
                            'active' => true
                        ]
                    );
                }

                $countriesMap = Country::pluck('id', 'iso')->toArray();

                // 2. Sync Cities
                foreach ($allApiCities as $apiCity) {
                    $countryId = $countriesMap[$apiCity['CountryCode']] ?? null;
                    if ($countryId) {
                        City::updateOrCreate(
                            [
                                'city_code' => $apiCity['CityCode']
                            ],
                            [
                                'country_id' => $countryId,
                                'title' => $apiCity['CityName'],
                                'title_ar' => $this->translateToArabic($apiCity['CityName'], 'city'),
                                'active' => true
                            ]
                        );
                    }
                }
                return true;
            } catch (\Exception $e) {
                Log::error('Location Sync Error: ' . $e->getMessage());
                return false;
            }
        });
    }

    /**
     * Simple translator for common city/country names.
     * In a production environment, this could call an external API.
     */
    protected function translateToArabic(string $name, string $type = 'city'): ?string
    {
        $dictionary = [
            // Countries
            'Saudi Arabia' => 'المملكة العربية السعودية',
            'United Arab Emirates' => 'الإمارات العربية المتحدة',
            'Egypt' => 'مصر',
            'Jordan' => 'الأردن',
            'Turkey' => 'تركيا',
            'United Kingdom' => 'المملكة المتحدة',
            'United States' => 'الولايات المتحدة',
            'France' => 'فرنسا',
            'Germany' => 'ألمانيا',
            'Qatar' => 'قطر',
            'Bahrain' => 'البحرين',
            'Kuwait' => 'الكويت',
            'Oman' => 'عمان',

            // Cities (Saudi)
            'Riyadh' => 'الرياض',
            'Jeddah' => 'جدة',
            'Mecca' => 'مكة المكرمة',
            'Medina' => 'المدينة المنورة',
            'Dammam' => 'الدمام',
            'Abha' => 'أبها',
            'Tabuk' => 'تبوك',
            'Taif' => 'الطائف',
            'Al Khobar' => 'الخبر',
            'Gizan' => 'جيزان',
            'Hail' => 'حائل',
            'Najran' => 'نجران',
            'Buraidah' => 'بريدة',
            'Yanbu' => 'ينبع',
            'Al Bahah' => 'الباحة',
            'Al Jawf' => 'الجوف',
            'Arar' => 'عرعر',

            // Cities (International)
            'Dubai' => 'دبي',
            'Abu Dhabi' => 'أبو ظبي',
            'Cairo' => 'القاهرة',
            'Istanbul' => 'اسطنبول',
            'London' => 'لندن',
            'Paris' => 'باريس',
            'New York' => 'نيويورك',
            'Doha' => 'الدوحة',
            'Manama' => 'المنامة',
            'Muscat' => 'مسقط',
            'Amman' => 'عمان',
            'Beirut' => 'بيروت',
        ];

        return $dictionary[$name] ?? $name; // Fallback to original name if not in dictionary
    }
}
