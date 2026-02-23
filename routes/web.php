<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\TripsController;
use App\Http\Controllers\Admin\TripCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Web\PaymentWebController;

// Customer Controllers
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\CustomerPaymentController;
use App\Http\Controllers\Customer\NotificationController as CustomerNotificationController;

use Illuminate\Support\Facades\Route;

// =============================================================================
// WEB VIEW PAYMENT ROUTES
// =============================================================================
Route::group(['prefix' => 'payments', 'as' => 'payments.web.'], function () {
    Route::get('/checkout/{booking_id}/{method}', [PaymentWebController::class, 'checkout'])->name('checkout');
    Route::post('/initiate', [PaymentWebController::class, 'initiateRedirect'])->name('initiate');
    Route::get('/success', [PaymentWebController::class, 'success'])->name('success');
    Route::get('/failure', [PaymentWebController::class, 'failure'])->name('failure');

    // Specialized callback that triggers verification then redirects to success/failure
    Route::get('/callback/{payment_type}', function (Illuminate\Http\Request $request, $payment_type) {
        $paymentId = $request->payment_id ?? $request->orderId ?? $request->id;
        $checkoutId = $request->id; // For HyperPay

        // We'll redirect to success or failure based on basic query params for now,
        // but ideally we verify here. For the WebView flow, we'll let the success/failure
        // pages handle the verification or use this intermediate route.
        if ($request->status === 'cancel') {
             return redirect()->route('payments.web.failure', ['error' => __('Payment cancelled by user.')]);
        }

        // Return a processing page that will then call the verify logic
        return view('payments.callback_processing', [
            'payment_type' => $payment_type,
            'payment_id' => $paymentId,
            'checkout_id' => $checkoutId,
            'status' => $request->status,
            'source' => $request->source
        ]);
    })->name('callback');
});

// =============================================================================
// FRONTEND ROUTES (Public)
// =============================================================================

// Language switcher
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Homepage
Route::get('/', [FrontendController::class, 'home'])->name('home');

// Trips
Route::get('/trips', [FrontendController::class, 'trips'])->name('trips.index');
Route::get('/trips/{id}', [FrontendController::class, 'tripShow'])->name('trips.show');

// banner
Route::get('/trips', [FrontendController::class, 'trips'])->name('trips.index');
Route::get('/trips/{id}', [FrontendController::class, 'tripShow'])->name('trips.show');

// Destinations
Route::get('/destinations', [FrontendController::class, 'destinations'])->name('destinations');

// About
Route::get('/about', [FrontendController::class, 'about'])->name('about');

// Contact
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact', [FrontendController::class, 'contactSubmit'])->name('contact.submit');

// FAQ
Route::get('/faq', [FrontendController::class, 'faq'])->name('faq');

// Search
Route::get('/search', [FrontendController::class, 'search'])->name('search');

// Search Model
Route::get('/searchModel', [FrontendController::class, 'searchModel'])->name('searchModel');

