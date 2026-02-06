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
        $stats = [
            'total_routes' => 245,
            'airlines' => 15,
            'today_searches' => 120
        ];
        return view('admin.bookings.flights.available', compact('stats'));
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
        $stats = [
            'total' => 156, // Mock data
            'pending' => 42,
            'confirmed' => 98,
            'cancelled' => 16
        ];
        return view('admin.bookings.flights.requests', compact('stats'));
    }

    public function ongoingFlights()
    {
        $stats = [
            'active_flights' => 12,
            'in_air' => 8,
            'on_ground' => 4,
            'delayed' => 1
        ];
        return view('admin.bookings.flights.ongoing', compact('stats'));
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
        $stats = [
            'total_hotels' => 45,
            'featured' => 12,
            'top_rated' => 8
        ];
        return view('admin.bookings.hotels.index', compact('stats'));
    }

    public function hotelRequests()
    {
        $stats = [
            'total' => 89,
            'pending' => 12,
            'confirmed' => 65,
            'cancelled' => 12
        ];
        return view('admin.bookings.hotels.requests', compact('stats'));
    }
}
