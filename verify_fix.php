<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Services\TBOHotelService;

$service = app(TBOHotelService::class);

try {
    echo "Testing getCityList() with new default 'SA'...\n";
    // We clear cache first to ensure it's a fresh call
    \Illuminate\Support\Facades\Cache::forget('tbo_city_list_SA');
    \Illuminate\Support\Facades\Cache::forget('tbo_city_list_all');
    
    $res = $service->getCityList();
    echo "Success! Found " . count($res) . " cities.\n";
    
    if (count($res) > 0) {
        echo "First 5 normalized cities:\n";
        foreach (array_slice($res, 0, 5) as $city) {
            echo "- {$city['CityName']} ({$city['CityCode']}) in {$city['CountryName']}\n";
        }
    }

} catch (\Exception $e) {
    echo "Verification Failed: " . $e->getMessage() . "\n";
}
