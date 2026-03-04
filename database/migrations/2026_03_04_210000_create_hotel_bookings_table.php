<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // TBO References
            $table->string('tbo_booking_id')->nullable()->index()->comment('TBO Booking ID after confirmation');
            $table->string('tbo_session_id')->nullable()->comment('TBO Session ID from search step');
            $table->string('tbo_result_token')->nullable()->comment('TBO ResultToken from pre-book step');

            // Hotel Info
            $table->string('hotel_code')->comment('TBO Hotel Code');
            $table->string('hotel_name');
            $table->string('hotel_name_ar')->nullable();
            $table->string('hotel_address')->nullable();
            $table->tinyInteger('star_rating')->nullable()->unsigned();

            // Location
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('city_name');
            $table->string('country_code', 5);

            // Room Info
            $table->string('room_type_code')->comment('TBO RatePlanCode');
            $table->string('room_type_name');
            $table->tinyInteger('rooms_count')->unsigned()->default(1);

            // Occupancy
            $table->tinyInteger('adults')->unsigned()->default(1);
            $table->tinyInteger('children')->unsigned()->default(0);

            // Dates
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->tinyInteger('nights_count')->unsigned();

            // Pricing
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('SAR');

            // Status
            $table->enum('status', ['draft', 'pending', 'confirmed', 'cancelled', 'failed'])->default('draft')
               ->comment('Internal status for payment tracking');
            $table->string('booking_state')->default('awaiting_payment')
               ->comment('Display state: awaiting_payment/preparing/confirmed/cancelled');

            // TBO Raw Responses (for debugging and certification)
            $table->json('cancellation_policy')->nullable()->comment('Cancellation policy from TBO');
            $table->json('tbo_raw_search')->nullable()->comment('Raw TBO search response');
            $table->json('tbo_raw_prebook')->nullable()->comment('Raw TBO pre-book response');
            $table->json('tbo_raw_booking')->nullable()->comment('Raw TBO booking confirmation response');

            // Misc
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
