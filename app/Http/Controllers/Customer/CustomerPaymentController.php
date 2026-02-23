<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\TripBooking;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomerPaymentController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * List all payments for the authenticated customer.
     */
    public function index()
    {
        $payments = Payment::whereHas('booking', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->with('booking.trip')
            ->latest()
            ->paginate(10);

        return view('frontend.customer.payments.index', compact('payments'));
    }

    /**
     * Show checkout page for a booking.
     */
    public function checkout($bookingId)
    {
        $booking = TripBooking::with(['trip', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if ($booking->status === 'confirmed') {
            return redirect()->route('customer.bookings.show', $bookingId)
                ->with('info', __('هذا الحجز مدفوع بالفعل.'));
        }

        if ($booking->status === 'cancelled') {
            return redirect()->route('customer.bookings.show', $bookingId)
                ->with('error', __('لا يمكن الدفع لحجز ملغى.'));
        }

        return view('frontend.customer.payments.checkout', compact('booking'));
    }

    /**
     * Download invoice for a confirmed booking.
     */
    public function downloadInvoice($bookingId)
    {
        $booking = TripBooking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->with(['trip', 'user', 'passengers'])
            ->findOrFail($bookingId);

        $payment = $booking->payments()->latest()->first();

        if ($payment && $payment->invoice_path && Storage::disk('public')->exists($payment->invoice_path)) {
            $path = Storage::disk('public')->path($payment->invoice_path);
            return response()->download($path, 'invoice-' . $booking->id . '.pdf');
        }

        $invoicePath = $this->invoiceService->generateInvoice($booking);
        if (!$invoicePath) {
            return back()->with('error', __('تعذّر توليد الفاتورة.'));
        }

        return response()->download(Storage::disk('public')->path($invoicePath), 'invoice-' . $booking->id . '.pdf');
    }
}
