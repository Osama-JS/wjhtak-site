<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\Favorite;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Booking stats
        $totalBookings     = TripBooking::where('user_id', $user->id)->count();
        $pendingBookings   = TripBooking::where('user_id', $user->id)->where('status', 'pending')->count();
        $confirmedBookings = TripBooking::where('user_id', $user->id)->where('status', 'confirmed')->count();
        $cancelledBookings = TripBooking::where('user_id', $user->id)->where('status', 'cancelled')->count();

        // Upcoming bookings (closest 3 confirmed)
        $upcomingBookings = TripBooking::with(['trip.images'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->limit(3)
            ->get();

        // Favorites count
        $favoritesCount = Favorite::where('user_id', $user->id)->count();

        // Recent payments
        $recentPayments = Payment::whereHas('booking', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('booking.trip')
            ->latest()
            ->limit(3)
            ->get();

        return view('frontend.customer.dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'cancelledBookings',
            'upcomingBookings',
            'favoritesCount',
            'recentPayments'
        ));
    }
}
