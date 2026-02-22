<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use App\Services\HyperPayService;
use App\Services\TabbyPaymentService;
use App\Services\TamaraPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebController extends Controller
{
    protected $hyperPayService;
    protected $tabbyService;
    protected $tamaraService;

    public function __construct(
        HyperPayService $hyperPayService,
        TabbyPaymentService $tabbyService,
        TamaraPaymentService $tamaraService
    ) {
        $this->hyperPayService = $hyperPayService;
        $this->tabbyService = $tabbyService;
        $this->tamaraService = $tamaraService;
    }

    /**
     * Show the unified checkout page
     */
    public function checkout(Request $request, $booking_id, $method)
    {
        try {
            $booking = TripBooking::with(['trip', 'user'])->findOrFail($booking_id);

            // Basic validation
            if ($booking->status === 'confirmed') {
                return redirect()->route('payments.web.success', ['booking_id' => $booking_id]);
            }

            $user = $booking->user;

            // Prepare dynamic data for the view
            $data = [
                'booking' => $booking,
                'trip' => $booking->trip,
                'user' => $user,
                'method' => $method,
                'amount' => $booking->total_price,
            ];

            // If HyperPay, we might need a checkout_id immediately to load the widget
            if (in_array($method, ['mada', 'visa_master', 'apple_pay'])) {
                $checkoutResult = $this->prepareHyperPayCheckout($booking, $method, $request);
                $data['checkout_id'] = $checkoutResult['id'] ?? null;
            }

            return view('payments.checkout', $data);

        } catch (\Exception $e) {
            Log::error("Web Checkout Error: " . $e->getMessage());
            return redirect()->route('payments.web.failure', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle Tamara/Tabby redirection initiation from the web page
     */
    public function initiateRedirect(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:trip_bookings,id',
            'method' => 'required|string|in:tabby,tamara',
        ]);

        try {
            $booking = TripBooking::with(['trip', 'user'])->findOrFail($request->booking_id);
            $method = $request->method;
            $user = $booking->user;

            if ($method === 'tabby') {
                return $this->initiateTabby($booking, $user, $request);
            }

            if ($method === 'tamara') {
                return $this->initiateTamara($booking, $user, $request);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    protected function prepareHyperPayCheckout($booking, $method, $request)
    {
        $params = [
            'merchantTransactionId' => 'BOOKING-' . $booking->id . '-' . time(),
        ];

        $customerParams = $this->hyperPayService->buildCustomerParams([
            'email' => $booking->user->email,
            'first_name' => $booking->user->first_name ?? $booking->user->full_name,
            'last_name' => $booking->user->last_name ?? 'User',
            'street' => $booking->user->address ?? 'Not Provided',
            'city' => $booking->user->city ?? 'Riyadh',
            'state' => 'Riyadh',
            'country' => 'SA',
            'postcode' => '00000',
        ]);

        $params = array_merge($params, $customerParams);

        return $this->hyperPayService->prepareCheckout(
            $booking->total_price,
            $method,
            $params
        );
    }

    protected function initiateTabby($booking, $user, $request)
    {
        $data = [
            'amount' => $booking->total_price,
            'customer_name' => $user->full_name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'order_id' => 'BOOKING-' . $booking->id . '-' . time(),
            'callback_url' => route('payments.web.callback', ['payment_type' => 'tabby']),
            'items' => [
                [
                    'title' => $booking->trip ? $booking->trip->title : 'Trip Booking',
                    'quantity' => 1,
                    'unit_price' => $booking->total_price,
                ]
            ],
            'city' => $user->city ?? 'Riyadh',
            'address' => $user->address ?? 'Saudi Arabia',
        ];

        $result = $this->tabbyService->initiateCheckout($data);
        return response()->json($result);
    }

    protected function initiateTamara($booking, $user, $request)
    {
        $data = [
            'amount' => $booking->total_price,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'first_name' => $user->first_name ?? $user->full_name,
            'last_name' => $user->last_name ?? 'User',
            'order_id' => 'BOOKING-' . $booking->id . '-' . time(),
            'callback_url' => route('payments.web.callback', ['payment_type' => 'tamara']),
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
            'city' => $user->city ?? 'Riyadh',
            'address' => $user->address ?? 'Saudi Arabia',
        ];

        $result = $this->tamaraService->initiateCheckout($data);
        return response()->json($result);
    }

    public function success(Request $request)
    {
        return view('payments.success', [
            'booking_id' => $request->booking_id,
            'transaction_id' => $request->transaction_id
        ]);
    }

    public function failure(Request $request)
    {
        return view('payments.failure', [
            'error' => $request->error ?? __('Payment failed or was cancelled.')
        ]);
    }
}
