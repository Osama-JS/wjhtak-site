<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Country;
use App\Models\City;
use App\Models\Banner;
use App\Models\Question;

class FrontendController extends Controller
{
    /**
     * Display the homepage.
     */
    public function home()
    {
        // Get active countries with trip counts
        $countries = Country::active()
            ->withCount(['trips' => function ($query) {
                $query->active();
            }])
            ->orderByDesc('trips_count')
            ->take(10)
            ->get();

        // Get featured destinations (top 4 countries by trips)
        $destinations = $countries->take(4);

        // Get featured trips (ads first, then by rating)
        $featuredTrips = Trip::active()
            ->with(['fromCountry', 'toCountry', 'images'])
            ->withAvg('rates', 'rate')
            ->orderByDesc('is_ad')
            ->orderByDesc('rates_avg_rate')
            ->take(6)
            ->get();

        // Get active banners
        $banners = Banner::active()
            ->with('trip')
            ->get();

        // Stats
        $stats = [
            'trips' => Trip::active()->count(),
            'destinations' => Country::active()->count(),
            'customers' => 10000, // You can get this from users table
            'rating' => 4.9,
        ];

        return view('frontend.home', compact(
            'countries',
            'destinations',
            'featuredTrips',
            'banners',
            'stats'
        ));
    }

    /**
     * Display the trips listing page.
     */
    public function trips(Request $request)
    {
        $query = Trip::active()
            ->with(['fromCountry', 'toCountry', 'images', 'company']);

        // Filter by country
        if ($request->filled('country')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_country_id', $request->country)
                  ->orWhere('to_country_id', $request->country);
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->withAvg('rates', 'rate')->orderByDesc('rates_avg_rate');
                break;
            case 'popular':
                $query->withCount('pageVisits')->orderByDesc('page_visits_count');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $trips = $query->paginate(12);

        // Get countries for filter
        $countries = Country::active()
            ->orderBy('nicename')
            ->get();

        return view('frontend.trips.index', compact('trips', 'countries'));
    }

    /**
     * Display a single trip.
     */
    public function tripShow($id)
    {
        $trip = Trip::active()
            ->with(['fromCountry', 'toCountry', 'fromCity', 'images', 'rates.user', 'company', 'itineraries'])
            ->findOrFail($id);

        // Increment page visits
        $trip->increment('page_visits');

        // Get related trips
        $relatedTrips = Trip::active()
            ->where('id', '!=', $trip->id)
            ->where(function ($query) use ($trip) {
                $query->where('to_country_id', $trip->to_country_id)
                      ->orWhere('from_country_id', $trip->from_country_id);
            })
            ->with(['fromCountry', 'toCountry', 'images'])
            ->take(3)
            ->get();

        return view('frontend.trips.show', compact('trip', 'relatedTrips'));
    }

    /**
     * Display the destinations page.
     */
    public function destinations()
    {
        // Get all active countries with trip counts
        $countries = Country::active()
            ->withCount(['trips' => function ($query) {
                $query->active();
            }])
            ->orderBy('nicename')
            ->get();

        // Featured countries (top 4 by trips)
        $featuredCountries = $countries->sortByDesc('trips_count')->take(4);

        return view('frontend.destinations', compact('countries', 'featuredCountries'));
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('frontend.about');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return view('frontend.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // TODO: Send email or save to database

        return back()->with('success', __('Thank you for your message. We will get back to you soon!'));
    }

    /**
     * Display the FAQ page.
     */
    public function faq()
    {
        $questions = Question::all();

        return view('frontend.faq', compact('questions'));
    }

    /**
     * Display search results.
     */
    public function search(Request $request)
    {
        $trips = null;

        if ($request->filled('q')) {
            $searchTerm = $request->q;

            $trips = Trip::active()
                ->where(function ($query) use ($searchTerm) {
                    $query->where('title', 'like', "%{$searchTerm}%")
                          ->orWhere('description', 'like', "%{$searchTerm}%");
                })
                ->with(['fromCountry', 'toCountry', 'images'])
                ->paginate(12);
        }

        return view('frontend.search', compact('trips'));
    }
}
