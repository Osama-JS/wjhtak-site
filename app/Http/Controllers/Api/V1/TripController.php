<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Models\Trip;
use App\Models\TripBooking;
use App\Models\Favorite;
use App\Models\BookingPassenger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class TripController extends Controller
{
    use ApiResponseTrait;
    /**
     * Get list of trips with filters.
     */
    #[OA\Get(
        path: "/api/v1/trips",
        summary: "Get trips list",
        operationId: "getTrips",
        description: "Retrieve a list of active trips with optional filters.",
        tags: ["Trips"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            ),
            new OA\Parameter(
                name: "search",
                in: "query",
                description: "Search by trip title",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "destination_id",
                in: "query",
                description: "Filter by city or country ID",
                required: false,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "price_min",
                in: "query",
                description: "Minimum price",
                required: false,
                schema: new OA\Schema(type: "number")
            ),
            new OA\Parameter(
                name: "price_max",
                in: "query",
                description: "Maximum price",
                required: false,
                schema: new OA\Schema(type: "number")
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                description: "Page number",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Trips retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Trips retrieved successfully"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "title", type: "string", example: "Amazing Paris"),
                                new OA\Property(property: "price", type: "number", example: 1500.00),
                                new OA\Property(property: "tickets", type: "integer", example: 10),
                                new OA\Property(property: "image", type: "string", example: "http://example.com/trips/1.jpg"),
                                new OA\Property(property: "to_country", type: "string", example: "France"),
                                new OA\Property(property: "is_favorite", type: "boolean", example: false),
                                new OA\Property(property: "base_capacity", type: "integer", example: 2),
                                new OA\Property(property: "extra_passenger_price", type: "number", example: 100.00),
                            ]
                        )),
                        new OA\Property(property: "pagination", type: "object", properties: [
                            new OA\Property(property: "pageNumber", type: "integer", example: 1),
                            new OA\Property(property: "pageSize", type: "integer", example: 10),
                            new OA\Property(property: "count", type: "integer", example: 50),
                            new OA\Property(property: "totalPages", type: "integer", example: 5),
                            new OA\Property(property: "hasNextPage", type: "boolean", example: true),
                            new OA\Property(property: "hasPreviousPage", type: "boolean", example: false),
                            new OA\Property(property: "nextPage", type: "string", example: "http://example.com/api/v1/trips?page=2"),
                            new OA\Property(property: "previousPage", type: "string", example: null),
                        ])
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Trip::query()->active();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('destination_id')) {
            $query->where(function($q) use ($request) {
                $q->where('to_city_id', $request->destination_id)
                  ->orWhere('to_country_id', $request->destination_id);
            });
        }

        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $trips = $query->with(['images', 'toCountry', 'toCity'])
            ->latest()
            ->paginate(10);

        // Get user favorites if logged in
        $userFavoriteIds = [];
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $userFavoriteIds = Favorite::where('user_id', $user->id)->pluck('trip_id')->toArray();
        }

        // Transform data
        $transformedData = $trips->getCollection()->map(function ($trip) use ($userFavoriteIds) {
            return [
                'id' => $trip->id,
                'title' => $trip->title, // Translatable if using Spatie Translatable
                'description' => $trip->description,
                'price' => $trip->price,
                'price_before_discount' => $trip->price_before_discount,
                'duration' => $trip->duration,
                'tickets' => $trip->tickets,
                'image' => $trip->image_url, // Accessor
                'to_country' => $trip->toCountry ? $trip->toCountry->name : null,
                'to_city' => $trip->toCity ? $trip->toCity->name : null,
                'is_active' => $trip->active,
                'expiry_date' => $trip->expiry_date,
                'is_favorite' => in_array($trip->id, $userFavoriteIds),
                'base_capacity' => $trip->base_capacity ?? 2,
                'extra_passenger_price' => $trip->extra_passenger_price ?? 0,
            ];
        });

        $trips->setCollection($transformedData);

        return $this->apiResponse(false, __('Trips retrieved successfully'), $trips);
    }

    /**
     * Get trip details.
     */
    #[OA\Get(
        path: "/api/v1/trips/{id}",
        summary: "Get trip details",
        operationId: "getTouristTripDetails",
        description: "Retrieve full details of a specific trip including itineraries.",
        tags: ["Trips"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            ),
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Trip ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Trip details retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Trip details retrieved successfully"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "title", type: "string", example: "Amazing Paris"),
                            new OA\Property(property: "duration", type: "string", example: "3 Days"),
                            new OA\Property(property: "tickets_available", type: "integer", example: 10),
                            new OA\Property(property: "expiry_date", type: "string", format: "date", example: "2024-12-31"),
                            new OA\Property(property: "company", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Wjhtak Tourism"),
                                new OA\Property(property: "logo", type: "string", example: "http://example.com/logo.png"),
                            ]),
                            new OA\Property(property: "location", type: "object", properties: [
                                new OA\Property(property: "country", type: "string", example: "France"),
                                new OA\Property(property: "city", type: "string", example: "Paris"),
                            ]),
                            new OA\Property(property: "base_capacity", type: "integer", example: 2),
                            new OA\Property(property: "extra_passenger_price", type: "number", example: 100.00),
                            new OA\Property(property: "images", type: "array", items: new OA\Items(type: "string")),
                            new OA\Property(property: "itineraries", type: "array", items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "day", type: "integer", example: 1),
                                    new OA\Property(property: "title", type: "string", example: "Arrival"),
                                    new OA\Property(property: "description", type: "string", example: "Arrive at airport..."),
                                ]
                            )),
                            new OA\Property(property: "is_favorite", type: "boolean", example: false),
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Trip not found"
            )
        ]
    )]
    public function show($id): JsonResponse
    {
        $trip = Trip::with(['images', 'toCountry', 'toCity', 'itineraries', 'company'])
            ->active()
            ->find($id);

        if (!$trip) {
            return $this->apiResponse(true, __('Trip not found or expired'), null, null, 404);
        }

        $data = [
            'id' => $trip->id,
            'title' => $trip->title,
            'description' => $trip->description,
            'price' => $trip->price,
            'price_before_discount' => $trip->price_before_discount,
            'duration' => $trip->duration,
            'tickets_available' => $trip->tickets,
            'expiry_date' => $trip->expiry_date,
            'company' => $trip->company ? [
                'id' => $trip->company->id,
                'name' => $trip->company->name,
                'logo' => $trip->company->logo_url, // Assuming accessor exists
            ] : null,
            'location' => [
                'country' => $trip->toCountry ? $trip->toCountry->name : null,
                'city' => $trip->toCity ? $trip->toCity->name : null,
            ],
            'base_capacity' => $trip->base_capacity ?? 2,
            'extra_passenger_price' => $trip->extra_passenger_price ?? 0,
            'images' => $trip->images->map(function ($img) {
                return asset('storage/' . $img->image_path);
            }),
            'itineraries' => $trip->itineraries->map(function ($itinerary) {
                return [
                    'day' => $itinerary->day_number,
                    'title' => $itinerary->title,
                    'description' => $itinerary->description,
                ];
            }),
            'is_favorite' => Auth::guard('sanctum')->check() && Favorite::where('user_id', Auth::guard('sanctum')->id())->where('trip_id', $trip->id)->exists(),
        ];

        return $this->apiResponse(false, __('Trip details retrieved successfully'), $data);
    }

    /**
     * Book a trip.
     */
    #[OA\Post(
        path: "/api/v1/trips/book",
        summary: "Book a trip",
        operationId: "bookTrip",
        description: "Book tickets for a specific trip. Requires authentication.",
        tags: ["Trips"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["trip_id", "tickets_count"],
                properties: [
                    new OA\Property(property: "trip_id", type: "integer", example: 1),
                    new OA\Property(property: "tickets_count", type: "integer", example: 2),
                    new OA\Property(property: "notes", type: "string", example: "Allergic to peanuts"),
                    new OA\Property(
                        property: "passengers",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "phone", type: "string", example: "+123456789"),
                                new OA\Property(property: "passport_number", type: "string", example: "A1234567"),
                                new OA\Property(property: "passport_expiry", type: "string", format: "date", example: "2030-12-31"),
                                new OA\Property(property: "nationality", type: "string", example: "USA"),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Booking successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Booking created successfully"),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation Error or Not enough tickets")
        ]
    )]
    public function book(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'tickets_count' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'passengers' => 'required|array|min:' . $request->tickets_count . '|max:' . $request->tickets_count,
            'passengers.*.name' => 'required|string|max:255',
            'passengers.*.phone' => 'nullable|string|max:20',
            'passengers.*.passport_number' => 'nullable|string|max:50',
            'passengers.*.passport_expiry' => 'nullable|date',
            'passengers.*.nationality' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed'), $validator->errors(), null, 422);
        }

        $trip = Trip::active()->find($request->trip_id);

        if (!$trip) {
            return $this->apiResponse(true, __('Trip not found or expired'), null, null, 404);
        }

        if ($trip->tickets < $request->tickets_count) {
             return $this->apiResponse(true, __('Not enough tickets available. Only :count left.', ['count' => $trip->tickets]), null, null, 422);
        }

        // Create booking
        $user = Auth::guard('sanctum')->user(); // Ensure using sanctum guard
        if (!$user) {
             return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        // Calculate dynamic price
        $passengersCount = count($request->passengers);
        $basePrice = $trip->price;
        $baseCapacity = $trip->base_capacity ?? 2;
        $extraPrice = $trip->extra_passenger_price ?? 0;

        $totalPrice = $basePrice;
        if ($passengersCount > $baseCapacity) {
            $extraPassengers = $passengersCount - $baseCapacity;
            $totalPrice += ($extraPassengers * $extraPrice);
        }

        $booking = TripBooking::create([
            'user_id' => $user->id,
            'trip_id' => $trip->id,
            'tickets_count' => $passengersCount,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'notes' => $request->notes,
            'booking_date' => now(),
        ]);

        // Save passengers
        foreach ($request->passengers as $passengerData) {
            $booking->passengers()->create($passengerData);
        }

        return $this->apiResponse(false, __('Booking created successfully'), $booking->load('passengers'));
    }

    /**
     * Get current user bookings.
     */
    #[OA\Get(
        path: "/api/v1/my-bookings",
        summary: "Get my bookings",
        operationId: "getMyBookings",
        description: "Retrieve a list of bookings for the authenticated user.",
        tags: ["Trips"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Bookings retrieved successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Bookings retrieved successful"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 101),
                                new OA\Property(property: "trip_id", type: "integer", example: 1),
                                new OA\Property(property: "tickets_count", type: "integer", example: 2),
                                new OA\Property(property: "total_price", type: "number", example: 3000.00),
                                new OA\Property(property: "status", type: "string", example: "pending"),
                                new OA\Property(property: "booking_date", type: "string", format: "date-time", example: "2024-05-20 10:00:00"),
                                new OA\Property(property: "trip", type: "object", properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "title", type: "string", example: "Amazing Paris"),
                                    new OA\Property(property: "image", type: "string", example: "http://example.com/trips/1.jpg"),
                                ])
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function myBookings(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $bookings = TripBooking::with(['trip.toCountry', 'trip.toCity'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return $this->apiResponse(false, __('Bookings retrieved successful'), $bookings);
    }

    /**
     * Toggle trip favorite state.
     */
    #[OA\Post(
        path: "/api/v1/trips/{id}/favorite",
        summary: "Toggle favorite",
        operationId: "toggleFavorite",
        description: "Add or remove a trip from user favorites. Requires authentication.",
        tags: ["Trips"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            ),
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Trip ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Operation successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Trip added to favorites"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "is_favorite", type: "boolean", example: true)
                        ])
                    ]
                )
            )
        ]
    )]
    public function toggleFavorite($id): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $trip = Trip::find($id);
        if (!$trip) {
            return $this->apiResponse(true, __('Trip not found'), null, null, 404);
        }

        $favorite = Favorite::where('user_id', $user->id)->where('trip_id', $id)->first();

        if ($favorite) {
            $favorite->delete();
            return $this->apiResponse(false, __('Trip removed from favorites'), ['is_favorite' => false]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'trip_id' => $id
            ]);
            return $this->apiResponse(false, __('Trip added to favorites'), ['is_favorite' => true]);
        }
    }

    /**
     * Get user favorite trips.
     */
    #[OA\Get(
        path: "/api/v1/favorites",
        summary: "Get my favorites",
        operationId: "getMyFavorites",
        description: "Retrieve a list of favorite trips for the authenticated user.",
        tags: ["Trips"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Favorites retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Favorites retrieved successfully"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "title", type: "string", example: "Amazing Paris"),
                                new OA\Property(property: "price", type: "number", example: 1500.00),
                                new OA\Property(property: "image", type: "string", example: "http://example.com/trips/1.jpg"),
                                new OA\Property(property: "to_country", type: "string", example: "France"),
                                new OA\Property(property: "to_city", type: "string", example: "Paris"),
                                new OA\Property(property: "is_favorite", type: "boolean", example: true),
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getFavorites(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $favorites = Favorite::with(['trip.images', 'trip.toCountry', 'trip.toCity'])
            ->where('user_id', $user->id)
            ->get()
            ->pluck('trip');

        // Optional: Transform trips data like in index() if needed
        $transformedData = $favorites->map(function ($trip) {
            if (!$trip) return null;
            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'price' => $trip->price,
                'image' => $trip->image_url,
                'to_country' => $trip->toCountry ? $trip->toCountry->name : null,
                'to_city' => $trip->toCity ? $trip->toCity->name : null,
                'is_favorite' => true,
            ];
        })->filter();

        return $this->apiResponse(false, __('Favorites retrieved successfully'), $transformedData);
    }

    /**
     * Get booking details.
     */
    #[OA\Get(
        path: "/api/v1/bookings/{id}",
        summary: "Get booking details",
        operationId: "getBookingDetails",
        description: "Retrieve comprehensive details of a specific booking for the authenticated user.",
        tags: ["Trips"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Booking ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Booking details retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Booking details retrieved successfully"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "id", type: "integer", example: 101),
                            new OA\Property(property: "tickets_count", type: "integer", example: 3),
                            new OA\Property(property: "total_price", type: "number", example: 600.00),
                            new OA\Property(property: "status", type: "string", example: "pending"),
                            new OA\Property(property: "booking_date", type: "string", format: "date", example: "2024-05-20"),
                            new OA\Property(property: "notes", type: "string", example: "Some special requests"),
                            new OA\Property(property: "trip", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "title", type: "string", example: "Amazing Paris"),
                                new OA\Property(property: "base_price", type: "number", example: 500.00),
                                new OA\Property(property: "base_capacity", type: "integer", example: 2),
                                new OA\Property(property: "extra_passenger_price", type: "number", example: 100.00),
                                new OA\Property(property: "image", type: "string", example: "http://example.com/trips/1.jpg"),
                                new OA\Property(property: "location", type: "string", example: "France, Paris"),
                            ]),
                            new OA\Property(property: "passengers", type: "array", items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                                    new OA\Property(property: "phone", type: "string", example: "+123456789"),
                                    new OA\Property(property: "passport_number", type: "string", example: "A1234567"),
                                    new OA\Property(property: "nationality", type: "string", example: "USA"),
                                ]
                            ))
                        ])
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Booking not found"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function bookingDetails($id): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $booking = TripBooking::with(['trip.toCountry', 'trip.toCity', 'trip.images', 'passengers'])
            ->where('user_id', $user->id)
            ->find($id);

        if (!$booking) {
            return $this->apiResponse(true, __('Booking not found'), null, null, 404);
        }

        $data = [
            'id' => $booking->id,
            'tickets_count' => $booking->tickets_count,
            'total_price' => $booking->total_price,
            'status' => $booking->status,
            'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
            'notes' => $booking->notes,
            'trip' => $booking->trip ? [
                'id' => $booking->trip->id,
                'title' => $booking->trip->title,
                'base_price' => $booking->trip->price,
                'base_capacity' => $booking->trip->base_capacity ?? 2,
                'extra_passenger_price' => $booking->trip->extra_passenger_price ?? 0,
                'image' => $booking->trip->image_url,
                'location' => ($booking->trip->toCountry ? $booking->trip->toCountry->name : '') .
                              ($booking->trip->toCity ? ', ' . $booking->trip->toCity->name : ''),
            ] : null,
            'passengers' => $booking->passengers->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'phone' => $p->phone,
                    'passport_number' => $p->passport_number,
                    'passport_expiry' => $p->passport_expiry ? $p->passport_expiry->format('Y-m-d') : null,
                    'nationality' => $p->nationality,
                ];
            }),
        ];

        return $this->apiResponse(false, __('Booking details retrieved successfully'), $data);
    }
}
