<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add hotel_booking_id support
            $table->unsignedBigInteger('hotel_booking_id')->nullable()->after('trip_booking_id');
            $table->foreign('hotel_booking_id')->references('id')->on('hotel_bookings')->nullOnDelete();

            // Make trip_booking_id nullable (was required before)
            $table->unsignedBigInteger('trip_booking_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['hotel_booking_id']);
            $table->dropColumn('hotel_booking_id');
            $table->unsignedBigInteger('trip_booking_id')->nullable(false)->change();
        });
    }
};
