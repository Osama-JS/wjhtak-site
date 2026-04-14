<?php
$username = 'WjhatTest';
$password = 'Wag@68617834';
$clientId = ''; // User provided empty
$baseUrl = 'https://api.tbotechnology.in/TBOHolidays_HotelAPI';

function tbo_post($endpoint, $payload) {
    global $username, $password, $clientId, $baseUrl;
    $url = "$baseUrl/$endpoint";
    
    $body = array_merge([
        'ClientId' => $clientId,
        'UserName' => $username,
        'Password' => $password,
        'EndUserIp' => '172.16.10.10',
    ], $payload);

    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode("$username:$password")
    ]);
    curl_setopt($c, CURLOPT_POST, true);
    curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($body));

    $response = curl_exec($c);
    $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
    
    return ['code' => $code, 'response' => $response];
}

echo "Testing AccountDetails...\n";
$res = tbo_post('AccountDetails', []);
echo "HTTP Code: {$res['code']}\n";
echo "Response: {$res['response']}\n\n";

echo "Testing CityList...\n";
$res = tbo_post('CityList', []);
echo "HTTP Code: {$res['code']}\n";
echo "Response: " . substr($res['response'], 0, 500) . "...\n";




