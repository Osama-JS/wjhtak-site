<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EarningsReportController extends Controller
{
    public function index(Request $request)
    {
        $query = TripBooking::where('booking_state', TripBooking::STATE_COMPLETED);

        // Filters
        if ($request->filled('company_id')) {
            $query->whereHas('trip', function($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Stats calculation
        $allCompleted = $query->with(['trip.company'])->get();
        $totalEarnings = 0;
        $totalBookingsCount = $allCompleted->count();
        $companyStats = [];
        $monthlyEarnings = [];

        foreach ($allCompleted as $booking) {
            $rate = $booking->trip?->company?->commission_rate ?? 0;
            $profit = ($booking->total_price * $rate / 100);
            $totalEarnings += $profit;

            // Company Distribution
            $cName = $booking->trip?->company?->name ?? __('Unknown');
            $companyStats[$cName] = ($companyStats[$cName] ?? 0) + $profit;

            // Monthly Trend (Last 6 months)
            $month = $booking->created_at->format('Y-m');
            $monthlyEarnings[$month] = ($monthlyEarnings[$month] ?? 0) + $profit;
        }

        // Sort months and take last 6
        ksort($monthlyEarnings);
        $monthlyEarnings = array_slice($monthlyEarnings, -6, 6, true);

        // Growth Calculation (Current Month vs Previous Month)
        $currentMonth = now()->format('Y-m');
        $prevMonth = now()->subMonth()->format('Y-m');
        $currentMonthEarnings = $monthlyEarnings[$currentMonth] ?? 0;
        $prevMonthEarnings = $monthlyEarnings[$prevMonth] ?? 0;

        $growth = 0;
        if ($prevMonthEarnings > 0) {
            $growth = (($currentMonthEarnings - $prevMonthEarnings) / $prevMonthEarnings) * 100;
        }

        $companies = Company::active()->get();

        return view('admin.earnings.index', compact(
            'companies',
            'totalEarnings',
            'totalBookingsCount',
            'companyStats',
            'monthlyEarnings',
            'growth',
            'currentMonthEarnings'
        ));
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        try {
            $query = TripBooking::with(['trip.company', 'user'])
                ->where('booking_state', TripBooking::STATE_COMPLETED);

            // Filters
            if ($request->filled('company_id')) {
                $query->whereHas('trip', function($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $bookings = $query->latest()->get();

            $data = $bookings->map(function ($booking) {
                $rate = $booking->trip?->company?->commission_rate ?? 0;
                $profit = ($booking->total_price * $rate / 100);

                return [
                    'id' => $booking->id,
                    'trip' => $booking->trip ? $booking->trip->title : __('Deleted Trip'),
                    'company' => $booking->trip?->company?->name ?? __('Unknown'),
                    'total_price' => number_format($booking->total_price, 2) . ' ' . __('SAR'),
                    'commission_rate' => $rate . '%',
                    'profit' => '<span class="text-success font-w600">' . number_format($profit, 2) . ' ' . __('SAR') . '</span>',
                    'date' => $booking->created_at->format('Y-m-d'),
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
