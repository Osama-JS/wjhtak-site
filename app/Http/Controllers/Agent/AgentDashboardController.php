<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripBooking;
use Illuminate\Support\Facades\Auth;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('home')->with('error', __('You are not linked to any company.'));
        }

        // Stats for the company's trips
        $totalTrips = Trip::where('company_id', $company->id)->count();

        $bookingsQuery = TripBooking::whereHas('trip', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        });

        $totalBookings     = (clone $bookingsQuery)->count();
        $pendingBookings   = (clone $bookingsQuery)->where('status', 'pending')->count();
        $confirmedBookings = (clone $bookingsQuery)->where('status', 'confirmed')->count();
        $cancelledBookings = (clone $bookingsQuery)->where('status', 'cancelled')->count();

        // New Detailed Stats
        $confirmedQuery = (clone $bookingsQuery)->where('status', 'confirmed');
        $totalEarnings   = $confirmedQuery->sum('total_price');
        $totalPassengers = $confirmedQuery->sum('tickets_count');

        $tripsQuery = Trip::where('company_id', $company->id);
        $activeTrips = (clone $tripsQuery)->where('active', true)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now()->toDateString());
            })->count();

        $completedTrips = (clone $tripsQuery)->where('expiry_date', '<', now()->toDateString())->count();

        // ─── Chart Data ───

        // 1. Revenue Growth (Last 6 Months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $monthName = $monthStart->translatedFormat('M Y');

            $revenue = (clone $confirmedQuery)
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->sum('total_price');

            $monthlyRevenue[] = [
                'label' => $monthName,
                'value' => (float) $revenue
            ];
        }

        // 2. Booking Status Distribution
        $statusDistribution = [
            'confirmed' => $confirmedBookings,
            'pending'   => $pendingBookings,
            'cancelled' => $cancelledBookings,
        ];

        // 3. Top 5 Trips by Bookings
        $topTrips = (clone $bookingsQuery)
            ->where('status', 'confirmed')
            ->selectRaw('trip_id, count(*) as count')
            ->groupBy('trip_id')
            ->orderByDesc('count')
            ->with('trip:id,title')
            ->limit(5)
            ->get()
            ->map(function($b) {
                return [
                    'label' => $b->trip->title ?? __('Unknown'),
                    'value' => $b->count
                ];
            });

        // Latest bookings for the company
        $latestBookings = TripBooking::with(['trip.images', 'user'])
            ->whereHas('trip', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('frontend.agent.dashboard', compact(
            'totalTrips',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'cancelledBookings',
            'totalEarnings',
            'totalPassengers',
            'activeTrips',
            'completedTrips',
            'monthlyRevenue',
            'statusDistribution',
            'topTrips',
            'latestBookings'
        ));
    }
}
