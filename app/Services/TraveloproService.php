<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TraveloproService
{
    protected $userId;
    protected $password;
    protected $access;
    protected $url;

    public function __construct()
    {
        $this->userId = config('services.travelopro.user_id');
        $this->password = config('services.travelopro.password');
        $this->access = config('services.travelopro.access');
        $this->url = config('services.travelopro.url');
    }

    /**
     * Search for flights.
     *
     * @param array $data
     * @return array
     */
    public function searchFlights(array $data)
    {
        // Construct the payload with all available fields
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(), // Get user's IP
            'requiredCurrency' => $data['requiredCurrency'] ?? 'SAR',
            'journeyType' => $data['journeyType'],
            'OriginDestinationInfo' => $this->formatItinerary($data['OriginDestinationInfo']),
            'class' => $data['class'] ?? 'Economy',
            'adults' => $data['adults'] ?? 1,
            'childs' => $data['childs'] ?? 0,
            'infants' => $data['infants'] ?? 0,
            // Optional fields included even if null/default
            'airlineCode' => $data['airlineCode'] ?? '',
            'directFlight' => $data['directFlight'] ?? 'false',
        ];

        // Log request for debugging (remove sensitive data in production)
        Log::info('Travelopro Search Request', ['payload' => $payload]);

        try {
            $response = Http::timeout(60)->post($this->url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Search Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to fetch flight data',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Search Exception', ['message' => $e->getMessage()]);

            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format OriginDestinationInfo array.
     *
     * @param array $itineraries
     * @return array
     */
    private function formatItinerary(array $itineraries)
    {
        // Ensure structure matches Travelopro expectation
        // Example:
        // [
        //    [
        //        "departureDate" => "2023-02-19",
        //        "airportOriginCode" => "DEL",
        //        "airportDestinationCode" => "BOM"
        //    ]
        // ]
        return array_map(function ($segment) {
            return [
                'departureDate' => $segment['departureDate'],
                'returnDate' => $segment['returnDate'] ?? '', // Required for Return journeyType
                'airportOriginCode' => $segment['airportOriginCode'],
                'airportDestinationCode' => $segment['airportDestinationCode'],
            ];
        }, $itineraries);
    }

    /**
     * Get list of airports.
     *
     * @return array
     */
    public function getAirportList()
    {
        return cache()->remember('travelopro_airports', 60 * 24, function () {
            $payload = [
                'user_id' => $this->userId,
                'user_password' => $this->password,
                'access' => $this->access,
                'ip_address' => request()->ip(),
            ];

            $url = str_replace('availability', 'airport_list', $this->url);

            Log::info('Travelopro Airport List Request');

            try {
                $response = Http::timeout(60)->post($url, $payload);

                if ($response->successful()) {
                     return $response->json();
                }

                Log::error('Travelopro Airport List Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];

            } catch (\Exception $e) {
                Log::error('Travelopro Airport List Exception', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get list of airlines.
     *
     * @return array
     */
    public function getAirlineList()
    {
        return cache()->remember('travelopro_airlines', 60 * 24, function () {
            $payload = [
                'user_id' => $this->userId,
                'user_password' => $this->password,
                'access' => $this->access,
                'ip_address' => request()->ip(),
            ];

            $url = str_replace('availability', 'airline_list', $this->url);

             Log::info('Travelopro Airline List Request');

            try {
                $response = Http::timeout(60)->post($url, $payload);

                if ($response->successful()) {
                    return $response->json();
                }

                 Log::error('Travelopro Airline List Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];

            } catch (\Exception $e) {
                Log::error('Travelopro Airline List Exception', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }
        /**
     * Validate flight fare.
     *
     * @param array $data
     * @return array
     */
    public function validateFare(array $data)
    {
        Log::info('Travelopro Validate Fare Request', ['data' => $data]);

        $payload = [
            'session_id' => $data['session_id'],
            'fare_source_code' => $data['fare_source_code'],
            'fare_source_code_inbound' => $data['fare_source_code_inbound'] ?? '',
        ];

        $url = str_replace('availability', 'revalidate', $this->url);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Validate Fare Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to validate fare',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Validate Fare Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create flight booking (PNR).
     *
     * @param array $data
     * @return array
     */
    public function createBooking(array $data)
    {
        Log::info('Travelopro Booking Request', ['data' => $data]);

        $payload = [
            'flightBookingInfo' => [
                'flight_session_id' => $data['flight_session_id'],
                'fare_source_code' => $data['fare_source_code'],
                'IsPassportMandatory' => $data['IsPassportMandatory'] ?? false,
                'areaCode' => $data['areaCode'] ?? '080',
                'countryCode' => $data['countryCode'] ?? '966',
                'fareType' => $data['fareType'] ?? 'Private',
                'fare_source_code_inbound' => $data['fare_source_code_inbound'] ?? null,
            ],
            'paxInfo' => [
                'clientRef' => $data['clientRef'] ?? uniqid('TR'),
                'customerEmail' => $data['customerEmail'],
                'customerPhone' => $data['customerPhone'],
                'bookingNote' => $data['bookingNote'] ?? '',
                'paxDetails' => [
                    $this->formatPaxDetails($data['passengers'])
                ]
            ]
        ];

        $url = str_replace('availability', 'booking', $this->url);

        try {
            $response = Http::timeout(90)->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Booking Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to create booking',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Booking Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format passenger details for booking.
     *
     * @param array $passengers
     * @return array
     */
    private function formatPaxDetails(array $passengers)
    {
        $formatted = [];

        // Group passengers by type (adt, chd, inf)
        $groups = [
            'adult' => [],
            'child' => [],
            'infant' => []
        ];

        foreach ($passengers as $pax) {
            $type = strtolower($pax['type']); // assuming type is passed as adult, child, infant
            if (isset($groups[$type])) {
                $groups[$type][] = $pax;
            }
        }

        foreach ($groups as $type => $paxList) {
            if (empty($paxList)) continue;

            $details = [
                'title' => array_column($paxList, 'title'),
                'firstName' => array_column($paxList, 'first_name'),
                'lastName' => array_column($paxList, 'last_name'),
                'dob' => array_column($paxList, 'dob'),
                'nationality' => array_column($paxList, 'nationality'),
                'passportNo' => array_column($paxList, 'passport_no'),
                'passportIssueCountry' => array_column($paxList, 'passport_issue_country'),
                'passportExpiryDate' => array_column($paxList, 'passport_expiry_date'),
            ];

            // Add extra services if present
            // Simplified handling: assuming extra services are passed as nested arrays
            if (isset($paxList[0]['extra_services_outbound'])) {
                 $details['ExtraServiceOutbound'] = array_column($paxList, 'extra_services_outbound');
            }
             if (isset($paxList[0]['extra_services_inbound'])) {
                 $details['ExtraServiceInbound'] = array_column($paxList, 'extra_services_inbound');
            }

            $formatted[$type] = $details;
        }

        return $formatted;
    }

    /**
     * Order ticket for booking.
     *
     * @param string $uniqueId
     * @return array
     */
    public function orderTicket(string $uniqueId)
    {
        Log::info('Travelopro Order Ticket Request', ['UniqueID' => $uniqueId]);

        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $uniqueId
        ];

        $url = str_replace('availability', 'ticket_order', $this->url);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Order Ticket Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

             return [
                'status' => 'error',
                'message' => 'Failed to order ticket',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Order Ticket Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get trip details.
     *
     * @param string $uniqueId
     * @return array
     */
    public function getTripDetails(string $uniqueId)
    {
        // Trip Details Request
        $payload = [
            'user_id' => $this->userId,
            'user_password' => $this->password,
            'access' => $this->access,
            'ip_address' => request()->ip(),
            'UniqueID' => $uniqueId
        ];

        $url = str_replace('availability', 'trip_details', $this->url);

        try {
            $response = Http::timeout(60)->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Travelopro Trip Details Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to get trip details',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Travelopro Trip Details Exception', ['message' => $e->getMessage()]);
             return [
                'status' => 'error',
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ];
        }
    }
}
