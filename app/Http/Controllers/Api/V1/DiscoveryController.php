<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\City;
use App\Models\Country;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class DiscoveryController extends Controller
{
    use ApiResponseTrait;
    /**
     * Get all active countries.
     */
    #[OA\Get(
        path: "/api/v1/countries",
        summary: "Get all countries",
        operationId: "getCountries",
        description: "Retrieve a list of all active countries with their flags and codes.",
        tags: ["Discovery"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Countries retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Countries retrieved successfully"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Saudi Arabia"),
                                new OA\Property(property: "name_ar", type: "string", example: "المملكة العربية السعودية"),
                                new OA\Property(property: "name_en", type: "string", example: "Saudi Arabia"),
                                new OA\Property(property: "code", type: "string", example: "SA"),
                                new OA\Property(property: "phone_code", type: "string", example: "966"),
                                new OA\Property(property: "flag", type: "string", example: "http://example.com/flags/sa.png"),
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getCountries(): JsonResponse
    {
        $countries = Country::active()->get()->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name_attribute,
                'name_ar' => $country->nicename, // nicename is usually the localized one in this schema
                'name_en' => $country->name,
                'code' => $country->numcode,
                'phone_code' => $country->phonecode,
                'flag' => $country->flag_url,
            ];
        });

        return $this->apiResponse(false, __('Countries retrieved successfully'), $countries);
    }

    /**
     * Get all active cities with their country information.
     */
    #[OA\Get(
        path: "/api/v1/cities",
        summary: "Get all cities",
        operationId: "getCities",
        description: "Retrieve a list of all active cities, optionally filtered by country.",
        tags: ["Discovery"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            ),
            new OA\Parameter(
                name: "country_id",
                in: "query",
                description: "Filter cities by country ID",
                required: false,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cities retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Cities retrieved successfully"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Riyadh"),
                                new OA\Property(property: "name_ar", type: "string", example: "الرياض"),
                                new OA\Property(property: "name_en", type: "string", example: "Riyadh"),
                                new OA\Property(property: "country", type: "object", properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Saudi Arabia"),
                                    new OA\Property(property: "flag", type: "string", example: "http://example.com/flags/sa.png"),
                                ]),
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getCities(Request $request): JsonResponse
    {
        $query = City::active()->with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $cities = $query->get()->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name_attribute,
                'name_ar' => $city->title,
                'name_en' => $city->title,
                'country' => $city->country ? [
                    'id' => $city->country->id,
                    'name' => $city->country->name_attribute,
                    'flag' => $city->country->flag_url,
                ] : null,
            ];
        });

        return $this->apiResponse(false, __('Cities retrieved successfully'), $cities);
    }

    /**
     * Get all active banners ordered by priority.
     */
    #[OA\Get(
        path: "/api/v1/banners",
        summary: "Get banners",
        operationId: "getBanners",
        description: "Retrieve a list of promotional banners for the home screen.",
        tags: ["Discovery"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Banners retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Banners retrieved successfully"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "title", type: "string", example: "Summer Sale"),
                                new OA\Property(property: "title_ar", type: "string", example: "عروض الصيف"),
                                new OA\Property(property: "title_en", type: "string", example: "Summer Sale"),
                                new OA\Property(property: "description", type: "string", example: "Get 50% off on flights"),
                                new OA\Property(property: "description_ar", type: "string", example: "احصل على خصم 50% على الرحلات"),
                                new OA\Property(property: "description_en", type: "string", example: "Get 50% off on flights"),
                                new OA\Property(property: "image", type: "string", example: "http://example.com/banners/1.jpg"),
                                new OA\Property(property: "link", type: "string", example: "https://example.com/promo"),
                                new OA\Property(property: "order", type: "integer", example: 1),
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getBanners(): JsonResponse
    {
        $banners = Banner::active()->ordered()->get()->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => app()->getLocale() === 'ar' ? $banner->title_ar : $banner->title_en,
                'title_ar' => $banner->title_ar,
                'title_en' => $banner->title_en,
                'description' => app()->getLocale() === 'ar' ? $banner->description_ar : $banner->description_en,
                'description_ar' => $banner->description_ar,
                'description_en' => $banner->description_en,
                'image' => $banner->image_url,
                'link' => $banner->link,
                'order' => $banner->sort_order,
            ];
        });

        return $this->apiResponse(false, __('Banners retrieved successfully'), $banners);
    }

    /**
     * Get all locations (Countries with their nested Cities).
     */
    #[OA\Get(
        path: "/api/v1/locations",
        summary: "Get hierarchical locations",
        operationId: "getLocations",
        description: "Retrieve a hierarchical list of countries and their cities.",
        tags: ["Discovery"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Locations retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Locations retrieved successfully"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Saudi Arabia"),
                                new OA\Property(property: "flag", type: "string", example: "http://example.com/flags/sa.png"),
                                new OA\Property(property: "cities", type: "array", items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: "id", type: "integer", example: 1),
                                        new OA\Property(property: "name", type: "string", example: "Riyadh"),
                                    ]
                                )),
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getLocations(): JsonResponse
    {
        $locations = Country::active()->with(['cities' => function($query) {
            $query->active();
        }])->get()->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name_attribute,
                'flag' => $country->flag_url,
                'cities' => $country->cities->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name' => $city->name_attribute,
                    ];
                })
            ];
        });

        return $this->apiResponse(false, __('Locations retrieved successfully'), $locations);
    }

    /**
     * Get all active FAQs.
     */
    #[OA\Get(
        path: "/api/v1/faqs",
        summary: "Get all FAQs",
        operationId: "getFaqs",
        description: "Retrieve a list of all active frequently asked questions.",
        tags: ["Discovery"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "FAQs retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "FAQs retrieved successfully"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "question", type: "string", example: "How to book?"),
                                new OA\Property(property: "answer", type: "string", example: "You can book via..."),
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getFaqs(): JsonResponse
    {
        $faqs = \App\Models\Question::all()->map(function ($faq) {
            return [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
            ];
        });

        return $this->apiResponse(false, __('FAQs retrieved successfully'), $faqs);
    }
}

