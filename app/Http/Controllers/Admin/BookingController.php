<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TraveloproService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $traveloproService;

    public function __construct(TraveloproService $traveloproService)
    {
        $this->traveloproService = $traveloproService;
    }

    // Flights
    public function availableFlights()
    {
        return view('admin.bookings.flights.available');
    }

    /**
     * Search for flights via AJAX/POST
     */
    public function searchFlights(Request $request)
    {
        try {
            $results = $this->traveloproService->searchFlights($request->all());

            if (isset($results['status']) && $results['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $results['message']], 500);
            }

            return response()->json(['error' => false, 'data' => $results]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Search Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while searching for flights.')], 500);
        }
    }

    /**
     * Validate the selected flight fare
     */
    public function validateFare(Request $request)
    {
        try {
            $result = $this->traveloproService->validateFare($request->all());

            if (isset($result['status']) && $result['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $result['message']], 500);
            }

            // Check if IsValid is true in response
            $isValid = $result['AirRevalidateResponse']['AirRevalidateResult']['IsValid'] ?? false;
            if ($isValid !== true && $isValid !== 'true') {
                 return response()->json(['error' => true, 'message' => __('Fare is no longer valid or available.')], 422);
            }

            return response()->json(['error' => false, 'data' => $result]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Validate Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while validating the fare.')], 500);
        }
    }

    /**
     * Create actual booking (PNR)
     */
    public function createBooking(Request $request)
    {
        try {
            $result = $this->traveloproService->createBooking($request->all());

            if (isset($result['status']) && $result['status'] === 'error') {
                return response()->json(['error' => true, 'message' => $result['message']], 500);
            }

            return response()->json(['error' => false, 'data' => $result]);
        } catch (\Exception $e) {
            Log::error('Admin Flight Booking Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => __('An error occurred while creating the booking.')], 500);
        }
    }

    public function flightRequests()
    {
        return view('admin.bookings.flights.requests');
    }

    public function ongoingFlights()
    {
        return view('admin.bookings.flights.ongoing');
    }

    /**
     * Utility endpoints for UI (Airports/Airlines)
     */
    public function getAirports()
    {
        return response()->json($this->traveloproService->getAirportList());
    }

    public function getAirlines()
    {
        return response()->json($this->traveloproService->getAirlineList());
    }

    // Hotels
    public function hotelList()
    {
        return view('admin.bookings.hotels.index');
    }

    public function hotelRequests()
    {
        return view('admin.bookings.hotels.requests');
    }
}
