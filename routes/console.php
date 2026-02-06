<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Trip;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// هذه المهمة ستعمل يومياً عند منتصف الليل
Schedule::call(function () {
    Trip::deactivateExpired();
})->daily();
