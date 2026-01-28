<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HyperPayService
{
    protected $baseUrl;
    protected $accessToken;
    protected $entityIds;

    public function __construct()
    {
        $this->baseUrl = config('hyperpay.base_url');
        $this->accessToken = config('hyperpay.access_token');
        $this->entityIds = config('hyperpay.entity_ids');
    }

    /**
     * Get Checkout ID to initialize payment
     *
     * @param float $amount
     * @param string $paymentType (mada, visa_master, apple_pay)
     * @param array $additionalParams (merchantTransactionId, customer.email, etc)
     * @return array|false
     */
    public function prepareCheckout($amount, $paymentType = 'visa_master', $additionalParams = [])
    {
        $entityId = $this->getEntityId($paymentType);

        if (!$entityId) {
            Log::error("HyperPay: Invalid payment type [{$paymentType}] or missing Entity ID.");
            return false;
        }

        $url = $this->baseUrl . 'checkouts';

        $params = array_merge([
            'entityId' => $entityId,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => config('hyperpay.currency', 'SAR'),
            'paymentType' => 'DB', // Debit
        ], $additionalParams);

        $response = Http::withToken($this->accessToken)
            ->asForm()
            ->post($url, $params);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error("HyperPay Prepare Checkout Failed: " . $response->body());
        return false;
    }

    /**
     * Get Payment Status
     *
     * @param string $checkoutId
     * @param string $paymentType
     * @return array|false
     */
    public function getPaymentStatus($checkoutId, $paymentType = 'visa_master')
    {
        $entityId = $this->getEntityId($paymentType);
        $url = $this->baseUrl . "checkouts/{$checkoutId}/payment";

        $response = Http::withToken($this->accessToken)
            ->get($url, [
                'entityId' => $entityId
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error("HyperPay Get Status Failed: " . $response->body());
        return false;
    }

    /**
     * Verify if the payment result code indicates success
     *
     * @param string $resultCode
     * @return bool
     */
    public function isSuccessful($resultCode)
    {
        // Success codes match patterns like 000.000.000, 000.100.110, etc.
        // https://wordpresshyperpay.docs.oppwa.com/reference/resultCodes
        return (bool) preg_match('/^(000\.000\.|000\.100\.1|000\.[36])/', $resultCode);
    }

    /**
     * Get Entity ID based on payment type
     */
    protected function getEntityId($type)
    {
        return $this->entityIds[$type] ?? null;
    }
}
