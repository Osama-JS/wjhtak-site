<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TraveloproService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class FlightController extends Controller
{
    protected $traveloproService;

    public function __construct(TraveloproService $traveloproService)
    {
        $this->traveloproService = $traveloproService;
    }

    #[OA\Post(
        path: "/api/flights/search",
        summary: "Search for flights",
        operationId: "searchFlights",
        description: "Search for flight availability using Travelopro API.",
        tags: ["Flights"],
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
                required: ["journeyType", "OriginDestinationInfo", "class", "adults"],
                properties: [
                    new OA\Property(property: "journeyType", type: "string", enum: ["OneWay", "Return", "Circle", "MultiCity"], example: "OneWay"),
                    new OA\Property(
                        property: "OriginDestinationInfo",
                        type: "array",
                        items: new OA\Items(
                            type: "object",
                            required: ["departureDate", "airportOriginCode", "airportDestinationCode"],
                            properties: [
                                new OA\Property(property: "departureDate", type: "string", format: "date", example: "2024-12-01"),
                                new OA\Property(property: "returnDate", type: "string", format: "date", example: "2024-12-10", description: "Required if journeyType is Return"),
                                new OA\Property(property: "airportOriginCode", type: "string", example: "DXB"),
                                new OA\Property(property: "airportDestinationCode", type: "string", example: "LHR")
                            ]
                        )
                    ),
                    new OA\Property(property: "class", type: "string", enum: ["Economy", "Business", "First", "PremiumEconomy"], example: "Economy"),
                    new OA\Property(property: "adults", type: "integer", example: 1),
                    new OA\Property(property: "childs", type: "integer", example: 0),
                    new OA\Property(property: "infants", type: "integer", example: 0),
                    new OA\Property(property: "airlineCode", type: "string", example: "", description: "Preferred airline code"),
                    new OA\Property(property: "directFlight", type: "boolean", example: false),
                    new OA\Property(property: "requiredCurrency", type: "string", example: "SAR")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful search",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Flights retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "SessionId", type: "string", example: "7906efba-09db-4481-8c60-0d7f5b5e6c44"),
                            new OA\Property(property: "AirSearchResult", type: "object", description: "Complex nested flight search results containing schedules and fares")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Service unavailable"),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'journeyType' => 'required|string|in:OneWay,Return,Circle,MultiCity',
            'OriginDestinationInfo' => 'required|array|min:1',
            'OriginDestinationInfo.*.departureDate' => 'required|date',
            'OriginDestinationInfo.*.returnDate' => 'nullable|date|after_or_equal:OriginDestinationInfo.*.departureDate',
            'OriginDestinationInfo.*.airportOriginCode' => 'required|string|size:3',
            'OriginDestinationInfo.*.airportDestinationCode' => 'required|string|size:3',
            'class' => 'required|string',
            'adults' => 'required|integer|min:1',
            'childs' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'airlineCode' => 'nullable|string',
            'directFlight' => 'nullable|boolean',
            'requiredCurrency' => 'nullable|string|size:3',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->searchFlights($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
             // Handle service specific error structure or generic error
             $message = $result['message'] ?? __('Failed to fetch flight data');
             return $this->apiResponse(true, $message, $result, null, 500);
        }

        return $this->apiResponse(false, __('Flights retrieved successfully.'), $result, null, 200);
    }

    #[OA\Get(
        path: "/api/flights/airports",
        summary: "Get list of airports",
        operationId: "getAirports",
        description: "Retrieve a list of supported airports from Travelopro.",
        tags: ["Flights"],
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
                description: "Successful retrieval",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Airports retrieved successfully."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "AirportCode", type: "string", example: "DXB"),
                                new OA\Property(property: "AirportName", type: "string", example: "Dubai International Airport"),
                                new OA\Property(property: "City", type: "string", example: "Dubai"),
                                new OA\Property(property: "Country", type: "string", example: "United Arab Emirates")
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getAirports()
    {
        $airports = $this->traveloproService->getAirportList();
        return $this->apiResponse(false, __('Airports retrieved successfully.'), $airports, null, 200);
    }

    #[OA\Get(
        path: "/api/flights/airlines",
        summary: "Get list of airlines",
        operationId: "getAirlines",
        description: "Retrieve a list of supported airlines from Travelopro.",
        tags: ["Flights"],
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
                description: "Successful retrieval",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Airlines retrieved successfully."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "AirLineCode", type: "string", example: "EK"),
                                new OA\Property(property: "AirLineName", type: "string", example: "Emirates"),
                                new OA\Property(property: "AirLineLogo", type: "string", example: "https://travelnext.works/api/airlines/EK.gif")
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function getAirlines()
    {
        $airlines = $this->traveloproService->getAirlineList();
        return $this->apiResponse(false, __('Airlines retrieved successfully.'), $airlines, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/validate-fare",
        summary: "Validate flight fare",
        operationId: "validateFare",
        description: "Verify if the selected flight fare is still available.",
        tags: ["Flights"],
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
                required: ["session_id", "fare_source_code"],
                properties: [
                    new OA\Property(property: "session_id", type: "string", example: "7906efba-09db-4481-8c60-0d7f5b5e6c44"),
                    new OA\Property(property: "fare_source_code", type: "string", example: "MTY2ODE2Njg2Ml8yNjA5Mzk"),
                    new OA\Property(property: "fare_source_code_inbound", type: "string", example: "")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Fare validated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Fare is valid."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function validateFare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'fare_source_code' => 'required|string',
            'fare_source_code_inbound' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->validateFare($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        // Check if IsValid is true in response
        $isValid = $result['AirRevalidateResponse']['AirRevalidateResult']['IsValid'] ?? false;
        if ($isValid !== true && $isValid !== 'true') {
             return $this->apiResponse(true, __('Fare is no longer valid or available.'), $result, null, 422);
        }

        return $this->apiResponse(false, __('Fare is valid.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/book",
        summary: "Create flight booking (PNR)",
        operationId: "bookFlight",
        description: "Create a PNR using passenger details.",
        tags: ["Flights"],
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
                required: ["flight_session_id", "fare_source_code", "customerEmail", "customerPhone", "passengers"],
                properties: [
                    new OA\Property(property: "flight_session_id", type: "string", example: "7906efba-09db-4481-8c60-0d7f5b5e6c44"),
                    new OA\Property(property: "fare_source_code", type: "string", example: "MTY2ODE2Njg2Ml8yNjA5Mzk"),
                    new OA\Property(property: "customerEmail", type: "string", format: "email", example: "customer@example.com"),
                    new OA\Property(property: "customerPhone", type: "string", example: "+966500000000"),
                    new OA\Property(property: "passengers", type: "array", items: new OA\Items(
                        type: "object",
                        required: ["firstName", "lastName", "dateOfBirth", "passportNumber"],
                        properties: [
                            new OA\Property(property: "title", type: "string", example: "Mr"),
                            new OA\Property(property: "firstName", type: "string", example: "John"),
                            new OA\Property(property: "lastName", type: "string", example: "Doe"),
                            new OA\Property(property: "dateOfBirth", type: "string", format: "date", example: "1990-01-01"),
                            new OA\Property(property: "passportNumber", type: "string", example: "A1234567"),
                            new OA\Property(property: "passportExpiry", type: "string", format: "date", example: "2030-01-01"),
                        ]
                    ))
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Booking created",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Booking created successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function book(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'flight_session_id' => 'required|string',
            'fare_source_code' => 'required|string',
            'customerEmail' => 'required|email',
            'customerPhone' => 'required|string',
            'passengers' => 'required|array|min:1',
            // Detailed validation for passengers can be added here
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->createBooking($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        return $this->apiResponse(false, __('Booking created successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/order-ticket",
        summary: "Order ticket",
        operationId: "orderTicket",
        description: "Issue ticket for a confirmed booking.",
        tags: ["Flights"],
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
                required: ["uniqueId"],
                properties: [
                    new OA\Property(property: "uniqueId", type: "string", example: "TR123456")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Ticket ordered",
                 content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ticket ordered successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function orderTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->orderTicket($request->uniqueId);

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        return $this->apiResponse(false, __('Ticket ordered successfully.'), $result, null, 200);
    }

    #[OA\Post(
        path: "/api/flights/trip-details",
        summary: "Get trip details",
        operationId: "getTripDetails",
        description: "Get full details of a trip including ticket numbers.",
        tags: ["Flights"],
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
                required: ["uniqueId"],
                properties: [
                     new OA\Property(property: "uniqueId", type: "string", example: "TR123456")
                ]
            )
        ),
        responses: [
             new OA\Response(
                response: 200,
                description: "Trip details retrieved",
                 content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Trip details retrieved successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function getTripDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->getTripDetails($request->uniqueId);

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->apiResponse(true, $result['message'], $result['details'] ?? $result['error'], null, 500);
        }

        return $this->apiResponse(false, __('Trip details retrieved successfully.'), $result, null, 200);
    }
}
