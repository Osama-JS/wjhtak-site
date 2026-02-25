<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Trip;
use App\Models\TripBooking;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// هذه المهمة ستعمل يومياً عند منتصف الليل لإغلاق الرحلات المنتهية
Schedule::call(function () {
    Trip::deactivateExpired();
})->daily();

// هذه المهمة ترسل إشعارات تذكيرية يومياً للحجوزات التي تنطلق رحلتها "غداً"
Schedule::call(function () {
    $tomorrow = now()->addDay()->toDateString();

    // جلب الحجوزات المؤكدة التي تنطلق رحلتها غداً
    $bookings = TripBooking::with(['user', 'trip'])
        ->where('status', 'confirmed')
        ->whereHas('trip', function ($query) use ($tomorrow) {
            $query->whereDate('start_date', $tomorrow);
        })
        ->get();

    if ($bookings->isEmpty()) {
        return;
    }

    $notificationService = app(NotificationService::class);
    $count = 0;

    foreach ($bookings as $booking) {
        if ($booking->user) {
            $title = __('Trip Reminder: :trip', ['trip' => $booking->trip->name]);
            $body = __('Your trip ":trip" starts tomorrow at :time. Be ready!', [
                'trip' => $booking->trip->name,
                'time' => \Carbon\Carbon::parse($booking->trip->start_date)->format('h:i A')
            ]);

            $data = [
                'booking_id' => $booking->id,
                'trip_id' => $booking->trip_id,
            ];

            $notificationService->sendToUser(
                $booking->user,
                Notification::TYPE_BOOKING_REMINDER,
                $title,
                $body,
                $data,
                true // queued
            );
            $count++;
        }
    }

    Log::info("Booking remiders scheduled task completed. Sent {$count} reminders for trips starting on {$tomorrow}.");
})->dailyAt('10:00');
