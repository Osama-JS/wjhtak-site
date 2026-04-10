<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use App\Models\HotelBookingGuest;
use App\Models\HotelBookingHistory;
use App\Services\TBOHotelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HotelBookingController extends Controller
{
    protected $tboService;

    public function __construct(TBOHotelService $tboService)
    {
        $this->tboService = $tboService;
    }

    /**
     * Show the booking form.
     */
    public function create(Request $request)
    {
        $request->validate([
            'session_id'   => 'required|string',
            'hotel_code'   => 'required|string',
            'result_token' => 'required|string',
            'hotel_name'   => 'required|string',
        ]);

        // Get search criteria from request or default
        $searchCriteria = [
            'adults'   => $request->get('adults', 2),
            'children' => $request->get('children', 0),
            'rooms'    => $request->get('rooms', 1),
            'check_in' => $request->get('check_in'),
            'check_out' => $request->get('check_out'),
        ];

        // Fetch hotel static info for display if needed
        $hotelInfo = $this->tboService->getHotelInfo($request->hotel_code);

        return view('frontend.hotels.booking', [
            'hotel'          => $hotelInfo,
            'hotel_name'     => $request->hotel_name,
            'sessionId'      => $request->session_id,
            'resultToken'    => $request->result_token,
            'hotelCode'      => $request->hotel_code,
            'searchCriteria' => $searchCriteria,
            'totalPrice'     => $request->get('total_price'),
            'roomTypeName'   => $request->get('room_type_name'),
        ]);
    }

    /**
     * Store the booking and redirect to payment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id'       => 'required|string',
            'result_token'     => 'required|string',
            'hotel_code'       => 'required|string',
            'hotel_name'       => 'required|string',
            'room_type_name'   => 'required|string',
            'check_in'         => 'required|date',
            'check_out'        => 'required|date|after:check_in',
            'adults'           => 'required|integer|min:1',
            'children'         => 'nullable|integer|min:0',
            'rooms'            => 'required|integer|min:1',
            'total_price'      => 'required|numeric|min:1',
            'guests'           => 'required|array|min:1',
            'guests.*.title'      => 'required|in:Mr,Mrs,Ms,Mstr',
            'guests.*.first_name' => 'required|string',
            'guests.*.last_name'  => 'required|string',
            'guests.*.type'       => 'required|in:adult,child',
            'guests.*.dob'        => 'nullable|date',
            'guests.*.passport_number' => 'nullable|string',
            'guests.*.passport_expiry' => 'nullable|date',
            'guests.*.nationality'     => 'nullable|string|size:2',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $user    = Auth::user();
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);

            // Create draft booking
            $booking = HotelBooking::create([
                'user_id'          => $user->id,
                'tbo_session_id'   => $request->session_id,
                'tbo_result_token' => $request->result_token,
                'hotel_code'       => $request->hotel_code,
                'hotel_name'       => $request->hotel_name,
                'city_name'        => $request->get('city_name', 'N/A'),
                'country_code'     => $request->get('country_code', 'SA'),
                'room_type_code'   => $request->get('room_type_code', 'N/A'),
                'room_type_name'   => $request->room_type_name,
                'check_in_date'    => $checkIn->toDateString(),
                'check_out_date'   => $checkOut->toDateString(),
                'nights_count'     => $checkIn->diffInDays($checkOut),
                'adults'           => $request->adults,
                'children'         => $request->children ?? 0,
                'rooms_count'      => $request->rooms,
                'total_price'      => $request->total_price,
                'currency'         => 'SAR',
                'status'           => HotelBooking::STATUS_DRAFT,
                'booking_state'    => HotelBooking::STATE_AWAITING_PAYMENT,
                'notes'            => $request->notes,
            ]);

            // Save guests
            foreach ($request->guests as $index => $guestData) {
                $isLead = ($index === 0);
                HotelBookingGuest::create([
                    'hotel_booking_id' => $booking->id,
                    'title'            => $guestData['title'],
                    'first_name'       => $guestData['first_name'],
                    'last_name'        => $guestData['last_name'],
                    'type'             => $guestData['type'],
                    'is_lead'          => $isLead,
                    'dob'              => $guestData['dob'] ?? null,
                    'passport_number'  => $guestData['passport_number'] ?? null,
                    'passport_expiry'  => $guestData['passport_expiry'] ?? null,
                    'nationality'     => $guestData['nationality'] ?? 'SA',
                ]);
            }

            // Log history
            HotelBookingHistory::create([
                'hotel_booking_id' => $booking->id,
                'user_id'          => $user->id,
                'action'           => 'booking_draft_created',
                'description'      => 'تم إنشاء الحجز (مسودة) في انتظار الدفع.',
                'new_state'        => HotelBooking::STATE_AWAITING_PAYMENT,
            ]);
             

            DB::commit();

            // Redirect to payment initiation
            return redirect()->route('customer.payments.checkout', $booking->id)
                ->with('success', __('Booking created successfully. Please complete payment.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Web Hotel Booking Error: ' . $e->getMessage());
            return back()->with('error', __('Failed to create booking. Please try again.'))->withInput();
        }
    }

    /**
     * List all hotel bookings for the authenticated customer.
     */
    public function index(Request $request)
    {
        $query = HotelBooking::where('user_id', Auth::id())
            ->latest();

        if ($request->filled('status')) {
            $query->where('booking_state', $request->status);
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('frontend.customer.hotel-bookings.index', compact('bookings'));
    }

    /**
     * Show hotel booking details.
     */
    public function show($id)
    {
        $booking = HotelBooking::with(['guests', 'payment', 'histories' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('frontend.customer.hotel-bookings.show', compact('booking'));
    }
    /**
     * Cancel a hotel booking.
     */
    public function cancel(Request $request, $id)
    {
        $booking = HotelBooking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status === HotelBooking::STATUS_CANCELLED) {
            return back()->with('error', __('Booking is already cancelled.'));
        }

        try {
            DB::beginTransaction();

            // 1. If it has a TBO booking ID, cancel it with TBO
            if ($booking->tbo_booking_id) {
                $this->tboService->cancelBooking($booking->tbo_booking_id);
            }

            // 2. Update local status
            $booking->update([
                'status'              => HotelBooking::STATUS_CANCELLED,
                'booking_state'       => HotelBooking::STATE_CANCELLED,
                'cancellation_reason' => $request->get('reason', __('Cancelled by customer.')),
            ]);

            // 3. Log history
            HotelBookingHistory::create([
                'hotel_booking_id' => $booking->id,
                'user_id'          => Auth::id(),
                'action'           => 'customer_cancellation',
                'description'      => __('Booking cancelled by customer.'),
                'previous_state'   => $booking->booking_state,
                'new_state'        => HotelBooking::STATE_CANCELLED,
            ]);

            DB::commit();

            return redirect()->route('customer.hotel-bookings.show', $id)
                ->with('success', __('Booking cancelled successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Web Customer Hotel Cancel Error [{$id}]: " . $e->getMessage());
            return back()->with('error', __('Failed to cancel booking: ') . $e->getMessage());
        }
    }
}
