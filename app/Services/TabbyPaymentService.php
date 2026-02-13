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
        $this->baseUrl = config('services.tabby.base_url');
        $this->publicKey = config('services.tabby.public_key');
        $this->secretKey = config('services.tabby.secret_key');
        $this->merchantCode = config('services.tabby.merchant_code');
    }

    /**
     * Initiate a checkout session.
     *
     * @param array $data Payment data
     * @return array Response containing checkout URL and session ID.
     */
    public function initiateCheckout(array $data): array
    {
        $payload = $this->preparePayload($data);

        $response = Http::withToken($this->publicKey)
            ->post("{$this->baseUrl}/checkout", $payload);

        if ($response->successful()) {
            $responseData = $response->json();
            return [
                'session_id' => $responseData['id'],
                'checkout_url' => $responseData['configuration']['available_products']['installments'][0]['web_url']
                    ?? $responseData['configuration']['available_products']['pay_later'][0]['web_url']
                    ?? null, // Fallback mostly
                'raw_response' => $responseData
            ];
        }

        Log::error('Tabby Checkout Failed', ['response' => $response->body()]);
        throw new \Exception('Failed to initiate Tabby checkout: ' . $response->body());
    }

    /**
     * Verify payment status.
     *
     * @param string $paymentId The payment or session ID to verify.
     * @return array Detailed payment status response.
     */
    public function verifyPayment(string $paymentId): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/payments/{$paymentId}");

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Tabby Verify Failed', ['response' => $response->body()]);
        throw new \Exception('Failed to verify Tabby payment: ' . $response->body());
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
            return $data['status'] ?? 'unknown';
        } catch (\Exception $e) {
            return 'failed';
        }
    }

    /**
     * Prepare the payload for Tabby API.
     */
    protected function preparePayload(array $data): array
    {
        return [
            'payment' => [
                'amount' => number_format($data['amount'], 2, '.', ''),
                'currency' => $data['currency'] ?? 'SAR',
                'description' => $data['description'] ?? 'Order Payment',
                'buyer' => [
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'],
                    'phone' => $data['customer_phone'],
                ],
                'shipping_address' => [
                    'city' => $data['city'] ?? 'Riyadh',
                    'address' => $data['address'] ?? 'Test Address',
                    'zip' => $data['zip'] ?? '0000',
                ],
                'order' => [
                    'reference_id' => $data['order_id'],
                    'items' => $data['items'] ?? [],
                ],
            ],
            'lang' => app()->getLocale(),
            'merchant_code' => $this->merchantCode,
            'merchant_urls' => [
                'success' => $data['callback_url'] . '?status=success',
                'cancel' => $data['callback_url'] . '?status=cancel',
                'failure' => $data['callback_url'] . '?status=failure',
            ],
        ];
    }
}
