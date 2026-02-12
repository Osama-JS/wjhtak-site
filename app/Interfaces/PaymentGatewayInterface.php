<?php

namespace App\Interfaces;

interface PaymentGatewayInterface
{
    /**
     * Initiate a checkout session.
     *
     * @param array $data Payment data
     * @return array Response containing checkout URL and session ID.
     */
    public function initiateCheckout(array $data): array;

    /**
     * Verify payment status.
     *
     * @param string $paymentId The payment or order ID to verify.
     * @return array Detailed payment status response.
     */
    public function verifyPayment(string $paymentId): array;

    /**
     * Get a simplified payment status string.
     *
     * @param string $paymentId
     * @return string
     */
    public function getPaymentStatus(string $paymentId): string;
}
