<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Notifications\TicketUploadedNotification;

class TripBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => TripBooking::count(),
            'awaiting_payment' => TripBooking::where('booking_state', TripBooking::STATE_AWAITING_PAYMENT)->count(),
            'preparing' => TripBooking::where('booking_state', TripBooking::STATE_PREPARING)->count(),
            'cancelled' => TripBooking::where('booking_state', TripBooking::STATE_CANCELLED)->count(),
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
                $stateLabels = [
                    TripBooking::STATE_AWAITING_PAYMENT => ['label' => __('Awaiting Payment'), 'class' => 'warning'],
                    TripBooking::STATE_PREPARING => ['label' => __('Preparing'), 'class' => 'info'],
                    TripBooking::STATE_ISSUING_TICKETS => ['label' => __('Issuing Tickets'), 'class' => 'primary'],
                    TripBooking::STATE_TICKETS_UPLOADED => ['label' => __('Tickets Uploaded'), 'class' => 'success'],
                    TripBooking::STATE_COMPLETED => ['label' => __('Completed'), 'class' => 'dark'],
                    TripBooking::STATE_CANCELLED => ['label' => __('Cancelled'), 'class' => 'danger'],
                ];

                $state = $booking->booking_state ?? TripBooking::STATE_AWAITING_PAYMENT;
                $stateData = $stateLabels[$state] ?? ['label' => __($state), 'class' => 'secondary'];

                $statusBadge = '<span class="badge badge-'. $stateData['class'] .'">' . $stateData['label'] . '</span>';

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
        $showBtn = '';
        if (auth()->user()->can('view bookings')) {
            $showBtn = '<a href="' . route('admin.trip-bookings.show', $booking->id) . '" class="btn btn-primary btn-sm me-1" title="' . __('View Details') . '"><i class="fas fa-eye"></i></a>';
        }

        // Status Buttons
        $statusBtns = '';
        if (auth()->user()->can('manage bookings')) {
            if ($booking->booking_state == TripBooking::STATE_AWAITING_PAYMENT) {
                // Only allow cancellation if awaiting payment (Confirmation is automatic)
                $statusBtns .= '<form action="' . route('admin.trip-bookings.update-status', $booking->id) . '" method="POST" class="d-inline confirm-action" data-confirm-message="' . __('Are you sure you want to cancel this booking?') . '">
                ' . csrf_field() . '
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="btn btn-danger btn-sm me-1" title="' . __('Cancel') . '"><i class="fas fa-times"></i></button>
                </form>';
            }

            $statusBtns .= '<form action="' . route('admin.trip-bookings.destroy', $booking->id) . '" method="POST" class="d-inline confirm-action" data-confirm-message="' . __('Are you sure you want to delete this booking?') . '">
                ' . csrf_field() . '
                ' . method_field('DELETE') . '
                <button type="submit" class="btn btn-danger btn-sm" title="' . __('Delete') . '"><i class="fas fa-trash"></i></button>
            </form>';
        }

        return '<div class="d-flex">' . $showBtn . $statusBtns . '</div>';
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $booking = TripBooking::with(['user', 'trip', 'passengers', 'payment', 'payments', 'bankTransfers'])->findOrFail($id);
        $latestBankTransfer = $booking->bankTransfers->sortByDesc('created_at')->first();
        return view('admin.trip_bookings.show', compact('booking', 'latestBankTransfer'));
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = TripBooking::findOrFail($id);

        $oldState = $booking->booking_state;
        $booking->update(['status' => $request->status]);

        if ($request->status == 'cancelled') {
            $booking->update(['booking_state' => TripBooking::STATE_CANCELLED]);
            \App\Models\BookingHistory::create([
                'trip_booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'action' => 'booking_cancelled',
                'description' => __('Booking was cancelled by admin.'),
                'previous_state' => $oldState,
                'new_state' => TripBooking::STATE_CANCELLED,
            ]);
        }

        return redirect()->back()->with('success', __('Booking status updated successfully.'));
    }

    /**
     * Update Booking State (Received, Preparing, etc)
     */
    public function updateBookingState(Request $request, $id)
    {
        $booking = TripBooking::findOrFail($id);
        $request->validate([
            'booking_state' => 'required|in:awaiting_payment,preparing,issuing_tickets,tickets_uploaded,completed,cancelled'
        ]);

        // Prevent manual move to preparing or awaiting payment unless it's a cancellation
        if (in_array($request->booking_state, [TripBooking::STATE_AWAITING_PAYMENT, TripBooking::STATE_PREPARING]) && $request->booking_state !== TripBooking::STATE_CANCELLED) {
             if ($booking->booking_state !== $request->booking_state) {
                 return redirect()->back()->with('error', __('This state transition must happen automatically.'));
             }
        }

        $oldState = $booking->booking_state;
        $newState = $request->booking_state;

        $updateData = ['booking_state' => $newState];
        if ($newState == TripBooking::STATE_CANCELLED) {
            $updateData['status'] = 'cancelled';
        }

        $booking->update($updateData);

        \App\Models\BookingHistory::create([
            'trip_booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'state_changed',
            'description' => __('Booking state manually updated to :state by admin.', ['state' => __($newState)]),
            'previous_state' => $oldState,
            'new_state' => $newState,
        ]);

        return redirect()->back()->with('success', __('Booking state updated successfully.'));
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
    /**
     * Upload ticket for a booking
     */
    public function uploadTicket(Request $request, $id)
    {
        $booking = TripBooking::with('user', 'trip')->findOrFail($id);

        $request->validate([
            'ticket_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'send_email' => 'nullable|boolean'
        ]);

        if ($request->hasFile('ticket_file')) {
            // Delete old ticket if exists
            if ($booking->ticket_file_path && Storage::disk('public')->exists($booking->ticket_file_path)) {
                Storage::disk('public')->delete($booking->ticket_file_path);
            }

            $path = $request->file('ticket_file')->store('tickets', 'public');

            $oldState = $booking->booking_state;
            $booking->update([
                'ticket_file_path' => $path,
                'booking_state' => \App\Models\TripBooking::STATE_TICKETS_UPLOADED
            ]);

            \App\Models\BookingHistory::create([
                'trip_booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'action' => 'ticket_uploaded',
                'description' => __('Tickets file uploaded by admin.'),
                'previous_state' => $oldState,
                'new_state' => \App\Models\TripBooking::STATE_TICKETS_SENT,
            ]);

            // Optional: send email to customer
            if ($request->has('send_email') && $booking->user) {
                $booking->user->notify(new TicketUploadedNotification($booking));
                return redirect()->back()->with('success', __('Ticket uploaded and sent to customer successfully.'));
            }

            return redirect()->back()->with('success', __('Ticket uploaded successfully.'));
        }

        return redirect()->back()->with('error', __('Failed to upload ticket.'));
    }

    /**
     * Manually re-send an already uploaded ticket to the customer via email.
     */
    public function sendTicket($id)
    {
        $booking = TripBooking::with('user', 'trip')->findOrFail($id);

        if (!$booking->ticket_file_path) {
            return redirect()->back()->with('error', __('No ticket has been uploaded for this booking yet.'));
        }

        if (!$booking->user) {
            return redirect()->back()->with('error', __('The customer account no longer exists.'));
        }

        $booking->user->notify(new TicketUploadedNotification($booking));

        return redirect()->back()->with('success', __('Ticket sent to customer successfully.'));
    }
}
