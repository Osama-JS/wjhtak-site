<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@system.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'user_type' => User::TYPE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Assign super-admin role
        $superAdmin->assignRole('super-admin');

        // Create additional admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'user_type' => User::TYPE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        $admin->assignRole('admin');

        // Create manager user
        $manager = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'first_name' => 'Manager',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'user_type' => User::TYPE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Assign manager role
        $manager->assignRole('manager');
    }
}
