<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HyperPayService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected $hyperPayService;

    public function __construct(HyperPayService $hyperPayService)
    {
        $this->hyperPayService = $hyperPayService;
    }

    /**
     * Initiate Payment Checkout
     */
    #[OA\Post(
        path: "/api/payment/initiate",
        summary: "Initiate HyperPay checkout",
        operationId: "initiatePayment",
        description: "Initializes a payment session and returns a Checkout ID for the mobile SDK.",
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
                required: ["amount", "payment_type"],
                properties: [
                    new OA\Property(property: "amount", type: "number", format: "float", example: 100.50),
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay"], example: "visa_master"),
                    new OA\Property(property: "order_id", type: "string", example: "ORDER-12345"),
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
                            new OA\Property(property: "id", type: "string", example: "8AC7A4CA728C..."),
                            new OA\Property(property: "result", type: "object")
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
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay',
            'order_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $params = [];
        if ($request->order_id) {
            $params['merchantTransactionId'] = $request->order_id;
        }

        // Add customer email if available
        if ($request->user()->email) {
            $params['customer.email'] = $request->user()->email;
        }

        $result = $this->hyperPayService->prepareCheckout(
            $request->amount,
            $request->payment_type,
            $params
        );

        if ($result && isset($result['id'])) {
            return $this->apiResponse(false, __('Checkout initialized successfully.'), $result);
        }

        return $this->apiResponse(true, __('Failed to initialize payment.'), null, null, 500);
    }

    /**
     * Verify Payment Status
     */
    #[OA\Post(
        path: "/api/payment/verify",
        summary: "Verify HyperPay payment status",
        operationId: "verifyPayment",
        description: "Checks the status of a payment using the Checkout ID after the transaction is completed on the client side.",
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
                required: ["checkout_id", "payment_type"],
                properties: [
                    new OA\Property(property: "checkout_id", type: "string", example: "8AC7A4CA728C..."),
                    new OA\Property(property: "payment_type", type: "string", enum: ["mada", "visa_master", "apple_pay"], example: "visa_master"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Payment status retrieved",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Payment successful."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Payment Failed")
        ]
    )]
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout_id' => 'required|string',
            'payment_type' => 'required|string|in:mada,visa_master,apple_pay',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->hyperPayService->getPaymentStatus($request->checkout_id, $request->payment_type);

        if ($result && isset($result['result']['code'])) {
            $isSuccess = $this->hyperPayService->isSuccessful($result['result']['code']);

            if ($isSuccess) {
                return $this->apiResponse(false, __('Payment successful.'), $result);
            }

            return $this->apiResponse(true, $result['result']['description'] ?? __('Payment failed.'), $result, null, 400);
        }

        return $this->apiResponse(true, __('Failed to verify payment.'), null, null, 500);
    }
}
