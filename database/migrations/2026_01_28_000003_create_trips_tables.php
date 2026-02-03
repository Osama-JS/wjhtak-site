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
        // Create trips table
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('tickets')->nullable();
            $table->text('description');
            $table->boolean('is_public')->default(true);
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->boolean('is_ad')->default(false);
            $table->string('duration')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('price_before_discount', 8, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('personnel_capacity')->default(2);

            // Foreign keys - NO DUPLICATION
            $table->foreignId('from_country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('from_city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('to_country_id')->constrained('countries')->onDelete('cascade');

            $table->unsignedBigInteger('admin_id')->nullable();
            $table->double('profit', 8, 2)->default(0.00);
            $table->double('percentage_profit_margin', 8, 2)->default(0.00);
            $table->boolean('active')->default(false);
            $table->timestamps();
            $table->softDeletes(); // For soft delete support

            // Performance indexes from recommendations
            $table->index('is_public', 'idx_trips_public');
            $table->index('expiry_date', 'idx_trips_expiry');
            $table->index('is_ad');
            $table->index('company_id');

            // Composite index for search optimization
            $table->index(['from_country_id', 'to_country_id', 'expiry_date'], 'idx_trips_search');
        });

        // Create trip_images table
        Schema::create('trip_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();

            // Index for performance
            $table->index('trip_id');
        });

        // Create trip_rates table
        Schema::create('trip_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->integer('user_id')->nullable();
            $table->integer('rate')->nullable();
            $table->text('review')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();

            // Indexes for performance
            $table->index('trip_id');
            $table->index('user_id');
        });

        // Create trip_page_visits table
        Schema::create('trip_page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->string('ip');
            $table->boolean('active')->default(false);

            // Indexes for analytics
            $table->index('trip_id');
            $table->index('user_id');
            $table->index('ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_page_visits');
        Schema::dropIfExists('trip_rates');
        Schema::dropIfExists('trip_images');
        Schema::dropIfExists('trips');
    }
};
