<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiscoveryController extends Controller
{
    /**
     * Get all active countries.
     */
    public function getCountries(): JsonResponse
    {
        $countries = Country::active()->get()->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name,
                'name_ar' => $country->name_ar,
                'name_en' => $country->name_en,
                'code' => $country->code,
                'phone_code' => $country->phone_code,
                'flag' => $country->flag_url,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }

    /**
     * Get all active cities with their country information.
     */
    public function getCities(Request $request): JsonResponse
    {
        $query = City::active()->with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $cities = $query->get()->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
                'name_ar' => $city->name_ar,
                'name_en' => $city->name_en,
                'country' => $city->country ? [
                    'id' => $city->country->id,
                    'name' => $city->country->name,
                    'flag' => $city->country->flag_url,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    /**
     * Get all active banners ordered by priority.
     */
    public function getBanners(): JsonResponse
    {
        $banners = Banner::active()->ordered()->get()->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'title_ar' => $banner->title_ar,
                'title_en' => $banner->title_en,
                'description' => $banner->description,
                'description_ar' => $banner->description_ar,
                'description_en' => $banner->description_en,
                'image' => $banner->image_url,
                'link' => $banner->link,
                'order' => $banner->order,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    /**
     * Get all locations (Countries with their nested Cities).
     */
    public function getLocations(): JsonResponse
    {
        $locations = Country::active()->with(['cities' => function($query) {
            $query->active();
        }])->get()->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name,
                'flag' => $country->flag_url,
                'cities' => $country->cities->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name' => $city->name,
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}
