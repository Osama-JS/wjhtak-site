<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Models\Payment;
use App\Services\HyperPayService;
use App\Services\TabbyPaymentService;
use App\Services\TamaraPaymentService;
use App\Services\InvoiceService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected $hyperPayService;
    protected $tabbyService;
    protected $tamaraService;
    protected $invoiceService;

    public function __construct(
        HyperPayService $hyperPayService,
        TabbyPaymentService $tabbyService,
        TamaraPaymentService $tamaraService,
        InvoiceService $invoiceService
    ) {
        $this->hyperPayService = $hyperPayService;
        $this->tabbyService = $tabbyService;
        $this->tamaraService = $tamaraService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Get Available Payment Methods
     */
    #[OA\Get(
        path: "/api/payment/methods",
        summary: "Get available payment methods",
        operationId: "getPaymentMethods",
        description: "Returns a list of active payment gateways and their configuration for the UI.",
        tags: ["Payment"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Payment methods retrieved."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "key", type: "string", example: "visa_master"),
                                new OA\Property(property: "name", type: "string", example: "Visa / MasterCard"),
                                new OA\Property(property: "type", type: "string", example: "card"),
                                new OA\Property(property: "icon", type: "string", example: "url_to_icon")
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function methods()
    {
        $methods = [
            [
                'key' => 'mada',
                'name' => __('Mada'),
                'type' => 'card',
                'icon' => asset('assets/img/payments/mada.png')
            ],
            [
                'key' => 'visa_master',
                'name' => __('Visa / MasterCard'),
                'type' => 'card',
                'icon' => asset('assets/img/payments/visa.png')
            ],
            [
                'key' => 'tabby',
                'name' => __('Tabby (Installments)'),
                'type' => 'redirect',
                'icon' => asset('assets/img/payments/tabby.png')
            ],
            [
                'key' => 'tamara',
                'name' => __('Tamara'),
                'type' => 'redirect',
                'icon' => asset('assets/img/payments/tamara.png')
            ],
            [
                'key' => 'apple_pay',
                'name' => __('Apple Pay'),
                'type' => 'digital_wallet',
                'icon' => asset('assets/img/payments/apple-pay.png')
            ]
        ];

        return $this->apiResponse(false, __('Payment methods retrieved successfully.'), $methods);
    }

    /**
     * Initiate Payment Checkout
     */
    #[OA\Post(
        path: "/api/payment/initiate",
        summary: "Initiate payment checkout (HyperPay, Tabby, Tamara)",
        operationId: "initiatePayment",
        description: "Initializes a payment session. For HyperPay, returns a Checkout ID. For Tabby/Tamara, returns a redirect URL.",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["booking_id", "payment_type"],
                properties: [
                    new OA\Property(property: "booking_id", type: "integer", example: 1, description: "ID of the trip booking to pay for"),
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay", "stc_pay", "tabby", "tamara"], example: "visa_master"),
                    // Custom fields for Tabby/Tamara override (optional)
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "phone", type: "string", example: "966500000000"),
                    new OA\Property(property: "email", type: "string", example: "john@example.com"),
                    new OA\Property(property: "callback_url", type: "string", example: "https://mysite.com/payment/callback"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Checkout initialized successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Checkout initialized successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "id", type: "string", description: "HyperPay Checkout ID", example: "B9C694..."),
                            new OA\Property(property: "session_id", type: "string", description: "Tabby/Tamara Session ID", example: "ch_..."),
                            new OA\Property(property: "checkout_url", type: "string", description: "Redirect URL for Tabby/Tamara", example: "https://checkout.tabby.ai/..."),
                            new OA\Property(property: "raw_response", type: "object", description: "Full gateway response for debugging")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation Error"),
            new OA\Response(response: 500, description: "Payment Gateway Error")
        ]
    )]
    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:trip_bookings,id',
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,tabby,stc_pay,tamara',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $user = $request->user();
            $booking = TripBooking::where('user_id', $user->id)->findOrFail($request->booking_id);

            // If already confirmed/paid (based on your logic)
            if ($booking->status === 'confirmed') {
                return $this->apiResponse(true, __('Booking is already confirmed/paid.'), null, null, 400);
            }

            $paymentType = $request->payment_type;

            // Handle Tabby
            if ($paymentType === 'tabby') {
                return $this->initiateTabby($request, $user, $booking);
            }

            // Handle Tamara
            if ($paymentType === 'tamara') {
                return $this->initiateTamara($request, $user, $booking);
            }

            // Handle HyperPay (Default)
            return $this->initiateHyperPay($request, $user, $booking);

        } catch (\Exception $e) {
            Log::error("Payment Initiation Error: " . $e->getMessage());
            return $this->apiResponse(true, __('Failed to initialize payment: ') . $e->getMessage(), null, null, 500);
        }
    }

    protected function initiateHyperPay(Request $request, $user, $booking)
    {
        $params = [
            'merchantTransactionId' => 'BOOKING-' . $booking->id . '-' . time(),
        ];

        if ($user->email) {
            $params['customer.email'] = $user->email;
        }

        $result = $this->hyperPayService->prepareCheckout(
            $booking->total_price,
            $request->payment_type,
            $params
        );

        if ($result && isset($result['id'])) {
            return $this->apiResponse(false, __('Checkout initialized successfully.'), $result);
        }

        throw new \Exception('HyperPay service returned error.');
    }

    protected function initiateTabby(Request $request, $user, $booking)
    {
        $data = [
            'amount' => $booking->total_price,
            'customer_name' => ($request->first_name && $request->last_name)
                                ? $request->first_name . ' ' . $request->last_name
                                : $user->full_name,
            'customer_email' => $request->email ?? $user->email,
            'customer_phone' => $request->phone ?? $user->phone,
            'order_id' => 'BOOKING-' . $booking->id . '-' . time(),
            'callback_url' => route('payment.callback', ['gateway' => 'tabby']),
            'items' => [
                [
                    'title' => $booking->trip ? $booking->trip->title : 'Trip Booking',
                    'quantity' => 1,
                    'unit_price' => $booking->total_price,
                ]
            ],
            'city' => $request->city ?? $user->city ?? 'Riyadh',
            'address' => $request->address ?? $user->address ?? 'Saudi Arabia',
        ];

        if (!$data['customer_email'] || !$data['customer_phone']) {
             throw new \Exception('Missing required customer data for Tabby (email, phone).');
        }

        $result = $this->tabbyService->initiateCheckout($data);

        return $this->apiResponse(false, __('Tabby checkout initialized.'), $result);
    }

    protected function initiateTamara(Request $request, $user, $booking)
    {
        $data = [
            'amount' => $booking->total_price,
            'customer_email' => $request->email ?? $user->email,
            'customer_phone' => $request->phone ?? $user->phone,
            'first_name' => $request->first_name ?? ($user->first_name ?? $user->full_name),
            'last_name' => $request->last_name ?? ($user->last_name ?? 'User'),
            'order_id' => 'BOOKING-' . $booking->id . '-' . time(),
            'callback_url' => route('payment.callback', ['gateway' => 'tamara']),
            'items' => [
                [
                    'name' => $booking->trip ? $booking->trip->title : 'Trip Booking',
                    'quantity' => 1,
                    'total_amount' => [
                        'amount' => $booking->total_price,
                        'currency' => 'SAR'
                    ],
                    'type' => 'Trip',
                    'reference_id' => (string) $booking->id
                ]
            ],
            'city' => $request->city ?? $user->city ?? 'Riyadh',
            'address' => $request->address ?? $user->address ?? 'Saudi Arabia',
        ];

         if (!$data['customer_email'] || !$data['customer_phone']) {
             throw new \Exception('Missing required customer data for Tamara (email, phone).');
        }

        $result = $this->tamaraService->initiateCheckout($data);

        return $this->apiResponse(false, __('Tamara checkout initialized.'), $result);
    }

    /**
     * Verify Payment Status
     */
    #[OA\Post(
        path: "/api/payment/verify",
        summary: "Verify payment status",
        operationId: "verifyPayment",
        description: "Checks the status of a payment and confirms the booking if successful.",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["payment_type"],
                properties: [
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay", "tabby", "tamara"]),
                    new OA\Property(property: "checkout_id", type: "string", description: "Required for HyperPay (mada, visa_master, apple_pay)"),
                    new OA\Property(property: "payment_id", type: "string", description: "Required for Tabby/Tamara"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Payment successful and booking confirmed",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Payment successful and booking confirmed."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "status", type: "string", example: "paid"),
                            new OA\Property(property: "transaction_id", type: "string", example: "T123456789"),
                            new OA\Property(property: "invoice_url", type: "string", example: "https://mytrip.com/storage/invoices/inv_1.pdf", description: "Direct URL to the generated PDF invoice"),
                            new OA\Property(property: "payment_method", type: "string", example: "mada", description: "The specific payment method used"),
                            new OA\Property(property: "raw_response", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Payment Failed or Pending"),
            new OA\Response(response: 500, description: "Server Error")
        ]
    )]
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,tabby,tamara',
            'checkout_id' => 'required_if:payment_type,mada,visa_master,apple_pay', // HyperPay
            'payment_id' => 'required_if:payment_type,tabby,tamara', // Tabby/Tamara ID
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $type = $request->payment_type;

            if ($type === 'tabby') {
                $result = $this->tabbyService->verifyPayment($request->payment_id);
                $status = $result['status'] ?? 'unknown';

                if ($status == 'authorized' || $status == 'closed') {
                     // Extract booking ID from reference_id (BOOKING-ID-TIME)
                     $reference = $result['order']['reference_id'] ?? '';
                     $bookingId = explode('-', $reference)[1] ?? null;

                     if ($bookingId) {
                         $invoicePath = $this->completePayment($bookingId, 'tabby', $request->payment_id, $result, $request->payment_type);
                         return $this->apiResponse(false, __('Payment successful and booking confirmed.'), [
                             'status' => 'paid',
                             'transaction_id' => $request->payment_id,
                             'invoice_url' => $invoicePath ? asset('storage/' . $invoicePath) : null,
                             'raw_response' => $result
                         ]);
                     }
                }
                return $this->apiResponse(true, __('Payment failed or pending.'), $result, null, 400);
            }

            if ($type === 'tamara') {
                $result = $this->tamaraService->verifyPayment($request->payment_id);
                $status = $result['status'] ?? 'unknown';

                 if ($status == 'authorised' || $status == 'fully_captured') {
                     $reference = $result['order_reference_id'] ?? '';
                     $bookingId = explode('-', $reference)[1] ?? null;

                     if ($bookingId) {
                        $invoicePath = $this->completePayment($bookingId, 'tamara', $request->payment_id, $result, $request->payment_type);
                        return $this->apiResponse(false, __('Payment successful and booking confirmed.'), [
                            'status' => 'paid',
                            'transaction_id' => $request->payment_id,
                            'invoice_url' => $invoicePath ? asset('storage/' . $invoicePath) : null,
                            'raw_response' => $result
                        ]);
                     }
                }
                return $this->apiResponse(true, __('Payment failed or pending.'), $result, null, 400);
            }

            // HyperPay Logic
            return $this->verifyHyperPay($request);

        } catch (\Exception $e) {
             return $this->apiResponse(true, $e->getMessage(), null, null, 500);
        }
    }

    protected function verifyHyperPay(Request $request)
    {
        $result = $this->hyperPayService->getPaymentStatus($request->checkout_id, $request->payment_type);

        if ($result && isset($result['result']['code'])) {
            $isSuccess = $this->hyperPayService->isSuccessful($result['result']['code']);

            if ($isSuccess) {
                $reference = $result['merchantTransactionId'] ?? '';
                $bookingId = explode('-', $reference)[1] ?? null;

                if ($bookingId) {
                    $invoicePath = $this->completePayment($bookingId, 'hyperpay', $request->checkout_id, $result, $request->payment_type);
                    return $this->apiResponse(false, __('Payment successful and booking confirmed.'), [
                        'status' => 'paid',
                        'transaction_id' => $request->checkout_id,
                        'invoice_url' => $invoicePath ? asset('storage/' . $invoicePath) : null,
                        'raw_response' => $result
                    ]);
                }
            }

            return $this->apiResponse(true, $result['result']['description'] ?? __('Payment failed.'), $result, null, 400);
        }

        throw new \Exception('HyperPay verification failed.');
    }

    protected function completePayment($bookingId, $gateway, $transactionId, $response, $paymentMethod = null)
    {
        $booking = TripBooking::with('trip')->find($bookingId);
        $invoicePath = null;

        if ($booking) {
            // Check if already confirmed (idempotency)
            if ($booking->status === 'confirmed') {
                return Payment::where('trip_booking_id', $bookingId)->first()->invoice_path ?? null;
            }

            // Deduct tickets from trip
            if ($booking->trip) {
                if ($booking->trip->tickets < $booking->tickets_count) {
                    Log::error("Overbooking detected for Trip #{$booking->trip->id}. Booking #{$bookingId} paid but tickets unavailable.");
                    // We still record the payment since money was taken, but maybe status should be 'overflow' or similar
                    // For now, we confirm but log a critical error for admin
                }

                $newCount = max(0, $booking->trip->tickets - $booking->tickets_count);
                $booking->trip->update(['tickets' => $newCount]);
                Log::info("Trip #{$booking->trip->id} tickets updated to {$newCount}");
            }

            // Update Booking
            $booking->update([
                'status' => 'confirmed',
                'updated_at' => now(),
            ]);

            // Record Payment
            $paymentData = [
                'trip_booking_id' => $booking->id,
                'payment_gateway' => $gateway,
                'transaction_id' => $transactionId,
                'amount' => $booking->total_price,
                'currency' => 'SAR',
                'status' => 'paid',
                'raw_response' => $response,
            ];

            try {
                $paymentData['payment_method'] = $paymentMethod;
                $payment = Payment::create($paymentData);
            } catch (\Exception $e) {
                unset($paymentData['payment_method']);
                $payment = Payment::create($paymentData);
                Log::warning("Could not save payment_method: " . $e->getMessage());
            }

            // Generate Invoice
            $invoicePath = $this->invoiceService->generateInvoice($booking);
            if ($invoicePath) {
                $payment->update(['invoice_path' => $invoicePath]);
            }

            Log::info("Booking #{$bookingId} paid via {$gateway}. Transaction: {$transactionId}");
        }

        return $invoicePath;
    }

    /**
     * Centralized callback handler that provides a server-side landing page
     */
    public function handleCallback(Request $request)
    {
        $gateway = $request->gateway;
        $status = $request->status;

        // For Tabby, the payment ID is payment_id. For Tamara, it might be tap_id or similar.
        // We will pass identifying info in the URL so the app can read it.
        $paymentId = $request->payment_id ?? $request->tap_id ?? $request->id;

        return response("
            <!DOCTYPE html>
            <html lang='ar' dir='rtl'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>معالجة الدفع...</title>
                <style>
                    body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f8fafc; color: #1e293b; text-align: center; }
                    .card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
                    .loader { border: 4px solid #f3f3f3; border-top: 4px solid #4f46e5; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 20px auto; }
                    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                </style>
            </head>
            <body>
                <div class='card'>
                    <div class='loader'></div>
                    <h2>يتم معالجة عملية الدفع...</h2>
                    <p>يرجى الانتظار، سيتم توجيهك إلى التطبيق تلقائياً.</p>
                    <p style='font-size: 0.8rem; color: #64748b;'>ID: {$paymentId}</p>
                </div>
                <script>
                    // This script is a fallback. The mobile app should intercept the URL before/at this point.
                    console.log('Payment ID identified: {$paymentId}');
                </script>
            </body>
            </html>
        ")->header('Content-Type', 'text/html');
    }
}
