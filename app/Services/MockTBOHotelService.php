<?php
namespace App\Services;

class MockTBOHotelService
{
    /**
     * Get a comprehensive list of mock cities.
     */
    public function getCityList(): array
    {
        return [
            ['CityCode' => 'RUH', 'CityName' => 'Riyadh', 'CountryCode' => 'SA', 'CountryName' => 'Saudi Arabia'],
            ['CityCode' => 'JED', 'CityName' => 'Jeddah', 'CountryCode' => 'SA', 'CountryName' => 'Saudi Arabia'],
            ['CityCode' => 'DMM', 'CityName' => 'Dammam', 'CountryCode' => 'SA', 'CountryName' => 'Saudi Arabia'],
            ['CityCode' => 'DXB', 'CityName' => 'Dubai', 'CountryCode' => 'AE', 'CountryName' => 'United Arab Emirates'],
            ['CityCode' => 'AUH', 'CityName' => 'Abu Dhabi', 'CountryCode' => 'AE', 'CountryName' => 'United Arab Emirates'],
            ['CityCode' => 'CAI', 'CityName' => 'Cairo', 'CountryCode' => 'EG', 'CountryName' => 'Egypt'],
            ['CityCode' => 'IST', 'CityName' => 'Istanbul', 'CountryCode' => 'TR', 'CountryName' => 'Turkey'],
            ['CityCode' => 'PAR', 'CityName' => 'Paris', 'CountryCode' => 'FR', 'CountryName' => 'France'],
            ['CityCode' => 'LON', 'CityName' => 'London', 'CountryCode' => 'GB', 'CountryName' => 'United Kingdom'],
            ['CityCode' => 'NYC', 'CityName' => 'New York', 'CountryCode' => 'US', 'CountryName' => 'United States'],
        ];
    }

    public function searchCities(string $query): array
    {
        $all = $this->getCityList();
        return array_values(array_filter($all, function($city) use ($query) {
            return stripos($city['CityName'], $query) !== false || stripos($city['CountryName'], $query) !== false;
        }));
    }

    /**
     * Centralized mock hotel data.
     */
    private function getMockHotels(): array
    {
        return [
            // Riyadh
            '1000001' => [
                'HotelCode' => '1000001',
                'HotelName' => 'Grand Plaza Riyadh',
                'HotelAddress' => 'King Fahd Road, Riyadh, SA',
                'StarRating' => 5,
                'HotelPicture' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&q=80&w=800',
                'LowestRate' => 1200.00,
                'Currency' => 'SAR',
                'CityCode' => 'RUH',
                'Description' => 'Located in the heart of Riyadh, Grand Plaza offers luxury accommodations with panoramic city views, a world-class spa, and fine dining.',
                'HotelFacilities' => ['Free WiFi', 'Pool', 'Fitness Center', 'Spa', 'Parking', 'Business Center'],
                'Images' => [
                    'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&q=80&w=800',
                    'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&q=80&w=800',
                    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&q=80&w=800'
                ]
            ],
            '1000002' => [
                'HotelCode' => '1000002',
                'HotelName' => 'Olaya Garden Inn',
                'HotelAddress' => 'Al Olaya Street, Riyadh, SA',
                'StarRating' => 4,
                'HotelPicture' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&q=80&w=800',
                'LowestRate' => 450.00,
                'Currency' => 'SAR',
                'CityCode' => 'RUH',
                'Description' => 'A modern business hotel in the Olaya district, featuring comfortable rooms, high-speed internet, and easy access to shopping malls.',
                'HotelFacilities' => ['Free WiFi', 'Breakfast', 'Parking', 'Meeting Rooms'],
                'Images' => [
                    'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&q=80&w=800',
                    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&q=80&w=800'
                ]
            ],
            // Dubai
            '2000001' => [
                'HotelCode' => '2000001',
                'HotelName' => 'Burj Al Sahab Luxury',
                'HotelAddress' => 'Sheikh Zayed Road, Dubai, AE',
                'StarRating' => 5,
                'HotelPicture' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=800',
                'LowestRate' => 1500.00,
                'Currency' => 'AED',
                'CityCode' => 'DXB',
                'Description' => 'Ultra-luxury living in the heart of Dubai with infinity pools, private beach access, and Michelin-star dining.',
                'HotelFacilities' => ['Beach Access', 'Infinity Pool', 'Spa', 'Butler Service', 'Free WiFi'],
                'Images' => [
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=800',
                    'https://images.unsplash.com/photo-1544124499-58912cbddaad?auto=format&fit=crop&q=80&w=800'
                ]
            ],
            // Paris
            '3000001' => [
                'HotelCode' => '3000001',
                'HotelName' => 'Lumière Paris Central',
                'HotelAddress' => 'Champs-Élysées, Paris, FR',
                'StarRating' => 5,
                'HotelPicture' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&q=80&w=800',
                'LowestRate' => 350.00,
                'Currency' => 'EUR',
                'CityCode' => 'PAR',
                'Description' => 'Elegant Parisian style meets modern comfort. Steps away from the Eiffel Tower and world-class boutiques.',
                'HotelFacilities' => ['Free WiFi', 'French Bakery', 'Concierge', 'City View'],
                'Images' => [
                    'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&q=80&w=800',
                    'https://images.unsplash.com/photo-1551882547-ff43c59fe4cf?auto=format&fit=crop&q=80&w=800'
                ]
            ],
        ];
    }

