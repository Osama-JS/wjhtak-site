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
        return Cache::remember('tbo_locations_synced', 60 * 24, function () {
            try {
                // Sync Countries
                $apiCountries = $this->tboService->countryList();
                foreach ($apiCountries as $apiCountry) {
                    Country::updateOrCreate(
                        ['iso' => $apiCountry['CountryCode']],
                        [
                            'name' => $apiCountry['CountryName'],
                            'nicename' => $apiCountry['CountryName'],
                            'active' => true
                        ]
                    );
                }

                // Sync Cities (Limit to major ones to keep it performant)
                $apiCities = $this->tboService->getCityList();
                $countriesMap = Country::pluck('id', 'iso')->toArray();

                foreach (array_slice($apiCities, 0, 1000) as $apiCity) {
                    $countryId = $countriesMap[$apiCity['CountryCode']] ?? null;
                    if ($countryId) {
                        City::updateOrCreate(
                            [
                                'title' => $apiCity['CityName'],
                                'country_id' => $countryId
                            ],
                            ['active' => true]
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
}
