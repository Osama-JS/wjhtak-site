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

            $table->integer('tickets_count');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->date('booking_date')->nullable();

            $table->timestamps();

            // Performance indexes
            $table->index('status');
            $table->index('user_id');
            $table->index('trip_id');
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