    public function searchHotels(array $criteria): array
    {
        $cityCode = $criteria['city_code'] ?? 'RUH';
        $allHotels = $this->getMockHotels();
        
        $filteredHotels = array_values(array_filter($allHotels, function($hotel) use ($cityCode) {
            return $hotel['CityCode'] === $cityCode;
        }));

        // If no hotels for this city, return Riyadh as default or a generic list
        if (empty($filteredHotels)) {
            $filteredHotels = array_slice(array_values($allHotels), 0, 3);
        }

        return [
            'session_id' => \Str::uuid()->toString(),
            'hotels' => $filteredHotels,
        ];
    }

    public function getHotelInfo(string $hotelCode): array
    {
        $allHotels = $this->getMockHotels();
        return $allHotels[$hotelCode] ?? [
            'HotelCode' => $hotelCode,
            'HotelName' => 'Standard Hotel ' . $hotelCode,
            'HotelAddress' => 'Generic Address',
            'StarRating' => 3,
            'HotelPicture' => 'https://via.placeholder.com/800x600',
            'LowestRate' => 300.00,
            'Currency' => 'SAR',
            'Description' => 'A comfortable stay with standard amenities.',
            'HotelFacilities' => ['Free WiFi', 'Breakfast'],
            'Images' => ['https://via.placeholder.com/800x600']
        ];
    }

    public function getRoomList(string $sessionId, string $hotelCode): array
    {
        $hotel = $this->getHotelInfo($hotelCode);
        $baseRate = $hotel['LowestRate'] ?? 200;
        $currency = $hotel['Currency'] ?? 'SAR';

        // Deterministic seed based on hotel code for consistent mock data
        mt_srand(crc32($hotelCode));

        $roomTemplates = [
            [
                'name' => 'Superior Room',
                'description' => 'A cozy and well-appointed room featuring modern amenities and a comfortable queen-size bed.',
                'multiplier' => 1.0,
                'images' => ['https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&q=80&w=800']
            ],
            [
                'name' => 'Deluxe King Room',
                'description' => 'Spacious room with a king-size bed, elegant decor, and a dedicated workspace.',
                'multiplier' => 1.25,
                'images' => ['https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&q=80&w=800']
            ],
            [
                'name' => 'Executive Suite',
                'description' => 'A luxurious suite with a separate living area, premium amenities, and stunning views.',
                'multiplier' => 1.8,
                'images' => ['https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=800']
            ],
            [
                'name' => 'Family Garden Suite',
                'description' => 'The perfect choice for families, offering two bedrooms and direct access to the hotel gardens.',
                'multiplier' => 2.1,
                'images' => ['https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&q=80&w=800']
            ],
            [
                'name' => 'Premium Ocean View',
                'description' => 'Enjoy breathtaking ocean views from your private balcony in this high-floor premium room.',
                'multiplier' => 1.5,
                'images' => ['https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&q=80&w=800']
            ]
        ];

        $ratePlans = [
            [
                'name' => 'Room Only',
                'multiplier' => 0.9,
                'inclusions' => ['Free WiFi', 'Daily Housekeeping', 'Non-Refundable']
            ],
            [
                'name' => 'Breakfast Included',
                'multiplier' => 1.0,
                'inclusions' => ['Free WiFi', 'Buffet Breakfast', 'Flexible Cancellation']
            ],
            [
                'name' => 'Half Board',
                'multiplier' => 1.3,
                'inclusions' => ['Free WiFi', 'Breakfast & Dinner', 'Free Parking', 'Pool Access']
            ],
            [
                'name' => 'All Inclusive',
                'multiplier' => 1.6,
                'inclusions' => ['All Meals & Drinks', 'Mini-bar Refilled Daily', 'SPA Access', 'Late Check-out']
            ]
        ];

        $generatedRooms = [];
        $pax = [2, 2, 3, 4, 3]; // Max pax mapping for templates

        // Generate 3-5 random rooms
        $numRooms = mt_rand(3, 5);
        $indices = array_rand($roomTemplates, min($numRooms, count($roomTemplates)));
        if (!is_array($indices)) $indices = [$indices];

        foreach ($indices as $i => $templateIdx) {
            $template = $roomTemplates[$templateIdx];
            $plan = $ratePlans[mt_rand(0, count($ratePlans) - 1)];

            $totalFare = round($baseRate * $template['multiplier'] * $plan['multiplier'], 2);

            $generatedRooms[] = [
                'RoomIndex' => $i + 1,
                'RoomTypeName' => $template['name'],
                'RatePlanName' => $plan['name'],
                'TotalFare' => $totalFare,
                'Currency' => $currency,
                'Inclusions' => $plan['inclusions'],
                'RoomDescription' => $template['description'],
                'RoomPicture' => $template['images'][0],
                'TotalPax' => $pax[$templateIdx] ?? 2,
                'RatePlanCode' => 'MOCK-RPC-' . ($i + 1)
            ];
        }

        // Reset seed
        mt_srand();

        return [
            'hotel_name' => $hotel['HotelName'],
            'address' => $hotel['HotelAddress'],
            'rooms' => $generatedRooms,
        ];
    }

