<?php

namespace App\Traits;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

trait PaymentLogTrait
{
    /**
     * Log a payment attempt as pending.
     */
    protected function logPendingPayment($bookingId, $gateway, $method, $transactionId, $amount, $rawResponse = null, $bookingType = 'trip')
    {
        try {
            $data = [
                'payment_method' => $method,
                'amount' => $amount,
                'currency' => 'SAR',
                'status' => 'pending',
                'raw_response' => $rawResponse,
            ];

            if ($bookingType === 'hotel') {
                $data['hotel_booking_id'] = $bookingId;
            } else {
                $data['trip_booking_id'] = $bookingId;
            }

            return Payment::updateOrCreate(
                [
                    'transaction_id' => $transactionId,
                    'payment_gateway' => $gateway
                ],
                $data
            );
        } catch (\Exception $e) {
            Log::error("Failed to log pending payment: " . $e->getMessage());
            return null;
        }
    }
}
