<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TapPaymentService implements PaymentGatewayInterface
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.tap.base_url'), '/');
        $this->secretKey = config('services.tap.secret_key');
        $this->publicKey = config('services.tap.public_key');
    }

    /**
     * Initiate a checkout session.
     * Endpoint: POST /charges
     */
    public function initiateCheckout(array $data): array
    {
        $payload = [
            'amount' => (float) $data['amount'],
            'currency' => $data['currency'] ?? 'SAR',
            'threeDSecure' => true,
            'save_card' => false,
            'description' => $data['description'] ?? 'Booking Payment',
            'statement_descriptor' => 'WJHTAK',
            'metadata' => [
                'booking_id' => $data['booking_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
            ],
            'customer' => [
                'first_name' => $data['first_name'] ?? 'Guest',
                'last_name' => $data['last_name'] ?? 'User',
                'email' => $data['customer_email'] ?? 'guest@example.com',
                'phone' => [
                    'country_code' => '966',
                    'number' => preg_replace('/[^0-9]/', '', $data['customer_phone'] ?? '0500000000'),
                ],
            ],
            'source' => ['id' => 'src_all'],
            'redirect' => [
                'url' => $data['callback_url'] ?? route('payments.web.callback', ['payment_type' => 'tap']),
            ],
        ];

        Log::info('Tap Checkout Request', ['payload' => $payload]);

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/charges", $payload);

        Log::info('Tap Checkout Response', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            return [
                'id' => $responseData['id'] ?? null,
                'checkout_url' => $responseData['transaction']['url'] ?? null,
                'status' => $responseData['status'] ?? 'initiated',
                'raw_response' => $responseData,
            ];
        }

        $error = $response->json() ?? ['message' => $response->body()];
        Log::error('Tap Checkout Failed', ['error' => $error]);

        throw new \Exception("Tap Error: " . ($error['errors'][0]['description'] ?? 'Unexpected error occurred'));
    }

    /**
     * Verify payment status.
     * Endpoint: GET /charges/{id}
     */
    public function verifyPayment(string $paymentId): array
    {
        Log::info("Tap: Verifying charge {$paymentId}");

        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/charges/{$paymentId}");

        if ($response->successful()) {
            return $response->json();
        }

        Log::error("Tap Verification Failed", ['status' => $response->status(), 'body' => $response->body()]);
        throw new \Exception("Failed to verify Tap payment: " . $response->body());
    }

    /**
     * Get simplified payment status.
     */
    public function getPaymentStatus(string $paymentId): string
    {
        try {
            $result = $this->verifyPayment($paymentId);
            $status = strtoupper($result['status'] ?? 'UNKNOWN');

            return match($status) {
                'CAPTURED' => 'paid',
                'AUTHORIZED' => 'paid', // Depending on business logic, authorized might be enough
                'FAILED' => 'failed',
                'DECLINED' => 'failed',
                'VOID' => 'cancelled',
                default => 'pending'
            };
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
