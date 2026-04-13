<?php

namespace App\Http\Controllers;

use App\Services\TBOHotelService;
use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HotelController extends Controller
{
    protected $tboService;

    public function __construct(TBOHotelService $tboService)
    {
        $this->tboService = $tboService;
    }

    /**
     * Display the hotel listing page.
     */
    public function index(Request $request)
    {
        if (\App\Models\Setting::get('show_hotels_page', '1') == '0') {
            return redirect()->route('home');
        }
        try {
            $hotels = [];
            $sessionId = null;

            // If a search was performed (city_code is present)
            if ($request->filled('city_code')) {
                // Find city by city_code (the numeric TBO code)
                $city = City::where('city_code', $request->city_code)->first();

                $criteria = [
                    'city_code'     => $city ? $city->city_code : $request->city_code,
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

            // Get countries for the filter dropdown
            $countries = Country::active()->orderBy('name')->get();

            // Get cities based on selected country
            $citiesQuery = City::active();
            if ($request->filled('country_iso')) {
                $country = Country::where('iso', $request->country_iso)->first();
                if ($country) {
                    $citiesQuery->where('country_id', $country->id);
                }
            }
            
            $cities = $citiesQuery->orderBy('title')->get();

            return view('frontend.hotels.index', [
                'hotels'    => $hotels,
                'sessionId' => $sessionId,
                'cities'    => $cities,
                'countries' => $countries,
                'filters'   => $request->all()
            ]);

        } catch (\Exception $e) {
            Log::error('Hotel Controller Index Error: ' . $e->getMessage());
            return view('frontend.hotels.index', [
                'hotels'    => [],
                'sessionId' => null,
                'cities'    => [],
                'countries' => [],
                'error'     => __('Failed to fetch hotels. Please try again.')
            ]);
        }
    }

    /**
     * Display hotel details.
     */
    public function show(Request $request, string $hotelCode)
    {
        if (\App\Models\Setting::get('show_hotels_page', '1') == '0') {
            return redirect()->route('home');
        }
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