// Default dashboard - redirect based on user type
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes - Protected by isAdmin middleware
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('users/data', [UserController::class, 'getData'])->name('users.data');
    Route::get('users/{user}/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);

    // Role Management
    Route::get('roles/data', [RoleController::class, 'getData'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    // Bookings
    Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function() {
        // Flights
        Route::get('flights/available', [BookingController::class, 'availableFlights'])->name('flights.available');
        Route::post('flights/search', [BookingController::class, 'searchFlights'])->name('flights.search');
        Route::post('flights/validate', [BookingController::class, 'validateFare'])->name('flights.validate');
        Route::post('flights/book', [BookingController::class, 'createBooking'])->name('flights.book');
        Route::get('flights/airports', [BookingController::class, 'getAirports'])->name('flights.airports');
        Route::get('flights/airlines', [BookingController::class, 'getAirlines'])->name('flights.airlines');
        Route::get('flights/requests', [BookingController::class, 'flightRequests'])->name('flights.requests');
        Route::get('flights/ongoing', [BookingController::class, 'ongoingFlights'])->name('flights.ongoing');

        // Hotels
        Route::get('hotels', [BookingController::class, 'hotelList'])->name('hotels.index');
        Route::get('hotels/requests', [BookingController::class, 'hotelRequests'])->name('hotels.requests');
    });

    // Countries Management
    Route::get('countries/data', [App\Http\Controllers\Admin\CountryController::class, 'getData'])->name('countries.data');
    Route::get('countries/active', [App\Http\Controllers\Admin\CountryController::class, 'getActiveCountries'])->name('countries.active');
    Route::post('countries/{country}/toggle-status', [App\Http\Controllers\Admin\CountryController::class, 'toggleStatus'])->name('countries.toggle-status');
    Route::resource('countries', App\Http\Controllers\Admin\CountryController::class);

    // Cities Management
    Route::get('cities/data', [App\Http\Controllers\Admin\CityController::class, 'getData'])->name('cities.data');
    Route::get('cities/by-country/{country}', [App\Http\Controllers\Admin\CityController::class, 'byCountry'])->name('cities.by-country');
    Route::post('cities/{city}/toggle-status', [App\Http\Controllers\Admin\CityController::class, 'toggleStatus'])->name('cities.toggle-status');
    Route::resource('cities', App\Http\Controllers\Admin\CityController::class);


    // companies Management
    Route::get('companies/data', [App\Http\Controllers\Admin\CompanyController::class, 'getData'])->name('companies.data');
    Route::get('companies/active', [App\Http\Controllers\Admin\CompanyController::class, 'getActivecompanies'])->name('companies.active');
    Route::post('companies/{company}/toggle-status', [App\Http\Controllers\Admin\CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
    Route::resource('companies', App\Http\Controllers\Admin\CompanyController::class);


    Route::get('company-codes/data', [App\Http\Controllers\Admin\Company_CodesController::class, 'getData'])->name('company-codes.data');
    Route::post('company-codes/{company_code}/toggle-status', [App\Http\Controllers\Admin\Company_CodesController::class, 'toggleStatus'])->name('company-codes.toggle-status');
    Route::resource('company-codes', App\Http\Controllers\Admin\Company_CodesController::class);


    // Banners Management
    Route::get('banners/data', [App\Http\Controllers\Admin\BannerController::class, 'getData'])->name('banners.data');
    Route::post('banners/{banner}/toggle-status', [App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
    Route::post('banners/reorder', [App\Http\Controllers\Admin\BannerController::class, 'reorder'])->name('banners.reorder');
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class);

    // Trips Management
    Route::get('trips/data', [App\Http\Controllers\Admin\TripsController::class, 'getData'])->name('trips.data');
    Route::post('trips/{trip}/toggle-status', [App\Http\Controllers\Admin\TripsController::class, 'toggleStatus'])->name('trips.toggle-status');
    Route::post('/trips/{trip}/renew', [App\Http\Controllers\Admin\TripsController::class, 'renew'])->name('trips.renew');
    Route::resource('trips', App\Http\Controllers\Admin\TripsController::class);
    Route::post('/trips/{trip}/images', [App\Http\Controllers\Admin\TripsController::class, 'imagestore'])->name('trips.images-store');
    Route::get('/trips/{id}/get-images', [App\Http\Controllers\Admin\TripsController::class, 'getImages'])->name('trips.get-images');
    Route::delete('/trips/{image}/destroyimages', [App\Http\Controllers\Admin\TripsController::class, 'imagedestroy'])->name('trips.images-destroy');

    // Trip Itinerary
    Route::get('/trips/{trip}/itinerary', [TripsController::class, 'itinerary'])->name('trips.itinerary');
    Route::post('/trips/{trip}/itinerary', [TripsController::class, 'storeItinerary'])->name('trips.itinerary.store');
    Route::put('/trips/itinerary/{itinerary}', [TripsController::class, 'updateItinerary'])->name('trips.itinerary.update');
    Route::post('/trips/itinerary/reorder', [TripsController::class, 'reorderItinerary'])->name('trips.itinerary.reorder');
    Route::delete('/trips/itinerary/{itinerary}', [TripsController::class, 'destroyItinerary'])->name('trips.itinerary.destroy');

    // Trip Categories
    Route::get('trip-categories/data', [TripCategoryController::class, 'getData'])->name('trip-categories.data');
    Route::get('trip-categories', [TripCategoryController::class, 'index'])->name('trip-categories.index');
    Route::get('trip-categories/all', [TripCategoryController::class, 'getAll'])->name('trip-categories.all');
    Route::resource('trip-categories', TripCategoryController::class)->except(['index']);

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Profile Management
    Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile/update', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/photo', [App\Http\Controllers\Admin\ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Permission Management
    Route::get('permissions/data', [PermissionController::class, 'getData'])->name('permissions.data');
    Route::resource('permissions', PermissionController::class);

    // Trip Bookings Management
    Route::get('trip-bookings/data', [App\Http\Controllers\Admin\TripBookingController::class, 'getData'])->name('trip-bookings.data');
    Route::post('trip-bookings/{id}/update-status', [App\Http\Controllers\Admin\TripBookingController::class, 'updateStatus'])->name('trip-bookings.update-status');
    Route::resource('trip-bookings', App\Http\Controllers\Admin\TripBookingController::class);

    // Notifications Management
    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/data', [AdminNotificationController::class, 'getData'])->name('notifications.data');
    Route::get('notifications/search-users', [AdminNotificationController::class, 'searchUsers'])->name('notifications.search-users');
    Route::post('notifications/send', [AdminNotificationController::class, 'send'])->name('notifications.send');
    Route::delete('notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');

    // Payments Management
    Route::get('payments', [App\Http\Controllers\Admin\PaymentLogController::class, 'index'])->name('payments.index');
    Route::get('payments/{id}', [App\Http\Controllers\Admin\PaymentLogController::class, 'show'])->name('payments.show');

    // Subscribers
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
    Route::get('/subscribers/data', [SubscriberController::class, 'getData'])->name('subscribers.data');
    Route::get('/subscribers/profile/{id}', [SubscriberController::class, 'profile'])->name('subscribers.profile');
    Route::post('/subscribers', [SubscriberController::class, 'store'])->name('subscribers.store');
    Route::get('/subscribers/{id}', [SubscriberController::class, 'show'])->name('subscribers.show');
    Route::put('/subscribers/{id}', [SubscriberController::class, 'update'])->name('subscribers.update');
    Route::post('/subscribers/{id}/toggle-status', [SubscriberController::class, 'toggleStatus'])->name('subscribers.toggle-status');
    Route::delete('/subscribers/{id}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');

    // Pages Management
    Route::get('pages/data', [App\Http\Controllers\Admin\PageController::class, 'getData'])->name('pages.data');
    Route::resource('pages', App\Http\Controllers\Admin\PageController::class);
});

// Public Page Display
Route::get('/page/{slug}', [App\Http\Controllers\Web\PageController::class, 'show'])->name('pages.show');

// Customer Routes
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');

    Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
});

// =============================================================================
// HYPERPAY TEST PAGE (Development Only)
// =============================================================================
Route::get('/hyperpay-test', function () {
    return view('hyperpay-test');
})->name('hyperpay.test');

// Backend route to create checkout (avoids CORS)
Route::post('/hyperpay-test/checkout', function (\Illuminate\Http\Request $request) {
    $service = app(\App\Services\HyperPayService::class);

    $params = [
        'merchantTransactionId' => 'TEST-' . time(),
        'customer.email' => $request->email ?? 'test@example.com',
        'customer.givenName' => $request->first_name ?? 'Test',
        'customer.surname' => $request->last_name ?? 'User',
        'billing.street1' => $request->street ?? 'Test Street',
        'billing.city' => $request->city ?? 'Riyadh',
        'billing.state' => $request->city ?? 'Riyadh',
        'billing.country' => 'SA',
        'billing.postcode' => '00000',
    ];

    $result = $service->prepareCheckout(
        (float) $request->amount,
        $request->payment_type ?? 'visa_master',
        $params
    );

    return response()->json($result ?: ['error' => 'Failed to create checkout']);
})->name('hyperpay.checkout');

Route::get('/hyperpay-test/result', function (\Illuminate\Http\Request $request) {
    $checkoutId = $request->id;
    $result = ['success' => false, 'message' => 'No checkout ID provided', 'data' => []];

    if ($checkoutId) {
        $service = app(\App\Services\HyperPayService::class);
        $data = $service->getPaymentStatus($checkoutId, 'visa_master');
        if (!$data) {
            $data = $service->getPaymentStatus($checkoutId, 'mada');
        }

        if ($data && isset($data['result']['code'])) {
            $result = [
                'success' => $service->isSuccessful($data['result']['code']),
                'message' => $data['result']['description'] ?? 'Unknown',
                'data' => $data,
            ];
        } else {
            $result['message'] = 'Could not retrieve payment status';
            $result['data'] = $data ?? [];
        }
    }

    return view('hyperpay-test', compact('result'));
})->name('hyperpay.result');

// =============================================================================
// TAMARA TEST PAGE (Development Only)
// =============================================================================
Route::get('/tamara-test', function () {
    return view('tamara-test');
})->name('tamara.test');

// Backend route to create Tamara checkout session
Route::post('/tamara-test/checkout', function (\Illuminate\Http\Request $request) {
    try {
        $service = app(\App\Services\TamaraPaymentService::class);

        $data = [
            'amount' => (float) $request->amount,
            'currency' => 'SAR',
            'customer_email' => $request->email ?? 'test@example.com',
            'customer_phone' => $request->phone ?? '966500000000',
            'first_name' => $request->first_name ?? 'Test',
            'last_name' => $request->last_name ?? 'User',
            'order_id' => 'TEST-' . time(),
            'callback_url' => url('/tamara-test/result'),
            'items' => [
                [
                    'name' => $request->item_name ?? 'Trip Booking Test',
                    'type' => 'Digital',
                    'reference_id' => 'ITEM-1',
                    'quantity' => 1,
                    'unit_price' => (float) $request->amount,
                    'total_amount' => [
                        'amount' => (float) $request->amount,
                        'currency' => 'SAR'
                    ],
                ]
            ],
            'city' => $request->city ?? 'Riyadh',
            'address' => $request->address ?? 'Test Address',
            'description' => 'Tamara Test Payment',
        ];

        $result = $service->initiateCheckout($data);
        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
    }
})->name('tamara.checkout');

// Tamara callback result page
Route::get('/tamara-test/result', function (\Illuminate\Http\Request $request) {
    $status = $request->status;
    $orderId = $request->orderId ?? $request->order_id ?? $request->paymentStatus ?? null;
    $result = ['status' => $status, 'order_id' => $orderId, 'data' => []];

    if ($orderId && $status === 'success') {
        try {
            $service = app(\App\Services\TamaraPaymentService::class);
            $data = $service->verifyPayment($orderId);
            $result['data'] = $data;
            $result['authorised'] = ($data['status'] ?? '') === 'authorised' || ($data['status'] ?? '') === 'approved';
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }
    }

    return view('tamara-test', compact('result'));
})->name('tamara.result');

// =============================================================================
// TABBY TEST PAGE (Development Only)
// =============================================================================
Route::get('/tabby-test', function () {
    return view('tabby-test');
})->name('tabby.test');

Route::post('/tabby-test/checkout', function (\Illuminate\Http\Request $request) {
    try {
        $service = app(\App\Services\TabbyPaymentService::class);

        $data = [
            'amount' => (float) $request->amount,
            'currency' => 'SAR',
            'customer_name' => ($request->first_name ?? 'Test') . ' ' . ($request->last_name ?? 'User'),
            'customer_email' => $request->email ?? 'test@example.com',
            'customer_phone' => $request->phone ?? '+966500000000',
            'first_name' => $request->first_name ?? 'Test',
            'last_name' => $request->last_name ?? 'User',
            'order_id' => 'TEST-' . time(),
            'callback_url' => url('/tabby-test/result'),
            'items' => [
                [
                    'title' => $request->item_name ?? 'Trip Booking Test',
                    'quantity' => 1,
                    'unit_price' => (float) $request->amount,
                    'reference_id' => 'ITEM-1',
                    'category' => 'Travel',
                ]
            ],
            'city' => $request->city ?? 'Riyadh',
            'address' => $request->address ?? 'Test Address',
            'description' => 'Tabby Test Payment',
        ];

        $result = $service->initiateCheckout($data);
        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
    }
})->name('tabby.checkout');

Route::get('/tabby-test/result', function (\Illuminate\Http\Request $request) {
    $status = $request->status;
    $paymentId = $request->payment_id ?? $request->id ?? null;
    $result = ['status' => $status, 'payment_id' => $paymentId, 'data' => []];

    if ($paymentId && $status === 'success') {
        try {
            $service = app(\App\Services\TabbyPaymentService::class);
            // verifyPayment now auto-captures if AUTHORIZED
            $data = $service->verifyPayment($paymentId);
            $result['data'] = $data;
            $finalStatus = strtoupper($data['status'] ?? 'UNKNOWN');
            $result['captured'] = $finalStatus === 'CLOSED';
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }
    }

    return view('tabby-test', compact('result'));
})->name('tabby.result');

// =============================================================================
// CUSTOMER (USER) ROUTES
// =============================================================================
Route::middleware(['auth', 'isCustomer'])->prefix('customer')->name('customer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Bookings
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{trip_id}', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/{id}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{id}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{id}/invoice', [CustomerBookingController::class, 'downloadInvoice'])->name('bookings.invoice');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{tripId}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Profile
    Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [CustomerProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profile/password', [CustomerProfileController::class, 'changePassword'])->name('profile.password');

    // Payments & Invoices
    Route::get('/payments', [CustomerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/checkout/{bookingId}', [CustomerPaymentController::class, 'checkout'])->name('payments.checkout');
    Route::get('/payments/{bookingId}/invoice', [CustomerPaymentController::class, 'downloadInvoice'])->name('payments.invoice');

    // Notifications
    Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [CustomerNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [CustomerNotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// Alias: redirect old /dashboard -> customer.dashboard
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';
