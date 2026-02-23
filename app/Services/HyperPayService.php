<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HyperPayService
{
    protected $baseUrl;
    protected $accessToken;
    protected $entityIds;
    protected $testMode;

    public function __construct()
    {
        $this->baseUrl = config('hyperpay.base_url');
        $this->accessToken = config('hyperpay.access_token');
        $this->entityIds = config('hyperpay.entity_ids');
        $this->testMode = config('hyperpay.test_mode', false);
    }

    /**
     * Get Checkout ID to initialize payment
     *
     * @param float $amount
     * @param string $paymentType (mada, visa_master, apple_pay)
     * @param array $additionalParams
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

        $params = [
            'entityId' => $entityId,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => config('hyperpay.currency', 'SAR'),
            'paymentType' => 'DB',
        ];

        // Add test mode parameters (REQUIRED for test server & 3DS2)
        if ($this->testMode) {
            $params['testMode'] = 'EXTERNAL';
            $params['customParameters[3DS2_enrolled]'] = 'true';
        }

        // Merge additional params (merchantTransactionId, billing, customer, etc.)
        $params = array_merge($params, $additionalParams);

        Log::info('HyperPay Prepare Checkout Request', ['url' => $url, 'params' => $params]);

        $response = Http::withToken($this->accessToken)
            ->asForm()
            ->post($url, $params);

        if ($response->successful()) {
            $result = $response->json();
            Log::info('HyperPay Prepare Checkout Success', ['checkout_id' => $result['id'] ?? 'N/A']);
            return $result;
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
        return (bool) preg_match('/^(000\.000\.|000\.100\.1|000\.[36])/', $resultCode);
    }

    /**
     * Build billing & customer params from user data
     *
     * @param array $userData Keys: email, first_name, last_name, street, city, state, country, postcode
     * @return array HyperPay formatted parameters
     */
    public function buildCustomerParams(array $userData): array
    {
        $params = [];

        // Customer Info
        if (!empty($userData['email'])) {
            $params['customer.email'] = $userData['email'];
        }
        $params['customer.givenName'] = $userData['first_name'] ?? 'User';
        $params['customer.surname'] = $userData['last_name'] ?? 'Guest';

        // Billing address (MANDATORY for 3DS2)
        $params['billing.street1'] = $userData['street'] ?? 'Saudi Arabia';
        $params['billing.city'] = $userData['city'] ?? 'Riyadh';
        $params['billing.state'] = $userData['state'] ?? 'Riyadh';
        $params['billing.country'] = $userData['country'] ?? 'SA';
        $params['billing.postcode'] = $userData['postcode'] ?? '12345';

        return $params;
    }

    /**
     * Get user-friendly translated message based on HyperPay result code
     *
     * @param string $resultCode
     * @param string|null $defaultDescription
     * @return string
     */
    public function getUserFriendlyMessage($resultCode, $defaultDescription = null): string
    {
        // Success codes
        if ($this->isSuccessful($resultCode)) {
            return __('payment.success');
        }

        // Map result code patterns to translation keys
        $codePatterns = [
            // Card/Account issues
            '100.100.303' => 'payment.insufficient_funds',
            '100.100.304' => 'payment.insufficient_funds',
            '800.100.151' => 'payment.card_declined',
            '800.100.152' => 'payment.card_declined',
            '800.100.153' => 'payment.card_invalid_cvv',
            '800.100.154' => 'payment.card_expired',
            '800.100.155' => 'payment.card_holder_invalid',
            '800.100.157' => 'payment.card_stolen',
            '800.100.159' => 'payment.card_fraud',
            '800.100.160' => 'payment.card_not_enrolled_3ds',
            '800.100.162' => 'payment.card_limit_exceeded',
            '800.100.163' => 'payment.card_limit_exceeded',
            '800.100.170' => 'payment.card_restriction',
            '800.100.171' => 'payment.card_restriction',
            '800.100.190' => 'payment.card_declined_issuer',

            // Technical/Session issues
            '700.400.200' => 'payment.checkout_expired',
            '700.400.300' => 'payment.checkout_expired',
            '700.400.530' => 'payment.checkout_expired',
            '700.400.560' => 'payment.checkout_already_used',
            '200.300.404' => 'payment.risk_rejected',
            '100.400.311' => 'payment.3ds_failed',
            '100.390.111' => 'payment.3ds_failed',
            '100.380.401' => 'payment.3ds_failed',
            '800.400.500' => 'payment.duplicate_request',

            // Network/connection
            '800.800.100' => 'payment.network_error',
            '800.800.102' => 'payment.timeout',
            '800.800.202' => 'payment.bank_unavailable',
            '900.100.100' => 'payment.internal_error',
        ];

        // Check exact match first
        if (isset($codePatterns[$resultCode])) {
            return __($codePatterns[$resultCode]);
        }

        // Check pattern-based matching
        if (preg_match('/^800\.100\./', $resultCode)) {
            return __('payment.card_declined');
        }
        if (preg_match('/^700\./', $resultCode)) {
            return __('payment.checkout_expired');
        }
        if (preg_match('/^800\.800\./', $resultCode)) {
            return __('payment.network_error');
        }
        if (preg_match('/^100\.39/', $resultCode)) {
            return __('payment.3ds_failed');
        }
        if (preg_match('/^900\./', $resultCode)) {
            return __('payment.internal_error');
        }

        // General fallback
        return __('payment.general_failure');
    }

    /**
     * Get Entity ID based on payment type
     */
    protected function getEntityId($type)
    {
        return $this->entityIds[$type] ?? null;
    }
}
