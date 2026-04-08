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
        Schema::create('tbo_cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_code')->unique();
            $table->string('name')->index();
            $table->string('name_ar')->nullable()->index();
            $table->string('country_code')->index();
            $table->string('country_name')->index();
            $table->string('country_name_ar')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbo_cities');
    }
};
