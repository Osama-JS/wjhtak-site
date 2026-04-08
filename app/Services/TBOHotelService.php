<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TBOHotelService
{
    protected ?string $clientId;
    protected ?string $username;
    protected ?string $password;
    protected ?string $clientCode;
    protected ?string $baseUrl;
    protected ?string $access;

    public function __construct()
    {
        $this->clientId   = config('services.tbo.client_id');
        $this->username   = config('services.tbo.username');
        $this->password   = config('services.tbo.password');
        $this->clientCode = config('services.tbo.client_code');
        $this->baseUrl    = rtrim(config('services.tbo.base_url', 'https://api.tbotechnology.in/HotelAPI_V5/'), '/');
        $this->access     = config('services.tbo.access', 'Test');

        // Validate that essential keys are present for Basic Auth
        if (empty($this->username) || empty($this->password)) {
            Log::error('TBO API: Missing Basic Auth credentials in .env. Please ensure TBO_USERNAME and TBO_PASSWORD are set.');
        }
    }

    // =============================================
    // Core Auth Credentials Payload
    // =============================================

    /**
     * Build the authentication credentials required by every TBO request.
     */
    protected function credentials(): array
    {
        return [
            'EndUserIp'    => (request()->ip() && request()->ip() !== '::1') ? request()->ip() : '172.16.10.10',
        ];
    }

    /**
     * Make an authenticated POST request to TBO API.
     */
    protected function post(string $endpoint, array $payload, int $timeout = 60): array
    {
        $url     = "{$this->baseUrl}/{$endpoint}";
        $body    = array_merge($this->credentials(), $payload);

        Log::info("TBO API Request [{$endpoint}]", ['payload' => array_merge($body, ['Password' => '***'])]);

        try {
            $response = Http::timeout($timeout)
                ->withBasicAuth($this->username, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $body);

            $json = $response->json();

            Log::info("TBO API Response [{$endpoint}]", ['status' => $response->status(), 'body' => $json]);

            if ($response->failed()) {
                throw new \Exception("TBO API HTTP Error {$response->status()}: " . $response->body());
            }

            // Check for internal TBO error codes in the response body
            $internalStatus = $json['Status']['Code'] ?? $json['Status'] ?? null;
            if ($internalStatus && !in_array($internalStatus, [200, 100, 0, 'Success'])) {
                $description = $json['Status']['Description'] ?? 'Internal TBO Error';
                throw new \Exception("TBO API Error [{$internalStatus}]: {$description}");
            }

            return $json ?? [];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("TBO API Connection Error [{$endpoint}]", ['message' => $e->getMessage()]);
            throw new \Exception('TBO API سغير متاح حالياً. يرجى المحاولة لاحقاً.');
        }
    }

    // =============================================
    // 1. City List (Static Data - Cached)
    // =============================================

    /**
     * Get TBO City List. Cached for 24 hours.
     */
    public function getCityList(): array
    {
        return Cache::remember('tbo_city_list', 60 * 24, function () {
            $response = $this->post('CityList', []);
            // TBO V2.1 might return it under 'CityList' or 'CityListResult' or directly
            return $response['CityList'] ?? $response['CityListResult'] ?? $response ?? [];
        });
    }

    /**
     * Search cities by name (from cached list).
     */
    public function searchCities(string $query): array
    {
        $cities = $this->getCityList();
        $query  = strtolower($query);

        return array_values(array_filter($cities, function ($city) use ($query) {
            return str_contains(strtolower($city['CityName'] ?? ''), $query)
                || str_contains(strtolower($city['CountryName'] ?? ''), $query);
        }));
    }

    /**
     * Get TBO City List by Country Code.
     */
    public function getCityListByCountry(string $countryCode): array
    {
        $response = $this->post('CityList', ['CountryCode' => $countryCode]);
        return $response['CityList'] ?? $response['CityListResult'] ?? $response ?? [];
    }

    // =============================================
    // 2. Hotel Search
    // =============================================

    /**
     * Search for available hotels.
     *
     * @param array $criteria {
     *   city_code: string,
     *   check_in: string (Y-m-d),
     *   check_out: string (Y-m-d),
     *   adults: int,
     *   children: int,
     *   children_ages: array,
     *   rooms: int,
     *   nationality: string (2-letter ISO),
     *   currency: string,
     * }
     */
    public function searchHotels(array $criteria): array
    {
        $checkIn  = $criteria['check_in'];
        $checkOut = $criteria['check_out'];
        $nights   = (int) \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut));

        $roomGuests = [];
        for ($i = 0; $i < ($criteria['rooms'] ?? 1); $i++) {
            $room = [
                'Adults'   => (int) ($criteria['adults'] ?? 1),
                'Children' => (int) ($criteria['children'] ?? 0),
            ];

            if (!empty($criteria['children_ages'])) {
                // TBO V2.1 uses 'ChildAge' for children ages array in PaxRooms
                $room['ChildAge'] = array_slice($criteria['children_ages'], $i * $room['Children'], $room['Children']);
            }

            $roomGuests[] = $room;
        }

        $payload = [
            'CheckIn'            => $checkIn,
            'CheckOut'           => $checkOut,
            'CityCode'           => $criteria['city_code'],
            'GuestNationality'   => $criteria['nationality'] ?? 'SA',
            'NoOfNights'         => $nights,
            'PreferredCurrencyCode' => $criteria['currency'] ?? 'SAR',
            'PaxRooms'           => $roomGuests,
            'IsDetailedResponse' => 'true',
            'Filters'            => [
                'Refundable'      => false,
                'NoOfRooms'       => $criteria['rooms'] ?? 1,
                'MealType'        => 'All',
            ],
        ];

        // Optional: filter by hotel code
        if (!empty($criteria['hotel_code'])) {
            $payload['HotelCodes'] = $criteria['hotel_code'];
        }

        $response = $this->post('search', $payload, 90);

        return [
            'session_id' => $response['HotelSearchResult']['HotelResults'][0]['SessionId']
                            ?? ($response['SessionId'] ?? null),
            'hotels'     => $response['HotelSearchResult']['HotelResults'] ?? [],
            'raw'        => $response,
        ];
    }

    // =============================================
    // 3. Hotel Details (Static Info)
    // =============================================

    /**
     * Get hotel details (images, description, amenities).
     * Cached for 24 hours per hotel.
     */
    public function getHotelInfo(string $hotelCode): array
    {
        return Cache::remember("tbo_hotel_{$hotelCode}", 60 * 24, function () use ($hotelCode) {
            $response = $this->post('Hoteldetails', [
                'HotelCodes' => $hotelCode,
                'Language'   => 'en',
            ]);

            return $response['Hotels'][0] ?? $response;
        });
    }

    // =============================================
    // 4. Room List (with Rates)
    // =============================================

    /**
     * Get room list for a specific hotel within a search session.
     */
    public function getRoomList(string $sessionId, string $hotelCode): array
    {
        $payload = [
            'SessionId' => $sessionId,
            'HotelCode' => $hotelCode,
        ];

        $response = $this->post('HotelRoom', $payload, 60);

        return [
            'hotel_name' => $response['HotelRoomsDetails']['HotelName'] ?? null,
            'address'    => $response['HotelRoomsDetails']['HotelAddress'] ?? null,
            'rooms'      => $response['HotelRoomsDetails']['Rooms'] ?? [],
            'raw'        => $response,
        ];
    }

    // =============================================
    // 5. Pre-Book (MANDATORY — Validate Price)
    // =============================================

    /**
     * Validate price and availability before final booking.
     * This step is MANDATORY by TBO Certification requirements.
     *
     * @param string $sessionId From search step
     * @param string $roomIndex  From room list
     * @param string $ratePlanCode Rate plan to book
     */
    public function preBook(string $sessionId, string $roomIndex, string $ratePlanCode): array
    {
        $payload = [
            'SessionId'    => $sessionId,
            'RoomIndex'    => (int) $roomIndex,
            'RatePlanCode' => $ratePlanCode,
        ];

        $response = $this->post('PreBook', $payload, 60);

        if (empty($response['PreBookResult'])) {
            throw new \Exception('TBO Pre-Book فشل: لا توجد نتيجة صحيحة.');
        }

        $result = $response['PreBookResult'];

        return [
            'available'          => $result['IsHotelPolicyCompliant'] ?? true,
            'result_token'       => $result['BookingCode'] ?? null, // used in HotelBook
            'price'              => $result['Price']['RoomPrice'] ?? null,
            'total_price'        => $result['Price']['Total'] ?? null,
            'cancellation_policy'=> $result['HotelCancellationPolicies'] ?? null,
            'raw'                => $response,
        ];
    }

    // =============================================
    // 6. Create Booking (HotelBook)
    // =============================================

    /**
     * Create the final hotel booking with TBO.
     *
     * @param string $bookingCode From preBook() step (BookingCode / ResultToken)
     * @param array  $guestDetails Array of guest info arrays
     * @param array  $contactInfo  { email, phone, first_name, last_name }
     * @param string $clientRef    Unique reference from our system
     */
    public function createBooking(
        string $bookingCode,
        array  $guestDetails,
        array  $contactInfo,
        string $clientRef
    ): array {
        $payload = [
            'BookingCode'  => $bookingCode,
            'ClientReferenceId' => $clientRef,
            'CustomerDetails' => [
                'CustomerEmail'   => $contactInfo['email'],
                'CustomerPhone'   => $contactInfo['phone'],
                'FirstName'       => $contactInfo['first_name'],
                'LastName'        => $contactInfo['last_name'],
            ],
            'GuestList' => $this->formatGuestList($guestDetails),
        ];

        $response = $this->post('Book', $payload, 120);

        $status = $response['BookResult']['Status'] ?? null;

        if ($status !== 'Confirmed') {
            $msg = $response['BookResult']['Remarks'] ?? 'فشل الحجز من TBO.';
            throw new \Exception("TBO Booking Failed: {$msg}");
        }

        return [
            'tbo_booking_id' => $response['BookResult']['TAID'] ?? null,
            'tbo_booking_ref'=> $response['BookResult']['Booking Reference'] ?? null,
            'status'         => $status,
            'voucher_url'    => $response['BookResult']['VoucherUrl'] ?? null,
            'raw'            => $response,
        ];
    }

    /**
     * Format guest list for TBO HotelBook request.
     */
    protected function formatGuestList(array $guests): array
    {
        return array_map(function ($guest, $index) {
            $g = [
                'Title'     => $guest['title'],
                'FirstName' => $guest['first_name'],
                'LastName'  => $guest['last_name'],
                'Type'      => ucfirst($guest['type'] ?? 'Adult'), // Adult or Child
                'IsLeadPax' => ($index === 0), // First guest is the lead passenger
            ];

            // TBO V2.1 requires Age for all guests
            if (!empty($guest['dob'])) {
                $g['DateOfBirth'] = $guest['dob'];
                $g['Age'] = (int) \Carbon\Carbon::parse($guest['dob'])->age;
            } else {
                // Default ages if DOB is missing (should be collected in UI)
                $g['Age'] = ($guest['type'] === 'child') ? 10 : 30;
            }

            if (!empty($guest['passport_number'])) {
                $g['PassportNumber'] = $guest['passport_number'];
                $g['PassportExpiry'] = $guest['passport_expiry'];
                $g['Nationality']    = $guest['nationality'];
            }

            return $g;
        }, $guests, array_keys($guests));
    }

    // =============================================
    // 7. Booking Details
    // =============================================

    /**
     * Retrieve booking details from TBO.
     */
    public function getBookingDetail(string $tboBookingId): array
    {
        $payload = [
            'BookingId' => $tboBookingId,
        ];

        $response = $this->post('BookingDetail', $payload, 30);

        return $response['BookingDetailResult'] ?? $response;
    }

    
    // =============================================
    // 8. Cancel Booking
    // =============================================

    /**
     * Cancel a confirmed booking with TBO.
     *
     * @param string $tboBookingId The TAID from TBO booking confirmation
     * @param string $requestType  'Cancellation' or 'Amendment'
     */
    public function cancelBooking(string $tboBookingId, string $requestType = 'Cancellation'): array
    {
        $payload = [
            'BookingId'   => $tboBookingId,
            'RequestType' => $requestType,
            'Remarks'     => 'Customer requested cancellation via website/app.',
        ];

        $response = $this->post('Cancel', $payload, 60);

        $status = $response['CancelResult']['Status'] ?? null;

        if ($status !== 'Cancelled') {
            $msg = $response['CancelResult']['Remarks'] ?? 'فشل الإلغاء.';
            throw new \Exception("TBO Cancellation Failed: {$msg}");
        }

        return [
            'cancelled'    => true,
            'tbo_ref'      => $tboBookingId,
            'remarks'      => $response['CancelResult']['Remarks'] ?? null,
            'raw'          => $response,
        ];
    }

    // =============================================
    // 9. Country List
    // =============================================    

    public function countryList(): array
    {
        return Cache::remember('tbo_country_list', 60*24, function () {
            $response = $this->post('CountryList', []);
            return $response['CountryList'] ?? [];
        });
    }

    // =============================================
    // 10. Booking Details Based on Date
    // =============================================

    /**
     * Retrieve booking details made for requested date range.
     * Maximum of 60 days booking details can be retrieved.
     *
     * @param string $fromDate Format: YYYY-MM-DD
     * @param string $toDate   Format: YYYY-MM-DD
     */
    public function getBookingDetailsByDate(string $fromDate, string $toDate): array
    {
        $payload = [
            'fromdate' => $fromDate,
            'todate'   => $toDate,
        ];

        $response = $this->post('BookingDetailsbasedondate', $payload, 60);

        return $response;
    }

    // =============================================
    // 11. Hotel Code List (Bulk Hotel Data)
    // =============================================

    /**
     * Fetch complete hotel details for a specific city.
     * Optionally, get detailed info including description, amenities, images, etc.
     * Cached per city for 24 hours to reduce API calls.
     *
     * @param string $cityCode Unique TBO City code
     * @param bool $isDetailedResponse Whether to fetch full hotel details
     * @return array
     * @throws \Exception
     */
    public function getHotelCodeList(string $cityCode, bool $isDetailedResponse = true): array
    {
        $cityCode = strtoupper($cityCode);

        return Cache::remember("tbo_hotel_list_{$cityCode}", 60 * 24, function () use ($cityCode, $isDetailedResponse) {
            $payload = [
                'CityCode'           => $cityCode,
                'IsDetailedResponse' => $isDetailedResponse ? 'true' : 'false',
            ];

            $response = $this->post('TBOHotelCodeList', $payload, 120);

            if (empty($response['Hotels'])) {
                // قد لا تكون هناك فنادق أو فشل في الاستجابة
                \Log::warning("TBOHotelService: No hotels found for city {$cityCode}");
                return [];
            }

            return $response['Hotels'];
        });
    }

    // =============================================
    // 12. Account Details (Agency Balance)
    // =============================================

    /**
     * Get the current agency balance with TBO.
     * Useful for pre-booking checks.
     *
     * @return array
     */
    public function getAgencyBalance(): array
    {
        $response = $this->post('AccountDetails', []);

        return [
            'balance'         => $response['AccountDetails']['Balance'] ?? 0,
            'credit_limit'    => $response['AccountDetails']['CreditLimit'] ?? 0,
            'currency'        => $response['AccountDetails']['Currency'] ?? 'SAR',
            'agency_name'     => $response['AccountDetails']['AgencyName'] ?? null,
            'raw'             => $response,
        ];
    }

    // =============================================
    // 13. Amendment (Modify Booking)
    // =============================================

    /**
     * Request an amendment for a booking (e.g. change dates/rooms).
     * This is a structural placeholder as amendments often require specific manual approval or complex logic.
     *
     * @param string $tboBookingId
     * @param array $amendmentData
     */
    public function amendBooking(string $tboBookingId, array $amendmentData): array
    {
        $payload = array_merge(['BookingId' => $tboBookingId], $amendmentData);

        // TBO uses 'Amendment' endpoint for both dates and guest changes
        $response = $this->post('Amendment', $payload, 60);

        return $response;
    }
}
