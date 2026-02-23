<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\Trip;
use App\Models\BookingPassenger;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * List all bookings for the authenticated customer.
     */
    public function index(Request $request)
    {
        $query = TripBooking::with(['trip.images'])
            ->where('user_id', Auth::id())
            ->latest();

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['pending', 'confirmed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('frontend.customer.bookings.index', compact('bookings'));
    }

    /**
     * Show booking details.
     */
    public function show($id)
    {
        $booking = TripBooking::with(['trip.images', 'trip.toCountry', 'trip.toCity', 'passengers', 'payments'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('frontend.customer.bookings.show', compact('booking'));
    }

    /**
     * Show the booking form for a specific trip.
     */
    public function create($trip_id)
    {
        $trip = Trip::active()->with(['images', 'fromCountry', 'toCountry'])->findOrFail($trip_id);

        return view('frontend.customer.bookings.create', compact('trip'));
    }

    /**
     * Create a new booking from the trip show page.
     */
    public function store(Request $request)
    {
        $request->validate([
            'trip_id'                      => 'required|exists:trips,id',
            'notes'                        => 'nullable|string|max:500',
            'passengers'                   => 'required|array|min:1|max:20',
            'passengers.*.name'            => 'required|string|max:255',
            'passengers.*.phone'           => 'nullable|string|max:20',
            'passengers.*.passport_number' => 'nullable|string|max:50',
            'passengers.*.passport_expiry' => 'nullable|date',
            'passengers.*.nationality'     => 'nullable|string|max:100',
        ]);

        $trip = Trip::active()->findOrFail($request->trip_id);

        $passengersCount = count($request->passengers);

        if ($trip->tickets < $passengersCount) {
            return back()->withErrors([
                'passengers' => __('لا توجد تذاكر كافية. المتاح: :count', ['count' => $trip->tickets])
            ])->withInput();
        }

        // Dynamic pricing
        $baseCapacity = $trip->base_capacity ?? 2;
        $extraPrice   = $trip->extra_passenger_price ?? 0;
        $totalPrice   = $trip->price;

        if ($passengersCount > $baseCapacity) {
            $totalPrice += ($passengersCount - $baseCapacity) * $extraPrice;
        }

        $booking = TripBooking::create([
            'user_id'        => Auth::id(),
            'trip_id'        => $trip->id,
            'tickets_count'  => $passengersCount,
            'total_price'    => $totalPrice,
            'status'         => 'pending',
            'notes'          => $request->notes,
            'booking_date'   => now(),
        ]);

        foreach ($request->passengers as $passengerData) {
            $booking->passengers()->create($passengerData);
        }

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', __('تم إنشاء الحجز بنجاح! يمكنك الآن إتمام الدفع.'));
    }

    /**
     * Cancel a pending booking.
     */
    public function cancel($id)
    {
        $booking = TripBooking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'pending') {
            return back()->with('error', __('لا يمكن إلغاء حجز تم تأكيده أو دفعه.'));
        }

        $booking->passengers()->delete();
        $booking->update(['status' => 'cancelled']);

        return redirect()->route('customer.bookings.index')
            ->with('success', __('تم إلغاء الحجز بنجاح.'));
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice($id)
    {
        $booking = TripBooking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->findOrFail($id);

        // Use existing invoice or generate new one
        $payment = $booking->payments()->latest()->first();

        if ($payment && $payment->invoice_path && Storage::disk('public')->exists($payment->invoice_path)) {
            $filePath = Storage::disk('public')->path($payment->invoice_path);
            return response()->download($filePath, 'invoice-' . $booking->id . '.pdf');
        }

        // Generate on demand
        $invoicePath = $this->invoiceService->generateInvoice($booking);

        if (!$invoicePath) {
            return back()->with('error', __('تعذّر توليد الفاتورة. الرجاء المحاولة لاحقاً.'));
        }

        $filePath = Storage::disk('public')->path($invoicePath);
        return response()->download($filePath, 'invoice-' . $booking->id . '.pdf');
    }
}
