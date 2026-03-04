<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_booking_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_booking_id')->constrained('hotel_bookings')->cascadeOnDelete();

            $table->enum('title', ['Mr', 'Mrs', 'Ms', 'Mstr'])->default('Mr');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('type', ['adult', 'child'])->default('adult');
            $table->string('nationality', 5)->nullable()->comment('ISO 2-letter country code');

            // Passport details (required by some hotels)
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->date('dob')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_booking_guests');
    }
};
