<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "My-Trip API Documentation",
    version: "1.0.0",
    description: "API documentation for My-Trip Platform",
    contact: new OA\Contact(email: "support@mytrip.com")
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Development Server"
)]
#[OA\Server(
    url: "https://blueviolet-hummingbird-437500.hostingersite.com/public",
    description: "Production Server 1"
)]
#[OA\Server(
    url: "https://lavenderblush-bear-243464.hostingersite.com/public/",
    description: "Production Server 2"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Use a token to access protected routes"
)]
abstract class Controller
{
    use \App\Traits\ApiResponseTrait;
}
