<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use App\Models\HotelBookingGuest;
use App\Models\HotelBookingHistory;
use App\Services\TBOHotelService;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class HotelController extends Controller
{
    use ApiResponseTrait;

    protected $tboService;
    protected $markupService;

    public function __construct()
    {
        $this->markupService = app(\App\Services\HotelMarkupService::class);
        $this->tboService    = app(\App\Services\TBOHotelService::class);
    }

    

    // =========================================================
    // 1. City List
    // =========================================================

    #[OA\Get(
        path: '/api/hotels/cities',
        summary: 'Get available cities for hotel search',
        operationId: 'hotelCities',
        description: 'Returns a list of cities supported by TBO Hotel API. Results are cached for 24 hours. Supports search by city name.',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: false, description: 'Search query (city name)', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'City list returned successfully',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'error', type: 'boolean', example: false),
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(properties: [
                        new OA\Property(property: 'CityCode', type: 'string', example: 'RUH'),
                        new OA\Property(property: 'CityName', type: 'string', example: 'Riyadh'),
                        new OA\Property(property: 'CountryCode', type: 'string', example: 'SA'),
                        new OA\Property(property: 'CountryName', type: 'string', example: 'Saudi Arabia'),
                    ])),
                ])
            ),
        ]
    )]
    /**
     * Get available cities for hotel search.
     */
    public function cities(Request $request)
    {
        try {
            $query = $request->get('q');

            if ($query && strlen($query) >= 2) {
                // Search in local TBO cities table (EN and AR)
                $cities = \App\Models\TboCity::search($query)
                    ->orderBy('name')
                    ->limit(50)
                    ->get();
            } else {
                // Return default list (first 100)
                $cities = \App\Models\TboCity::orderBy('name')->limit(100)->get();
            }

            $data = $cities->map(fn($city) => [
                'CityCode'      => $city->city_code,
                'CityName'      => $city->name,
                'CityNameAr'    => $city->name_ar,
                'CountryCode'   => $city->country_code,
                'CountryName'   => $city->country_name,
                'CountryNameAr' => $city->country_name_ar,
            ]);

            return $this->apiResponse(false, __('Cities retrieved successfully.'), $data);
        } catch (\Exception $e) {
            Log::error('TBO Local Cities Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Failed to retrieve cities.'), null, null, 500);
        }
    }

    // =========================================================
    // 2. Hotel Search
    // =========================================================

    #[OA\Post(
        path: '/api/hotels/search',
        summary: 'Search for available hotels',
        operationId: 'hotelSearch',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['city_code', 'check_in', 'check_out', 'adults', 'rooms'],
                properties: [
                    new OA\Property(property: 'city_code',      type: 'string',  example: 'RUH',        description: 'TBO City Code (from /api/hotels/cities)'),
                    new OA\Property(property: 'check_in',       type: 'string',  example: '2026-04-01',  description: 'Check-in date (Y-m-d)'),
                    new OA\Property(property: 'check_out',      type: 'string',  example: '2026-04-05',  description: 'Check-out date (Y-m-d)'),
                    new OA\Property(property: 'adults',         type: 'integer', example: 2),
                    new OA\Property(property: 'children',       type: 'integer', example: 0),
                    new OA\Property(property: 'children_ages',  type: 'array',   items: new OA\Items(type: 'integer'), example: [], description: 'Ages of children (required if children > 0)'),
                    new OA\Property(property: 'rooms',          type: 'integer', example: 1),
                    new OA\Property(property: 'nationality',    type: 'string',  example: 'SA', description: 'Guest nationality (ISO 2-letter)'),
                    new OA\Property(property: 'currency',       type: 'string',  example: 'SAR'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hotels found successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Hotels found successfully.'),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'session_id', type: 'string', example: '7906efba-09db-4481-8c60-0d7f5b5e6c44', description: 'Session ID required for room listing and pre-book'),
                            new OA\Property(property: 'count', type: 'integer', example: 10),
                            new OA\Property(property: 'hotels', type: 'array', items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'HotelCode', type: 'string', example: '1000001'),
                                    new OA\Property(property: 'HotelName', type: 'string', example: 'Grand Plaza Hotel'),
                                    new OA\Property(property: 'HotelAddress', type: 'string', example: 'King Fahd Rd, Riyadh'),
                                    new OA\Property(property: 'StarRating', type: 'integer', example: 4),
                                    new OA\Property(property: 'HotelPicture', type: 'string', example: 'http://...'),
                                    new OA\Property(property: 'LowestRate', type: 'number', example: 450.00),
                                    new OA\Property(property: 'Latitude', type: 'string', example: '24.7136'),
                                    new OA\Property(property: 'Longitude', type: 'string', example: '46.6753'),
                                ]
                            )),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation Error'),
            new OA\Response(response: 500, description: 'TBO API Error'),
        ]
    )]
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_code'     => 'required|string',
            'check_in'      => 'required|date|after_or_equal:today',
            'check_out'     => 'required|date|after:check_in',
            'adults'        => 'required|integer|min:1|max:9',
            'children'      => 'nullable|integer|min:0|max:6',
            'children_ages' => 'nullable|array',
            'children_ages.*' => 'integer|min:0|max:17',
            'rooms'         => 'required|integer|min:1|max:9',
            'nationality'   => 'nullable|string|size:2',
            'currency'      => 'nullable|string|size:3',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $result = $this->tboService->searchHotels($request->all());

            // Apply Markup
            $hotelsWithMarkup = $this->markupService->applyMarkupToHotels($result['hotels']);

            return $this->apiResponse(false, __('Hotels found successfully.'), [
                'session_id' => $result['session_id'],
                'count'      => count($hotelsWithMarkup),
                'hotels'     => $hotelsWithMarkup,
            ]);
        } catch (\Exception $e) {
            Log::error('TBO Hotel Search Error: ' . $e->getMessage());
            return $this->apiResponse(true,$e->getMessage(), null, null, 500);
        }
    }

    // =========================================================
    // 3. Hotel Rooms
    // =========================================================

    #[OA\Get(
        path: '/api/hotels/{hotelCode}/rooms',
        summary: 'Get available rooms for a specific hotel',
        operationId: 'hotelRooms',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'hotelCode',  in: 'path',  required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'session_id', in: 'query', required: true, description: 'Session ID from /search', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Room list returned successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'hotel_name', type: 'string', example: 'Grand Plaza Hotel'),
                            new OA\Property(property: 'address', type: 'string', example: 'King Fahd Rd, Riyadh'),
                            new OA\Property(property: 'rooms', type: 'array', items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'RoomIndex', type: 'integer', example: 1),
                                    new OA\Property(property: 'RoomTypeName', type: 'string', example: 'Deluxe Room'),
                                    new OA\Property(property: 'RatePlanCode', type: 'string', example: 'R123'),
                                    new OA\Property(property: 'TotalFare', type: 'number', example: 500.00),
                                    new OA\Property(property: 'Currency', type: 'string', example: 'SAR'),
                                    new OA\Property(property: 'Inclusions', type: 'array', items: new OA\Items(type: 'string'), example: ['Breakfast Included']),
                                ]
                            )),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation Error'),
        ]
    )]
    public function rooms(Request $request, string $hotelCode)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $result = $this->tboService->getRoomList($request->session_id, $hotelCode);

            // Apply Markup
            if (!empty($result['rooms'])) {
                $result['rooms'] = $this->markupService->applyMarkupToRooms($result['rooms']);
            }

            return $this->apiResponse(false, __('Room list retrieved successfully.'), $result);
        } catch (\Exception $e) {
            Log::error("TBO Hotel Rooms Error [{$hotelCode}]: " . $e->getMessage());
            return $this->apiResponse(true, __('Failed to retrieve rooms.'), null, null, 500);
        }
    }

    // =========================================================
    // 4. Pre-Book (MANDATORY — Price Validation)
    // =========================================================

    #[OA\Post(
        path: '/api/hotels/pre-book',
        summary: 'Validate price and availability before booking (MANDATORY step)',
        operationId: 'hotelPreBook',
        description: "**MANDATORY:** Must be called before /api/hotels/book to validate the price is still available.\n\nReturns a `result_token` (BookingCode) and the final `total_price`. If the price changes, show the user the updated price before proceeding.",
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['session_id', 'room_index', 'rate_plan_code'],
                properties: [
                    new OA\Property(property: 'session_id',      type: 'string', description: 'Session ID from /search'),
                    new OA\Property(property: 'room_index',      type: 'integer', example: 1, description: 'Room index number from /rooms response'),
                    new OA\Property(property: 'rate_plan_code',  type: 'string', description: 'RatePlanCode from selected room'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Pre-book successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'available',      type: 'boolean', example: true),
                            new OA\Property(property: 'result_token',   type: 'string', description: 'Mandatory: Use this in /book request as the unique key'),
                            new OA\Property(property: 'total_price',    type: 'number', example: 1250.00),
                            new OA\Property(property: 'currency',       type: 'string', example: 'SAR'),
                            new OA\Property(property: 'cancellation_policy', type: 'object', properties: [
                                new OA\Property(property: 'LastCancellationDeadline', type: 'string', example: '2026-03-25T00:00:00'),
                                new OA\Property(property: 'Rules', type: 'array', items: new OA\Items(type: 'object')),
                            ]),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation Error'),
            new OA\Response(response: 500, description: 'Pre-book failed'),
        ]
    )]
    public function preBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id'     => 'required|string',
            'room_index'     => 'required|integer|min:1',
            'rate_plan_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $result = $this->tboService->preBook(
                $request->session_id,
                $request->room_index,
                $request->rate_plan_code
            );

            // Store the raw result in cache for the book step (expires in 30 mins)
            if (isset($result['result_token'])) {
                \Cache::put('tbo_prebook_' . $result['result_token'], $result, 60 * 30);
            }

            // Apply Markup to the pre-book result
            if (isset($result['total_price'])) {
                $result['total_price'] = $this->markupService->applyMarkup($result['total_price']);
            }

            return $this->apiResponse(false, __('Room is available. Proceed to payment.'), $result);
        } catch (\Exception $e) {
            Log::error('TBO Pre-Book Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Room is no longer available. Please search again.'), null, null, 500);
        }
    }

    // =========================================================
    // 5. Book (Initiate Draft + Payment)
    // =========================================================

    #[OA\Post(
        path: '/api/hotels/book',
        summary: 'Create a hotel booking (Draft — awaits payment)',
        operationId: 'hotelBook',
        description: "Creates a draft booking record in the database. **Does NOT confirm with TBO yet.** Confirmation happens after payment via `/api/payment/hotel/verify`.\n\nCall `/api/payment/hotel/initiate` next to start payment.",
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['result_token', 'hotel_code', 'hotel_name', 'city_name', 'country_code', 'room_type_code',
                           'room_type_name', 'check_in', 'check_out', 'adults', 'rooms', 'total_price', 'guests'],
                properties: [
                    new OA\Property(property: 'result_token',    type: 'string', description: 'BookingCode from /pre-book'),
                    new OA\Property(property: 'session_id',      type: 'string'),
                    new OA\Property(property: 'hotel_code',      type: 'string'),
                    new OA\Property(property: 'hotel_name',      type: 'string'),
                    new OA\Property(property: 'hotel_name_ar',   type: 'string', nullable: true),
                    new OA\Property(property: 'hotel_address',   type: 'string', nullable: true),
                    new OA\Property(property: 'hotel_image',     type: 'string', nullable: true),
                    new OA\Property(property: 'city_name',       type: 'string'),
                    new OA\Property(property: 'country_code',    type: 'string', example: 'SA'),
                    new OA\Property(property: 'room_type_code',  type: 'string'),
                    new OA\Property(property: 'room_type_name',  type: 'string'),
                    new OA\Property(property: 'check_in',        type: 'string', example: '2026-04-01'),
                    new OA\Property(property: 'check_out',       type: 'string', example: '2026-04-05'),
                    new OA\Property(property: 'adults',          type: 'integer', example: 2),
                    new OA\Property(property: 'children',        type: 'integer', example: 0),
                    new OA\Property(property: 'rooms',           type: 'integer', example: 1),
                    new OA\Property(property: 'total_price',     type: 'number',  example: 1200.00),
                    new OA\Property(property: 'currency',        type: 'string',  example: 'SAR'),
                    new OA\Property(property: 'notes',           type: 'string',  nullable: true),
                    new OA\Property(property: 'guests', type: 'array', description: 'List of guests', items: new OA\Items(
                        required: ['title', 'first_name', 'last_name', 'type'],
                        properties: [
                            new OA\Property(property: 'title',           type: 'string', enum: ['Mr', 'Mrs', 'Ms', 'Mstr']),
                            new OA\Property(property: 'first_name',      type: 'string'),
                            new OA\Property(property: 'last_name',       type: 'string'),
                            new OA\Property(property: 'type',            type: 'string', enum: ['adult', 'child']),
                            new OA\Property(property: 'nationality',     type: 'string', nullable: true),
                            new OA\Property(property: 'passport_number', type: 'string', nullable: true),
                            new OA\Property(property: 'passport_expiry', type: 'string', nullable: true, example: '2030-01-01'),
                            new OA\Property(property: 'dob',             type: 'string', nullable: true, example: '1990-05-20'),
                        ]
                    )),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Draft booking created — proceed to payment',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'error', type: 'boolean', example: false),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'hotel_booking_id', type: 'integer', description: 'Use this ID in /api/payment/hotel/initiate'),
                        new OA\Property(property: 'status',           type: 'string', example: 'draft'),
                        new OA\Property(property: 'total_price',      type: 'number'),
                    ]),
                ])
            ),
            new OA\Response(response: 422, description: 'Validation Error'),
        ]
    )]
    public function book(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'result_token'   => 'required|string',
            'hotel_code'     => 'required|string',
            'hotel_name'     => 'required|string',
            'hotel_image'    => 'nullable|string',
            'city_name'      => 'required|string',
            'country_code'   => 'required|string|size:2',
            'room_type_code' => 'required|string',
            'room_type_name' => 'required|string',
            'check_in'       => 'required|date',
            'check_out'      => 'required|date|after:check_in',
            'adults'         => 'required|integer|min:1',
            'children'       => 'nullable|integer|min:0',
            'rooms'          => 'required|integer|min:1',
            'total_price'    => 'required|numeric|min:1',
            'currency'       => 'nullable|string|size:3',
            'guests'         => 'required|array|min:1',
            'guests.*.title'           => 'required|in:Mr,Mrs,Ms,Mstr',
            'guests.*.first_name'      => 'required|string',
            'guests.*.last_name'       => 'required|string',
            'guests.*.type'            => 'required|in:adult,child',
            'guests.*.nationality'     => 'nullable|string|size:2',
            'guests.*.passport_number' => 'nullable|string',
            'guests.*.passport_expiry' => 'nullable|date',
            'guests.*.dob'             => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            DB::beginTransaction();

            $user    = $request->user();
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);

            // Retrieve raw pre-book data from cache
            $rawPrebook = \Cache::get('tbo_prebook_' . $request->result_token);

            // Create draft booking
            $booking = HotelBooking::create([
                'user_id'        => $user->id,
                'tbo_session_id' => $request->session_id,
                'tbo_result_token' => $request->result_token,
                'hotel_code'     => $request->hotel_code,
                'hotel_name'     => $request->hotel_name,
                'hotel_name_ar'  => $request->hotel_name_ar,
                'hotel_address'  => $request->hotel_address,
                'hotel_image'    => $request->hotel_image,
                'city_name'      => $request->city_name,
                'country_code'   => $request->country_code,
                'room_type_code' => $request->room_type_code,
                'room_type_name' => $request->room_type_name,
                'check_in_date'  => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'nights_count'   => $checkIn->diffInDays($checkOut),
                'adults'         => $request->adults,
                'children'       => $request->children ?? 0,
                'rooms_count'    => $request->rooms,
                'total_price'    => $request->total_price,
                'currency'       => $request->currency ?? 'SAR',
                'status'         => HotelBooking::STATUS_DRAFT,
                'booking_state'  => HotelBooking::STATE_AWAITING_PAYMENT,
                'notes'          => $request->notes,
                'tbo_raw_prebook'=> $rawPrebook['raw'] ?? $rawPrebook,
            ]);

            // Save guests
            foreach ($request->guests as $guestData) {
                HotelBookingGuest::create([
                    'hotel_booking_id' => $booking->id,
                    'title'            => $guestData['title'],
                    'first_name'       => $guestData['first_name'],
                    'last_name'        => $guestData['last_name'],
                    'type'             => $guestData['type'],
                    'nationality'      => $guestData['nationality'] ?? null,
                    'passport_number'  => $guestData['passport_number'] ?? null,
                    'passport_expiry'  => $guestData['passport_expiry'] ?? null,
                    'dob'              => $guestData['dob'] ?? null,
                ]);
            }

            // Log history
            HotelBookingHistory::create([
                'hotel_booking_id' => $booking->id,
                'user_id'          => $user->id,
                'action'           => 'booking_draft_created',
                'description'      => 'تم إنشاء الحجز في انتظار الدفع.',
                'new_state'        => HotelBooking::STATE_AWAITING_PAYMENT,
            ]);

            DB::commit();

            return $this->apiResponse(false, __('Booking created. Please proceed to payment.'), [
                'hotel_booking_id' => $booking->id,
                'status'           => $booking->status,
                'booking_state'    => $booking->booking_state,
                'total_price'      => $booking->total_price,
                'currency'         => $booking->currency,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hotel Book Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Failed to create booking.'), null, null, 500);
        }
    }

    // =========================================================
    // 6. My Bookings
    // =========================================================

    #[OA\Get(
        path: '/api/hotels/bookings',
        summary: 'Get authenticated user hotel bookings',
        operationId: 'myHotelBookings',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['draft', 'pending', 'confirmed', 'cancelled', 'failed'])),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hotel bookings list retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Hotel bookings retrieved.'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'hotel_name', type: 'string'),
                                new OA\Property(property: 'hotel_image', type: 'string', nullable: true),
                                new OA\Property(property: 'check_in_date', type: 'string', format: 'date'),
                                new OA\Property(property: 'total_price', type: 'number'),
                                new OA\Property(property: 'booking_state_label', type: 'string'),
                            ]
                        )),
                    ]
                )
            ),
        ]
    )]
    public function myBookings(Request $request)
    {
        try {
            $query = HotelBooking::where('user_id', $request->user()->id)
                ->with(['guests', 'payment'])
                ->when($request->status, fn($q, $s) => $q->where('status', $s))
                ->latest();

            $bookings = $query->paginate($request->get('per_page', 10));

            $bookings->getCollection()->transform(function ($booking) {
                return [
                    'id'                  => $booking->id,
                    'hotel_code'          => $booking->hotel_code,
                    'hotel_name'          => $booking->hotel_name,
                    'hotel_name_ar'       => $booking->hotel_name_ar,
                    'hotel_image'         => $booking->hotel_image,
                    'city_name'           => $booking->city_name,
                    'star_rating'         => $booking->star_rating,
                    'check_in_date'       => $booking->check_in_date->format('Y-m-d'),
                    'check_out_date'      => $booking->check_out_date->format('Y-m-d'),
                    'nights_count'        => $booking->nights_count,
                    'rooms_count'         => $booking->rooms_count,
                    'total_price'         => (float)$booking->total_price,
                    'currency'            => $booking->currency,
                    'status'              => $booking->status,
                    'booking_state'       => $booking->booking_state,
                    'booking_state_label' => __($booking->booking_state),
                    'created_at'          => $booking->created_at->toDateTimeString(),
                    'guests_count'        => $booking->guests->count(),
                ];
            });

            return $this->apiResponse(false, __('Hotel bookings retrieved.'), $bookings);
        } catch (\Exception $e) {
            Log::error('Hotel My Bookings Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Failed to retrieve bookings.'), null, null, 500);
        }
    }

    // =========================================================
    // 7. Booking Detail
    // =========================================================

    #[OA\Get(
        path: '/api/hotels/bookings/{id}',
        summary: 'Get single hotel booking details',
        operationId: 'hotelBookingDetail',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Booking details retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'hotel_name', type: 'string'),
                            new OA\Property(property: 'guests', type: 'array', items: new OA\Items(type: 'object')),
                            new OA\Property(property: 'payment', type: 'object', nullable: true),
                            new OA\Property(property: 'history', type: 'array', items: new OA\Items(type: 'object')),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Booking not found'),
        ]
    )]
    public function bookingDetail(Request $request, int $id)
    {
        try {
            $booking = HotelBooking::where('user_id', $request->user()->id)
                ->with(['guests', 'payment', 'histories'])
                ->findOrFail($id);

            $data = [
                'id'                  => $booking->id,
                'tbo_booking_id'      => $booking->tbo_booking_id,
                'hotel_code'          => $booking->hotel_code,
                'hotel_name'          => $booking->hotel_name,
                'hotel_name_ar'       => $booking->hotel_name_ar,
                'hotel_address'       => $booking->hotel_address,
                'hotel_image'         => $booking->hotel_image,
                'city_name'           => $booking->city_name,
                'star_rating'         => $booking->star_rating,
                'room_type_name'      => $booking->room_type_name,
                'check_in_date'       => $booking->check_in_date->format('Y-m-d'),
                'check_out_date'      => $booking->check_out_date->format('Y-m-d'),
                'nights_count'        => $booking->nights_count,
                'rooms_count'         => $booking->rooms_count,
                'adults'              => $booking->adults,
                'children'            => $booking->children,
                'total_price'         => (float)$booking->total_price,
                'currency'            => $booking->currency,
                'status'              => $booking->status,
                'booking_state'       => $booking->booking_state,
                'booking_state_label' => __($booking->booking_state),
                'notes'               => $booking->notes,
                'cancellation_policy' => $booking->cancellation_policy,
                'created_at'          => $booking->created_at->toDateTimeString(),
                'guests'              => $booking->guests->map(fn($g) => [
                    'id'               => $g->id,
                    'title'            => $g->title,
                    'first_name'       => $g->first_name,
                    'last_name'        => $g->last_name,
                    'type'             => $g->type,
                    'is_lead'          => (bool)$g->is_lead,
                    'nationality'      => $g->nationality,
                    'passport_number'  => $g->passport_number,
                ]),
                'payment'             => $booking->payment ? [
                    'status'         => $booking->payment->status,
                    'amount'         => (float)$booking->payment->amount,
                    'method'         => $booking->payment->payment_method,
                    'transaction_id' => $booking->payment->transaction_id,
                    'date'           => $booking->payment->created_at->toDateTimeString(),
                ] : null,
                'history'             => $booking->histories->map(fn($h) => [
                    'action'      => $h->action,
                    'description' => $h->description,
                    'new_state'   => $h->new_state,
                    'date'        => $h->created_at->toDateTimeString(),
                ]),
            ];

            return $this->apiResponse(false, __('Booking details retrieved.'), $data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->apiResponse(true, __('Booking not found.'), null, null, 404);
        } catch (\Exception $e) {
            Log::error("Hotel Booking Detail Error [{$id}]: " . $e->getMessage());
            return $this->apiResponse(true, __('Failed to retrieve booking details.'), null, null, 500);
        }
    }

    // =========================================================
    // 8. Cancel Booking
    // =========================================================

    #[OA\Post(
        path: '/api/hotels/bookings/{id}/cancel',
        summary: 'Cancel a hotel booking',
        operationId: 'cancelHotelBooking',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'reason', type: 'string', nullable: true, description: 'Optional cancellation reason'),
            ])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Booking cancelled successfully'),
            new OA\Response(response: 400, description: 'Cannot cancel this booking'),
            new OA\Response(response: 404, description: 'Booking not found'),
        ]
    )]
    public function cancel(Request $request, int $id)
    {
        try {
            $booking = HotelBooking::where('user_id', $request->user()->id)->findOrFail($id);

            // Already cancelled
            if ($booking->status === HotelBooking::STATUS_CANCELLED) {
                return $this->apiResponse(true, __('Booking is already cancelled.'), null, null, 400);
            }

            // Draft/failed bookings can be cancelled locally without TBO call
            if (in_array($booking->status, [HotelBooking::STATUS_DRAFT, HotelBooking::STATUS_FAILED])) {
                $booking->update([
                    'status'              => HotelBooking::STATUS_CANCELLED,
                    'booking_state'       => HotelBooking::STATE_CANCELLED,
                    'cancellation_reason' => $request->reason,
                ]);

                HotelBookingHistory::create([
                    'hotel_booking_id' => $booking->id,
                    'user_id'          => $request->user()->id,
                    'action'           => 'booking_cancelled_locally',
                    'description'      => 'تم إلغاء الحجز (لم يتم الدفع بعد).',
                    'previous_state'   => HotelBooking::STATE_AWAITING_PAYMENT,
                    'new_state'        => HotelBooking::STATE_CANCELLED,
                ]);

                return $this->apiResponse(false, __('Booking cancelled successfully.'));
            }

            // Confirmed booking — must cancel with TBO
            if (empty($booking->tbo_booking_id)) {
                return $this->apiResponse(true, __('Cannot cancel: TBO booking reference missing.'), null, null, 400);
            }

            DB::beginTransaction();

            $cancelResult = $this->tboService->cancelBooking($booking->tbo_booking_id);

            $booking->update([
                'status'              => HotelBooking::STATUS_CANCELLED,
                'booking_state'       => HotelBooking::STATE_CANCELLED,
                'cancellation_reason' => $request->reason,
                'tbo_raw_booking'     => array_merge($booking->tbo_raw_booking ?? [], ['cancellation' => $cancelResult['raw']]),
            ]);

            HotelBookingHistory::create([
                'hotel_booking_id' => $booking->id,
                'user_id'          => $request->user()->id,
                'action'           => 'booking_cancelled_tbo',
                'description'      => 'تم إلغاء الحجز من TBO بنجاح.',
                'previous_state'   => HotelBooking::STATE_CONFIRMED,
                'new_state'        => HotelBooking::STATE_CANCELLED,
            ]);

            DB::commit();

            return $this->apiResponse(false, __('Booking cancelled successfully.'), [
                'tbo_booking_id' => $booking->tbo_booking_id,
                'remarks'        => $cancelResult['remarks'] ?? null,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->apiResponse(true, __('Booking not found.'), null, null, 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Hotel Cancel Error [{$id}]: " . $e->getMessage());
            return $this->apiResponse(true, __('Failed to cancel booking: ') . $e->getMessage(), null, null, 500);
        }
    }


    // =========================================================
    // 9. Country List
    // =========================================================


    #[OA\Get(
        path: '/api/hotels/country-list',
        summary: 'Get TBO country list',
        operationId: 'countryList',
        tags: ['Hotels'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Country list retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            ),
            new OA\Response(response: 500, description: 'Failed to retrieve country list'),
        ]
    )]
    public function countryList()
    {
        try {
            // Cached TBO CountryList for 24 hours
            $countries = \Cache::remember('tbo_country_list', 60 * 24, function () {
                return $this->tboService->countryList();
            });

            return $this->apiResponse(false, __('Country list retrieved successfully.'), $countries);

        } catch (\Exception $e) {
            \Log::error('CountryList Error: ' . $e->getMessage());

            return $this->apiResponse(true, __('Failed to retrieve country list.'), [], null, 500);
        }
    }


    // =========================================================
    // 11. Booking Details Based on Date
    // =========================================================

    #[OA\Post(
        path: '/api/hotels/bookings/by-date',
        summary: 'Retrieve booking details made for requested date range',
        operationId: 'hotelBookingDetailBasedOnDate',
        description: 'Maximum of 60 days (about 2 months) booking details can be retrieved.',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['from_date', 'to_date'],
                properties: [
                    new OA\Property(property: 'from_date', type: 'string', format: 'date', example: '2023-11-09', description: 'Format: YYYY-MM-DD'),
                    new OA\Property(property: 'to_date',   type: 'string', format: 'date', example: '2023-11-10', description: 'Format: YYYY-MM-DD'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Booking details retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Booking details retrieved successfully.'),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'Status', type: 'object', properties: [
                                new OA\Property(property: 'Code', type: 'integer', example: 200),
                                new OA\Property(property: 'Description', type: 'string', example: 'HotelBookingDetailBasedOnDate Successful'),
                            ]),
                            new OA\Property(property: 'BookingDetail', type: 'array', items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'Index', type: 'integer', example: 1),
                                    new OA\Property(property: 'BookingId', type: 'string', example: '264056'),
                                    new OA\Property(property: 'ConfirmationNo', type: 'string', example: 'GOF05R'),
                                    new OA\Property(property: 'BookingDate', type: 'string', example: '10-Nov-2023'),
                                    new OA\Property(property: 'Currency', type: 'string', example: 'USD'),
                                    new OA\Property(property: 'AgentMarkup', type: 'string', example: '0.00'),
                                    new OA\Property(property: 'AgencyName', type: 'string', example: 'ATravels'),
                                    new OA\Property(property: 'BookingStatus', type: 'string', example: 'Vouchered'),
                                    new OA\Property(property: 'BookingPrice', type: 'string', example: '583.89'),
                                    new OA\Property(property: 'TripName', type: 'string', example: 'Sharma_02Dec_Dubai'),
                                    new OA\Property(property: 'TBOHotelCode', type: 'string', example: '1022623'),
                                    new OA\Property(property: 'CheckInDate', type: 'string', example: '02-Dec-2023'),
                                    new OA\Property(property: 'CheckOutDate', type: 'string', example: '10-Dec-2023'),
                                    new OA\Property(property: 'ClientReferenceNumber', type: 'string', example: '123680'),
                                ]
                            )),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation Error'),
            new OA\Response(response: 500, description: 'Internal Server Error'),
        ]
    )]
    public function bookingDetailsByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d',
            'to_date'   => 'required|date_format:Y-m-d|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $fromDate = Carbon::parse($request->from_date);
            $toDate = Carbon::parse($request->to_date);

            if ($fromDate->diffInDays($toDate) > 60) {
                return $this->apiResponse(true, __('Maximum of 60 days booking details can be retrieved.'), null, null, 422);
            }

            $result = $this->tboService->getBookingDetailsByDate($request->from_date, $request->to_date);

            return $this->apiResponse(false, __('Booking details retrieved successfully.'), $result);
        } catch (\Exception $e) {
            Log::error('Hotel Booking Details By Date Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Failed to retrieve booking details: ') . $e->getMessage(), null, null, 500);
        }
    }

    // =========================================================
    // 12. TBOHotelCodeList
    // =========================================================

    #[OA\Post(
        path: '/api/hotels/code-list',
        summary: 'Fetch complete detail of hotels by city code',
        operationId: 'hotelCodeList',
        description: 'Returns hotel names, addresses, descriptions, images, etc. associated with a city code.',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['city_code'],
                properties: [
                    new OA\Property(property: 'city_code', type: 'string', example: '130452', description: 'Unique TBOH City code'),
                    new OA\Property(property: 'is_detailed', type: 'boolean', example: true, description: 'Default is true'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hotel code list retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'Status', type: 'object', properties: [
                                new OA\Property(property: 'Code', type: 'integer', example: 200),
                                new OA\Property(property: 'Description', type: 'string', example: 'Success'),
                            ]),
                            new OA\Property(property: 'Hotels', type: 'array', items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'HotelCode', type: 'string', example: '1010099'),
                                    new OA\Property(property: 'HotelName', type: 'string', example: 'Holiday Inn Express New York - Manhattan West Side'),
                                    new OA\Property(property: 'HotelRating', type: 'string', example: 'ThreeStar'),
                                    new OA\Property(property: 'Address', type: 'string'),
                                    new OA\Property(property: 'Description', type: 'string'),
                                    new OA\Property(property: 'Map', type: 'string'),
                                    new OA\Property(property: 'PhoneNumber', type: 'string'),
                                    new OA\Property(property: 'PinCode', type: 'string'),
                                    new OA\Property(property: 'HotelWebsiteUrl', type: 'string'),
                                    new OA\Property(property: 'CityName', type: 'string'),
                                ]
                            )),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation Error'),
            new OA\Response(response: 500, description: 'Internal Server Error'),
        ]
    )]
    public function hotelCodeList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_code' => 'required|string',
            'is_detailed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $isDetailed = $request->get('is_detailed', true);
            $result = $this->tboService->getHotelCodeList($request->city_code, $isDetailed);

            return $this->apiResponse(false, __('Hotel code list retrieved successfully.'), $result);
        } catch (\Exception $e) {
            Log::error('Hotel Code List Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Failed to retrieve hotel list: ') . $e->getMessage(), null, null, 500);
        }
    }
    // =========================================================
    // 11. Booking Details By Date (Remote Lookup)
    // =========================================================

    #[OA\Get(
        path: '/api/hotels/bookings/by-date',
        summary: 'Get hotel bookings from TBO by date range (Remote Lookup)',
        operationId: 'hotelBookingsByDate',
        tags: ['Hotels'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'from_date', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-03-01')),
            new OA\Parameter(name: 'to_date',   in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-03-31')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Bookings retrieved successfully'),
            new OA\Response(response: 422, description: 'Validation Error'),
            new OA\Response(response: 500, description: 'TBO API Error'),
        ]
    )]
    public function bookingsByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date|format:Y-m-d',
            'to_date'   => 'required|date|format:Y-m-d|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $result = $this->tboService->getBookingDetailsByDate($request->from_date, $request->to_date);
            
            return $this->apiResponse(false, __('Bookings retrieved from TBO.'), $result);
        } catch (\Exception $e) {
            Log::error('TBO Bookings By Date Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Failed to retrieve bookings from TBO.'), null, null, 500);
        }
    }
}
