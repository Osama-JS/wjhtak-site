<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\HotelController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Discovery Routes
Route::prefix('v1')->group(function () {
    Route::get('/countries', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getCountries']);
    Route::get('/cities', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getCities']);
    Route::get('/banners', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getBanners']);
    Route::get('/locations', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getLocations']);
    Route::get('/faqs', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getFaqs']);
    Route::get('/categories', [\App\Http\Controllers\Api\V1\DiscoveryController::class, 'getCategories']);

    // Trips
    Route::get('/trips/featured', [\App\Http\Controllers\Api\V1\TripController::class, 'featured']);
    Route::get('/trips', [\App\Http\Controllers\Api\V1\TripController::class, 'index']);
    Route::get('/trips/{id}', [\App\Http\Controllers\Api\V1\TripController::class, 'show']);
    Route::post('/trips/book', [\App\Http\Controllers\Api\V1\TripController::class, 'book'])->middleware('auth:sanctum');

    // My Bookings & Favorites
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/my-bookings', [\App\Http\Controllers\Api\V1\TripController::class, 'myBookings']);
        Route::get('/bookings/{id}', [\App\Http\Controllers\Api\V1\TripController::class, 'bookingDetails']);
        Route::get('/bookings/{id}/invoice', [\App\Http\Controllers\Api\V1\TripController::class, 'downloadInvoice']);
        Route::get('/bookings/{id}/ticket', [\App\Http\Controllers\Api\V1\TripController::class, 'downloadTicket']);
        Route::post('/bookings/{id}/cancel', [\App\Http\Controllers\Api\V1\TripController::class, 'cancelBooking']);
        Route::get('/favorites', [\App\Http\Controllers\Api\V1\TripController::class, 'getFavorites']);
        Route::post('/trips/{id}/favorite', [\App\Http\Controllers\Api\V1\TripController::class, 'toggleFavorite']);
        Route::post('/trips/{id}/like', [\App\Http\Controllers\Api\V1\TripController::class, 'toggleLike']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
    });
});

// Public Routes
Route::get('/app-settings', [AppSettingController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Flight Routes (Public for Testing)
Route::post('/flights/search', [FlightController::class, 'search']);
Route::get('/flights/airports', [FlightController::class, 'getAirports']);
Route::get('/flights/airlines', [FlightController::class, 'getAirlines']);
Route::post('/flights/validate-fare', [FlightController::class, 'validateFare']);
Route::post('/flights/book', [FlightController::class, 'book']);
Route::post('/flights/order-ticket', [FlightController::class, 'orderTicket']);
Route::post('/flights/trip-details', [FlightController::class, 'getTripDetails']);

// Public Hotel Routes
Route::prefix('hotels')->group(function () {
    Route::get('/cities', [HotelController::class, 'cities']);
    Route::post('/search', [HotelController::class, 'search']);
    Route::get('/{hotelCode}/rooms', [HotelController::class, 'rooms']);
    Route::post('/pre-book', [HotelController::class, 'preBook']);
    Route::get('/country-list', [HotelController::class, 'countryList']);
    Route::post('/code-list', [HotelController::class, 'hotelCodeList']);
});

// Protected Routes (Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::get('/check-token', [AuthController::class, 'checkToken']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/profile/change-password', [AuthController::class, 'changePassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);

    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::post('/initiate', [PaymentController::class, 'initiate']);
        Route::post('/bank-transfer', [PaymentController::class, 'submitBankTransfer']);

        // Hotel Payment Routes
        Route::post('/hotel/initiate', [PaymentController::class, 'initiateHotel']);
    });

    // Protected Hotel Routes (Booking related)
    Route::prefix('hotels')->group(function () {
        Route::post('/book', [HotelController::class, 'book']);
        Route::get('/bookings', [HotelController::class, 'myBookings']);
        Route::get('/bookings/{id}', [HotelController::class, 'bookingDetail']);
        Route::post('/bookings/by-date', [HotelController::class, 'bookingDetailsByDate']);
        Route::post('/bookings/{id}/cancel', [HotelController::class, 'cancel']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

// Payment Routes (public — no auth required)
Route::get('/payment/methods', [PaymentController::class, 'methods']);
Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
Route::post('/payment/verify', [PaymentController::class, 'verify']);
Route::post('/payment/hotel/verify', [PaymentController::class, 'verifyHotel']);
