<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentLogController extends Controller
{
    /**
     * Display a listing of payment logs.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking.user', 'booking.trip'])->latest();

        // Search by transaction ID or Booking ID
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhere('trip_booking_id', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by Gateway
        if ($request->gateway) {
            $query->where('payment_gateway', $request->gateway);
        }

        // Filter by Status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Display the specified payment detail (raw response).
     */
    public function show($id)
    {
        $payment = Payment::with(['booking.user', 'booking.trip'])->findOrFail($id);
        return response()->json($payment);
    }
}
