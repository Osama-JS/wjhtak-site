<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$username = env('TBO_USERNAME', 'WjhtakTest');
$password = env('TBO_PASSWORD', 'Wag@68617834');
$clientId = env('TBO_CLIENT_ID', '220146');

$endpoints = [
    'http://api.tbotechnology.in/TBOHolidays_HotelAPI/',
    'https://api.tbotechnology.in/HotelAPI_V5/',
    'http://api.tbotechnology.in/HotelAPI_V5/',
];

foreach ($endpoints as $baseUrl) {
    $baseUrl = rtrim($baseUrl, '/');
    echo "\n" . str_repeat('=', 50) . "\n";
    echo "Testing Base URL: $baseUrl\n";
    echo str_repeat('=', 50) . "\n";

    // Method 1: Basic Auth Only
    echo "\n[Method 1] Basic Auth Only\n";
    try {
        $response = Http::timeout(10)->withBasicAuth($username, $password)
            ->post("$baseUrl/CityList", ['EndUserIp' => '127.0.0.1']);
        echo "Status: " . $response->status() . "\n";
        echo "Body snippet: " . substr($response->body(), 0, 200) . "...\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    // Method 2: Body Credentials Only
    echo "\n[Method 2] Body Credentials Only\n";
    try {
        $response = Http::timeout(10)->post("$baseUrl/CityList", [
            'ClientId' => $clientId,
            'UserName' => $username,
            'Password' => $password,
            'EndUserIp' => '127.0.0.1'
        ]);
        echo "Status: " . $response->status() . "\n";
        echo "Body snippet: " . substr($response->body(), 0, 200) . "...\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    // Method 3: Both Basic Auth and Body Credentials
    echo "\n[Method 3] Both Basic Auth and Body Credentials\n";
    try {
        $response = Http::timeout(10)->withBasicAuth($username, $password)
            ->post("$baseUrl/CityList", [
                'ClientId' => $clientId,
                'UserName' => $username,
                'Password' => $password,
                'EndUserIp' => '127.0.0.1'
            ]);
        echo "Status: " . $response->status() . "\n";
        echo "Body snippet: " . substr($response->body(), 0, 200) . "...\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

