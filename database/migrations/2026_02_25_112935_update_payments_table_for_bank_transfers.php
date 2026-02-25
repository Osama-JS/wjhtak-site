<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add user_id if it doesn't exist
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('trip_booking_id')->constrained('users')->onDelete('cascade');
            }

            // Update enum column - for MySQL we need a raw statement to be safe with enums
            // Alternatively, if using change() it might require doctrine/dbal
            // DB::statement("ALTER TABLE payments MODIFY COLUMN payment_gateway ENUM('hyperpay', 'tabby', 'tamara', 'bank_transfer') NOT NULL");
        });

        // Using a separate statement for the enum to be safe across different DB states
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_gateway ENUM('hyperpay', 'tabby', 'tamara', 'bank_transfer') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_gateway ENUM('hyperpay', 'tabby', 'tamara') NOT NULL");
    }
};
