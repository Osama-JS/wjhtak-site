<?php

namespace App\Traits;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

trait PaymentLogTrait
{
    /**
     * Log a payment attempt as pending.
     */
    protected function logPendingPayment($bookingId, $gateway, $method, $transactionId, $amount, $rawResponse = null)
    {
        try {
            return Payment::updateOrCreate(
                [
                    'transaction_id' => $transactionId,
                    'payment_gateway' => $gateway
                ],
                [
                    'trip_booking_id' => $bookingId,
                    'payment_method' => $method,
                    'amount' => $amount,
                    'currency' => 'SAR',
                    'status' => 'pending',
                    'raw_response' => $rawResponse,
                ]
            );
        } catch (\Exception $e) {
            Log::error("Failed to log pending payment: " . $e->getMessage());
            return null;
        }
    }
}
