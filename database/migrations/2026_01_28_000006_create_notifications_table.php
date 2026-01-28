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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('icon');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->longText('data')->nullable(); // Additional JSON data
            $table->boolean('is_show')->default(false);
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('is_show');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
