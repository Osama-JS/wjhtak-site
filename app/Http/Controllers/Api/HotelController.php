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

    protected TBOHotelService $tboService;

    public function __construct(TBOHotelService $tboService)
    {
        $this->tboService = $tboService;
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
    public function cities(Request $request)
    {
        try {
            $query = $request->get('q');

            if ($query && strlen($query) >= 2) {
                $cities = $this->tboService->searchCities($query);
            } else {
                $cities = $this->tboService->getCityList();
                // Limit to first 100 if no query
                $cities = array_slice($cities, 0, 100);
            }

            return $this->apiResponse(false, __('Cities retrieved successfully.'), $cities);
        } catch (\Exception $e) {
            Log::error('TBO Cities Error: ' . $e->getMessage());
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
            new OA\Response(response: 200, description: 'Hotels found',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'error', type: 'boolean', example: false),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'session_id', type: 'string', description: 'Session ID required for room listing and pre-book'),
                        new OA\Property(property: 'hotels', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'count', type: 'integer'),
                    ]),
                ])
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

            return $this->apiResponse(false, __('Hotels found successfully.'), [
                'session_id' => $result['session_id'],
                'count'      => count($result['hotels']),
                'hotels'     => $result['hotels'],
            ]);
        } catch (\Exception $e) {
            Log::error('TBO Hotel Search Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Hotel search failed. Please try again.'), null, null, 500);
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
            new OA\Response(response: 200, description: 'Room list returned',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'error', type: 'boolean'),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'hotel_name', type: 'string'),
                        new OA\Property(property: 'address', type: 'string'),
                        new OA\Property(property: 'rooms', type: 'array', items: new OA\Items(type: 'object')),
                    ]),
                ])
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
            new OA\Response(response: 200, description: 'Pre-book successful',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'error', type: 'boolean', example: false),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'available',      type: 'boolean'),
                        new OA\Property(property: 'result_token',   type: 'string', description: 'Use this in /book request'),
                        new OA\Property(property: 'total_price',    type: 'number'),
                        new OA\Property(property: 'currency',       type: 'string', example: 'SAR'),
                        new OA\Property(property: 'cancellation_policy', type: 'object'),
                    ]),
                ])
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

            // Create draft booking
            $booking = HotelBooking::create([
                'user_id'        => $user->id,
                'tbo_session_id' => $request->session_id,
                'tbo_result_token' => $request->result_token,
                'hotel_code'     => $request->hotel_code,
                'hotel_name'     => $request->hotel_name,
                'hotel_name_ar'  => $request->hotel_name_ar,
                'hotel_address'  => $request->hotel_address,
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
            new OA\Response(response: 200, description: 'Bookings list'),
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
            new OA\Response(response: 200, description: 'Booking detail'),
            new OA\Response(response: 404, description: 'Booking not found'),
        ]
    )]
    public function bookingDetail(Request $request, int $id)
    {
        try {
            $booking = HotelBooking::where('user_id', $request->user()->id)
                ->with(['guests', 'payment', 'histories'])
                ->findOrFail($id);

            return $this->apiResponse(false, __('Booking details retrieved.'), $booking);
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
}
