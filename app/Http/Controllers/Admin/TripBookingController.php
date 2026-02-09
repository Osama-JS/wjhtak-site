<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => TripBooking::count(),
            'confirmed' => TripBooking::where('status', 'confirmed')->count(),
            'pending' => TripBooking::where('status', 'pending')->count(),
            'cancelled' => TripBooking::where('status', 'cancelled')->count(),
        ];
        return view('admin.trip_bookings.index', compact('stats'));
    }

    /**
     * Get data for DataTables
     */
    public function getData()
    {
        try {
            $bookings = TripBooking::with(['user', 'trip'])->latest()->get();

            $data = $bookings->map(function ($booking) {
                $statusBadge = '<span class="badge badge-warning">' . __('Pending') . '</span>';
                if ($booking->status == 'confirmed') {
                    $statusBadge = '<span class="badge badge-success">' . __('Confirmed') . '</span>';
                } elseif ($booking->status == 'cancelled') {
                    $statusBadge = '<span class="badge badge-danger">' . __('Cancelled') . '</span>';
                }

                return [
                    'id' => $booking->id,
                    'user' => $booking->user ? $booking->user->full_name . '<br><small class="text-muted">' . $booking->user->phone . '</small>' : __('Guest'),
                    'trip' => $booking->trip ? $booking->trip->title . '<br><small class="text-muted">' . $booking->booking_date->format('Y-m-d') . '</small>' : __('Deleted Trip'),
                    'price' => number_format($booking->total_price, 2) . ' ' . __('SAR'),
                    'tickets' => '<span class="badge badge-info">' . $booking->tickets_count . '</span>',
                    'status' => $statusBadge,
                    'created_at' => $booking->created_at->format('Y-m-d H:i'),
                    'actions' => $this->getActionButtons($booking),
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getActionButtons($booking)
    {
        $showBtn = '<a href="' . route('admin.trip-bookings.show', $booking->id) . '" class="btn btn-primary btn-sm me-1" title="' . __('View Details') . '"><i class="fas fa-eye"></i></a>';

        // Status Buttons
        $statusBtns = '';
        if ($booking->status == 'pending') {
            $statusBtns .= '<form action="' . route('admin.trip-bookings.update-status', $booking->id) . '" method="POST" class="d-inline confirm-action" data-confirm-message="' . __('Are you sure you want to confirm this booking?') . '">
                ' . csrf_field() . '
                <input type="hidden" name="status" value="confirmed">
                <button type="submit" class="btn btn-success btn-sm me-1" title="' . __('Confirm') . '"><i class="fas fa-check"></i></button>
            </form>';

            $statusBtns .= '<form action="' . route('admin.trip-bookings.update-status', $booking->id) . '" method="POST" class="d-inline confirm-action" data-confirm-message="' . __('Are you sure you want to cancel this booking?') . '">
            ' . csrf_field() . '
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="btn btn-danger btn-sm me-1" title="' . __('Cancel') . '"><i class="fas fa-times"></i></button>
            </form>';
        }

        $deleteBtn = '<form action="' . route('admin.trip-bookings.destroy', $booking->id) . '" method="POST" class="d-inline confirm-action" data-confirm-message="' . __('Are you sure you want to delete this booking?') . '">
            ' . csrf_field() . '
            ' . method_field('DELETE') . '
            <button type="submit" class="btn btn-danger btn-sm" title="' . __('Delete') . '"><i class="fas fa-trash"></i></button>
        </form>';

        return '<div class="d-flex">' . $showBtn . $statusBtns . $deleteBtn . '</div>';
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $booking = TripBooking::with(['user', 'trip', 'passengers'])->findOrFail($id);
        return view('admin.trip_bookings.show', compact('booking'));
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = TripBooking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return redirect()->back()->with('success', __('Booking status updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $booking = TripBooking::findOrFail($id);
        $booking->passengers()->delete();
        $booking->delete();

        return redirect()->route('admin.trip-bookings.index')->with('success', __('Booking deleted successfully.'));
    }
}
