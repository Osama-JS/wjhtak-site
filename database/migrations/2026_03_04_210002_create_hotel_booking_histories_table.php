<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_booking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_booking_id')->constrained('hotel_bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('action');
            $table->text('description')->nullable();
            $table->string('previous_state')->nullable();
            $table->string('new_state')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_booking_histories');
    }
};
