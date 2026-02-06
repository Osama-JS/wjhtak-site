<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FrontendController;

use Illuminate\Support\Facades\Route;

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
    Route::post('companies/{companie}/toggle-status', [App\Http\Controllers\Admin\CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
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
});

// Customer Routes
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');

    Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
