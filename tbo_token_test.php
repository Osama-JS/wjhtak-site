<?php
$url = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/Authenticate';

$cred = 'WjhtakTest:Wag@68617834';

$c = curl_init($url);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
// Test sending as body
$body = json_encode([
    'ClientId' => 'ApiIntegrationNew',
    'UserName' => 'WjhtakTest',
    'Password' => 'Wag@68617834',
    'EndUserIp' => '172.16.10.10',
]);

curl_setopt($c, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_POSTFIELDS, $body);

$response = curl_exec($c);
$code = curl_getinfo($c, CURLINFO_HTTP_CODE);
echo "URL: $url | Body Auth Code: $code | Response: " . substr($response, 0, 150) . "\n";


// Test Basic auth
$c = curl_init('http://api.tbotechnology.in/TBOHolidays_HotelAPI/CityList');
$cred = 'ApiIntegrationNew-WjhtakTest:Wag@68617834';
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Basic ' . base64_encode($cred)]);
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_POSTFIELDS, json_encode(['EndUserIp'=>'172.16.10.10']));
$response = curl_exec($c);
echo "CityList with ApiIntegrationNew prefix: $response\n";
