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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('description_ar', 2000)->nullable();
            $table->string('description_en', 2000)->nullable();
            $table->string('link')->nullable();
            $table->string('image_path');
            $table->string('mobile_image_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(false);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('active');
            $table->index('trip_id');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
