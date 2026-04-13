<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Http;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$username = 'WjhtakTest';
$password = 'Wag@68617834';
$clientId = 'ApiIntegrationNew';
$baseUrl = 'https://api.tbotechnology.in/TBOHolidays_HotelAPI';

echo "Testing Split Auth: Basic Auth (Prefixed) + Body Auth (Clean)\n";

$response = Http::withBasicAuth("$clientId-$username", $password)
    ->post("$baseUrl/CityList", [
        'ClientId' => $clientId,
        'UserName' => $username,
        'Password' => $password,
        'EndUserIp' => '172.16.10.10'
    ]);

echo "Status: " . $response->status() . "\n";
echo "Response: " . $response->body() . "\n";
