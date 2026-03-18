<?php
namespace App\Services;

class MockTBOHotelService
{
    public function getCityList(): array
    {
        return [
            [
                'CityCode' => 'RUH',
                'CityName' => 'Riyadh',
                'CountryCode' => 'SA',
                'CountryName' => 'Saudi Arabia',
            ],
            [
                'CityCode' => 'JED',
                'CityName' => 'Jeddah',
                'CountryCode' => 'SA',
                'CountryName' => 'Saudi Arabia',
            ],
            [
                'CityCode' => 'DXB',
                'CityName' => 'Dubai',
                'CountryCode' => 'AE',
                'CountryName' => 'United Arab Emirates',
            ],
            // أضف المزيد حسب الحاجة
        ];
    }

    public function searchCities(string $query): array
    {
        $all = $this->getCityList();
        return array_filter($all, fn($city) => stripos($city['CityName'], $query) !== false);
    }


    public function searchHotels(array $data): array
    {
        return [
            'session_id' => \Str::uuid()->toString(),

            'hotels' => [
                [
                    'HotelCode' => '1000001',
                    'HotelName' => 'Grand Plaza Hotel',
                    'HotelAddress' => 'King Fahd Rd, Riyadh',
                    'StarRating' => 4,
                    'HotelPicture' => 'https://via.placeholder.com/300',
                    'LowestRate' => 450.00,
                    'Latitude' => '24.7136',
                    'Longitude' => '46.6753',
                ],
                [
                    'HotelCode' => '1000002',
                    'HotelName' => 'Royal Inn Hotel',
                    'HotelAddress' => 'Olaya St, Riyadh',
                    'StarRating' => 5,
                    'HotelPicture' => 'https://via.placeholder.com/300',
                    'LowestRate' => 650.00,
                    'Latitude' => '24.7130',
                    'Longitude' => '46.6700',
                ],
                [
                    'HotelCode' => '1000003',
                    'HotelName' => 'Budget Stay Hotel',
                    'HotelAddress' => 'Exit 5, Riyadh',
                    'StarRating' => 3,
                    'HotelPicture' => 'https://via.placeholder.com/300',
                    'LowestRate' => 250.00,
                    'Latitude' => '24.7200',
                    'Longitude' => '46.6800',
                ],
            ],
        ];
    }


    public function getRoomList(string $sessionId, string $hotelCode): array
    {
        return [
            'hotel_name' => 'Grand Plaza Hotel',
            'address' => 'King Fahd Rd, Riyadh',

            'rooms' => [
                [
                    'RoomIndex' => 1,
                    'RoomTypeName' => 'Deluxe Room',
                    'RatePlanCode' => 'R123',
                    'TotalFare' => 500.00,
                    'Currency' => 'SAR',
                    'Inclusions' => ['Breakfast Included'],
                ],
                [
                    'RoomIndex' => 2,
                    'RoomTypeName' => 'Executive Suite',
                    'RatePlanCode' => 'R456',
                    'TotalFare' => 850.00,
                    'Currency' => 'SAR',
                    'Inclusions' => ['Breakfast + Dinner'],
                ],
                [
                    'RoomIndex' => 3,
                    'RoomTypeName' => 'Standard Room',
                    'RatePlanCode' => 'R789',
                    'TotalFare' => 300.00,
                    'Currency' => 'SAR',
                    'Inclusions' => ['Room Only'],
                ],
            ],
        ];
    }


    public function preBook(string $sessionId, int $roomIndex, string $ratePlanCode): array
    {
        // محاكاة تغيير السعر أحيانًا
        $basePrice = match($roomIndex) {
            1 => 500,
            2 => 850,
            3 => 300,
            default => 400,
        };

        // احتمال تغيير السعر (محاكاة TBO الحقيقي)
        $finalPrice = $basePrice + rand(-50, 100);

        return [
            'available' => true,

            // مهم جدًا (يستخدم في الحجز النهائي)
            'result_token' => 'TBO-' . strtoupper(\Str::random(10)),

            'total_price' => $finalPrice,
            'currency' => 'SAR',

            'cancellation_policy' => [
                'LastCancellationDeadline' => now()->addDays(2)->toDateTimeString(),
                'Rules' => [
                    [
                        'FromDate' => now()->toDateTimeString(),
                        'ToDate' => now()->addDay()->toDateTimeString(),
                        'ChargeType' => 'Percentage',
                        'Charge' => 0,
                    ],
                    [
                        'FromDate' => now()->addDay()->toDateTimeString(),
                        'ToDate' => now()->addDays(2)->toDateTimeString(),
                        'ChargeType' => 'Percentage',
                        'Charge' => 50,
                    ],
                    [
                        'FromDate' => now()->addDays(2)->toDateTimeString(),
                        'ToDate' => now()->addDays(2)->toDateTimeString(),
                        'ChargeType' => 'Percentage',
                        'Charge' => 100,
                    ],
                ],
            ],
        ];
    }


