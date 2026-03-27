<?php

namespace App\Http\Controllers;

use App\Services\TBOHotelService;
use App\Services\MockTBOHotelService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HotelController extends Controller
{
    protected $tboService;

    public function __construct()
    {
        if (env('APP_API_MODE') === 'mock') {
            $this->tboService = new MockTBOHotelService();
        } else {
            $this->tboService = app(TBOHotelService::class);
        }
    }

    /**
     * Display the hotel listing page.
     */
    public function index(Request $request)
    {
        try {
            $hotels = [];
            $sessionId = null;

            // If a search was performed (city_code is present)
            if ($request->filled('city_code')) {
                $criteria = [
                    'city_code'     => $request->city_code,
                    'check_in'      => $request->get('check_in', Carbon::now()->addDays(7)->toDateString()),
                    'check_out'     => $request->get('check_out', Carbon::now()->addDays(10)->toDateString()),
                    'adults'        => $request->get('adults', 2),
                    'rooms'         => $request->get('rooms', 1),
                    'children'      => $request->get('children', 0),
                    'children_ages' => $request->get('children_ages', []),
                    'nationality'   => $request->get('nationality', 'SA'),
                    'currency'      => $request->get('currency', 'SAR'),
                ];

                $result = $this->tboService->searchHotels($criteria);
                $hotels = $result['hotels'] ?? [];
                $sessionId = $result['session_id'] ?? null;
            }

            // Get cities for the search dropdown
            $cities = $this->tboService->getCityList();
            // Limit for performance if needed, or handle via AJAX search
            $topCities = array_slice($cities, 0, 50);

            return view('frontend.hotels.index', [
                'hotels'    => $hotels,
                'sessionId' => $sessionId,
                'cities'    => $topCities,
                'filters'   => $request->all()
            ]);

        } catch (\Exception $e) {
            Log::error('Hotel Controller Index Error: ' . $e->getMessage());
            return view('frontend.hotels.index', [
                'hotels'    => [],
                'sessionId' => null,
                'cities'    => [],
                'error'     => __('Failed to fetch hotels. Please try again.')
            ]);
        }
    }

    /**
     * Display hotel details.
     */
    public function show(Request $request, string $hotelCode)
    {
        try {
            $sessionId = $request->get('session_id');
            
            // Get Hotel Static Info
            $hotelInfo = $this->tboService->getHotelInfo($hotelCode);
            
            // Get Rooms (Rates) if sessionId is present
            $rooms = [];
            if ($sessionId) {
                $roomResult = $this->tboService->getRoomList($sessionId, $hotelCode);
                $rooms = $roomResult['rooms'] ?? [];
            }

            return view('frontend.hotels.show', [
                'hotel'     => $hotelInfo,
                'rooms'     => $rooms,
                'sessionId' => $sessionId,
                'hotelCode' => $hotelCode
            ]);

        } catch (\Exception $e) {
            Log::error("Hotel Controller Show Error [{$hotelCode}]: " . $e->getMessage());
            return abort(404, __('Hotel details not found.'));
        }
    }
}
