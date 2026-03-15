<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'users_total' => \App\Models\User::count(),
            'users_new_today' => \App\Models\User::whereDate('created_at', today())->count(),
            'trips_active' => \App\Models\Trip::active()->count(),
            'trips_expired' => \App\Models\Trip::expired()->count(),
            'bookings_total' => \App\Models\TripBooking::count(),
            'bookings_pending' => \App\Models\TripBooking::where('booking_state', \App\Models\TripBooking::STATE_AWAITING_PAYMENT)->count(),
            'revenue_total' => \App\Models\TripBooking::where('booking_state', \App\Models\TripBooking::STATE_COMPLETED)->sum('total_price'),
            'companies_count' => \App\Models\Company::count(),
        ];

        $greeting = $this->getGreeting();
        $adminName = auth()->user()->name;

        // Chart Data: Monthly Users (Current Year)
        $monthlyData = \App\Models\User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartLabels = [];
        $chartData = [];
        $months = [
            1 => __('Jan'), 2 => __('Feb'), 3 => __('Mar'), 4 => __('Apr'),
            5 => __('May'), 6 => __('Jun'), 7 => __('Jul'), 8 => __('Aug'),
            9 => __('Sep'), 10 => __('Oct'), 11 => __('Nov'), 12 => __('Dec')
        ];

        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = $months[$i];
            $found = $monthlyData->where('month', $i)->first();
            $chartData[] = $found ? $found->count : 0;
        }

        $latestUsers = \App\Models\User::latest()->take(5)->get();
        $recentBookings = \App\Models\TripBooking::with(['user', 'trip'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'chartLabels', 'chartData', 'latestUsers', 'greeting', 'adminName', 'recentBookings'));
    }

    private function getGreeting()
    {
        $hour = date('H');
        if ($hour < 12) {
            return __('Good Morning');
        } elseif ($hour < 17) {
            return __('Good Afternoon');
        } else {
            return __('Good Evening');
        }
    }
}
