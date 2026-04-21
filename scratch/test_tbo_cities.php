<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\TBOHotelService::class);

try {
    echo "Testing CityList...\n";
    $cities = $service->getCityList('SA');
    echo "Found " . count($cities) . " cities in SA.\n";
    foreach(array_slice($cities, 0, 5) as $city) {
        echo "- {$city['CityName']} (Code: {$city['CityCode']})\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
