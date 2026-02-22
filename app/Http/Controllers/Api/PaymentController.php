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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Traits\PaymentLogTrait;
use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    use ApiResponseTrait, PaymentLogTrait;

    protected $hyperPayService;
    protected $tabbyService;
    protected $tamaraService;
    protected $tapService;
    protected $invoiceService;
    protected $notificationService;

    public function __construct(
        HyperPayService $hyperPayService,
        TabbyPaymentService $tabbyService,
        TamaraPaymentService $tamaraService,
        \App\Services\TapPaymentService $tapService,
        InvoiceService $invoiceService,
        NotificationService $notificationService
    ) {
        $this->hyperPayService = $hyperPayService;
        $this->tabbyService = $tabbyService;
        $this->tamaraService = $tamaraService;
        $this->tapService = $tapService;
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
        summary: "Initiate payment checkout (WebView Flow)",
        operationId: "initiatePayment",
        description: "Initializes a payment session and returns a URL to be opened in a WebView.\n\nThe backend will handle all gateway communications and user data pre-filling. The app developer should monitor the WebView URL to detect final success/failure: \n\n- **Success URL pattern:** `/payments/success?booking_id={id}`\n- **Failure URL pattern:** `/payments/failure?error={msg}`",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["booking_id", "payment_type"],
                properties: [
                    new OA\Property(property: "booking_id", type: "integer", example: 1, description: "ID of the trip booking to pay for"),
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay", "tabby", "tamara"], example: "visa_master", description: "Payment method key"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Checkout URL generated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Checkout link generated."),
                        new OA\Property(property: "payment_url", type: "string", description: "URL to open in a WebView for payment completion", example: "https://domain.com/payments/checkout/1/visa_master")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Booking already confirmed"),
            new OA\Response(response: 422, description: "Validation Error"),
            new OA\Response(response: 500, description: "Server Error")
        ]
    )]
    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:trip_bookings,id',
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,tabby,stc_pay,tamara,tap',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $user = $request->user();
            $booking = TripBooking::where('user_id', $user->id)->findOrFail($request->booking_id);

            // If already confirmed/paid
            if ($booking->status === 'confirmed') {
                return $this->apiResponse(true, __('Booking is already confirmed/paid.'), null, null, 400);
            }

            // Generate the WebView URL
            $paymentUrl = route('payments.web.checkout', [
                'booking_id' => $booking->id,
                'method' => $request->payment_type
            ]);

            return $this->apiResponse(false, __('Checkout link generated successfully.'), [
                'payment_url' => $paymentUrl
            ]);

        } catch (\Exception $e) {
            Log::error("Payment Initiation Error: " . $e->getMessage());
            return $this->apiResponse(true, __('Failed to generate payment link: ') . $e->getMessage(), null, null, 500);
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
            $this->logPendingPayment($booking->id, 'hyperpay', $request->payment_type, $result['id'], $booking->total_price, $result);
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

        if ($result['payment_id'] ?? null) {
            $this->logPendingPayment($booking->id, 'tabby', 'installments', $result['payment_id'], $booking->total_price, $result);
        }

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

        if ($result['order_id'] ?? null) {
            $this->logPendingPayment($booking->id, 'tamara', 'installments', $result['order_id'], $booking->total_price, $result);
        }

        return $this->apiResponse(false, __('Tamara checkout initialized.'), $result);
    }

    protected function initiateTap(Request $request, $user, $booking)
    {
        $data = [
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'customer_email' => $request->email ?? $user->email,
            'customer_phone' => $request->phone ?? $user->phone,
            'first_name' => $request->first_name ?? ($user->first_name ?? $user->full_name),
            'last_name' => $request->last_name ?? ($user->last_name ?? 'User'),
            'order_id' => 'BOOKING-' . $booking->id . '-' . time(),
            'callback_url' => route('payment.callback', ['gateway' => 'tap']),
            'description' => 'Booking #' . $booking->id . ' - ' . ($booking->trip->title ?? 'Trip'),
        ];

        $result = $this->tapService->initiateCheckout($data);

        if ($result['id'] ?? null) {
            $this->logPendingPayment($booking->id, 'tap', 'card', $result['id'], $booking->total_price, $result);
        }

        return $this->apiResponse(false, __('Tap checkout initialized.'), $result);
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
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,tabby,tamara,tap',
            'id' => 'required_without_all:checkout_id,payment_id', // Fallback for WebView
            'checkout_id' => 'required_without_all:id,payment_id|required_if:payment_type,mada,visa_master,apple_pay',
            'payment_id' => 'required_without_all:id,checkout_id|required_if:payment_type,tabby,tamara',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $id = $request->id ?? $request->checkout_id ?? $request->payment_id;

        try {
            $type = $request->payment_type;

            if ($type === 'tabby') {
                $result = $this->tabbyService->verifyPayment($id);
                $status = strtoupper($result['status'] ?? 'UNKNOWN');

                if ($status === 'AUTHORIZED' || $status === 'CLOSED') {
                     // Extract booking ID from reference_id (BOOKING-ID-TIME)
                     $reference = $result['order']['reference_id'] ?? '';
                     $bookingId = explode('-', $reference)[1] ?? null;

                     if ($bookingId) {
                         $this->completePayment($bookingId, 'tabby', $id, $result, $request->payment_type);
                         return $this->apiResponse(false, __('payment.success'), [
                             'status' => 'paid',
                             'booking_id' => $bookingId,
                             'transaction_id' => $id,
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

            if ($type === 'tap') {
                $result = $this->tapService->verifyPayment($id);
                $status = strtoupper($result['status'] ?? 'UNKNOWN');

                if ($status === 'CAPTURED' || $status === 'AUTHORIZED') {
                     $bookingId = $result['metadata']['booking_id'] ?? explode('-', $result['reference']['order'] ?? '')[1] ?? null;

                     if ($bookingId) {
                         $this->completePayment($bookingId, 'tap', $id, $result, $request->payment_type);
                         return $this->apiResponse(false, __('Payment successful and booking confirmed.'), [
                             'status' => 'paid',
                             'booking_id' => (int) $bookingId,
                             'transaction_id' => $id,
                             'raw_response' => $result
                         ]);
                     }
                }

                return $this->apiResponse(true, __('Payment failed or was declined.'), [
                    'payment_status' => $status,
                    'raw_response' => $result
                ], null, 400);
            }

            if ($type === 'tamara') {
                $result = $this->tamaraService->verifyPayment($request->payment_id);
                $status = $result['status'] ?? 'unknown';

                 if ($status == 'authorised' || $status == 'fully_captured') {
                     $reference = $result['order_reference_id'] ?? '';
                     $bookingId = explode('-', $reference)[1] ?? null;

                      if ($bookingId) {
                         $this->completePayment($bookingId, 'tamara', $id, $result, $request->payment_type);
                         return $this->apiResponse(false, __('Payment successful and booking confirmed.'), [
                             'status' => 'paid',
                             'booking_id' => $bookingId,
                             'transaction_id' => $id,
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
        $id = $request->id ?? $request->checkout_id;
        $result = $this->hyperPayService->getPaymentStatus($id, $request->payment_type);

        if ($result && isset($result['result']['code'])) {
            $resultCode = $result['result']['code'];
            $isSuccess = $this->hyperPayService->isSuccessful($resultCode);
            $userMessage = $this->hyperPayService->getUserFriendlyMessage($resultCode);

            if ($isSuccess) {
                $reference = $result['merchantTransactionId'] ?? '';
                $bookingId = explode('-', $reference)[1] ?? null;

                if ($bookingId) {
                    $this->completePayment($bookingId, 'hyperpay', $id, $result, $request->payment_type);
                    return $this->apiResponse(false, $userMessage, [
                        'status' => 'paid',
                        'booking_id' => $bookingId,
                        'transaction_id' => $id,
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

            // Record or Update Payment
            $payment = Payment::updateOrCreate(
                ['transaction_id' => $transactionId],
                [
                    'trip_booking_id' => $booking->id,
                    'payment_gateway' => $gateway,
                    'payment_method' => $paymentMethod ?? 'unknown',
                    'amount' => $booking->total_price,
                    'currency' => 'SAR',
                    'status' => 'paid',
                    'raw_response' => $response,
                ]
            );

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
