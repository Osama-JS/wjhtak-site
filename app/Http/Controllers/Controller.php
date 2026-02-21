<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Wjhtak API Documentation",
    version: "1.0.0",
    description: "API documentation for Wjhtak Platform",
    contact: new OA\Contact(email: "support@wjhtak.com")
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Local Server"
)]
#[OA\Server(
    url: "https://n.wjhtak.com",
    description: "Production Server"
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
