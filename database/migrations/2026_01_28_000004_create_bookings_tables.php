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
        // Create trip_bookings table
        Schema::create('trip_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');

            // Ticket and passport information - using JSON for better structure
            $table->json('tickets')->nullable();
            $table->json('passport'); // Changed from LONGTEXT to JSON
            $table->json('nationality'); // Changed from LONGTEXT to JSON (supports multiple travelers)
            $table->json('passport_expiry'); // Changed from LONGTEXT to JSON

            // Payment information
            $table->string('payment_method');
            $table->string('card_last_four');
            $table->string('payment_reference');
            $table->string('payment_uid')->nullable();
            $table->decimal('total_paid', 10, 2);
            $table->decimal('vat', 10, 2);

            // Trip dates
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();

            // Pricing breakdown
            $table->decimal('trip_price', 10, 2);
            $table->decimal('app_profit', 10, 2);
            $table->decimal('company_profit', 10, 2);

            // Status - Changed from BIGINT to ENUM for better performance
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');

            // Document paths
            $table->json('passport_image_path'); // Changed to JSON for multiple travelers
            $table->json('visa_image_path'); // Changed to JSON for multiple travelers

            // Company code information
            $table->foreignId('company_code_id')->nullable()->constrained('company_codes')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->string('company_code')->nullable();
            $table->enum('code_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('code_value', 8, 2)->default(0.00);
            $table->string('code_attached_file')->nullable();

            $table->timestamps();

            // Performance indexes from recommendations
            $table->index('status', 'idx_booking_status');
            $table->index('created_at', 'idx_bookings_created');
            $table->index('user_id');
            $table->index('trip_id');
            $table->index('payment_method');
        });

        // Create booking_fees table
        Schema::create('booking_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_booking_id')->constrained('trip_bookings')->onDelete('cascade');
            $table->string('title');
            $table->string('desc')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('card_last_four')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_uid')->nullable();
            $table->decimal('total_paid', 10, 2)->default(0.00);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['un_paid', 'in_procees', 'canceled', 'done'])->default('un_paid');
            $table->timestamps();

            // Indexes for performance
            $table->index('trip_booking_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_fees');
        Schema::dropIfExists('trip_bookings');
    }
};
