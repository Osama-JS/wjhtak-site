<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TripBooking;
use App\Models\HotelBooking;
use App\Models\HotelBookingHistory;
use App\Models\Payment;
use App\Models\BankTransfer;
use App\Services\HyperPayService;
use App\Services\TabbyPaymentService;
use App\Services\TamaraPaymentService;
use App\Services\TBOHotelService;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
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
    protected $tboHotelService;

    public function __construct(
        HyperPayService $hyperPayService,
        TabbyPaymentService $tabbyService,
        TamaraPaymentService $tamaraService,
        \App\Services\TapPaymentService $tapService,
        InvoiceService $invoiceService,
        NotificationService $notificationService,
        TBOHotelService $tboHotelService
    ) {
        $this->hyperPayService = $hyperPayService;
        $this->tabbyService = $tabbyService;
        $this->tamaraService = $tamaraService;
        $this->tapService = $tapService;
        $this->invoiceService = $invoiceService;
        $this->notificationService = $notificationService;
        $this->tboHotelService = $tboHotelService;
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
                'key' => 'tamara',
                'name' => __('Tamara'),
                'type' => 'redirect',
                'icon' => asset('assets/img/payments/tamara.png')
            ],

            [
                'key' => 'bank_transfer',
                'name' => __('Bank Transfer'),
                'type' => 'manual',
                'icon' => asset('assets/img/payments/bank-transfer.png')
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
    public function initiate(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:trip_bookings,id',
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,stc_pay,tamara,tabby,tap',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $user = $request->user();
            $booking = TripBooking::where('user_id', $user->id)->findOrFail($request->booking_id);

            // If already confirmed/paid or not pending
            if ($booking->status !== 'pending') {
                return $this->apiResponse(true, __('Booking is not pending payment or has already been paid.'), null, null, 400);
            }

            // Generate the WebView URL
            $paymentUrl = route('payments.web.checkout', [
                'booking_id' => $booking->id,
                'method' => $request->payment_type,
                'source' => 'api'
            ]);

            return $this->apiResponse(false, __('Checkout link generated successfully.'), [
                'payment_url' => $paymentUrl
            ]);

        } catch (\Exception $e) {
            Log::error("Payment Initiation Error: " . $e->getMessage());
            return $this->apiResponse(true, __('Failed to generate payment link: ') . $e->getMessage(), null, null, 500);
        }
    }

    #[OA\Post(
        path: '/api/payment/bank-transfer',
        summary: 'Submit a new bank transfer receipt',
        description: "Allows the mobile app user to upload their bank transfer receipt for a specific booking.\n\n**Instructions for Mobile Devs:**\n1. Display the 'bank_transfer' method in the checkout based on the `/api/payment/methods` API.\n2. When selected by the user, present a form to upload the receipt image/pdf, enter sender name, and optional receipt number.\n3. Send the data to this endpoint via `multipart/form-data`.\n4. If successful, the API returns a success message and sets the booking payment status to 'pending verification'. Admin will manually approve/reject.\n5. You do NOT need to call `/api/payment/initiate` or `/api/payment/verify` for Bank Transfers.",
        tags: ['Payment'],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['booking_id', 'receipt_image', 'sender_name'],
                    properties: [
                        new OA\Property(property: 'booking_id', type: 'integer', description: 'The Trip Booking ID'),
                        new OA\Property(property: 'receipt_image', type: 'string', format: 'binary', description: 'Image or PDF of the bank receipt'),
                        new OA\Property(property: 'sender_name', type: 'string', description: 'Name of the sender on the bank account'),
                        new OA\Property(property: 'receipt_number', type: 'string', description: 'Optional reference number'),
                        new OA\Property(property: 'notes', type: 'string', description: 'Any extra notes'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Bank transfer submitted successfully"),
            new OA\Response(response: 400, description: "Booking already confirmed"),
            new OA\Response(response: 422, description: "Validation Error"),
            new OA\Response(response: 500, description: "Server Error")
        ]
    )]
    public function submitBankTransfer(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:trip_bookings,id',
            'receipt_image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'sender_name' => 'required|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $user = $request->user();
            $booking = TripBooking::where('user_id', $user->id)->findOrFail($request->booking_id);

            // Check if already paid or under review
            if ($booking->status !== 'pending' && $booking->status !== 'failed') {
                return $this->apiResponse(true, __('Booking is already paid or under review.'), null, null, 400);
            }

            // Handle File Upload
            $path = $request->file('receipt_image')->store('bank_transfers', 'public');

            // Create record
            $bankTransfer = BankTransfer::create([
                'trip_booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'receipt_number' => $request->receipt_number,
                'sender_name' => $request->sender_name,
                'receipt_image' => $path,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            \App\Models\BookingHistory::create([
                'trip_booking_id' => $booking->id,
                'user_id' => $user->id,
                'action' => 'bank_transfer_submitted',
                'description' => __('Customer submitted bank transfer receipt.'),
                'previous_state' => null,
                'new_state' => TripBooking::STATE_AWAITING_PAYMENT,
            ]);

            return $this->apiResponse(false, __('Bank transfer submitted successfully. It will be reviewed by admin soon.'), $bankTransfer);

        } catch (\Exception $e) {
            Log::error("Bank Transfer Submission Error: " . $e->getMessage());
            return $this->apiResponse(true, __('Failed to submit transfer: ') . $e->getMessage(), null, null, 500);
        }
    }

    protected function initiateHyperPay(\Illuminate\Http\Request $request, $user, $booking)
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

    protected function initiateTabby(\Illuminate\Http\Request $request, $user, $booking)
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

    protected function initiateTamara(\Illuminate\Http\Request $request, $user, $booking)
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

    protected function initiateTap(\Illuminate\Http\Request $request, $user, $booking)
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
    public function verify(\Illuminate\Http\Request $request)
    {
        Log::info('Payment verification started', $request->all());
        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay,tamara',
            'id' => 'required_without_all:checkout_id,payment_id', // Fallback for WebView
            'checkout_id' => 'required_without_all:id,payment_id|required_if:payment_type,mada,visa_master,apple_pay',
            'payment_id' => 'required_without_all:id,checkout_id|required_if:payment_type,tamara',
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

    protected function verifyHyperPay(\Illuminate\Http\Request $request)
    {
        Log::info('Verifying HyperPay payment', ['id' => $request->id ?? $request->checkout_id, 'type' => $request->payment_type]);
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
            } else {
                // Record failed attempt
                $reference = $result['merchantTransactionId'] ?? '';
                $bookingId = explode('-', $reference)[1] ?? null;
                if ($bookingId) {
                    Payment::updateOrCreate(
                        ['transaction_id' => $id],
                        [
                            'trip_booking_id' => $bookingId,
                            'payment_gateway' => 'hyperpay',
                            'payment_method' => $request->payment_type ?? 'unknown',
                            'amount' => $result['amount'] ?? 0,
                            'currency' => $result['currency'] ?? 'SAR',
                            'status' => 'failed',
                            'raw_response' => $result,
                        ]
                    );
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

                $newCount = max(0, (int)$booking->trip->tickets - (int)$booking->tickets_count);
                $booking->trip->update(['tickets' => $newCount]);
                Log::info("Trip #{$booking->trip->id} tickets updated to {$newCount}");
            }

            // Update Booking
            $booking->update([
                'status' => 'confirmed',
                'booking_state' => TripBooking::STATE_PREPARING,
                'updated_at' => now(),
            ]);

            // Create Booking History
            \App\Models\BookingHistory::create([
                'trip_booking_id' => $booking->id,
                'user_id' => null, // System action
                'action' => 'payment_confirmed',
                'description' => __('Payment successful via :gateway. Booking state set to Preparing.', ['gateway' => $gateway]),
                'previous_state' => null,
                'new_state' => TripBooking::STATE_PREPARING,
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
    public function handleCallback(\Illuminate\Http\Request $request)
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

    // =========================================================
    // Hotel Payment — Initiate
    // =========================================================

    #[OA\Post(
        path: '/api/payment/hotel/initiate',
        summary: 'Initiate hotel booking payment (WebView Flow)',
        operationId: 'initiateHotelPayment',
        description: "Generates a checkout WebView URL for hotel booking payment.\n\n**Flow:**\n1. Call `POST /api/hotels/book` → get `hotel_booking_id`\n2. Call this endpoint → get `payment_url`\n3. Open `payment_url` in WebView\n4. Monitor URL for success/failure pattern\n5. On success, call `/api/payment/hotel/verify`\n\n- **Success URL:** `/payments/success?hotel_booking_id={id}`\n- **Failure URL:** `/payments/failure?error={msg}`",
        tags: ['Hotels', 'Payment'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['hotel_booking_id', 'payment_type'],
                properties: [
                    new OA\Property(property: 'hotel_booking_id', type: 'integer', description: 'ID from /api/hotels/book'),
                    new OA\Property(property: 'payment_type', type: 'string', enum: ['mada', 'visa_master', 'apple_pay', 'tamara', 'tabby', 'tap'], example: 'visa_master'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Payment URL generated',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'error', type: 'boolean', example: false),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'payment_url', type: 'string'),
                        new OA\Property(property: 'hotel_booking_id', type: 'integer'),
                    ]),
                ])
            ),
            new OA\Response(response: 400, description: 'Booking already paid'),
            new OA\Response(response: 422, description: 'Validation Error'),
        ]
    )]
    public function initiateHotel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_booking_id' => 'required|integer|exists:hotel_bookings,id',
            'payment_type'     => 'required|string|in:mada,visa_master,apple_pay,tamara,tabby,tap',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            $user    = $request->user();
            $booking = HotelBooking::where('user_id', $user->id)->findOrFail($request->hotel_booking_id);

            if ($booking->status === HotelBooking::STATUS_CONFIRMED) {
                return $this->apiResponse(true, __('Booking is already confirmed/paid.'), null, null, 400);
            }

            if ($booking->status === HotelBooking::STATUS_CANCELLED) {
                return $this->apiResponse(true, __('Cannot pay for a cancelled booking.'), null, null, 400);
            }

            // Update status to pending
            $booking->update(['status' => HotelBooking::STATUS_PENDING]);

            $paymentUrl = route('payments.web.checkout', [
                'booking_id'   => $booking->id,
                'booking_type' => 'hotel',
                'method'       => $request->payment_type,
                'source'       => 'api',
            ]);

            return $this->apiResponse(false, __('Checkout link generated successfully.'), [
                'hotel_booking_id' => $booking->id,
                'payment_url'      => $paymentUrl,
            ]);

        } catch (\Exception $e) {
            Log::error('Hotel Payment Initiate Error: ' . $e->getMessage());
            return $this->apiResponse(true, __('Failed to generate payment link.'), null, null, 500);
        }
    }

    // =========================================================
    // Hotel Payment — Verify & Confirm with TBO
    // =========================================================

    #[OA\Post(
        path: '/api/payment/hotel/verify',
        summary: 'Verify hotel payment and confirm booking with TBO',
        operationId: 'verifyHotelPayment',
        description: "After successful gateway payment, this endpoint:\n1. Verifies payment status with the gateway\n2. Calls TBO `HotelBook` to confirm the reservation\n3. Stores TBO booking ID and raw response\n4. Sets booking status to `confirmed`\n5. Generates invoice\n6. Sends push notification to user",
        tags: ['Hotels', 'Payment'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['hotel_booking_id', 'payment_type'],
                properties: [
                    new OA\Property(property: 'hotel_booking_id', type: 'integer'),
                    new OA\Property(property: 'payment_type', type: 'string', enum: ['mada', 'visa_master', 'apple_pay', 'tamara', 'tabby', 'tap']),
                    new OA\Property(property: 'checkout_id', type: 'string', nullable: true, description: 'Required for HyperPay (mada, visa_master, apple_pay)'),
                    new OA\Property(property: 'payment_id',  type: 'string', nullable: true, description: 'Required for Tabby / Tamara / Tap'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Payment verified and booking confirmed with TBO',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'error', type: 'boolean', example: false),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'hotel_booking_id', type: 'integer'),
                        new OA\Property(property: 'tbo_booking_id',   type: 'string'),
                        new OA\Property(property: 'status',           type: 'string', example: 'confirmed'),
                        new OA\Property(property: 'invoice_url',      type: 'string'),
                    ]),
                ])
            ),
            new OA\Response(response: 400, description: 'Payment failed'),
            new OA\Response(response: 422, description: 'Validation Error'),
            new OA\Response(response: 500, description: 'Gateway or TBO error'),
        ]
    )]
    public function verifyHotel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_booking_id' => 'required|integer|exists:hotel_bookings,id',
            'payment_type'     => 'required|string|in:mada,visa_master,apple_pay,tamara,tabby,tap',
            'checkout_id'      => 'required_if:payment_type,mada,visa_master,apple_pay|nullable|string',
            'payment_id'       => 'required_if:payment_type,tamara,tabby,tap|nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        try {
            // Fetch the booking without requiring user authentication
            // since the user might be redirected from the payment gateway without an active API token
            $booking = HotelBooking::with(['guests', 'user'])->findOrFail($request->hotel_booking_id);
            $user = $booking->user;

            if (!$user) {
                return $this->apiResponse(true, __('Failed to find the associated user for this booking.'), null, null, 404);
            }

            if ($booking->status === HotelBooking::STATUS_CONFIRMED) {
                return $this->apiResponse(false, __('Booking is already confirmed.'), [
                    'hotel_booking_id' => $booking->id,
                    'tbo_booking_id'   => $booking->tbo_booking_id,
                    'status'           => $booking->status,
                ]);
            }

            // ---- Step 1: Verify payment with gateway ----
            $transactionId = null;
            $paymentVerified = false;
            $gatewayResponse = [];
            $type = $request->payment_type;
            $id   = $request->checkout_id ?? $request->payment_id;

            if (in_array($type, ['mada', 'visa_master', 'apple_pay'])) {
                // HyperPay verification
                $result = $this->hyperPayService->getPaymentStatus($id, $type);
                if ($result && isset($result['result']['code'])) {
                    $paymentVerified = $this->hyperPayService->isSuccessful($result['result']['code']);
                    $gatewayResponse = $result;
                    $transactionId   = $id;
                }
            } elseif ($type === 'tabby') {
                $result = $this->tabbyService->verifyPayment($id);
                $status = strtoupper($result['status'] ?? '');
                $paymentVerified = in_array($status, ['AUTHORIZED', 'CLOSED']);
                $gatewayResponse = $result;
                $transactionId   = $id;
            } elseif ($type === 'tamara') {
                $result = $this->tamaraService->verifyPayment($id);
                $status = $result['status'] ?? '';
                $paymentVerified = in_array($status, ['authorised', 'fully_captured']);
                $gatewayResponse = $result;
                $transactionId   = $id;
            } elseif ($type === 'tap') {
                $result = $this->tapService->verifyPayment($id);
                $status = strtoupper($result['status'] ?? '');
                $paymentVerified = in_array($status, ['CAPTURED', 'AUTHORIZED']);
                $gatewayResponse = $result;
                $transactionId   = $id;
            } elseif ($type === 'tap') {
                $result = $this->tapService->verifyPayment($id);
                $status = strtoupper($result['status'] ?? '');
                $paymentVerified = in_array($status, ['CAPTURED', 'AUTHORIZED']);
                $gatewayResponse = $result;
                $transactionId   = $id;
            }

            if (!$paymentVerified) {
                // Send failure notification
                if ($booking->user) {
                    $this->notificationService->sendToUser(
                        $booking->user,
                        Notification::TYPE_PAYMENT_FAILED,
                        __('Payment Failed'),
                        __('Your hotel payment was not completed. Please try again.'),
                        ['hotel_booking_id' => (string) $booking->id, 'gateway' => $type]
                    );
                }
                return $this->apiResponse(true, __('payment.general_failure'), ['gateway' => $type], null, 400);
            }

            // ---- Step 2: Confirm booking with TBO ----
            DB::beginTransaction();

            // Pre-flight check: Agency Balance
            try {
                $agencyInfo = $this->tboHotelService->getAgencyBalance();
                $netPrice   = $booking->tbo_raw_prebook['Price']['Total'] ?? ($booking->total_price * 0.9); // Fallback to 90% if missing, though it should be there

                if ($agencyInfo['balance'] < $netPrice) {
                    Log::critical("TBO Booking Failed: Insufficient Agency Balance. Available: {$agencyInfo['balance']}, Net Price Required: {$netPrice}");
                    throw new \Exception(__('Insufficient agency credit balance to complete this booking. Please contact support.'));
                }
            } catch (\Exception $balanceEx) {
                Log::error("Agency Balance Check Failed: " . $balanceEx->getMessage());
                // In test mode we might want to continue, but in production we should stop
                if (config('app.env') === 'production') {
                    throw $balanceEx;
                }
            }

            $guests = $booking->guests->map->toTboFormat()->toArray();
            $lead   = $booking->guests->firstWhere('type', 'adult') ?? $booking->guests->first();

            $tboResult = $this->tboHotelService->createBooking(
                $booking->tbo_result_token,
                $guests,
                [
                    'email'      => $user->email,
                    'phone'      => $user->phone,
                    'first_name' => $lead?->first_name ?? $user->first_name ?? $user->full_name,
                    'last_name'  => $lead?->last_name  ?? ($user->last_name ?? 'User'),
                ],
                'HOTEL-' . $booking->id . '-' . time()
            );

            // ---- Step 3: Update booking in DB ----
            $booking->update([
                'tbo_booking_id'  => $tboResult['tbo_booking_id'],
                'status'          => HotelBooking::STATUS_CONFIRMED,
                'booking_state'   => HotelBooking::STATE_CONFIRMED,
                'tbo_raw_booking' => array_merge($booking->tbo_raw_booking ?? [], $tboResult['raw']),
            ]);

            // ---- Step 4: Record payment ----
            $payment = Payment::updateOrCreate(
                ['transaction_id' => $transactionId],
                [
                    'hotel_booking_id' => $booking->id,
                    'user_id'          => $user->id,
                    'payment_gateway'  => match($type) {
                        'mada', 'visa_master', 'apple_pay' => 'hyperpay',
                        default => $type,
                    },
                    'payment_method' => $type,
                    'amount'         => $booking->total_price,
                    'currency'       => $booking->currency,
                    'status'         => 'paid',
                    'raw_response'   => $gatewayResponse,
                ]
            );

            // ---- Step 5: Generate Invoice ----
            $invoicePath = $this->invoiceService->generateInvoice($booking);
            if ($invoicePath) {
                $payment->update(['invoice_path' => $invoicePath]);
            }

            // ---- Step 6: History Log ----
            HotelBookingHistory::create([
                'hotel_booking_id' => $booking->id,
                'user_id'          => null,
                'action'           => 'payment_confirmed',
                'description'      => "الدفع تم بنجاح عبر {$type}. تم تأكيد الحجز مع TBO. رقم الحجز: {$tboResult['tbo_booking_id']}",
                'previous_state'   => HotelBooking::STATE_AWAITING_PAYMENT,
                'new_state'        => HotelBooking::STATE_CONFIRMED,
            ]);

            DB::commit();

            // ---- Step 7: Send success notifications ----
            if ($booking->user) {
                $this->notificationService->sendToUser(
                    $booking->user,
                    Notification::TYPE_PAYMENT_SUCCESS,
                    __('Payment Successful'),
                    __('Your payment of :amount SAR for hotel ":hotel" has been confirmed.', [
                        'amount' => $booking->total_price,
                        'hotel'  => $booking->hotel_name,
                    ]),
                    [
                        'hotel_booking_id' => (string) $booking->id,
                        'tbo_booking_id'   => $tboResult['tbo_booking_id'],
                        'amount'           => (string) $booking->total_price,
                        'gateway'          => $type,
                    ]
                );

                $this->notificationService->sendToUser(
                    $booking->user,
                    Notification::TYPE_BOOKING_CONFIRMED,
                    __('Hotel Booking Confirmed'),
                    __('Your hotel booking at ":hotel" (:checkin - :checkout) has been confirmed. TBO Ref: :ref', [
                        'hotel'   => $booking->hotel_name,
                        'checkin' => \Carbon\Carbon::parse($booking->check_in_date)->format('Y-m-d'),
                        'checkout'=> \Carbon\Carbon::parse($booking->check_out_date)->format('Y-m-d'),
                        'ref'     => $tboResult['tbo_booking_id'],
                    ]),
                    [
                        'hotel_booking_id' => (string) $booking->id,
                        'tbo_booking_id'   => $tboResult['tbo_booking_id'],
                    ]
                );
            }

            return $this->apiResponse(false, __('payment.success'), [
                'hotel_booking_id' => $booking->id,
                'tbo_booking_id'   => $tboResult['tbo_booking_id'],
                'status'           => 'confirmed',
                'invoice_url'      => $invoicePath ? asset('storage/' . $invoicePath) : null,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hotel Payment Verify Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->apiResponse(true, __('Payment verified but hotel booking failed: ') . $e->getMessage(), null, null, 500);
        }
    }
}
