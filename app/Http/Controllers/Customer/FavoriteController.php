<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display all favorites.
     */
    public function index()
    {
        $favorites = Favorite::with(['trip.images', 'trip.toCountry', 'trip.toCity'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('frontend.customer.favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite (AJAX).
     */
    public function toggle($tripId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => true, 'message' => __('يجب تسجيل الدخول أولاً.')], 401);
        }

        $trip = Trip::find($tripId);
        if (!$trip) {
            return response()->json(['error' => true, 'message' => __('الرحلة غير موجودة.')], 404);
        }

        $favorite = Favorite::where('user_id', Auth::id())->where('trip_id', $tripId)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['error' => false, 'message' => __('تم الإزالة من المفضلة.'), 'is_favorite' => false]);
        }

        Favorite::create(['user_id' => Auth::id(), 'trip_id' => $tripId]);
        return response()->json(['error' => false, 'message' => __('تمت الإضافة للمفضلة.'), 'is_favorite' => true]);
    }
}
