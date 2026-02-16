<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TabbyPaymentService implements PaymentGatewayInterface
{
    protected $baseUrl;
    protected $publicKey;
    protected $secretKey;
    protected $merchantCode;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.tabby.base_url'), '/');
        $this->publicKey = config('services.tabby.public_key');
        $this->secretKey = config('services.tabby.secret_key');
        $this->merchantCode = config('services.tabby.merchant_code');
    }

    /**
     * Initiate a checkout session.
     * Endpoint: POST /api/v2/checkout
     *
     * @param array $data Payment data
     * @return array Response containing checkout URL and session ID.
     */
    public function initiateCheckout(array $data): array
    {
        $payload = $this->preparePayload($data);

        Log::info('Tabby Checkout Request', [
            'url' => "{$this->baseUrl}/checkout",
            'payload' => $payload,
        ]);

        $response = Http::withToken($this->publicKey)
            ->post("{$this->baseUrl}/checkout", $payload);

        Log::info('Tabby Checkout Response', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            // Extract checkout URL from available products
            $checkoutUrl = null;
            $products = $responseData['configuration']['available_products'] ?? [];

            // Try installments first, then pay_later
            if (!empty($products['installments'])) {
                $checkoutUrl = $products['installments'][0]['web_url'] ?? null;
            }
            if (!$checkoutUrl && !empty($products['pay_later'])) {
                $checkoutUrl = $products['pay_later'][0]['web_url'] ?? null;
            }

            return [
                'session_id' => $responseData['id'] ?? null,
                'payment_id' => $responseData['payment']['id'] ?? null,
                'checkout_url' => $checkoutUrl,
                'status' => $responseData['status'] ?? 'created',
                'raw_response' => $responseData,
            ];
        }

        $errorBody = $response->json() ?? ['message' => $response->body()];
        Log::error('Tabby Checkout Failed', ['status' => $response->status(), 'response' => $errorBody]);

        $errorMessage = $errorBody['message'] ?? ($errorBody['error'] ?? 'Unknown error');
        throw new \Exception("Failed to initiate Tabby checkout: {$errorMessage}");
    }

    /**
     * Verify payment status and auto-capture if AUTHORIZED.
     *
     * Flow per Tabby docs:
     * 1. GET /payments/{id} → check status
     * 2. If AUTHORIZED → POST /payments/{id}/captures → CLOSED
     *
     * @param string $paymentId The payment ID to verify.
     * @return array Payment details with final status.
     */
    public function verifyPayment(string $paymentId): array
    {
        Log::info("Tabby: Verifying payment {$paymentId}");

        // Step 1: Get payment status
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/payments/{$paymentId}");

        if (!$response->successful()) {
            Log::error("Tabby: Failed to get payment {$paymentId}", [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);
            throw new \Exception("Failed to retrieve Tabby payment: " . $response->body());
        }

        $paymentData = $response->json();
        $status = strtoupper($paymentData['status'] ?? 'UNKNOWN');

        Log::info("Tabby: Payment {$paymentId} status = {$status}");

        // Step 2: If AUTHORIZED, capture the payment
        if ($status === 'AUTHORIZED') {
            $captureResult = $this->capturePayment($paymentId, $paymentData);
            if ($captureResult) {
                $paymentData['status'] = 'CLOSED';
                $paymentData['capture_result'] = $captureResult;
                Log::info("Tabby: Payment {$paymentId} captured successfully → CLOSED");
            }
        }

        return $paymentData;
    }

    /**
     * Capture an authorized payment.
     * Endpoint: POST /api/v2/payments/{id}/captures
     *
     * @param string $paymentId
     * @param array $paymentData Original payment data for amount extraction
     * @return array|null Capture response or null on failure
     */
    public function capturePayment(string $paymentId, array $paymentData = []): ?array
    {
        $amount = $paymentData['amount'] ?? '0.00';

        $capturePayload = [
            'amount' => $amount,
        ];

        // Add items if available
        if (!empty($paymentData['order']['items'])) {
            $capturePayload['items'] = $paymentData['order']['items'];
        }

        Log::info("Tabby: Capturing payment {$paymentId}", ['payload' => $capturePayload]);

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/payments/{$paymentId}/captures", $capturePayload);

        if ($response->successful()) {
            $result = $response->json();
            Log::info("Tabby: Capture successful for {$paymentId}", ['response' => $result]);
            return $result;
        }

        Log::error("Tabby: Capture failed for {$paymentId}", [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ]);

        return null;
    }

    /**
     * Get a simplified payment status string.
     *
     * @param string $paymentId
     * @return string
     */
    public function getPaymentStatus(string $paymentId): string
    {
        try {
            $data = $this->verifyPayment($paymentId);
            return strtolower($data['status'] ?? 'unknown');
        } catch (\Exception $e) {
            Log::error("Tabby getPaymentStatus failed: " . $e->getMessage());
            return 'failed';
        }
    }

    /**
     * Prepare the payload for Tabby API checkout.
     * Structure follows Tabby API v2 documentation.
     */
    protected function preparePayload(array $data): array
    {
        $currency = $data['currency'] ?? 'SAR';
        $amount = number_format((float)($data['amount'] ?? 0), 2, '.', '');

        // Build items array with required structure
        $items = [];
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $items[] = [
                    'title' => $item['title'] ?? $item['name'] ?? 'Item',
                    'description' => $item['description'] ?? ($item['title'] ?? 'Item'),
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => number_format((float)($item['unit_price'] ?? $data['amount']), 2, '.', ''),
                    'category' => $item['category'] ?? 'Travel',
                    'reference_id' => (string)($item['reference_id'] ?? '1'),
                ];
            }
        }

        return [
            'payment' => [
                'amount' => $amount,
                'currency' => $currency,
                'description' => $data['description'] ?? 'Order Payment',
                'buyer' => [
                    'name' => $data['customer_name'] ?? (($data['first_name'] ?? 'Guest') . ' ' . ($data['last_name'] ?? 'User')),
                    'email' => $data['customer_email'] ?? '',
                    'phone' => $data['customer_phone'] ?? '',
                ],
                'shipping_address' => [
                    'city' => $data['city'] ?? 'Riyadh',
                    'address' => $data['address'] ?? 'Saudi Arabia',
                    'zip' => $data['zip'] ?? '00000',
                ],
                'order' => [
                    'reference_id' => $data['order_id'] ?? ('ORDER-' . time()),
                    'items' => $items,
                    'tax_amount' => '0.00',
                    'shipping_amount' => '0.00',
                    'discount_amount' => '0.00',
                ],
                'buyer_history' => [
                    'registered_since' => now()->subYear()->toIso8601String(),
                    'loyalty_level' => 0,
                ],
            ],
            'lang' => app()->getLocale() === 'ar' ? 'ar' : 'en',
            'merchant_code' => $this->merchantCode,
            'merchant_urls' => [
                'success' => $data['callback_url'] . '?status=success',
                'cancel' => $data['callback_url'] . '?status=cancel',
                'failure' => $data['callback_url'] . '?status=failure',
            ],
        ];
    }
}
