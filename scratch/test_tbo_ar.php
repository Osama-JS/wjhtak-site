<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Services\TBOHotelService;

$service = app(TBOHotelService::class);

try {
    echo "Testing CityList with Language => 'ar'...\n";
    $payload = ['CountryCode' => 'SA', 'Language' => 'ar']; // Guessing Language parameter
    $url = config('services.tbo.base_url') . '/CityList';
    
    // Using reflection or a quick closure to access the protected post method if needed, 
    // but TBOHotelService::getCityList doesn't support Language parameter yet.
    // Let's just try a raw post request.
    
    $response = \Illuminate\Support\Facades\Http::withBasicAuth(config('services.tbo.username'), config('services.tbo.password'))
        ->post($url, array_merge(['EndUserIp' => '172.16.10.10'], $payload));
    
    echo "Status: " . $response->status() . "\n";
    $json = $response->json();
    $cities = $json['CityList'] ?? $json['CityListResult'] ?? $json ?? [];
    
    if (count($cities) > 0) {
        echo "First city in AR request:\n";
        print_r(array_slice($cities, 0, 1));
    }

} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
