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
            $table->string('title');
            $table->string('desc', 2000)->nullable();
            $table->string('image_path');
            $table->string('mobile_image_path')->nullable();
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
