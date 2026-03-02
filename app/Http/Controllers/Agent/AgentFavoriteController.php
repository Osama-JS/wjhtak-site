<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentFavoriteController extends Controller
{
    /**
     * Display all agent favorites.
     */
    public function index()
    {
        $favorites = Favorite::with(['trip.images', 'trip.toCountry', 'trip.toCity', 'trip.company'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('frontend.agent.favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite (AJAX).
     * Reusing same logic as customer for consistency.
     */
    public function toggle($tripId)
    {
        $trip = Trip::find($tripId);
        if (!$trip) {
            return response()->json(['error' => true, 'message' => __('Trip not found.')], 404);
        }

        $favorite = Favorite::where('user_id', Auth::id())->where('trip_id', $tripId)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['error' => false, 'message' => __('Removed from favorites.'), 'is_favorite' => false]);
        }

        Favorite::create(['user_id' => Auth::id(), 'trip_id' => $tripId]);
        return response()->json(['error' => false, 'message' => __('Added to favorites.'), 'is_favorite' => true]);
    }
}
