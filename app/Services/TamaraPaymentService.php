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
        $this->baseUrl = rtrim(config('services.tamara.base_url'), '/');
        $this->apiToken = config('services.tamara.api_token');
        $this->notificationKey = config('services.tamara.notification_key');
    }

    /**
     * Initiate a checkout session.
     *
     * @param array $data Payment data
     * @return array Response containing checkout URL, session ID, and order ID.
     */
    public function initiateCheckout(array $data): array
    {
        $payload = $this->preparePayload($data);

        Log::info('Tamara Checkout Request', ['url' => "{$this->baseUrl}/checkout", 'payload' => $payload]);

        $response = Http::withToken($this->apiToken)
            ->post("{$this->baseUrl}/checkout", $payload);

        Log::info('Tamara Checkout Response', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body()
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            return [
                'session_id' => $responseData['checkout_id'] ?? null,
                'order_id' => $responseData['order_id'] ?? null,
                'checkout_url' => $responseData['checkout_url'] ?? null,
                'raw_response' => $responseData
            ];
        }

        $errorBody = $response->json() ?? ['message' => $response->body()];
        Log::error('Tamara Checkout Failed', ['status' => $response->status(), 'response' => $errorBody]);

        $errorMessage = $errorBody['message'] ?? ($errorBody['error_message'] ?? 'Unknown error');
        throw new \Exception("Failed to initiate Tamara checkout: {$errorMessage}");
    }

    /**
     * Verify payment by authorising the order.
     * Called after customer completes payment on Tamara and is redirected back.
     *
     * @param string $orderId The Tamara order_id to authorise.
     * @return array Detailed payment status response.
     */
    public function verifyPayment(string $orderId): array
    {
        Log::info("Tamara: Authorising order {$orderId}");

        // Step 1: Try to authorise the order (required after customer approves)
        $response = Http::withToken($this->apiToken)
            ->post("{$this->baseUrl}/orders/{$orderId}/authorise");

        if ($response->successful()) {
            $data = $response->json();
            Log::info("Tamara: Order {$orderId} authorised successfully", ['response' => $data]);
            return array_merge($data, ['status' => $data['status'] ?? 'authorised']);
        }

        Log::warning("Tamara: Authorise failed for order {$orderId}", [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body()
        ]);

        // Step 2: If authorise fails (already authorised, cancelled, etc.), get order details
        $detailsResponse = Http::withToken($this->apiToken)
            ->get("{$this->baseUrl}/orders/{$orderId}");

        if ($detailsResponse->successful()) {
            $data = $detailsResponse->json();
            Log::info("Tamara: Order {$orderId} details fetched", ['status' => $data['status'] ?? 'unknown']);
            return $data;
        }

        Log::error("Tamara: Could not verify order {$orderId}", [
            'authorise_status' => $response->status(),
            'details_status' => $detailsResponse->status(),
        ]);

        throw new \Exception("Failed to verify Tamara payment for order {$orderId}");
    }

    /**
     * Get a simplified payment status string.
     *
     * @param string $orderId
     * @return string
     */
    public function getPaymentStatus(string $orderId): string
    {
        try {
            $data = $this->verifyPayment($orderId);
            return $data['status'] ?? 'unknown';
        } catch (\Exception $e) {
            Log::error("Tamara getPaymentStatus failed: " . $e->getMessage());
            return 'failed';
        }
    }

    /**
     * Prepare the payload for Tamara API checkout.
     * Follows Tamara official documentation structure.
     */
    protected function preparePayload(array $data): array
    {
        $currency = $data['currency'] ?? 'SAR';
        $amount = $data['amount'];

        // Build items array with required fields
        $items = [];
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $itemAmount = $item['total_amount']['amount'] ?? $item['unit_price'] ?? $amount;
                $items[] = [
                    'name' => $item['name'] ?? $item['title'] ?? 'Item',
                    'type' => $item['type'] ?? 'Digital',
                    'reference_id' => (string) ($item['reference_id'] ?? '1'),
                    'sku' => $item['sku'] ?? 'ITEM-' . ($item['reference_id'] ?? '1'),
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => [
                        'amount' => number_format((float)$itemAmount, 2, '.', ''),
                        'currency' => $currency,
                    ],
                    'total_amount' => [
                        'amount' => number_format((float)$itemAmount * ($item['quantity'] ?? 1), 2, '.', ''),
                        'currency' => $currency,
                    ],
                ];
            }
        }

        $firstName = $data['first_name'] ?? 'Guest';
        $lastName = $data['last_name'] ?? 'User';
        $phone = $data['customer_phone'] ?? '';
        $city = $data['city'] ?? 'Riyadh';
        $address = $data['address'] ?? 'Saudi Arabia';

        return [
            'total_amount' => [
                'amount' => number_format((float)$amount, 2, '.', ''),
                'currency' => $currency,
            ],
            'shipping_amount' => [
                'amount' => '0.00',
                'currency' => $currency,
            ],
            'tax_amount' => [
                'amount' => '0.00',
                'currency' => $currency,
            ],
            'order_reference_id' => $data['order_id'],
            'order_number' => $data['order_id'],
            'discount' => [
                'amount' => [
                    'amount' => '0.00',
                    'currency' => $currency,
                ],
                'name' => 'No discount',
            ],
            'items' => $items,
            'consumer' => [
                'email' => $data['customer_email'] ?? '',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => $phone,
            ],
            'country_code' => $data['country_code'] ?? 'SA',
            'description' => $data['description'] ?? 'Trip Booking Payment',
            'locale' => app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US',
            'merchant_url' => [
                'success' => $data['callback_url'] . '?status=success',
                'failure' => $data['callback_url'] . '?status=failure',
                'cancel' => $data['callback_url'] . '?status=cancel',
                'notification' => $data['notification_url'] ?? ($data['callback_url'] . '/webhook'),
            ],
            'payment_type' => $data['payment_type_tamara'] ?? 'PAY_BY_INSTALMENTS',
            'instalments' => $data['instalments'] ?? 3,
            'shipping_address' => [
                'city' => $city,
                'country_code' => $data['country_code'] ?? 'SA',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'line1' => $address,
                'phone_number' => $phone,
            ],
            'billing_address' => [
                'city' => $city,
                'country_code' => $data['country_code'] ?? 'SA',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'line1' => $address,
                'phone_number' => $phone,
            ],
        ];
    }
}