    public function preBook(string $sessionId, int $roomIndex, string $ratePlanCode): array
    {
        return [
            'available' => true,
            'result_token' => 'TBO-MOCK-' . strtoupper(\Str::random(10)),
            'total_price' => 500.00 + rand(-20, 50),
            'currency' => 'SAR',
            'cancellation_policy' => [
                'LastCancellationDeadline' => now()->addDays(2)->toDateTimeString(),
                'Rules' => [
                    ['FromDate' => now()->toDateTimeString(), 'ToDate' => now()->addDay()->toDateTimeString(), 'ChargeType' => 'Percentage', 'Charge' => 0],
                    ['FromDate' => now()->addDay()->toDateTimeString(), 'ToDate' => now()->addDays(2)->toDateTimeString(), 'ChargeType' => 'Percentage', 'Charge' => 100],
                ],
            ],
        ];
    }

    public function countryList(): array
    {
        return [
            ['CountryCode' => 'SA', 'CountryName' => 'Saudi Arabia'],
            ['CountryCode' => 'AE', 'CountryName' => 'United Arab Emirates'],
            ['CountryCode' => 'EG', 'CountryName' => 'Egypt'],
            ['CountryCode' => 'TR', 'CountryName' => 'Turkey'],
            ['CountryCode' => 'FR', 'CountryName' => 'France'],
            ['CountryCode' => 'GB', 'CountryName' => 'United Kingdom'],
            ['CountryCode' => 'US', 'CountryName' => 'United States'],
        ];
    }

    public function createBooking(string $bookingCode, array $guestDetails, array $contactInfo, string $clientRef): array
    {
        return [
            'tbo_booking_id' => 'MOCK-' . rand(1000, 9999),
            'tbo_booking_ref'=> 'REF-' . strtoupper(\Str::random(6)),
            'status'         => 'Confirmed',
            'voucher_url'    => 'https://via.placeholder.com/800x1200?text=Mock+Voucher',
        ];
    }

    public function getBookingDetail(string $tboBookingId): array
    {
        return [
            'BookingId' => $tboBookingId,
            'Status' => 'Confirmed',
            'CheckInDate' => '2026-04-10',
            'CheckOutDate' => '2026-04-15',
            'HotelName' => 'Grand Plaza Riyadh',
        ];
    }

    public function cancelBooking(string $tboBookingId, string $requestType = 'Cancellation'): array
    {
        return [
            'cancelled' => true,
            'tbo_ref' => $tboBookingId,
        ];
    }

    public function getBookingDetailsByDate(string $fromDate, string $toDate): array
    {
        return [
            "Status" => ["Code" => 200, "Description" => "Success"],
            "BookingDetail" => []
        ];
    }

    public function getHotelCodeList(string $cityCode, bool $isDetailedResponse = true): array
    {
        $hotels = array_filter($this->getMockHotels(), fn($h) => $h['CityCode'] === strtoupper($cityCode));
        return ["Status" => ["Code" => 200, "Description" => "Success"], "Hotels" => array_values($hotels)];
    }
}