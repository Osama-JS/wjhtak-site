<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AppSettingController extends Controller
{
    #[OA\Get(
        path: "/api/app-settings",
        summary: "Get app configuration and status",
        operationId: "getAppSettings",
        description: "Returns the current app settings including maintenance mode, minimum required version, and app store links.",
        tags: ["System"],
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
                description: "App settings retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "App settings retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "maintenance_mode", type: "boolean", example: false),
                            new OA\Property(property: "app_min_version", type: "string", example: "1.0.0"),
                            new OA\Property(property: "android_url", type: "string", example: "https://play.google.com/..."),
                            new OA\Property(property: "ios_url", type: "string", example: "https://apps.apple.com/..."),
                            new OA\Property(property: "site_name", type: "string", example: "My Trip")
                        ])
                    ]
                )
            )
        ]
    )]
    public function index()
    {
        $settings = [
            'maintenance_mode' => (bool) Setting::get('maintenance_mode', '0'),
            'app_min_version' => Setting::get('app_min_version', '1.0.0'),
            'android_url' => Setting::get('android_url', ''),
            'ios_url' => Setting::get('ios_url', ''),
            'site_name' => app()->getLocale() == 'ar' ? Setting::get('site_name_ar') : Setting::get('site_name_en'),
        ];

        return $this->apiResponse(false, __('App settings retrieved successfully.'), $settings);
    }
}
