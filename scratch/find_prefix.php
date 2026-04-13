<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Setup basic Laravel env for Http facade
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$username = 'WjhtakTest';
$password = 'Wag@68617834';
$clientId = '220146';
$baseUrl = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI';

$prefixes = ['', 'ApiIntegrationNew-', $clientId . '-', '220146-'];

foreach ($prefixes as $prefix) {
    echo "\nTesting with prefix: '$prefix'\n";
    $fullUser = $prefix . $username;
    
    try {
        $response = Http::withBasicAuth($fullUser, $password)
            ->post("$baseUrl/CityList", ['EndUserIp' => '172.16.10.10']);
        
        echo "Status: " . $response->status() . "\n";
        $json = $response->json();
        $code = $json['Status']['Code'] ?? $json['Status'] ?? 'N/A';
        $desc = $json['Status']['Description'] ?? 'No Description';
        echo "TBO Status: $code | Desc: $desc\n";
        
        if ($code == 200 || $code == 100 || $code == 0) {
            echo "SUCCESS! Prefix '$prefix' works.\n";
            exit(0);
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "\nAll prefixes failed.\n";
