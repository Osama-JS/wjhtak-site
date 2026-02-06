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
            'users' => \App\Models\User::count(),
            'countries' => \App\Models\Country::count(),
            'cities' => \App\Models\City::count(),
            'banners' => \App\Models\Banner::count(),
            'companies' => \App\Models\Company::count(),
            'company_codes' => \App\Models\Company_Codes::count(),
            'hotel_requests' => 0, // Placeholder if no model found yet
            'flight_requests' => 0, // Placeholder
        ];

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

        return view('admin.dashboard', compact('stats','chartLabels', 'chartData', 'latestUsers'));
    }
}
