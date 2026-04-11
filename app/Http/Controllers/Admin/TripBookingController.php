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
            $bookings = TripBooking::with(['user', 'trip.company'])->latest()->get();

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
                    'company' => $booking->trip && $booking->trip->company ? $booking->trip->company->name : '---',
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
                // Cancellation is now handled in show page only as requested
            }

            $statusBtns .= '<button type="button" class="btn btn-danger btn-sm open-delete-modal"
                data-id="' . $booking->id . '"
                data-url="' . route('admin.trip-bookings.destroy', $booking->id) . '"
                title="' . __('Delete') . '">
                <i class="fas fa-trash"></i>
            </button>';
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
     * Update status (Used for cancellation)
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = TripBooking::findOrFail($id);

        $request->validate([
            'status' => 'required|in:cancelled',
            'cancellation_reason' => 'required|string|max:1000'
        ]);

        $oldState = $booking->booking_state;

        // Update state and reason, but keep 'status' logic if needed
        $booking->update([
            'booking_state' => TripBooking::STATE_CANCELLED,
            'cancellation_reason' => $request->cancellation_reason,
            // 'status' => 'cancelled' // User asked to keep status as is? "تبق على الحقل booking_status وليس على ال status"
            // Actually, if we cancel, it should probably reflect in the status too unless they use it for something else.
            // But per request I will focus on booking_state.
        ]);

        \App\Models\BookingHistory::create([
            'trip_booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'booking_cancelled',
            'description' => __('Booking was cancelled by admin. Reason: :reason', ['reason' => $request->cancellation_reason]),
            'previous_state' => $oldState,
            'new_state' => TripBooking::STATE_CANCELLED,
        ]);

        return redirect()->back()->with('success', __('Booking has been cancelled successfully.'));
    }

    /**
     * Update Booking State (Received, Preparing, etc)
     */
    public function updateBookingState(Request $request, $id)
    {
        $booking = TripBooking::findOrFail($id);
        $request->validate([
            'booking_state' => 'required|in:awaiting_payment,preparing,issuing_tickets,tickets_uploaded,completed'
        ]);

        // Prevent admin from changing state if it's awaiting payment
        if ($booking->booking_state === TripBooking::STATE_AWAITING_PAYMENT) {
            return redirect()->back()->with('error', __('This booking is awaiting payment. You can only cancel it using the Cancel button or wait for payment.'));
        }

        // Prevent manual move to preparing or awaiting payment
        if (in_array($request->booking_state, [TripBooking::STATE_AWAITING_PAYMENT, TripBooking::STATE_PREPARING])) {
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
        $booking = TripBooking::with(['payments', 'bankTransfers'])->findOrFail($id);

        // Strict Deletion Rules
        if ($booking->status === 'confirmed') {
            return redirect()->back()->with('error', __('Cannot delete a confirmed booking. Please cancel it first if needed (if allowed).'));
        }

        if ($booking->payments()->whereIn('status', ['paid', 'approved', 'completed'])->exists()) {
            return redirect()->back()->with('error', __('Cannot delete a booking with successful payments.'));
        }

        if ($booking->bankTransfers()->whereIn('status', ['approved', 'pending'])->exists()) {
            return redirect()->back()->with('error', __('Cannot delete a booking with active or pending bank transfers.'));
        }

        $booking->passengers()->delete();
        $booking->histories()->delete();
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
                'new_state' => \App\Models\TripBooking::STATE_TICKETS_UPLOADED,
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
