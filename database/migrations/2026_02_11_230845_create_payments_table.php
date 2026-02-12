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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_booking_id')->constrained('trip_bookings')->onDelete('cascade');
            $table->enum('payment_gateway', ['hyperpay', 'tabby', 'tamara']);
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('SAR');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->json('raw_response')->nullable();
            $table->string('invoice_path')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('trip_booking_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
