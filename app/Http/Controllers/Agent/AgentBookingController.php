<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $query = TripBooking::whereHas('trip', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->with(['trip', 'user']);

        // Stats (unfiltered)
        $stats = [
            'total'     => (clone $query)->count(),
            'received'  => (clone $query)->where('booking_state', TripBooking::STATE_RECEIVED)->count(),
            'confirmed' => (clone $query)->where('booking_state', TripBooking::STATE_CONFIRMED)->count(),
            'cancelled' => (clone $query)->where('booking_state', TripBooking::STATE_CANCELLED)->count(),
        ];

        // Apply Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }

        if ($request->filled('status')) {
            $query->where('booking_state', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $bookings = $query->latest()->paginate(10);

        $trips = \App\Models\Trip::where('company_id', $company->id)->get();
        $states = [
            TripBooking::STATE_RECEIVED,
            TripBooking::STATE_PREPARING,
            TripBooking::STATE_CONFIRMED,
            TripBooking::STATE_TICKETS_SENT,
            TripBooking::STATE_CANCELLED,
        ];

        return view('frontend.agent.bookings.index', compact('bookings', 'stats', 'trips', 'states'));
    }

    public function show(TripBooking $booking)
    {
        $this->authorizeAgent($booking);
        $booking->load(['passengers', 'trip.images', 'trip.toCountry', 'trip.toCity', 'user']);
        return view('frontend.agent.bookings.show', compact('booking'));
    }

    public function uploadTickets(Request $request, TripBooking $booking)
    {
        $this->authorizeAgent($booking);

        $request->validate([
            'tickets_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('tickets_file')) {
            $path = $request->file('tickets_file')->store('bookings/tickets', 'public');
            $booking->update(['tickets' => $path]);

            // Optionally notify user
            // $booking->user->notify(new TicketsUploadedNotification($booking));
        }

        return back()->with('success', __('Tickets uploaded successfully'));
    }

    protected function authorizeAgent(TripBooking $booking)
    {
        if ($booking->trip->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