    public function countryList(): array
    {
        return [
            "Status" => [
                "Code" => 200,
                "Description" => "Success"
            ],
            "CountryList" => [
                [
                    "Code" => "SA",
                    "Name" => "Saudi Arabia"
                ],
                [
                    "Code" => "AE",
                    "Name" => "United Arab Emirates"
                ],
                [
                    "Code" => "EG",
                    "Name" => "Egypt"
                ],
                [
                    "Code" => "TR",
                    "Name" => "Turkey"
                ],
                [
                    "Code" => "FR",
                    "Name" => "France"
                ]
            ]
        ];
    }

    public function getBookingDetailsByDate(string $fromDate, string $toDate): array
    {
        return [
            "Status" => [
                "Code" => 200,
                "Description" => "HotelBookingDetailBasedOnDate Successful"
            ],
            "BookingDetail" => [
                [
                    "Index" => 1,
                    "BookingId" => "264056",
                    "ConfirmationNo" => "GOF05R",
                    "BookingDate" => "10-Nov-2023",
                    "Currency" => "USD",
                    "AgentMarkup" => "0.00",
                    "AgencyName" => "ATravels",
                    "BookingStatus" => "Vouchered",
                    "BookingPrice" => "583.89",
                    "TripName" => "Sharma_02Dec_Dubai",
                    "TBOHotelCode" => "1022623",
                    "CheckInDate" => "02-Dec-2023",
                    "CheckOutDate" => "10-Dec-2023",
                    "ClientReferenceNumber" => "123680"
                ],
                [
                    "Index" => 2,
                    "BookingId" => "263915",
                    "ConfirmationNo" => "7L4F4E",
                    "BookingDate" => "09-Nov-2023",
                    "Currency" => "USD",
                    "AgentMarkup" => "0.00",
                    "AgencyName" => "ATravels",
                    "BookingStatus" => "Vouchered",
                    "BookingPrice" => "983.23",
                    "TripName" => "One_20Nov_London",
                    "TBOHotelCode" => "1407362",
                    "CheckInDate" => "20-Nov-2023",
                    "CheckOutDate" => "21-Nov-2023",
                    "ClientReferenceNumber" => "20230320978y8"
                ]
            ]
        ];
    }

    public function getHotelCodeList(string $cityCode, bool $isDetailedResponse = true): array
    {
        return [
            "Status" => [
                "Code" => 200,
                "Description" => "Success"
            ],
            "Hotels" => [
                [
                    "HotelCode" => "1010099",
                    "HotelName" => "Holiday Inn Express New York - Manhattan West Side",
                    "HotelRating" => "ThreeStar",
                    "Address" => "538 West 48th Street New York City New York 10036",
                    "Attractions" => [
                        "American Lyric Theater - 0.9 km / 0.5 mi",
                        "Times Square - 1.1 km / 0.7 mi"
                    ],
                    "CountryName" => "USA",
                    "CountryCode" => "US",
                    "Description" => "A stay at Holiday Inn Express New York - Manhattan West Side places you in the heart of New York.",
                    "FaxNumber" => "1-212-582-0693",
                    "HotelFacilities" => [
                        "Free breakfast",
                        "Free WiFi",
                        "Fitness facilities"
                    ],
                    "Map" => "40.764167|-73.994468",
                    "PhoneNumber" => "1-212-582-0692",
                    "PinCode" => "10036",
                    "HotelWebsiteUrl" => "http://www.ihg.com/holidayinnexpress/hotels/us/en/newyork/nychk/hotel",
                    "CityName" => "New York"
                ]
            ]
        ];
    }
}