<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use App\Models\HotelBookingHistory;
use App\Services\TBOHotelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HotelBookingController extends Controller
{
    protected TBOHotelService $tboService;

    public function __construct(TBOHotelService $tboService)
    {
        $this->tboService = $tboService;
    }

    /**
     * Display a listing of all hotel bookings.
     */
    public function index(Request $request)
    {
        $query = HotelBooking::with(['user', 'payment'])
            ->when($request->search, function ($q, $s) {
                $q->where('hotel_name', 'like', "%{$s}%")
                  ->orWhere('tbo_booking_id', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('full_name', 'like', "%{$s}%"));
            })
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->date_from, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to,   fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest();

        // Export CSV
        if ($request->export === 'csv') {
            return $this->exportCsv($query->get());
        }

        $bookings = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => HotelBooking::count(),
            'confirmed' => HotelBooking::where('status', 'confirmed')->count(),
            'pending'   => HotelBooking::whereIn('status', ['draft', 'pending'])->count(),
            'cancelled' => HotelBooking::where('status', 'cancelled')->count(),
            'revenue'   => HotelBooking::where('status', 'confirmed')->sum('total_price'),
        ];

        return view('admin.hotel-bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Show a single booking detail.
     */
    public function show(int $id)
    {
        $booking = HotelBooking::with(['user', 'guests', 'payment', 'histories.user'])
            ->findOrFail($id);

        return view('admin.hotel-bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking from admin panel (proxies to TBO).
     */
    public function cancel(Request $request, int $id)
    {
        $booking = HotelBooking::findOrFail($id);

        if ($booking->status === HotelBooking::STATUS_CANCELLED) {
            return redirect()->back()->with('error', 'الحجز ملغى بالفعل.');
        }

        try {
            if ($booking->tbo_booking_id) {
                $this->tboService->cancelBooking($booking->tbo_booking_id);
            }

            $booking->update([
                'status'              => HotelBooking::STATUS_CANCELLED,
                'booking_state'       => HotelBooking::STATE_CANCELLED,
                'cancellation_reason' => $request->reason ?? 'إلغاء إداري',
            ]);

            HotelBookingHistory::create([
                'hotel_booking_id' => $booking->id,
                'user_id'          => auth()->id(),
                'action'           => 'admin_cancellation',
                'description'      => 'تم إلغاء الحجز من قِبل الإدارة. السبب: ' . ($request->reason ?? 'غير محدد'),
                'previous_state'   => $booking->booking_state,
                'new_state'        => HotelBooking::STATE_CANCELLED,
            ]);

            return redirect()->back()->with('success', 'تم إلغاء الحجز بنجاح.');

        } catch (\Exception $e) {
            Log::error("Admin Hotel Cancel Error [{$id}]: " . $e->getMessage());
            return redirect()->back()->with('error', 'فشل الإلغاء: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $booking = HotelBooking::with(['payment', 'payments'])->findOrFail($id);

            // 1. Check if the booking itself is confirmed
            if ($booking->status === HotelBooking::STATUS_CONFIRMED) {
                return redirect()->back()->with('error', __('Cannot delete a confirmed hotel booking. Please cancel it first if allowed.'));
            }

            // 2. Check for successful or active payments
            $hasSuccessPayment = $booking->payments->whereIn('status', ['paid', 'approved', 'completed'])->count() > 0;
            if ($hasSuccessPayment) {
                return redirect()->back()->with('error', __('Cannot delete booking with successful or approved payments.'));
            }

            // Perform deletion
            // We use standard delete as SoftDeletes is enabled on the model

            // Delete related data if needed (optional since soft delete)
            // But if we want to clean up:
            $booking->guests()->delete();
            $booking->histories()->delete();
            $booking->delete();

            return redirect()->route('admin.hotel-bookings.index')->with('success', __('Hotel booking deleted successfully.'));

        } catch (\Exception $e) {
            Log::error("Hotel Booking Delete Error [{$id}]: " . $e->getMessage());
            return redirect()->back()->with('error', __('Failed to delete booking: ') . $e->getMessage());
        }
    }

    /**
     * Remote TBO Booking Lookup/Reconciliation.
     */
    public function remoteBookings(Request $request)
    {
        $fromDate = $request->get('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate   = $request->get('to_date',   now()->format('Y-m-d'));

        $remoteBookings = [];
        $error = null;

        if ($request->has(['from_date', 'to_date'])) {
            try {
                $response = $this->tboService->getBookingDetailsByDate($fromDate, $toDate);
                $remoteBookings = $response['BookingDetail'] ?? $response ?? [];
                
                // If it's a single object instead of array (TBO sometimes does this)
                if (isset($remoteBookings['BookingId'])) {
                    $remoteBookings = [$remoteBookings];
                }
            } catch (\Exception $e) {
                Log::error("TBO Remote Lookup Error: " . $e->getMessage());
                $error = $e->getMessage();
            }
        }

        // Cross-reference with local database
        $localRefs = HotelBooking::whereIn('tbo_booking_id', array_column($remoteBookings, 'BookingId'))
            ->pluck('id', 'tbo_booking_id')
            ->toArray();

        return view('admin.hotel-bookings.remote', compact('remoteBookings', 'fromDate', 'toDate', 'localRefs', 'error'));
    }

    /**
     * Export bookings as CSV.
     */
    protected function exportCsv($bookings)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="hotel_bookings_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM for Arabic support in Excel

            fputcsv($handle, [
                'ID', 'رقم TBO', 'العميل', 'الفندق', 'المدينة',
                'تسجيل الوصول', 'تسجيل المغادرة', 'الإجمالي', 'الحالة', 'التاريخ'
            ]);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->id,
                    $b->tbo_booking_id ?? '-',
                    $b->user?->full_name ?? '-',
                    $b->hotel_name,
                    $b->city_name,
                    $b->check_in_date?->format('Y-m-d'),
                    $b->check_out_date?->format('Y-m-d'),
                    $b->total_price . ' ' . $b->currency,
                    $b->status,
                    $b->created_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
