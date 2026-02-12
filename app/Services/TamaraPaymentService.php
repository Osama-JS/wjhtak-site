<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TamaraPaymentService implements PaymentGatewayInterface
{
    protected $baseUrl;
    protected $apiToken;
    protected $notificationKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tamara.base_url');
        $this->apiToken = config('services.tamara.api_token');
        $this->notificationKey = config('services.tamara.notification_key');
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

        $response = Http::withToken($this->apiToken)
            ->post("{$this->baseUrl}/checkout", $payload);

        if ($response->successful()) {
            $responseData = $response->json();
            return [
                'session_id' => $responseData['checkout_id'],
                'order_id' => $responseData['order_id'],
                'checkout_url' => $responseData['checkout_url'],
                'raw_response' => $responseData
            ];
        }

        Log::error('Tamara Checkout Failed', ['response' => $response->body()]);
        throw new \Exception('Failed to initiate Tamara checkout: ' . $response->body());
    }

    /**
     * Verify payment status.
     *
     * @param string $paymentId The order ID to verify (Tamara uses Order ID usually for status check).
     * @return array Detailed payment status response.
     */
    public function verifyPayment(string $paymentId): array
    {
        // For Tamara, we usually "Authorise" the order after redirect.
        // However, for status check, we might use GET /orders/{orderId} if available or similar.
        // Based on docs, usually we call "Authorise Order" using the orderId we got.

        $response = Http::withToken($this->apiToken)
            ->post("{$this->baseUrl}/orders/{$paymentId}/authorise");

        if ($response->successful()) {
            return $response->json();
        }

        // If authorise fails, maybe it's already authorised or cancelled.
        // Let's try to get order details if authorise fails.
        $responseDetails = Http::withToken($this->apiToken)
             ->get("{$this->baseUrl}/orders/{$paymentId}");

        if ($responseDetails->successful()) {
            return $responseDetails->json();
        }

        Log::error('Tamara Verify Failed', ['response' => $response->body()]);
        throw new \Exception('Failed to verify Tamara payment: ' . $response->body());
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
     * Prepare the payload for Tamara API.
     */
    protected function preparePayload(array $data): array
    {
        return [
            'total_amount' => [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'SAR',
            ],
            'shipping_amount' => [
                'amount' => 0,
                'currency' => $data['currency'] ?? 'SAR',
            ],
            'tax_amount' => [
                'amount' => 0,
                'currency' => $data['currency'] ?? 'SAR',
            ],
            'order_reference_id' => $data['order_id'],
            'order_number' => $data['order_id'],
            'items' => $data['items'] ?? [],
            'consumer' => [
                'email' => $data['customer_email'],
                'first_name' => $data['first_name'] ?? 'Guest',
                'last_name' => $data['last_name'] ?? 'User',
                'phone_number' => $data['customer_phone'],
            ],
            'country_code' => 'SA', // Assuming SA for now, can be dynamic
            'description' => $data['description'] ?? 'Order Payment',
            'merchant_url' => [
                'success' => $data['callback_url'] . '?status=success',
                'failure' => $data['callback_url'] . '?status=failure',
                'cancel' => $data['callback_url'] . '?status=cancel',
                'notification' => $data['callback_url'] . '/webhook', // Optional
            ],
            'payment_type' => 'PAY_BY_INSTALMENTS', // Defaulting to installments
            'instalments' => 3, // Default, can be dynamic
            'shipping_address' => [
                'city' => $data['city'] ?? 'Riyadh',
                'country_code' => 'SA',
                'first_name' => $data['first_name'] ?? 'Guest',
                'last_name' => $data['last_name'] ?? 'User',
                'line1' => $data['address'] ?? 'Test Address',
                'phone_number' => $data['customer_phone'],
            ],
            'billing_address' => [
                 'city' => $data['city'] ?? 'Riyadh',
                'country_code' => 'SA',
                'first_name' => $data['first_name'] ?? 'Guest',
                'last_name' => $data['last_name'] ?? 'User',
                'line1' => $data['address'] ?? 'Test Address',
                'phone_number' => $data['customer_phone'],
            ],
        ];
    }
}
