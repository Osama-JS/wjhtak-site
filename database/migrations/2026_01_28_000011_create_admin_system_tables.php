<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: This migration creates admin system tables for compatibility with wh.sql
     * These tables work alongside Laravel Spatie permissions package
     */
    public function up(): void
    {
        // Create admin_users table
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 190)->unique();
            $table->string('password', 60);
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->text('emails')->nullable();
            $table->text('phone_numbers')->nullable();
            $table->text('files')->nullable();
            $table->rememberToken();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            // Foreign key - self-referencing for hierarchy
            $table->foreign('admin_id')->references('id')->on('admin_users')->onDelete('set null');

            // Indexes
            $table->index('admin_id');
        });

        // Create admin_roles table
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        // Create admin_permissions table
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->string('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->timestamps();
        });

        // Create admin_menu table
        Schema::create('admin_menu', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('icon', 50);
            $table->string('uri')->nullable();
            $table->string('permission')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('parent_id');
        });

        // Create admin_role_users pivot table
        Schema::create('admin_role_users', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('user_id');
            $table->timestamps();

            $table->index(['role_id', 'user_id']);
        });

        // Create admin_role_permissions pivot table
        Schema::create('admin_role_permissions', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->timestamps();

            $table->index(['role_id', 'permission_id']);
        });

        // Create admin_role_menu pivot table
        Schema::create('admin_role_menu', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('menu_id');
            $table->timestamps();

            $table->index(['role_id', 'menu_id']);
        });

        // Create admin_user_permissions pivot table
        Schema::create('admin_user_permissions', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('permission_id');
            $table->timestamps();

            $table->index(['user_id', 'permission_id']);
        });

        // Create admin_operation_log table
        Schema::create('admin_operation_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('path');
            $table->string('method', 10);
            $table->string('ip');
            $table->text('input');
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_operation_log');
        Schema::dropIfExists('admin_user_permissions');
        Schema::dropIfExists('admin_role_menu');
        Schema::dropIfExists('admin_role_permissions');
        Schema::dropIfExists('admin_role_users');
        Schema::dropIfExists('admin_menu');
        Schema::dropIfExists('admin_permissions');
        Schema::dropIfExists('admin_roles');
        Schema::dropIfExists('admin_users');
    }
};
