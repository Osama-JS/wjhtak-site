<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Temporarily change to VARCHAR to allow all values
        DB::statement("ALTER TABLE trip_bookings MODIFY COLUMN booking_state VARCHAR(50) NOT NULL");

        // 2. Update existing data to new states
        DB::statement("UPDATE trip_bookings SET booking_state = 'awaiting_payment' WHERE booking_state = 'received' OR booking_state IS NULL");
        DB::statement("UPDATE trip_bookings SET booking_state = 'preparing' WHERE booking_state = 'confirmed'");
        DB::statement("UPDATE trip_bookings SET booking_state = 'tickets_uploaded' WHERE booking_state = 'tickets_sent'");

        // 3. Modify to the new ENUM definition
        DB::statement("ALTER TABLE trip_bookings MODIFY COLUMN booking_state ENUM('awaiting_payment', 'preparing', 'issuing_tickets', 'tickets_uploaded', 'completed', 'cancelled') NOT NULL DEFAULT 'awaiting_payment'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE trip_bookings MODIFY COLUMN booking_state ENUM('received', 'preparing', 'confirmed', 'tickets_sent', 'cancelled') NOT NULL DEFAULT 'received'");
    }
};
