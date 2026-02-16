<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TripBooking;
use App\Models\Payment;
use App\Services\HyperPayService;
use App\Services\TabbyPaymentService;
use App\Services\TamaraPaymentService;
use App\Services\InvoiceService;
use App\Services\NotificationService;
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
    protected $notificationService;

    public function __construct(
        HyperPayService $hyperPayService,
        TabbyPaymentService $tabbyService,
        TamaraPaymentService $tamaraService,
        InvoiceService $invoiceService,
        NotificationService $notificationService
    ) {
        $this->hyperPayService = $hyperPayService;
        $this->tabbyService = $tabbyService;
        $this->tamaraService = $tamaraService;
        $this->invoiceService = $invoiceService;
        $this->notificationService = $notificationService;
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
        description: "Initializes a payment session.\n\n**HyperPay (mada, visa_master, apple_pay):** Returns a `checkout_id` to load Payment Widget in WebView. Billing address and customer data are auto-filled from user profile but can be overridden.\n\n**Tabby/Tamara:** Returns a `checkout_url` to redirect the user.",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "Response language: ar (Arabic) or en (English). Affects error messages.",
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
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay", "stc_pay", "tabby", "tamara"], example: "visa_master", description: "Payment method key"),
                    new OA\Property(property: "first_name", type: "string", example: "Mohammed", description: "Optional - overrides user profile first name"),
                    new OA\Property(property: "last_name", type: "string", example: "Ali", description: "Optional - overrides user profile last name"),
                    new OA\Property(property: "email", type: "string", example: "user@example.com", description: "Optional - overrides user profile email"),
                    new OA\Property(property: "phone", type: "string", example: "966500000000", description: "Optional - for Tabby/Tamara"),
                    new OA\Property(property: "address", type: "string", example: "King Fahd Road", description: "Optional - billing street address for HyperPay 3DS2"),
                    new OA\Property(property: "city", type: "string", example: "Riyadh", description: "Optional - billing city for HyperPay 3DS2"),
                    new OA\Property(property: "state", type: "string", example: "Riyadh", description: "Optional - billing state for HyperPay 3DS2"),
                    new OA\Property(property: "postcode", type: "string", example: "12345", description: "Optional - billing postcode for HyperPay 3DS2"),
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
                        new OA\Property(property: "data", type: "object", description: "Response varies by gateway", properties: [
                            new OA\Property(property: "id", type: "string", description: "HyperPay Checkout ID — use this to load paymentWidgets.js", example: "A1B2C3D4E5.uat"),
                            new OA\Property(property: "session_id", type: "string", description: "Tabby/Tamara Session ID", example: "ch_..."),
                            new OA\Property(property: "checkout_url", type: "string", description: "Redirect URL (Tabby/Tamara only)", example: "https://checkout.tabby.ai/..."),
                            new OA\Property(property: "raw_response", type: "object", description: "Full gateway response")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Booking already confirmed"),
            new OA\Response(response: 422, description: "Validation Error (missing booking_id or payment_type)"),
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

        // Build customer and billing params (required by HyperPay for 3DS2)
        $customerParams = $this->hyperPayService->buildCustomerParams([
            'email' => $request->email ?? $user->email,
            'first_name' => $request->first_name ?? ($user->first_name ?? $user->full_name),
            'last_name' => $request->last_name ?? ($user->last_name ?? 'User'),
            'street' => $request->address ?? $user->address ?? 'Not Provided',
            'city' => $request->city ?? $user->city ?? 'Riyadh',
            'state' => $request->state ?? 'Riyadh',
            'country' => 'SA',
            'postcode' => $request->postcode ?? '00000',
        ]);

        $params = array_merge($params, $customerParams);

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
        summary: "Verify payment status, capture, and confirm booking",
        operationId: "verifyPayment",
        description: "Checks the payment status with the gateway. If successful, confirms the booking, deducts tickets, records the payment, and generates an invoice.\n\n**Error/success messages** are translated based on `Accept-Language` header.\n\n### Gateway-specific behavior:\n- **HyperPay (mada, visa_master, apple_pay):** Send `checkout_id`. Checks result code.\n- **Tabby:** Send `payment_id`. Auto-captures if AUTHORIZED → CLOSED.\n- **Tamara:** Send `payment_id` (order_id). Auto-authorises if approved.",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "Response language: ar or en. Controls success/error messages.",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["payment_type"],
                properties: [
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay", "stc_pay", "tabby", "tamara"], description: "Must match the type used in /initiate"),
                    new OA\Property(property: "checkout_id", type: "string", example: "A1B2C3D4E5.uat", description: "Required for HyperPay — the id returned from /initiate"),
                    new OA\Property(property: "payment_id", type: "string", example: "pay_abc123", description: "Required for Tabby (payment_id from callback) / Tamara (order_id from callback)"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Payment successful — booking confirmed, tickets deducted, invoice generated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "تمت عملية الدفع بنجاح!", description: "Translated success message"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "status", type: "string", example: "paid"),
                            new OA\Property(property: "transaction_id", type: "string", example: "A1B2C3D4E5.uat", description: "checkout_id (HyperPay) or payment_id (Tabby/Tamara)"),
                            new OA\Property(property: "invoice_url", type: "string", example: "https://site.com/storage/invoices/inv_42.pdf"),
                            new OA\Property(property: "raw_response", type: "object", description: "Full gateway response")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Payment failed — translated error message",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "فشلت عملية الدفع. يرجى المحاولة مرة أخرى.", description: "User-friendly translated error"),
                        new OA\Property(property: "data", type: "object", description: "Structure varies by gateway", properties: [
                            new OA\Property(property: "result_code", type: "string", example: "100.100.303", description: "HyperPay only — result code for debugging"),
                            new OA\Property(property: "description", type: "string", example: "insufficient funds", description: "HyperPay only — original English description"),
                            new OA\Property(property: "payment_status", type: "string", example: "REJECTED", description: "Tabby/Tamara — gateway payment status (REJECTED, EXPIRED, etc.)"),
                            new OA\Property(property: "raw_response", type: "object", description: "Full gateway response")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation Error — missing payment_type or checkout_id/payment_id"),
            new OA\Response(response: 500, description: "Server Error — gateway unreachable or internal failure")
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
                // verifyPayment now auto-captures AUTHORIZED → CLOSED
                $result = $this->tabbyService->verifyPayment($request->payment_id);
                $status = strtoupper($result['status'] ?? 'UNKNOWN');

                if ($status === 'AUTHORIZED' || $status === 'CLOSED') {
                     // Extract booking ID from reference_id (BOOKING-ID-TIME)
                     $reference = $result['order']['reference_id'] ?? '';
                     $bookingId = explode('-', $reference)[1] ?? null;

                     if ($bookingId) {
                         $invoicePath = $this->completePayment($bookingId, 'tabby', $request->payment_id, $result, $request->payment_type);
                         return $this->apiResponse(false, __('payment.success'), [
                             'status' => 'paid',
                             'transaction_id' => $request->payment_id,
                             'invoice_url' => $invoicePath ? asset('storage/' . $invoicePath) : null,
                             'raw_response' => $result
                         ]);
                     }
                }

                // Tabby payment failed — send notification
                $tabbyRef = $result['order']['reference_id'] ?? '';
                $tabbyBookingId = explode('-', $tabbyRef)[1] ?? null;
                if ($tabbyBookingId) {
                    $failedBooking = TripBooking::with(['user', 'trip'])->find($tabbyBookingId);
                    if ($failedBooking && $failedBooking->user) {
                        $this->notificationService->sendToUser(
                            $failedBooking->user,
                            Notification::TYPE_PAYMENT_FAILED,
                            __('Payment Failed'),
                            __('Your payment for ":trip" was not completed. Please try again.', [
                                'trip' => $failedBooking->trip->title ?? __('Trip'),
                            ]),
                            [
                                'booking_id' => (string) $failedBooking->id,
                                'gateway' => 'tabby',
                                'status' => $status,
                            ]
                        );
                    }
                }

                return $this->apiResponse(true, __('payment.general_failure'), [
                    'payment_status' => $status,
                    'raw_response' => $result,
                ], null, 400);
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

                // Tamara payment failed — send notification
                $tamaraRef = $result['order_reference_id'] ?? '';
                $tamaraBookingId = explode('-', $tamaraRef)[1] ?? null;
                if ($tamaraBookingId) {
                    $failedBooking = TripBooking::with(['user', 'trip'])->find($tamaraBookingId);
                    if ($failedBooking && $failedBooking->user) {
                        $this->notificationService->sendToUser(
                            $failedBooking->user,
                            Notification::TYPE_PAYMENT_FAILED,
                            __('Payment Failed'),
                            __('Your payment for ":trip" was not completed. Please try again.', [
                                'trip' => $failedBooking->trip->title ?? __('Trip'),
                            ]),
                            [
                                'booking_id' => (string) $failedBooking->id,
                                'gateway' => 'tamara',
                                'status' => $status,
                            ]
                        );
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
            $resultCode = $result['result']['code'];
            $isSuccess = $this->hyperPayService->isSuccessful($resultCode);
            $userMessage = $this->hyperPayService->getUserFriendlyMessage($resultCode);

            if ($isSuccess) {
                $reference = $result['merchantTransactionId'] ?? '';
                $bookingId = explode('-', $reference)[1] ?? null;

                if ($bookingId) {
                    $invoicePath = $this->completePayment($bookingId, 'hyperpay', $request->checkout_id, $result, $request->payment_type);
                    return $this->apiResponse(false, $userMessage, [
                        'status' => 'paid',
                        'transaction_id' => $request->checkout_id,
                        'invoice_url' => $invoicePath ? asset('storage/' . $invoicePath) : null,
                        'raw_response' => $result
                    ]);
                }
            }

            return $this->apiResponse(true, $userMessage, [
                'result_code' => $resultCode,
                'description' => $result['result']['description'] ?? null,
                'raw_response' => $result,
            ], null, 400);
        }

        // HyperPay failure — send notification
        if (isset($result['merchantTransactionId'])) {
            $hpRef = $result['merchantTransactionId'] ?? '';
            $hpBookingId = explode('-', $hpRef)[1] ?? null;
            if ($hpBookingId) {
                $failedBooking = TripBooking::with(['user', 'trip'])->find($hpBookingId);
                if ($failedBooking && $failedBooking->user) {
                    $this->notificationService->sendToUser(
                        $failedBooking->user,
                        Notification::TYPE_PAYMENT_FAILED,
                        __('Payment Failed'),
                        __('Your payment for ":trip" was not completed. Please try again.', [
                            'trip' => $failedBooking->trip->title ?? __('Trip'),
                        ]),
                        [
                            'booking_id' => (string) $failedBooking->id,
                            'gateway' => 'hyperpay',
                            'result_code' => $resultCode ?? '',
                        ]
                    );
                }
            }
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

            // Send payment success + booking confirmed notifications
            if ($booking->user) {
                $tripTitle = $booking->trip ? $booking->trip->title : __('Trip');

                // Payment success notification
                $this->notificationService->sendToUser(
                    $booking->user,
                    Notification::TYPE_PAYMENT_SUCCESS,
                    __('Payment Successful'),
                    __('Your payment of :amount SAR for ":trip" has been confirmed.', [
                        'amount' => $booking->total_price,
                        'trip' => $tripTitle,
                    ]),
                    [
                        'booking_id' => (string) $booking->id,
                        'trip_id' => (string) ($booking->trip_id ?? ''),
                        'amount' => (string) $booking->total_price,
                        'gateway' => $gateway,
                    ]
                );

                // Booking confirmed notification
                $this->notificationService->sendToUser(
                    $booking->user,
                    Notification::TYPE_BOOKING_CONFIRMED,
                    __('Booking Confirmed'),
                    __('Your booking for ":trip" has been confirmed. Enjoy your trip!', [
                        'trip' => $tripTitle,
                    ]),
                    [
                        'booking_id' => (string) $booking->id,
                        'trip_id' => (string) ($booking->trip_id ?? ''),
                        'tickets_count' => (string) $booking->tickets_count,
                    ]
                );
            }
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
