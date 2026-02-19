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
        Schema::table('trip_itineraries', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('day_number');
        });

        // Initialize sort_order with current day_number values
        DB::table('trip_itineraries')->update([
            'sort_order' => DB::raw('day_number')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_itineraries', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
