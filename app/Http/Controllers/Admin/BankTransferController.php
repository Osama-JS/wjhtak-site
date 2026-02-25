<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTransfer;
use App\Models\Payment;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankTransferController extends Controller
{
    public function index()
    {
        return view('admin.bank_transfers.index');
    }

    public function getData()
    {
        try {
            $transfers = BankTransfer::with(['user', 'booking.trip'])->latest()->get();

            $data = $transfers->map(function ($row) {
                $statusClass = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger'
                ][$row->status] ?? 'secondary';

                $statusBadge = '<span class="badge badge-'. $statusClass .'">'. strtoupper(__($row->status)) .'</span>';

                return [
                    'id' => $row->id,
                    'user' => [
                        'full_name' => $row->user ? $row->user->full_name : __('Guest'),
                        'phone' => $row->user ? $row->user->phone : ''
                    ],
                    'booking' => [
                        'trip' => [
                            'title' => ($row->booking && $row->booking->trip) ? $row->booking->trip->title : '—'
                        ]
                    ],
                    'amount' => number_format($row->booking->total_price ?? 0, 2) . ' ' . __('SAR'),
                    'sender_name' => $row->sender_name,
                    'receipt_number' => $row->receipt_number,
                    'status' => $statusBadge,
                    'created_at' => $row->created_at->format('Y-m-d H:i'),
                    'actions' => '<a href="' . route('admin.bank-transfers.show', $row->id) . '" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>'
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $transfer = BankTransfer::with(['user', 'booking.trip', 'booking.passengers'])->findOrFail($id);
        return view('admin.bank_transfers.show', compact('transfer'));
    }

    public function approve(Request $request, $id)
    {
        $transfer = BankTransfer::with('booking')->findOrFail($id);

        if ($transfer->status !== 'pending') {
            return back()->with('error', __('This transfer has already been processed.'));
        }

        DB::beginTransaction();
        try {
            // 1. Update Transfer Status
            $transfer->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);

            // 2. Update Booking Status
            $booking = $transfer->booking;
            $booking->update([
                'status' => 'confirmed'
            ]);

            // 3. Create Payment Record
            Payment::create([
                'trip_booking_id' => $booking->id,
                'user_id' => $transfer->user_id,
                'amount' => $booking->total_price,
                'payment_gateway' => 'bank_transfer',
                'payment_method' => 'manual',
                'transaction_id' => 'BT-' . strtoupper(Str::random(12)),
                'status' => 'paid',
                'raw_response' => [
                    'bank_transfer_id' => $transfer->id,
                    'user_reference' => $transfer->receipt_number,
                    'sender_name' => $transfer->sender_name
                ]
            ]);

            // 4. Generate Invoice & Send Notification
            $invoiceService = app(InvoiceService::class);
            $invoicePath = $invoiceService->generateInvoice($booking);

            $notificationService = app(NotificationService::class);
            $notificationService->sendToUser(
                $booking->user,
                'payment_success',
                __('Payment Approved'),
                __('Your bank transfer for booking #:id has been approved. Your trip is now confirmed.', ['id' => $booking->id]),
                ['booking_id' => $booking->id]
            );

            DB::commit();
            return redirect()->route('admin.bank-transfers.index')->with('success', __('Bank transfer approved successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error: ') . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $transfer = BankTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return back()->with('error', __('This transfer has already been processed.'));
        }

        $transfer->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        // Notify user
        $transfer->load('user');
        $notificationService = app(NotificationService::class);
        $notificationService->sendToUser(
            $transfer->user,
            'payment_failed',
            __('Payment Rejected'),
            __('Your bank transfer for booking #:id was rejected. Reason: :reason', ['id' => $transfer->trip_booking_id, 'reason' => $request->rejection_reason]),
            ['booking_id' => $transfer->trip_booking_id]
        );

        return redirect()->route('admin.bank-transfers.index')->with('success', __('Bank transfer rejected successfully.'));
    }
}
