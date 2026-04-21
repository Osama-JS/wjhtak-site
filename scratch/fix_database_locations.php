<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Country;
use App\Models\City;

$countries = [
    'Saudi Arabia' => 'SA',
    'Egypt' => 'EG',
    'UAE' => 'AE',
];

foreach ($countries as $name => $iso) {
    Country::where('nicename', $name)
        ->orWhere('name', $name)
        ->update(['iso' => $iso, 'active' => true]);
}

$cities = [
    'الرياض' => '131569',
    'جدة' => '121307',
    'مكة المكرمة' => '128362',
    'المدينة المنورة' => '128221',
    'دبي' => '115936',
    'أبو ظبي' => '100371',
    'القاهرة' => '107413',
    'شرم الشيخ' => '135544',
];

foreach ($cities as $title => $code) {
    City::where('title', $title)
        ->update(['city_code' => $code, 'active' => true]);
}

echo "Database updated with ISO codes and City codes.\n";
