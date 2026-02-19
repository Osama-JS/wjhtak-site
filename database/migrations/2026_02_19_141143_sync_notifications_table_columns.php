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
        Schema::table('notifications', function (Blueprint $table) {
            // Rename is_show to is_read if it exists
            if (Schema::hasColumn('notifications', 'is_show')) {
                $table->renameColumn('is_show', 'is_read');
            } elseif (!Schema::hasColumn('notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('data');
            }

            // Add type if it doesn't exist
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type', 50)->default('general')->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'is_read')) {
                $table->renameColumn('is_read', 'is_show');
            }
            if (Schema::hasColumn('notifications', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
